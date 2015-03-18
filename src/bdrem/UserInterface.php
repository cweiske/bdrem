<?php
/**
 * Part of bdrem
 *
 * PHP version 5
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @link      http://cweiske.de/bdrem.htm
 */
namespace bdrem;

/**
 * Generic user interface class
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
abstract class UserInterface
{
    /**
     * Configuration
     * @var Config
     */
    protected $config;

    /**
     * Start the user interface, load config, parse and render events.
     *
     * @return void
     */
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
                    . '- ' . implode("\n- ", $this->config->cfgFiles)
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

    /**
     * Load parameters for the CLI option parser.
     *
     * @return \Console_CommandLine CLI option parser
     */
    protected function loadParameters()
    {
        $parser = new \Console_CommandLine();
        $parser->description = 'Birthday reminder';
        $parser->version = '0.6.0';

        $parser->addOption(
            'daysNext',
            array(
                'short_name'  => '-n',
                'long_name'   => '--next',
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
                'long_name'   => '--prev',
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
                    'ical',
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

    /**
     * Let the CLI option parser parse the options.
     *
     * @param object $parser Option parser
     *
     * @return object Parsed command line parameters
     */
    protected function parseParameters(\Console_CommandLine $parser)
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
            if (isset($result->options['ansi'])) {
                $this->config->ansi = $result->options['ansi'];
            }
            return $result;
        } catch (\Exception $exc) {
            $this->preRenderParameterError();
            $parser->displayError($exc->getMessage());
        }
    }

    /**
     * Output the events
     *
     * @param array $arEvents Event objects to render
     *
     * @return void
     */
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

    /**
     * Load the configured renderer
     *
     * @return Renderer Renderer instance
     */
    protected function getRenderer()
    {
        $renderer = ucfirst($this->config->renderer);
        if ($renderer == 'Htmltable') {
            $renderer = 'HtmlTable';
        }
        $class = '\\bdrem\\Renderer_' . $renderer;
        return new $class();
    }

    /**
     * Handle any commands given on the CLI
     *
     * @param object $res Command line parameters and options
     *
     * @return void
     */
    protected function handleCommands($res)
    {
    }

    /**
     * Do something before a parameter parsing error is shown
     *
     * @return void
     */
    protected function preRenderParameterError()
    {
    }
}
?>