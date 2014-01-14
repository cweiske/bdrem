#!/usr/bin/env php
<?php
namespace bdrem;

if (file_exists(__DIR__ . '/../src/bdrem/Autoloader.php')) {
    require_once __DIR__ . '/../src/bdrem/Autoloader.php';
    Autoloader::register();
}
$cli = new Cli();
$cli->run();
?>
