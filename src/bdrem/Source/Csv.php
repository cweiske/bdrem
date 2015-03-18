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
 * Reads comma separated value files (CSV)
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @link      http://cweiske.de/bdrem.htm
 */
class Source_Csv
{
    /**
     * Full path of CSV file
     * @var string
     */
    protected $filename;

    /**
     * If the first line is to be seen as header
     * @var boolean
     */
    protected $firstLineIsHeader = true;

    /**
     * If the CSV does not contain a column for the event type,
     * this text will be used.
     * @var string
     */
    protected $defaultEvent = 'Birthday';

    /**
     * Field separator in the CSV file
     * @var string
     */
    protected $separator = ',';

    /**
     * Position of the name, event and date columns. First column is 0.
     * Use "false" to disable the column (useful for "event").
     *
     * @var array
     */
    protected $columns = array(
        'name'  => 0,
        'event' => 1,
        'date'  => 2,
    );

    /**
     * Set the CSV file name
     *
     * @param array|string $config Config array or path to CSV file
     */
    public function __construct($config)
    {
        if (is_string($config)) {
            $config = array(
                'filename' => $config
            );
        }
        $this->filename = $config['filename'];
        if (!file_exists($this->filename)) {
            throw new \Exception(
                'CSV file does not exist: ' . $this->filename
            );
        }
        if (isset($config['columns'])) {
            $this->columns = $config['columns'];
        }
        if (isset($config['defaultEvent'])) {
            $this->defaultEvent = $config['defaultEvent'];
        }
        if (isset($config['separator'])) {
            $this->separator = $config['separator'];
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
        $handle = fopen($this->filename, 'r');
        if ($handle === false) {
            throw new \Exception('Error opening CSV file');
        }

        $first = true;
        $arEvents = array();
        while (($row = fgetcsv($handle, 1000, $this->separator)) !== false) {
            if ($first && $this->firstLineIsHeader) {
                $first = false;
                continue;
            }

            if ($this->columns['event'] === false) {
                $eventType = $this->defaultEvent;
            } else {
                if (!isset($row[$this->columns['event']])) {
                    throw new \Exception('Event column does not exist in CSV');
                }
                $eventType = $row[$this->columns['event']];
            }

            if (!isset($row[$this->columns['date']])) {
                throw new \Exception('Date column does not exist in CSV');
            }
            if ($row[$this->columns['date']] == '') {
                continue;
            }
            //convert from DD.MM.YYYY to YYYY-MM-DD
            $date = implode(
                '-',
                array_reverse(
                    explode('.', $row[$this->columns['date']])
                )
            );

            if (!isset($row[$this->columns['name']])) {
                throw new \Exception('Name column does not exist in CSV');
            }

            $event = new Event(
                (string) $row[$this->columns['name']],
                $eventType,
                $date
            );
            if ($event->isWithin($strDate, $nDaysPrevious, $nDaysNext)) {
                $arEvents[] = $event;
            }
        }
        fclose($handle);
        return $arEvents;
    }
}
?>
