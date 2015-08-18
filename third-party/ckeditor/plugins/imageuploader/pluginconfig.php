<?php

if(isset($_POST["newpath"])){
    $temppath = $_POST["newpath"];
    $data = '
$useruploadfolder = "'.$temppath.'";
$useruploadpath = "../../../'.$temppath.'/";
    '.PHP_EOL;
    $fp = fopen('pluginconfig.php', 'a');
    fwrite($fp, $data);
} 

// Version of the plugin
$currentpluginver = "2.6";

// Path to the upload folder, please set the path using the Image Browser Settings menu.

$useruploadroot = "http://$_SERVER[HTTP_HOST]";

$useruploadfolder = "ckeditor/plugins/imageuploader/uploads";
$useruploadpath = "../../../$useruploadfolder/";

$useruploadfolder = "ckeditor/plugins/imageuploader/uploadss";
$useruploadpath = "../../../ckeditor/plugins/imageuploader/uploadss/";
    

$useruploadfolder = "ckeditor/plugins/imageuploader/uploads";
$useruploadpath = "../../../ckeditor/plugins/imageuploader/uploads/";
    
