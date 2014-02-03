<?php
namespace bdrem;

class Web extends UserInterface
{
    protected function render($arEvents)
    {
        $r = new Renderer_Html();
        echo $r->render($arEvents);
    }

    protected function loadParameters()
    {
        if (isset($_GET['daysBefore'])) {
            $this->config->daysBefore = (int) $_GET['daysBefore'];
        }
        if (isset($_GET['daysAfter'])) {
            $this->config->daysAfter = (int) $_GET['daysAfter'];
        }
    }
}
?>
