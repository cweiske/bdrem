#!/usr/bin/env php
<?php
if (ini_get('phar.readonly') == 1) {
    //re-run this script with phar writing activated
    passthru(PHP_BINARY . ' -dphar.readonly=0 ' . escapeshellarg($argv[0]));
    exit();
}

$pharfile = __DIR__ . '/../dist/bdrem-0.1.0.phar';
if (file_exists($pharfile)) {
    unlink($pharfile);
}
$phar = new Phar($pharfile, 0, 'bdrem.phar');
$phar->startBuffering();

// add all files in the project
$phar->buildFromDirectory(
    realpath(__DIR__ . '/../'),
    '#'
    . '^' . preg_quote(realpath(__DIR__ . '/../'), '#')
    . '/(data/bdrem.config.php.dist|lib/|src/bdrem/|www/|README\.rst)'
    . '#'
);

//remove shebang from bin/bdrem.php
$bin = file_get_contents(__DIR__ . '/../bin/bdrem.php');
$phar->addFromString('bin/bdrem.php', substr($bin, strpos($bin, "\n") + 1));

$phar->setStub(file_get_contents(__DIR__ . '/../src/phar-stub.php'));
$phar->stopBuffering();
?>
