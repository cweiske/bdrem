<?php
namespace bdrem;

abstract class UserInterface
{
    protected $config;

    public function run()
    {
        $this->config = new Config();
        $this->config->load();
        $this->config->date = date('Y-m-d');
        setlocale(LC_TIME, $this->config->locale);
        $source = $this->config->loadSource();

        $this->loadParameters($this->config);
        $arEvents = $source->getEvents(
            $this->config->date,
            $this->config->daysBefore, $this->config->daysAfter
        );
        usort($arEvents, '\\bdrem\\Event::compare');
        $this->render($arEvents);
    }

    protected function loadParameters()
    {
    }

    abstract protected function render($arEvents);
}
?>