<?php
 
//Connect to the database

 
//Grab the destination page from the link
$redirect = mysql_real_escape_string($_GET['page']);
 
//Insert the destination page and timestamp into your database
 
$page_insert = mysql_query("INSERT INTO tracking_table (`rec_use_page`, `rec_use_date`) VALUES ('$redirect', now())") or die(mysql_error());
 
//Redirect the user to their intended location
 
header("Location: $redirect");
 
?>