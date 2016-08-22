<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 12:55 PM
 */

namespace PharCli\Command;
use \Composer\Command\RequireCommand as ComposerRequireCommand;

class RequireCommand extends ComposerRequireCommand
{
    use ComposerCommandTrait;
}