<?php
namespace bdrem;

class Cli
{
    public function run()
    {
        $cfg = new Config();
        $cfg->load();
        $source = $cfg->loadSource();

        $arEvents = $source->getEvents(
            date('Y-m-d'), $cfg->daysBefore, $cfg->daysAfter
        );
        usort($arEvents, '\\bdrem\\Event::compare');

        $r = new Renderer_Console();
        echo $r->render($arEvents);
    }
}
?>
