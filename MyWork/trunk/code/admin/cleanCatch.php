<?php
// cleanCatch.php

function deldir($dir) {
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }
 
  closedir($dh);
  return true;
  //删除当前文件夹：
//  if(rmdir($dir)) {
//    return true;
//  } else {
//    return false;
//  }
}


if (deldir('Runtime/Cache')){
	echo "success Cache";
} else {
	echo "error Cache";
}

if (deldir('Runtime/Data')){
	echo "success Data";
} else {
	echo "error Data";
}

if (deldir('Runtime/Temp')){
	echo "success Temp";
} else {
	echo "error Temp";
}

@unlink('Runtime/~runtime.php');
