<?php
namespace bdrem;

class Config
{
    public $source;
    public $daysBefore;
    public $daysAfter;

    public function load()
    {
        $f = __DIR__ . '/../../data/bdrem.config.php';
        if (file_exists($f)) {
            return $this->loadFile($f);
        }

        throw new \Exception('No config file found');
    }

    protected function loadFile($filename)
    {
        include $filename;
        $this->source = $source;
        $this->daysBefore = $daysBefore;
        $this->daysAfter = $daysAfter;
    }

    public function loadSource()
    {
        if ($this->source === null) {
            throw new \Exception('No source defined');
        }

        $settings = $this->source;
        $class = '\\bdrem\\Source_' . array_shift($settings);

        return new $class($settings[0]);
        //$rm = new \ReflectionMethod($class, '__construct');
        //return $rm->invokeArgs(null, $settings);
        //return call_user_func_array($class . '::__construct', $settings);
    }
}
?>
