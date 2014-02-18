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

    protected function getLocalDate($dateStr)
    {
        if ($dateStr{0} != '?') {
            return strftime('%x', strtotime($dateStr));
        }

        $dateStr = str_replace('????', '1899', $dateStr);
        return str_replace(
            array('1899', '99'),
            array('????', '??'),
            strftime('%x', strtotime($dateStr))
        );
    }
}
?>
