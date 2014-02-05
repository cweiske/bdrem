<?php
namespace bdrem;

class WebText extends Web
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
