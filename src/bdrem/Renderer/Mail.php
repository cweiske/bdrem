<?php
namespace bdrem;

require_once 'Mail/mime.php';

class Renderer_Mail extends Renderer
{
    public function render($arEvents)
    {
        $todays = array();
        foreach ($arEvents as $event) {
            if ($event->days == 0) {
                $todays[] = $this->shorten($event->title, 15);
            }
        }
        $subject = 'Birthday reminder';
        if (count($todays)) {
            $subject .= ': ' . implode(', ', $todays);
        }

        $rc = new Renderer_Console();
        $rh = new Renderer_Html();

        $hdrs = array(
            'From'    => $this->config->get('mail_from', 'birthday@example.org'),
            'Subject' => $subject
        );
        $mime = new \Mail_mime(
            array(
                'eol' => "\n",
                'head_charset' => 'utf-8',
                'text_charset' => 'utf-8',
                'html_charset' => 'utf-8',
            )
        );

        $mime->setTXTBody($rc->render($arEvents));
        $mime->setHTMLBody($rh->render($arEvents));

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);
        $textHeaders = '';
        foreach ($hdrs as $k => $v) {
            $textHeaders .= $k . ':' . $v  . "\n";
        }

        foreach ((array) $this->config->get('mail_to') as $recipient) {
            mail($recipient, $subject, $body, $textHeaders);
        }
    }

    protected function shorten($str, $len)
    {
        if (mb_strlen($str) <= $len) {
            return $str;
        }

        return mb_substr($str, 0, $len - 1) . '…';
    }
}
?>