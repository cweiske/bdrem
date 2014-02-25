<?php
namespace bdrem;

abstract class UserInterface
{
    protected $config;

    public function run()
    {
        try {
            $this->config = new Config();
            $parser = $this->loadParameters();
            $res = $this->parseParameters($parser);

            $this->config->load();
            if (!$this->config->cfgFileExists) {
                throw new \Exception(
                    "No config file found. Looked at the following places:\n"
                    . '- ' . implode ("\n- ", $this->config->cfgFiles)
                );
            }

            setlocale(LC_TIME, $this->config->locale);
            $this->handleCommands($res);

            $source = $this->config->loadSource();
            $arEvents = $source->getEvents(
                $this->config->date,
                $this->config->daysPrev, $this->config->daysNext
            );
            usort($arEvents, '\\bdrem\\Event::compare');
            $this->render($arEvents);
        } catch (\Exception $e) {
            $this->preRenderParameterError();
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    protected function loadParameters()
    {
        $parser = new \Console_CommandLine();
        $parser->description = 'Birthday reminder';
        $parser->version = '0.1.0';

        $parser->addOption(
            'daysNext',
            array(
                'short_name'  => '-n',
                'long_name'   => '--days-next',
                'description' => 'Show NUM days after date',
                'help_name'   => 'NUM',
                'action'      => 'StoreInt',
                'default'     => null,
            )
        );
        $parser->addOption(
            'daysPrev',
            array(
                'short_name'  => '-p',
                'long_name'   => '--previous',
                'description' => 'Show NUM days before date',
                'help_name'   => 'NUM',
                'action'      => 'StoreInt',
                'default'     => null,
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
            'stopOnEmpty',
            array(
                'short_name'  => '-e',
                'long_name'   => '--stoponempty',
                'description' => 'Output nothing when list is empty',
                'action'      => 'StoreTrue',
                'default'     => false
            )
        );
        $parser->addOption(
            'date',
            array(
                'short_name'  => '-d',
                'long_name'   => '--date',
                'description' => 'Date to show events for',
                'action'      => 'StoreString'
            )
        );
        $parser->addOption(
            'configfile',
            array(
                'short_name'  => '-c',
                'long_name'   => '--config',
                'help_name'   => 'FILE',
                'description' => 'Path to configuration file',
                'action'      => 'StoreString'
            )
        );

        return $parser;
    }

    protected function parseParameters($parser)
    {
        try {
            $result = $parser->parse();

            if ($result->options['configfile'] !== null) {
                $this->config->cfgFiles = array($result->options['configfile']);
            }

            $this->config->daysNext    = $result->options['daysNext'];
            $this->config->daysPrev    = $result->options['daysPrev'];
            $this->config->renderer    = $result->options['renderer'];
            $this->config->stopOnEmpty = $result->options['stopOnEmpty'];
            $this->config->setDate($result->options['date']);
            return $result;
        } catch (\Exception $exc) {
            $this->preRenderParameterError();
            $parser->displayError($exc->getMessage());
        }
    }

    protected function render($arEvents)
    {
        $r = $this->getRenderer();
        $r->config = $this->config;

        if ($this->config->stopOnEmpty && count($arEvents) == 0) {
            $r->handleStopOnEmpty();
            return;
        }
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

    protected function handleCommands($res)
    {
    }

    protected function preRenderParameterError()
    {
    }
}
?>