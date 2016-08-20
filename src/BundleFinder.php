<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 11:38 AM
 */

namespace PharCli;


use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BundleFinder
{
    public function find($bundlesDirectory)
    {
        $bundles = [];
        // iterate through vendor folders
        foreach(new \DirectoryIterator($bundlesDirectory) as $vendorIterator) {
            if ( (!$vendorIterator->isDir()) || $vendorIterator->isDot()) {
                continue;
            }

            // iterate through project folders
            foreach(new \DirectoryIterator($vendorIterator->getPathname()) as $projectIterator) {
                if ( (!$projectIterator->isDir()) || $projectIterator->isDot()) {
                    continue;
                }

                if (($bundle = $this->findBundle($projectIterator->getPathname())) !== null) {
                    $bundles[] = $bundle;
                }
            }
        }

        return $bundles;
    }

    protected function findBundle($dir) {
        if (!is_dir($dir)) {
            return null;
        }

        if (!class_exists('Symfony\Component\Finder\Finder')) {
            throw new \RuntimeException('You need the symfony/finder component to register bundle commands.');
        }

        $finder = new Finder();
        $finder->files()->name('*Bundle.php')->in($dir);


        foreach ($finder as $file) {
            $reflection = $this->getClassReflection($file->getPathname());
            if(!$reflection) {
                continue;
            }

            if ($reflection->isSubclassOf(Bundle::class)
                && !$reflection->isAbstract()
                && (!$reflection->getConstructor() || !$reflection->getConstructor()->getNumberOfRequiredParameters())
            ) {
                return $reflection->getName();
            }
        }
        return null;
    }

    protected function getClassReflection($file)
    {
        $fp = fopen($file, 'r');
        if (!$fp) {
            return null;
        }
        $class = $namespace = $buffer = '';
        $i = 0;
        while (!$class) {
            if (feof($fp)) break;

            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) continue;

            for (;$i<count($tokens);$i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1;$j<count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1;$j<count($tokens);$j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }
        return new \ReflectionClass("$namespace\\$class");
    }

}