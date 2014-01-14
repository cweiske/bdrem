<?php
namespace bdrem;

class Event
{
    /**
     * Title of the event or name of the person that has the event
     */
    public $title;

    /**
     * Type of the event, e.g. "birthday"
     */
    public $type;

    /**
     * Date of the event. 
     * ???? as year is allowed
     *
     * @var string YYYY-MM-DD
     */
    public $date;

    /**
     * Which repetition this is
     *
     * @var integer
     */
    public $age;

    /**
     * Number of days until the event (positive) or since the event (negative)
     *
     * @var integer
     */
    public $days;



    public function __construct($title = null, $type = null, $date = null)
    {
        $this->title = $title;
        $this->type  = $type;
        $this->date  = $date;
    }

    /**
     * Checks if the event's date is within the given date.
     * Also calculates the age and days since the event.
     *
     * @return boolean True if the event's date is within the given range
     */
    public function isWithin($strDate, $nDaysBefore, $nDaysAfter)
    {
        list($rYear, $rMonth, $rDay) = explode('-', $strDate);
        list($eYear, $eMonth, $eDay) = explode('-', $this->date);

        if ($rMonth == $eMonth && $rDay == $eDay) {
            $this->days = 0;
            if ($eYear == '????') {
                $this->age = null;
            } else {
                $this->age = $rYear - $eYear;
            }
            return true;
        }

        $yearOffset = 0;
        if ($eMonth < 3 && $rMonth > 10) {
            $yearOffset = 1;
        } else if ($eMonth > 10 && $rMonth < 3) {
            $yearOffset = -1;
        }

        $rD = new \DateTime($strDate);
        $eD = new \DateTime(($rYear + $yearOffset) . '-' . $eMonth . '-' . $eDay);

        $nDiff = (int) $rD->diff($eD)->format('%r%a');

        $this->days = $nDiff;
        if ($eYear == '????') {
            $this->age = null;
        } else {
            $this->age = $rYear - $eYear + $yearOffset;
        }

        if ($nDiff > 0) {
            return $nDiff <= $nDaysAfter;
        } else {
            return -$nDiff <= $nDaysBefore;
        }

        return false;
    }

    /**
     * @return integer x < 0: e1 is less than e2
     *                 x > 0: e1 is larger than e2
     */
    public static function compare(Event $e1, Event $e2)
    {
        list($e1Year, $e1Month, $e1Day) = explode('-', $e1->date);
        list($e2Year, $e2Month, $e2Day) = explode('-', $e2->date);
        
        if ($e1Month < 3 && $e2Month > 10) {
            return 1;
        } else if ($e1Month > 10 && $e2Month < 3) {
            return -1;
        } else if ($e1Month != $e2Month) {
            return $e1Month - $e2Month;
        } else if ($e1Day != $e2Day) {
            return $e1Day - $e2Day;
        }
        return strcmp($e1->title, $e2->title);
    }
}
?>
