<?php
namespace bdrem;

class Renderer_HtmlTable
{
    public function render($arEvents)
    {
        $s = <<<HTM
<table>
 <thead>
  <tr>
   <th colspan="2">Days</th>
   <th>Age</th>
   <th>Event</th>
   <th>Name</th>
   <th>Date</th>
   <th>Day</th>
  </tr>
 </thead>
 <tbody>

HTM;
        foreach ($arEvents as $event) {
            $class = 'd' . $event->days;
            if ($event->days < 0) {
                $class .= ' prev';
            } else if ($event->days == 0) {
                $class .= ' today';
            } else {
                $class .= ' next';
            }
            $s .= sprintf(
                '<tr class="' . trim($class) . '">'
                . '<td class="icon"></td>'
                . '<td class="r">%d</td>'
                . '<td class="r">%s</td>'
                . '<td>%s</td>'
                . '<td>%s</td>'
                . '<td class="r">%s</td>'
                . '<td>%s</td>'
                . "</tr>\n",
                $event->days,
                $event->age,
                $event->title,
                $event->type,
                $event->date,
                strftime('%a', strtotime($event->localDate))
            );
        }
        $s .= <<<HTM
 </tbody>
</table>

HTM;
        return $s;
    }
}
?>
