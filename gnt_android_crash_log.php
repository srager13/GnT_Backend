<?php
  
  $fileName = '~/gnt_android_crash_logs/'.date('Y-m-d_H-i-s').'.txt';
    $file = fopen($fileName, 'w') or die('Could not create report file: ' . $fileName);
  foreach($_POST as $key => $value) {
    $reportLine = $key." = ".$value."\n";
      fwrite($file, $reportLine) or die('Could not write to report file ' . $reportLine);
  }
  fclose($file);
?>
