<?php
namespace bdrem;

class Web
{
    public function run()
    {
        $cfg = new Config();
        $cfg->load();
        setlocale(LC_TIME, $cfg->locale);
        $source = $cfg->loadSource();

        $arEvents = $source->getEvents(
            date('Y-m-d'), $cfg->daysBefore, $cfg->daysAfter
        );
        usort($arEvents, '\\bdrem\\Event::compare');

        $r = new Renderer_Html();
        echo $r->render($arEvents);
    }
}
?>
