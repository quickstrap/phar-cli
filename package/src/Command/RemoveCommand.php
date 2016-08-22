<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 1:07 PM
 */

namespace PharCli\Command;

use \Composer\Command\RemoveCommand as ComposerRemoveCommand;

class RemoveCommand extends ComposerRemoveCommand
{
    use ComposerCommandTrait;
}