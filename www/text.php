<?php
/**
 * Start the bdrem plain text web interface
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

if (file_exists(__DIR__ . '/../src/bdrem/Autoloader.php')) {
    include_once __DIR__ . '/../src/bdrem/Autoloader.php';
    Autoloader::register();
}
$web = new WebText();
$web->run();
?>
