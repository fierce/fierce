<?php

// Version of the plugin
$currentpluginver = "2.6";

// Path to the upload folder, please set the path using the Image Browser Settings menu.

$useruploadroot = "http://$_SERVER[HTTP_HOST]";

$useruploadfolder = "/images";
$useruploadpath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "$useruploadfolder/";
if (!is_dir($useruploadpath)) {
  mkdir($useruploadpath);
}
