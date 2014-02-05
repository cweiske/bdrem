<?php
namespace bdrem;

class Renderer_Html extends Renderer
{
    protected $httpContentType = 'application/xhtml+xml; charset=utf-8';

    public function render($arEvents)
    {
        $tr = new Renderer_HtmlTable();
        $table = $tr->render($arEvents);
        $s = <<<HTM
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title>bdrem</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
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
  </style>
 </head>
 <body>
$table
 </body>
</html>
HTM;
        return $s;
    }
}
?>
