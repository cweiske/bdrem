<?php
namespace bdrem;

class Renderer_Console extends Renderer
{
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
                        $event->date,
                        strftime('%a', strtotime($event->localDate))
                    ),
                    $colorCode
                )
            );
        }
        return $tbl->getTable();
    }

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

    protected function loadConfig()
    {
        if (isset($this->config->ansi)) {
            $this->ansi = $this->config->ansi;
        }
    }
}
?>
