<?php
include 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


$TableName = "routes";
$user = "root";
$password = "123";
$host = "localhost";
$DBConnect = mysqli_connect($host, $user,$password);
$DBName = "saved_routes";
mysqli_select_db($DBConnect,$DBName);
$SQLstring = "TRUNCATE TABLE routes";
$QueryResult = mysqli_query($DBConnect, $SQLstring);
echo "<td>DB has been cleared!</td>";
?>