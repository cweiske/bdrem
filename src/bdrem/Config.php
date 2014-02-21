<?php
namespace bdrem;

class Config
{
    public $source;
    public $date;
    public $daysPrev = 3;
    public $daysNext = 7;
    public $locale;
    public $stopOnEmpty = false;
    public $cfgFileExists;

    public function load()
    {
        $f = __DIR__ . '/../../data/bdrem.config.php';
        if (file_exists($f)) {
            $this->cfgFileExists = true;
            return $this->loadFile($f);
        }

        $this->cfgFileExists = false;
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

    public function setDate($date)
    {
        if ($date === null) {
            $this->date = date('Y-m-d');
        } else {
            $dt = new \DateTime($date);
            $this->date = $dt->format('Y-m-d');
        }
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
