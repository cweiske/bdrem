<?php
namespace bdrem;

class WebText extends Web
{
    protected function render($arEvents)
    {
        header('Content-type: text/plain; charset=utf-8');
        $r = new Renderer_Console();
        echo $r->render($arEvents);
    }
}
?>
