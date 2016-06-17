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
     * Add HTML part to email
     * @var bool
     */
    public $html = true;

    /**
     * CSS "inline" in tags, or "separate" in a style block
     * @var string
     */
    public $css = 'inline';

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

        $rc  = new Renderer_Console();
        $rht = new Renderer_HtmlTable();

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
        if ($this->html) {
            if ($this->css == 'inline') {
                $html = $this->inlineCss(
                    $rht->render($arEvents),
                    Renderer_Html::getCss()
                );
            } else {
                $html = '<style type="text/css">'
                    . Renderer_Html::getCss()
                    . '</style>'
                    . $rht->render($arEvents);
            }
            $mime->setHTMLBody($this->minifyHtml($html));
        }

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);
        $textHeaders = '';
        foreach ($hdrs as $k => $v) {
            $textHeaders .= $k . ': ' . $v  . "\n";
        }

        if (!$this->config->get('debug', false)) {
            foreach ((array) $this->config->get('mail_to') as $recipient) {
                mail($recipient, $subject, $body, $textHeaders);
            }
        } else {
            echo "Subject: " . $subject . "\n";
            echo $textHeaders;
            echo "\n";
            echo $body;
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

    /**
     * Takes the HTML and CSS code and inlines CSS into HTML.
     *
     * This is important for some e-mail clients which do
     * not interpret <style> tags but only support inline styles.
     *
     * Works nicely with bdrem's CSS. If you need more CSS selector
     * support, have a look at https://github.com/jjriv/emogrifier
     *
     * @param string $html HTML code
     * @param string $css  CSS code
     *
     * @return string HTML with inlined CSS
     */
    protected function inlineCss($html, $css)
    {
        preg_match_all(
            '#([^{]+) {([^}]+)}#m',
            $css,
            $parts
        );
        $rules = array();
        foreach ($parts[1] as $key => $rule) {
            $mrules = explode(',', $rule);
            foreach ($mrules as $rule) {
                $rule  = trim($rule);
                $style = trim($parts[2][$key]);
                $rules[$rule] = preg_replace(
                    '#([:;]) +#', '\1',
                    str_replace(
                        ["\r", "\n", '    '],
                        ['', '', ' '],
                        $style
                    )
                );
            }
        }
        $sx = simplexml_load_string($html);
        foreach ($rules as $rule => $style) {
            $mode = null;
            $parts = explode(' ', $rule);
            $xp = '';
            foreach ($parts as $part) {
                $part = trim($part);
                if (strpos($part, ':') !== false) {
                    //.foo:before
                    list($part, $mode) = explode(':', $part);
                    if ($mode == 'hover') {
                        continue 2;
                    }
                }
                if (strpos($part, '.') === false) {
                    //tag only
                    if ($part == '') {
                        $xp = '//*';
                    } else {
                        $xp .= '//' . $part;
                    }
                } else {
                    //tag.class
                    list($tag, $class) = explode('.', $part);
                    if ($tag == '') {
                        $tag = '*';
                    }
                    $xp .= '//' . $tag
                        . '[contains('
                        . 'concat(" ", normalize-space(@class), " "), '
                        . '" ' . $class . ' "'
                        . ')]';
                }
            }
            $res = $sx->xpath($xp);
            //var_dump($res);die();
            //var_dump($xp, $style);
            foreach ($res as $xelem) {
                if ($mode === null) {
                    $xelem['style'] .= $style;
                } else if ($mode == 'before') {
                    $xelem[0] = preg_replace(
                        '#content:\s*"(.+)"#', '\1', $style
                    );
                }
            }
        }

        $html = $sx->asXML();
        //strip xml header
        $lines = explode("\n", $html);
        unset($lines[0]);
        $html = implode("\n", $lines);

        //echo $html . "\n";die();
        return $html;
    }

    /**
     * Remove whitespace between tags
     *
     * @param string $html HTML code
     *
     * @return string Smaller HTML code
     */
    protected function minifyHtml($html)
    {
        $html = trim(preg_replace("#[\n\r ]+<#", '<', $html));
        return $html;
    }
}
?>