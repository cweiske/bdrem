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
 * HTTP user interface that renders a HTML page
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Web extends UserInterface
{
    /**
     * Load parameters for the CLI option parser.
     * Set the default renderer to "html".
     *
     * @return \Console_CommandLine CLI option parser
     */
    protected function loadParameters()
    {
        $parser = parent::loadParameters();
        //set default renderer to html
        $parser->options['renderer']->default = 'html';

        return $parser;
    }

    /**
     * Sends HTTP headers before a parameter error is shown
     *
     * @return void
     */
    protected function preRenderParameterError()
    {
        header('Content-type: text/plain; charset=utf-8');
    }
}
?>
