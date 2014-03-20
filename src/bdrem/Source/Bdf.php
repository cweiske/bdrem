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
 * Reads birthday reminder 2's birthday files (.bdf).
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @link      http://cweiske.de/bdrem.htm
 */
class Source_Bdf
{
    /**
     * Full path of bdf birthday file
     * @var string
     */
    protected $filename;

    /**
     * Set the birthday file name
     *
     * @param string $filename Path to bdf file
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        if (!file_exists($this->filename)) {
            throw new \Exception(
                'Birthday file does not exist: ' . $this->filename
            );
        }
    }

    /**
     * Return all events for the given date range
     *
     * @param string  $strDate       Date the events shall be found for,
     *                               YYYY-MM-DD
     * @param integer $nDaysPrevious Include number of days before $strDate
     * @param integer $nDaysNext     Include number of days after $strDate
     *
     * @return Event[] Array of matching event objects
     */
    public function getEvents($strDate, $nDaysPrevious, $nDaysNext)
    {
        $x = simplexml_load_file($this->filename);

        $arEvents = array();
        foreach ($x->content->person as $xPerson) {
            $date = implode(
                '-',
                array_reverse(
                    explode('.', (string) $xPerson->date)
                )
            );
            $event = new Event(
                (string) $xPerson->name,
                (string) $xPerson->event,
                $date
            );
            if ($event->isWithin($strDate, $nDaysPrevious, $nDaysNext)) {
                $arEvents[] = $event;
            }
        }
        return $arEvents;
    }
}
?>
