<?php
function dblogin() {
$dbh = mysqli_connect("localhost", "SQL_USERNAME", "SQL_PASSWORD","DB_NAME") or die(mysqli_error($dbh));
return $dbh;
}
?>
