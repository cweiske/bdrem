<?php
if (!in_array('phar', stream_get_wrappers()) || !class_exists('Phar', false)) {
    echo "Phar extension not avaiable\n";
    exit(255);
}

$web = 'www/index.php';
$cli = 'bin/bdrem.php';

function rewritePath($path)
{
    if ($path == '' || $path == '/') {
        return 'www/index.php';
    }
    return $path;
}

//Phar::interceptFileFuncs();
set_include_path(
    'phar://' . __FILE__
    . PATH_SEPARATOR . 'phar://' . __FILE__ . '/lib/'
);
Phar::webPhar(null, $web, null, array(), 'rewritePath');
include 'phar://' . __FILE__ . '/' . $cli;
__HALT_COMPILER();
?>
