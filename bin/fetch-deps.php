#!/usr/bin/env php
<?php
/**
 * reads pear package dependencies from deps.txt and copies them into lib/
 */
$deps = file(__DIR__ . '/../deps.txt');
$libdir = __DIR__ . '/../lib/';

error_reporting(error_reporting() & ~E_STRICT & ~E_DEPRECATED);
require_once 'PEAR/Registry.php';
$reg = new PEAR_Registry();

foreach ($deps as $dep) {
    $dep = trim($dep);
    list($channel, $pkgname) = explode('/', $dep);
    $pkginfo = $reg->packageInfo($pkgname, null, $channel);
    if ($pkginfo === null) {
        echo 'Package not found: ' . $dep . "\n";
        exit(1);
    }

    echo "Copying " . $channel . '/' . $pkgname . "\n";
    $files = 0;
    foreach ($pkginfo['filelist'] as $fileinfo) {
        if ($fileinfo['role'] != 'php') {
            continue;
        }

        $orig = $fileinfo['installed_as'];
        $path = $libdir . ltrim(
            $fileinfo['baseinstalldir'] . '/' . $fileinfo['name'], '/'
        );
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!copy($orig, $path)) {
            echo " Error copying $orig to $path\n";
            exit(2);
        }
        ++$files;
    }
    echo " copied $files files\n";
}
?>
