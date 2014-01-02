<?php
require("connect.php");

// Start XML file, create parent node

$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node); 

// Select all the rows in the markers table

$query = "SELECT * FROM markers WHERE 1";
$result = mysql_query($query);
if (!$result) {  
  die('Invalid query: ' . mysql_error());
} 

header("Content-type: text/xml"); 

// Iterate through the rows, adding XML nodes for each

while ($row = @mysql_fetch_assoc($result)){  
  // ADD TO XML DOCUMENT NODE  
  $node = $dom->createElement("marker");  
  $newnode = $parnode->appendChild($node);   
  $newnode->setAttribute("name",$row['name']);
  $newnode->setAttribute("address", $row['address']);  
  $newnode->setAttribute("lat", $row['lat']);  
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("idate", $row['imgdate']);
  $newnode->setAttribute("type", $row['type']);
  $newnode->setAttribute("pic", $row['pic']);
} 

echo $dom->saveXML();

?>
