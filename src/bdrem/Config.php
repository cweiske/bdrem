<?php
namespace bdrem;

class Config
{
    public $source;
    public $daysBefore;
    public $daysAfter;
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
        $this->source = $source;
        $this->daysBefore = $daysBefore;
        $this->daysAfter = $daysAfter;
        if (isset($locale)) {
            $this->locale = $locale;
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
}
?>
