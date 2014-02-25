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

    public $cfgFiles = array();
    public $cfgFileExists;

    public function load()
    {
        $this->loadConfigFilePaths();
        foreach ($this->cfgFiles as $file) {
            if (file_exists($file)) {
                $this->cfgFileExists = true;
                return $this->loadFile($file);
            }
        }
        $this->cfgFileExists = false;
    }

    protected function loadConfigFilePaths()
    {
        $pharFile = \Phar::running();
        if ($pharFile == '') {
            $this->cfgFiles[] = __DIR__ . '/../../data/bdrem.config.php';
        } else {
            //remove phar:// from the path
            $this->cfgFiles[] = substr($pharFile, 7) . '.config.php';
        }

        //TODO: add ~/.config/bdrem.php

        $this->cfgFiles[] = '/etc/bdrem.php';
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
