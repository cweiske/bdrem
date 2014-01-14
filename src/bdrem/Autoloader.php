<?php
namespace bdrem;

class Autoloader
{
    public function load($class)
    {
        $file = strtr($class, '_\\', '//') . '.php';
        if (stream_resolve_include_path($file)) {
            require $file;
        }
    }

    public static function register()
    {
        set_include_path(
            get_include_path() . PATH_SEPARATOR . __DIR__ . '/../'
        );
        spl_autoload_register(array(new self(), 'load'));
    }
}
?>