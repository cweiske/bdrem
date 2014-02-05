<?php
namespace bdrem;

class Web extends UserInterface
{
    protected function loadParameters()
    {
        $parser = parent::loadParameters();
        //set default renderer to html
        $parser->options['renderer']->default = 'html';

        return $parser;
    }

    protected function preRenderParameterError()
    {
        header('Content-type: text/plain; charset=utf-8');
    }
}
?>
