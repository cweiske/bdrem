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

require_once 'Mail/mime.php';

/**
 * Send out mails
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Renderer_Mail extends Renderer
{
    /**
     * Render the events - send out mails.
     *
     * Uses the config's "mail_to" array as recipients.
     * Sends out a single mail for each recipient.
     * Config "mail_from" can also be used.
     *
     * @param array $arEvents Array of events to display
     *
     * @return void
     */
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
            'Auto-Submitted' => 'auto-generated'
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

    /**
     * Shorten the given string to the specified length.
     * Adds ... when the string was too long
     *
     * @param string  $str String to shorten
     * @param integer $len Maximum length of the string
     *
     * @return string Shortened string
     */
    protected function shorten($str, $len)
    {
        if (mb_strlen($str) <= $len) {
            return $str;
        }

        return mb_substr($str, 0, $len - 1) . '…';
    }
}
?>