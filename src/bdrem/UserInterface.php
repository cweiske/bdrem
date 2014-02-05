<?php
namespace bdrem;

abstract class UserInterface
{
    protected $config;

    public function run()
    {
        $this->config = new Config();
        $this->config->load();
        $this->config->date = date('Y-m-d');
        setlocale(LC_TIME, $this->config->locale);
        $source = $this->config->loadSource();

        $parser = $this->loadParameters();
        $this->parseParameters($parser);

        $arEvents = $source->getEvents(
            $this->config->date,
            $this->config->daysBefore, $this->config->daysAfter
        );
        usort($arEvents, '\\bdrem\\Event::compare');
        $this->render($arEvents);
    }

    protected function loadParameters()
    {
        $parser = new \Console_CommandLine();
        $parser->description = 'Birthday reminder';
        $parser->version = '0.1.0';

        $parser->addOption(
            'daysAfter',
            array(
                'short_name'  => '-a',
                'long_name'   => '--days-after',
                'description' => 'Show NUM days after date',
                'help_name'   => 'NUM',
                'action'      => 'StoreInt',
                'default'     => $this->config->daysAfter,
            )
        );
        $parser->addOption(
            'daysBefore',
            array(
                'short_name'  => '-b',
                'long_name'   => '--days-before',
                'description' => 'Show NUM days before date',
                'help_name'   => 'NUM',
                'action'      => 'StoreInt',
                'default'     => $this->config->daysBefore,
            )
        );
        $parser->addOption(
            'renderer',
            array(
                'short_name'  => '-r',
                'long_name'   => '--renderer',
                'description' => 'Output mode',
                'action'      => 'StoreString',
                'choices'     => array(
                    'console',
                    'html',
                    'htmltable',
                    'mail',
                ),
                'default'     => 'console',
                'add_list_option' => true,
            )
        );
        $parser->addOption(
            'quiet',
            array(
                'short_name'  => '-q',
                'long_name'   => '--quiet',
                'description' => "Don't print status messages to stdout",
                'action'      => 'StoreTrue'
            )
        );
        return $parser;
    }

    protected function parseParameters($parser)
    {
        try {
            $result = $parser->parse();
            // do something with the result object
            $this->config->daysAfter  = $result->options['daysAfter'];
            $this->config->daysBefore = $result->options['daysBefore'];
            $this->config->renderer   = $result->options['renderer'];
            $this->config->quiet      = $result->options['quiet'];
        } catch (\Exception $exc) {
            $this->preRenderParameterError();
            $parser->displayError($exc->getMessage());
        }
    }

    protected function render($arEvents)
    {
        $r = $this->getRenderer();
        $r->config = $this->config;
        $r->renderAndOutput($arEvents);
    }

    protected function getRenderer()
    {
        $renderer = ucfirst($this->config->renderer);
        if ($renderer == 'Htmltable') {
            $renderer = 'HtmlTable';
        }
        $class = '\\bdrem\\Renderer_' . $renderer;
        return new $class();
    }

    protected function preRenderParameterError()
    {
    }
}
?>