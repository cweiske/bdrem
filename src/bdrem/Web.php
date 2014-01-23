<?php
namespace bdrem;

class Web extends UserInterface
{
    protected function render($arEvents)
    {
        $r = new Renderer_Html();
        echo $r->render($arEvents);
    }

    protected function loadParameters($cfg)
    {
        if (isset($_GET['daysBefore'])) {
            $cfg->daysBefore = (int) $_GET['daysBefore'];
        }
        if (isset($_GET['daysAfter'])) {
            $cfg->daysAfter = (int) $_GET['daysAfter'];
        }
    }
}
?>
