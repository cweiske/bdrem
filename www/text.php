<?php
namespace bdrem;

if (file_exists(__DIR__ . '/../src/bdrem/Autoloader.php')) {
    require_once __DIR__ . '/../src/bdrem/Autoloader.php';
    Autoloader::register();
}
$web = new WebText();
$web->run();
?>
