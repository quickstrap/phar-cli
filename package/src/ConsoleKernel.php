<?php

namespace PharCli;


use Phar;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;


class ConsoleKernel extends Kernel
{
    public function __construct()
    {
        $this->rootDir = __DIR__ . '/package/';
        parent::__construct('dev',true);
    }


    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles()
    {
        $finder = new BundleFinder();

        // TODO cache results under composer.lock hash value
        $bundles = $finder->find(__DIR__ . '/../bundles');
        $bundles = array_merge($bundles, $finder->find(__DIR__ . '/../vendor'));

        $instances = [];
        foreach($bundles as $bundleClass) {
            $instances[] = new $bundleClass();
        }
        return $instances;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../config.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        $name = basename(Phar::running(false));
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . '/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        $name = basename(Phar::running(false));
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . '/logs/'.$this->environment;
    }
}