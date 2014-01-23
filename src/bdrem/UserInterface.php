<?php
namespace bdrem;

abstract class UserInterface
{
    public function run()
    {
        $cfg = new Config();
        $cfg->load();
        setlocale(LC_TIME, $cfg->locale);
        $source = $cfg->loadSource();

        $this->loadParameters($cfg);
        $arEvents = $source->getEvents(
            date('Y-m-d'), $cfg->daysBefore, $cfg->daysAfter
        );
        usort($arEvents, '\\bdrem\\Event::compare');
        $this->render($arEvents);
    }

    protected function loadParameters($cfg)
    {
    }

    abstract protected function render($arEvents);
}
?>