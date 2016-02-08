<?php

$path = "/opt/sark/"; 
$path .= strip_tags($_GET['dtype']); 
$path .= '/';
$fullPath = $path.strip_tags($_GET['dfile']);

if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        break;
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;
?>
