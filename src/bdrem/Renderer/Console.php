<?php
namespace bdrem;

class Renderer_Console
{
    public function render($arEvents)
    {
        $s  = "Days Age Name                                     Event                Date\n";
        $s .= "---- --- ---------------------------------------- -------------------- ----------\n";
        foreach ($arEvents as $event) {
            $s .= sprintf(
                "%3d %4s %s %s %s\n",
                $event->days,
                $event->age,
                $this->str_pad($event->title, 40),
                $this->str_pad($event->type, 20),
                $event->date
            );
        }
        return $s;
    }

    public function str_pad(
        $input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT
    ) {
        $l = mb_strlen($input, 'utf-8');
        if ($l >= $pad_length) {
            return $input;
        }

        $p = str_repeat($pad_string, $pad_length - $l);
        if ($pad_type == STR_PAD_RIGHT) {
            return $input . $p;
        } else {
            return $p . $input;
        }
    }
}
?>
