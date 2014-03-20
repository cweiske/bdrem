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
 * Render events on the terminal as ASCII table
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Renderer_Console extends Renderer
{
    /**
     * HTTP content type
     * @var string
     */
    protected $httpContentType = 'text/plain; charset=utf-8';

    /**
     * Use ANSI color codes for output coloring
     *
     * @var boolean
     */
    public $ansi = false;

    /**
     * @var \Console_Color2
     */
    protected $cc;

    /**
     * Render events as console table
     *
     * @param array $arEvents Array of events to render
     *
     * @return string ASCII table
     */
    public function render($arEvents)
    {
        $this->loadConfig();
        if ($this->ansi) {
            $this->cc = new \Console_Color2();
        }

        $tbl = new \Console_Table(
            CONSOLE_TABLE_ALIGN_LEFT,
            array('intersection' => '', 'horizontal' => '-', 'vertical' => ''),
            1, null, $this->ansi
        );
        $tbl->setAlign(0, CONSOLE_TABLE_ALIGN_RIGHT);
        $tbl->setAlign(1, CONSOLE_TABLE_ALIGN_RIGHT);

        $tbl->setHeaders(
            $this->ansiWrap(
                array('Days', 'Age', 'Name', 'Event', 'Date', 'Day'),
                '%_%9'
            )
        );
        $tbl->setBorderVisibility(
            array(
                'left'   => false,
                'right'  => false,
                'top'    => true,
                'bottom' => false,
                'inner'  => true,
            )
        );

        foreach ($arEvents as $event) {
            $colorCode = null;
            if ($event->days == 0) {
                $colorCode = '%R';
            }
            $tbl->addRow(
                $this->ansiWrap(
                    array(
                        $event->days,
                        $event->age,
                        wordwrap($event->title, 30, "\n", true),
                        wordwrap($event->type, 20, "\n", true),
                        $this->getLocalDate($event->date),
                        strftime('%a', strtotime($event->localDate))
                    ),
                    $colorCode
                )
            );
        }
        return $tbl->getTable();
    }

    /**
     * Wrap each string in an array in an ANSI color code
     *
     * @param array  $data      Array of strings
     * @param string $colorCode ANSI color code or name
     *
     * @return array Wrapped data
     */
    protected function ansiWrap($data, $colorCode = null)
    {
        if (!$this->ansi || $colorCode === null) {
            return $data;
        }

        foreach ($data as $k => &$value) {
            $value = $this->cc->convert(
                $colorCode . $value . '%n'
            );
        }
        return $data;
    }

    /**
     * Load configuration values into the class
     *
     * @return void
     */
    protected function loadConfig()
    {
        if (isset($this->config->ansi)) {
            $this->ansi = $this->config->ansi;
        }
    }
}
?>
