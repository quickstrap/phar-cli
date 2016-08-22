<?php
Phar::mapPhar();
//Phar::interceptFileFuncs();
include 'phar://' . __FILE__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'phar-cli.php';

__HALT_COMPILER();