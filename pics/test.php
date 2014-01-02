<?php

$pic = "P7210021.JPG";

if(strlen($argv[1])>0) { 
	$pic = $argv[1]; 
	echo "Using $argv[1]\n";
}
# Reading EXIF data:
$exif = exif_read_data($pic,0,TRUE);

# Degrees:
#$latdeg=$exif["GPS"]["GPSLatitude"][0];
#echo "LAT DEG: $latdeg\n";
#
#$longdeg=$exif["GPS"]["GPSLongitude"][0];
#
# Minutes:
#$min=$exif["GPS"]["GPSLatitude"][1];
#echo "MIN: $min\n";
#
## Seconds:
#$sec=$exif["GPS"]["GPSLatitude"][2];
#echo "SEC: $sec\n";
#
## Hemisphere (N, S, W ou E):
#$hem=$exif["GPS"]["GPSLatitudeRef"];
#echo "HEM: $hem\n";
#
## Altitude:
#$alt=$exif["GPS"]["GPSAltitude"][0];
#echo "ALT: $alt\n";

if ($exif){
      $lat = $exif['GPS']['GPSLatitude']; 
      $log = $exif['GPS']['GPSLongitude'];
      if (!$lat || !$log) exit;
  // latitude values //
      $lat_degrees = divide($lat[0]);
      $lat_minutes = divide($lat[1]);
      $lat_seconds = divide($lat[2]);
      $lat_hemi = $exif['GPS']['GPSLatitudeRef'];
 
  // longitude values //
      $log_degrees = divide($log[0]);
      $log_minutes = divide($log[1]);
      $log_seconds = divide($log[2]);
      $log_hemi = $exif['GPS']['GPSLongitudeRef'];
 
      $lat_decimal = toDecimal($lat_degrees, $lat_minutes, $lat_seconds, $lat_hemi);
      $log_decimal = toDecimal($log_degrees, $log_minutes, $log_seconds, $log_hemi);
 
     echo "\n LAT: ----------------------------------\n";
     print_r($lat_decimal);
     echo "\n LOG: ----------------------------------\n";
     print_r($log_decimal);
     echo "\n----------------------------------\n";

}
function divide($a)
{
  // evaluate the string fraction and return a float //	
    $e = explode('/', $a);
  // prevent division by zero //
    if (!$e[0] || !$e[1]) {
      return 0;
    }	else{
    return $e[0] / $e[1];
    }
}
function toDecimal($deg, $min, $sec, $hemi)
{
    $d = $deg + $min/60 + $sec/3600;
    return ($hemi=='S' || $hemi=='W') ? $d*=-1 : $d;
}
?>
