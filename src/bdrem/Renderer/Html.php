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
 * HTML page renderer. Renders a full HTML page.
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Renderer_Html extends Renderer
{
    /**
     * HTTP content type
     * @var string
     */
    protected $httpContentType = 'application/xhtml+xml; charset=utf-8';

    /**
     * Send out HTTP headers when nothing shall be outputted.
     *
     * @return void
     */
    public function handleStopOnEmpty()
    {
        header('HTTP/1.0 204 No Content');
    }

    /**
     * Generate a HTML page with the given events.
     *
     * @param array $arEvents Events to display on the HTML page
     *
     * @return string HTML code
     *
     * @see Renderer_HtmlTable
     */
    public function render($arEvents)
    {
        $links = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            if (!isset($_SERVER['REQUEST_SCHEME'])) {
                $_SERVER['REQUEST_SCHEME'] = 'http';
            }
            $links = '  <link rel="alternate" type="text/calendar" href="'
                . $_SERVER['REQUEST_SCHEME'] . '://'
                . $_SERVER['HTTP_HOST']
                . preg_replace('#\?.+$#', '', $_SERVER['REQUEST_URI'])
                . '?renderer=ical'
                . '"/>'
                . "\n";
            $links .= '  <link rel="alternate" type="text/plain" href="'
                . $_SERVER['REQUEST_SCHEME'] . '://'
                . $_SERVER['HTTP_HOST']
                . preg_replace('#\?.+$#', '', $_SERVER['REQUEST_URI'])
                . '?renderer=console'
                . '"/>'
                . "\n";
        }

        $tr = new Renderer_HtmlTable();
        $table = $tr->render($arEvents);
        $css = static::getCss();
        $s = <<<HTM
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title>bdrem</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
$links  <style type="text/css">$css</style>
 </head>
 <body>
$table
 </body>
</html>
HTM;
        return $s;
    }

    /**
     * Get the CSS for the HTML table
     */
    public static function getCss()
    {
        return <<<CSS
table {
    border: 1px solid black;
    border-collapse: collapse;
    margin-left: auto;
    margin-right: auto;
}
td, th {
    border: 1px solid grey;
    border-left: 0px;
    border-right: 0px;
    padding: 0.1ex 1ex;
}

tr.prev td {
    background-color: #C4DDF4;
}
tr.today td {
    background-color: #FEDCBA;
}
tr.next td {
    background-color: #DEFABC;
}
tr:hover td {
    background-color: white;
}

.r {
    text-align: right;
}

tr td.icon {
    background-color: white;
}
tr.prev td.icon {
    color: #00A;
}
tr.today td.icon {
    color: black;
    background-color: #FEDCBA;
}
tr.next td.icon {
    color: #080;
}

tr.d-3 td.icon:before {
    content: "\342\227\224"
}
tr.d-2 td.icon:before {
    content: "\342\227\221"
}
tr.d-1 td.icon:before {
    content: "\342\227\225"
}
tr.d0 td.icon:before {
    content: "\342\230\205"
}
tr.d1 td.icon:before {
    content: "\342\227\225"
}
tr.d2 td.icon:before {
    content: "\342\227\221"
}
tr.d3 td.icon:before {
    content: "\342\227\224"
}
CSS;
    }
}
?>
