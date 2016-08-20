<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 10:38 AM
 */

namespace PharCli;


use Composer\Command\BaseCommand as ComposerCommand;
use Composer\Command\ShowCommand;
use Composer\Command\UpdateCommand;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use PharCli\Command\RemoveCommand;
use PharCli\Command\RequireCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;


class PharCliApplication extends Application
{
    /** @var  Kernel */
    private $kernel;
    /** @var  bool */
    private $commandsRegistered;
    /** @var  ConsoleIO */
    private $io;

    /**
     * PharCli constructor.
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        parent::__construct('PharCli', '0.0.1');

        $this->kernel = $kernel;
    }

    public function get($name)
    {
        $this->registerCommands();

        return parent::get($name);
    }

    public function all($namespace = null)
    {
        $this->registerCommands();

        return parent::all($namespace);
    }

    protected function getDefaultCommands()
    {
        $default = parent::getDefaultCommands();

        $composerCommands = [
            new UpdateCommand(),
            new RemoveCommand(),
            new RequireCommand(),
            new ShowCommand()
        ];

        $default = array_merge($default, $composerCommands);

        return $default;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $newWorkingDir = __DIR__ . '/../';
        $oldWorkingDir = getcwd();
        chdir($newWorkingDir);

        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        $this->io = new ConsoleIO($input, $output, $this->getHelperSet());

        $composer = Factory::create($this->io);
        $container->set('composer', $composer);

        foreach($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }

            if ($command instanceof ComposerCommand) {
                $command->setComposer($composer);
                $command->setIO($this->io);
                // works around call to getComposer that expects option to exist globally
                $command->getDefinition()->addOption(new InputOption('no-plugins'));
            }

        }

        $returnCode = parent::doRun($input, $output);

        $this->kernel->shutdown();

        chdir($oldWorkingDir);

        return $returnCode;
    }

    protected function registerCommands()
    {
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        foreach ($this->kernel->getBundles() as $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($this);
            }
        }

        if ($container->hasParameter('console.command.ids')) {
            foreach ($container->getParameter('console.command.ids') as $id) {
                $this->add($container->get($id));
            }
        }
    }

    public function resetComposer() {}
    public function getComposer() {
        $container = $this->kernel->getContainer();
        return $container->get('composer');
    }
}