<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 11:01 PM
 */

namespace PharCli\Command;

use \Composer\Command\ShowCommand as ComposerShowCommand;

class ShowCommand extends ComposerShowCommand
{
    use ComposerCommandTrait;

    protected function updateDependencies()
    {
        // no work is done so skip
    }
}