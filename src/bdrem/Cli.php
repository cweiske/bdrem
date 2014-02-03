<?php
namespace bdrem;

class Cli extends UserInterface
{
    protected function loadParameters()
    {
        $params = $GLOBALS['argv'];
        array_shift($params);
        $storeInto = null;
        foreach ($params as $param) {
            if ($storeInto !== null) {
                $this->config->$storeInto = (int)$param;
                $storeInto = null;
                continue;
            }

            if ($param == '--days-after' || $param == '-a') {
                $storeInto = 'daysAfter';
                continue;
            } else if ($param == '--days-before' || $param == '-b') {
                $storeInto = 'daysBefore';
                continue;
            }
            $storeInto = null;
        }
    }

    protected function render($arEvents)
    {
        $r = new Renderer_Mail();
        $r->config = $this->config;
        $r->ansi = true;
        echo $r->render($arEvents);
    }
}
?>
