<?php
  $files = glob(cacheme_directory()."*");
  $now   = time();
  $days = 60 * 60 * 24 * 3;

  foreach ($files as $file) {
    if (is_file($file)) {
      if ($now - filemtime($file) >= $days) { // 2 days
        unlink($file);
      }
    }
  }
?>