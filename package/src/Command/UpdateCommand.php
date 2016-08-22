<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 11:02 PM
 */

namespace PharCli\Command;

use Composer\Command\UpdateCommand as ComposerUpdateCommand;

class UpdateCommand extends ComposerUpdateCommand
{
    use ComposerCommandTrait;
}