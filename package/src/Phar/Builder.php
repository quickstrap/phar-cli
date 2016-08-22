<?php
namespace PharCli\Phar;


use Phar;

class Builder
{
    const PACKAGE_DIR = __DIR__ . '/../..';
    const ROOT_DIR = __DIR__  . '/../../..';

    public function build()
    {
        $config = $this->getConfig();

        $pharName = sprintf("%s/bin/%s.phar", self::ROOT_DIR, $config['phar_name']);

        @unlink($pharName);

        $phar = new Phar($pharName);
        $phar->buildFromDirectory(self::PACKAGE_DIR);
        $phar->setStub(file_get_contents(self::ROOT_DIR . '/build/resources/stub.php'));

        return $pharName;
    }

    private function getConfig()
    {
        $default = parse_ini_file(self::ROOT_DIR . '/build/config.ini.dist');
        if (file_exists($override = self::ROOT_DIR . '/build/config.ini.dist')) {
            $default = array_merge($default, parse_ini_file($override));
        }

        return $default;
    }
}