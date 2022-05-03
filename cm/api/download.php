<?php

$url = $_GET['src'];
$filename = $_GET['filename'];
$url = str_replace(' ', '%20', $url);
$filedata = @file_get_contents($url);

// SUCCESS
if ($filedata) {
    // GET A NAME FOR THE FILE
    $basename = basename($filename);

    // THESE HEADERS ARE USED ON ALL BROWSERS
    header("Content-Type: application-x/force-download");
    header("Content-Disposition: attachment; filename=$basename");
    header("Content-length: " . (string) (strlen($filedata)));
    header("Expires: " . gmdate("D, d M Y H:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"))) . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    // THIS HEADER MUST BE OMITTED FOR IE 6+
    if (false === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE ')) {
        header("Cache-Control: no-cache, must-revalidate");
    }

    // THIS IS THE LAST HEADER
    header("Pragma: no-cache");

    // FLUSH THE HEADERS TO THE BROWSER
    flush();

    // CAPTURE THE FILE IN THE OUTPUT BUFFERS - WILL BE FLUSHED AT SCRIPT END
    ob_start();
    echo $filedata;
}

// FAILURE
else {
    die("ERROR: UNABLE TO OPEN $filename");
}
