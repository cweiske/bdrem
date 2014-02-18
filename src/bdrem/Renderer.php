<?php
namespace bdrem;

abstract class Renderer
{
    protected $httpContentType = null;

    public function renderAndOutput($arEvents)
    {
        if (PHP_SAPI != 'cli' && $this->httpContentType !== null) {
            header('Content-type: ' . $this->httpContentType);
        }
        echo $this->render($arEvents);
    }

    public function handleStopOnEmpty()
    {
    }

    abstract public function render($arEvents);
}
?>
