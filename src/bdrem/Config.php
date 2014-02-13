<?php
namespace bdrem;

class Config
{
    public $source;
    public $date;
    public $daysPrev;
    public $daysNext;
    public $locale;

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
        $vars = get_defined_vars();
        foreach ($vars as $k => $value) {
            $this->$k = $value;
        }
    }

    public function loadSource()
    {
        if ($this->source === null) {
            throw new \Exception('No source defined');
        }

        $settings = $this->source;
        $class = '\\bdrem\\Source_' . array_shift($settings);

        return new $class($settings[0]);
    }

    public function get($varname, $default = '')
    {
        if (!isset($this->$varname) || $this->$varname == '') {
            return $default;
        }
        return $this->$varname;
    }
}
?>
