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
 * Base event renderer
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
abstract class Renderer
{
    /**
     * HTTP content type of output
     * @var string
     */
    protected $httpContentType = null;

    /**
     * Call the renderer and output the rendering result to shell or browser
     *
     * @param array $arEvents Event objects to render
     *
     * @return void
     */
    public function renderAndOutput($arEvents)
    {
        if (PHP_SAPI != 'cli' && $this->httpContentType !== null) {
            header('Content-type: ' . $this->httpContentType);
        }
        echo $this->render($arEvents);
    }

    /**
     * Do something when there are no events to render
     *
     * @return void
     */
    public function handleStopOnEmpty()
    {
    }

    /**
     * Display the events in some way
     *
     * @param array $arEvents Events to display
     *
     * @return string Event representation
     */
    abstract public function render($arEvents);

    /**
     * Converts the given date string according to the user's locale setting.
     *
     * @param string $dateStr Date in format YYYY-MM-DD
     *
     * @return string Formatted date
     */
    protected function getLocalDate($dateStr)
    {
        if ($dateStr{0} != '?') {
            return strftime('%x', strtotime($dateStr));
        }

        $dateStr = str_replace('????', '1899', $dateStr);
        return str_replace(
            array('1899', '99'),
            array('????', '??'),
            strftime('%x', strtotime($dateStr))
        );
    }
}
?>
