<?php
namespace bdrem;

/**
 * Reads birthday reminder 2's birthday files (.bdf).
 */
class Source_Bdf
{
    protected $filename;

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
     * @param string $strDate Date the events shall be found for, YYYY-MM-DD
     */
    public function getEvents($strDate, $nDaysPrev, $nDaysNext)
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
            if ($event->isWithin($strDate, $nDaysPrev, $nDaysNext)) {
                $arEvents[] = $event;
            }
        }
        return $arEvents;
    }
}
?>
