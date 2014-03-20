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
 * Command line user interface for the terminal/shell.
 * Renders an ASCII table by default.
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Cli extends UserInterface
{
    /**
     * Load parameters for the CLI option parser.
     * Set the default renderer to "console" and adds some CLI-only commands
     * like "readme" and "config".
     *
     * @return \Console_CommandLine CLI option parser
     */
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

    /**
     * Handle any commands given on the CLI
     *
     * @param object $res Command line parameters and options
     *
     * @return void
     */
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

    /**
     * Handle the "readme" command and output the readme.
     *
     * @return void
     */
    protected function showReadme()
    {
        readfile(__DIR__ . '/../../README.rst');
        exit();
    }

    /**
     * Handle the "config" command and output the default configuration
     *
     * @return void
     */
    protected function extractConfig()
    {
        readfile(__DIR__ . '/../../data/bdrem.config.php.dist');
        exit();
    }
}
?>
