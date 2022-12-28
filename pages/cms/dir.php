<?php

require_once 'cms_config.php';
$dir = $maildir;
$i = 0;
// Open a directory, and read its contents
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
      echo $file . "<br>";
	  $files[$i] = $file;
		$i++;}
    closedir($dh);
  }
}


