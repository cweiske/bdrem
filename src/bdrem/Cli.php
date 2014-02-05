<?php
namespace bdrem;

class Cli extends UserInterface
{
    protected function loadParameters()
    {
        $parser = parent::loadParameters();
        //set default renderer to console
        $parser->options['renderer']->default = 'console';

        return $parser;
    }
}
?>
