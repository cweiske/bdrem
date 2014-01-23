<?php
namespace bdrem;

class Renderer_Console
{
    public function render($arEvents)
    {
        $tbl = new \Console_Table(
            CONSOLE_TABLE_ALIGN_LEFT,
            array('sect' => '', 'rule' => '-', 'vert' => '')
        );
        $tbl->setAlign(0, CONSOLE_TABLE_ALIGN_RIGHT);
        $tbl->setAlign(1, CONSOLE_TABLE_ALIGN_RIGHT);

        $tbl->setHeaders(
            array('Days', 'Age', 'Name', 'Event', 'Date', 'Day')
        );

        foreach ($arEvents as $event) {
            $tbl->addRow(
                array(
                    $event->days,
                    $event->age,
                    wordwrap($event->title, 30, "\n", true),
                    wordwrap($event->type, 20, "\n", true),
                    $event->date,
                    strftime('%a', strtotime($event->localDate))
                )
            );
        }
        return $tbl->getTable();
    }
}
?>
