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
 * HTTP user interface that renders a ASCII table
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class WebText extends Web
{
    /**
     * Load parameters for the CLI option parser.
     * Set the default renderer to "console".
     *
     * @return \Console_CommandLine CLI option parser
     */
    protected function loadParameters()
    {
        $parser = parent::loadParameters();
        //set default renderer to console
        $parser->options['renderer']->default = 'console';

        return $parser;
    }
}
?>
