<?php
namespace bdrem;

class Cli extends UserInterface
{
    protected function loadParameters()
    {
        $parser = parent::loadParameters();
        //set default renderer to console
        $parser->options['renderer']->default = 'console';

        //only on CLI
        $parser->addCommand(
            'readme', array(
                'description' => 'Show README.rst file'
            )
        );
        $parser->addCommand(
            'config', array(
                'description' => 'Extract configuration file'
            )
        );

        return $parser;
    }

    protected function handleCommands($res)
    {
        if ($res->command_name == '') {
            return;
        } else if ($res->command_name == 'readme') {
            $this->showReadme();
        } else if ($res->command_name == 'config') {
            $this->extractConfig();
        } else {
            throw new \Exception('Unknown command');
        }
    }

    protected function showReadme()
    {
        readfile(__DIR__ . '/../../README.rst');
        exit();
    }

    protected function extractConfig()
    {
        readfile(__DIR__ . '/../../data/bdrem.config.php.dist');
        exit();
    }
}
?>
