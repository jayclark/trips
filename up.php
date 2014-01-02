<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Upload</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body>

<?php
#include("connect.php");

#if(isset($_REQUEST['submit'])) {
if(isset($_POST['upload'])) {

    $uploaddir = 'pics/';
    foreach ($_FILES["pic"]["error"] as $key => $error)
    {
        if ($error == UPLOAD_ERR_OK)
        {
            $tmp_name = $_FILES["pic"]["tmp_name"][$key];
            $name = $_FILES["pic"]["name"][$key];
            $uploadfile = $uploaddir.basename(str_replace(' ','-',$name));

            echo "<hr>\n";
            echo "tmp_name: $tmp_name<br>\n";
            echo "name: $name<br>\n";
            echo "uploadfile: $uploadfile<br>\n";

            if (move_uploaded_file($tmp_name, $uploadfile))
            {
                echo "Success: File ".$name." uploaded.<br/>";
                if(import_to_db($uploadfile)>0) {
                  echo "Resize file for map. <br>\n";
                  resize_file_for_map($uploadfile);
                } else {
                  # No use for the file, so remove it.
                  echo "Removed file: $name <br/>\n";
                  unlink($uploadfile);
                }
            }
            else
            {
                echo "Error: File ".$name." cannot be uploaded.<br/>";
            }
        }
        else
        {
            echo "Upload failed<br>$error\n";
        }
    }

} else {
	display_upload_form();
}


function import_to_db($pic) {
    include("connect.php");
    # Reading EXIF data:
    $exif = exif_read_data($pic,0,TRUE);

    if ($exif){

        echo "<hr>\n";
        foreach ($exif as $key => $section) {
            foreach ($section as $name => $val) {
                echo "$key.$name: $val<br />\n";
            }
        }
        echo "<hr>\n";

       $img_date = $exif['EXIF']['DateTimeOriginal'];

       $lat = $exif['GPS']['GPSLatitude']; 
       $log = $exif['GPS']['GPSLongitude'];
       if (!$lat || !$log) {
         echo "<br><b>Sorry:</b> No Lat/Long info found in the image EXIF<br/>\n";
	     return 0;
       }
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
 
       echo "Picture Latitude: $lat_decimal<br\>\n";
       echo "Picture Longitude: $log_decimal<br\>\n";

       if (($lat_decimal=='0')||($log_decimal=='0')) {
         echo "<br><b>Sorry:</b> No Lat/Long info found in the image EXIF<br/>\n";
	     return 0;
       }
	
        # SQL insert/update query
        $name = mysql_real_escape_string($_REQUEST['name']);
        $address = mysql_real_escape_string($_REQUEST['address']);
        $type = mysql_real_escape_string($_REQUEST['type']);
        $lat_dec = mysql_real_escape_string($lat_decimal);
        $log_dec = mysql_real_escape_string($log_decimal);
        $idate = mysql_real_escape_string($img_date);
        $p = mysql_real_escape_string($pic);

        $query = "INSERT INTO markers VALUES (NULL, '".$name."', ".
		"'".$address."', '".$lat_dec."', '".$log_dec."', ".
		"'".$idate."', '".$type."', '".$p."') ";

	    echo "sql: $query <br>\n";
        $result = mysql_query($query);

        if($result) {
          echo "DB Success<br>\n";
        } else {
          echo "DB ERROR<br>\n";
          return 0;
        }

        return 1;
    }
    else
    {
        echo "No EXIF data <br>\n";
    }
}

function resize_file_for_map($file) {


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
function display_upload_form() {

print <<<HTML

<form action="" method="post" enctype="multipart/form-data">
<input type="file" name="pic[]" multiple><br>
<input type="submit" name="upload" value="Upload">
</form>
</body>
</html>

HTML;

}



?>
