<?php
namespace PharCli\Command;


use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\IO\IOInterface;
use Phar;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

trait ComposerCommandTrait
{
    private $composer;
    /** @var  string */
    private $workingDir;
    /** @var  string */
    private $oldWorkingDir;

    /** @return Application */
    abstract public function getApplication();
    abstract public function setIO(IOInterface $io);
    /** @return IOInterface */
    abstract public function getIO();

    /** @return InputDefinition */
    abstract public function getDefinition();

    protected function ensureWritablePhar(OutputInterface $output)
    {
        if(! empty(ini_get('phar.readonly'))) {
            $output->writeln("You must set phar.readonly to off in the php.ini configuration to execute this command.");
            throw new \RuntimeException("Invalid environment configuration.");
        }
    }

    protected function setupTempWorkDir()
    {
        $name = basename(Phar::running(false));
        $this->workingDir = $workDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name . '/composer';

        @mkdir($workDir, 0600, true);
        if(!file_exists($workDir)) {
            throw new \RuntimeException("Failed to create working directory for composer.");
        }

        $this->oldWorkingDir = getcwd();

        $runningPhar = Phar::running(false);
        $phar = new Phar($runningPhar);
        $phar->extractTo($workDir, null, true);

        chdir($workDir);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '256M');
        $this->ensureWritablePhar($output);

        $this->setupTempWorkDir();

        $io = new ConsoleIO($input, $output, $this->getApplication()->getHelperSet());

        $this->setIO($io);
        $this->setComposer($this->getComposer());
        $this->getDefinition()->addOption(new InputOption('no-plugins'));
        $out = parent::execute($input, $output);

        chdir($this->oldWorkingDir);

        $this->updateDependencies();

        return $out;
    }

    protected function updateDependencies()
    {
        $this->getIO()->write("Updating Phar ...");
        $runningPhar = Phar::running(false);
        $workDir = $this->workingDir;

        // do this after php ends so we don't run into an internal corruption error
        register_shutdown_function(function() use ($runningPhar, $workDir) {
            $phar = new Phar($runningPhar);
            $phar->buildFromDirectory($workDir);
        });
    }

    public function getComposer($required = true, $disablePlugins = null)
    {
        if (null === $this->composer) {
            $this->composer = Factory::create($this->getIO(),
                $this->workingDir . DIRECTORY_SEPARATOR . 'composer.json',
                $disablePlugins);
        }

        return $this->composer;
    }

    public function resetComposer()
    {
        $this->composer = null;
    }
}