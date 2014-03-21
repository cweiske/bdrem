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
 * Renders events in an iCalendar file
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Renderer_Ical extends Renderer
{
    /**
     * HTTP content type
     * @var string
     */
    protected $httpContentType = 'text/calendar; charset=utf-8';

    /**
     * Render the events in an iCalendar file
     *
     * @param array $arEvents Event objects to render
     *
     * @return string iCal file
     */
    public function render($arEvents)
    {
        $s = "BEGIN:VCALENDAR\n"
            . "VERSION:2.0\n"
            . "PRODID:-//bdrem\n";
        foreach ($arEvents as $event) {
            $props = array('BEGIN' => 'VEVENT');
            
            $props['UID'] = str_replace(
                array('-', '????'), array('', '0000'), $event->date
            )
                . '.' . $event->age
                . '.' . md5($event->title . '/' . $event->type)
                . '@bdrem';
            $props['DTSTART']  = str_replace('-', '', $event->date);
            $props['DURATION'] = 'P1D';
            $props['SUMMARY']  = sprintf(
                '%s - %s. %s', $event->title, $event->age, $event->type
            );
            $props['END'] = 'VEVENT';

            foreach ($props as $key => $value) {
                $s .= $key . ':' . $value . "\n";
            }
        }
        $s .= "END:VCALENDAR\n";
        return $s;
    }
}
?>
