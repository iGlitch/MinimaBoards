<?php
function dblogin_write() {
$dbh = mysqli_connect("localhost", "SQL_USERNAME", "SQL_PASSWORD","DB_Name") or die(mysqli_error($dbh));
return $dbh;
}
?>
