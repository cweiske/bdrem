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
 * Renders events in a HTML table.
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Renderer_HtmlTable extends Renderer
{
    /**
     * HTTP content type
     * @var string
     */
    protected $httpContentType = 'text/html; charset=utf-8';

    /**
     * Render the events in a HTML table
     *
     * @param array $arEvents Event objects to render
     *
     * @return string HTML table
     */
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
                . '<td>%s</td>'
                . '<td>%s</td>'
                . "</tr>\n",
                $event->days,
                $event->age,
                htmlspecialchars($event->title),
                htmlspecialchars($event->type),
                $this->getLocalDate($event->date),
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
