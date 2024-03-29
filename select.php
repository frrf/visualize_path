<?php
include 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$batch_id = $_POST["batch_id"];

// snipit from https://gist.github.com/SeanCannon/6585889
function array_flatten($array = null, $depth = 1) {
  $result = [];
  if (!is_array($array)) $array = func_get_args();
  foreach ($array as $key => $value) {
      if (is_array($value) && $depth) {
          $result = array_merge($result, array_flatten($value, $depth - 1));
      } else {
          $result = array_merge($result, [$key => $value]);
      }
  }
  return $result;
}

function show_route($route, $routeCounts) {
  // OPEN MAP
  $reader = IOFactory::createReader('Xlsx');
  $spreadsheet = $reader->load("maps/main.xlsx");
  $map = $spreadsheet->getActiveSheet();

  // READ THE CELLS
  foreach ($map->getRowIterator() as $row) {
    $cellIterate = $row->getCellIterator();
    $cellIterate->setIterateOnlyExistingCells(false);

    echo "<tr>";
    foreach ($cellIterate as $cell) {
      $occurances = $routeCounts[$cell->getValue()];
      if ($cell->getValue() == NULL) {
        echo "<td class=\"cell cell_empty\"></td>";
      } else if(in_array($cell->getValue(), $route)) { 
        $routeCellIndex = array_search($cell->getValue(), $route); 
        if ($routeCellIndex == 0) { 
          echo "<td id=\"start\" 
          class=\"cell cell_match\" 
          style=\"background-color: rgb(0, 251," . 251 - ($routeCellIndex * 251/count($route)) . ");\">
          <span class=\"route\">#" . $routeCellIndex+1 . "</span>
          <span class=\"route route_occurances\">x" . $occurances . "</span>
          </td>";
        } else if ($routeCellIndex+1 == count($route)) {
          echo "<td id=\"end\" 
          class=\"cell cell_match\" 
          style=\"background-color: rgb(0, 251," . 251 - ($routeCellIndex * 251/count($route)) . ");\">
          <span class=\"route\">#" . $routeCellIndex+1 . "</span>
          <span class=\"route route_occurances\">x" . $occurances . "</span>
          </td>";
        }else {
          echo "<td 
            class=\"cell cell_match\" 
            style=\"background-color: rgb(0, 251," . 251 - ($routeCellIndex * 251/count($route)) . ");\">
            <span class=\"route\">#" . $routeCellIndex+1 . "</span>
            <span class=\"route route_occurances\">x" . $occurances . "</span>
            </td>";
        }
      } else {
        echo "<td class=\"cell\">" . $cell->getValue() . "</td>";
      }
    }
    echo "</tr>";
  }
}

function select_batch($batch_id) {
  if($batch_id != '') {
    $batch_id = $batch_id;

    $user = "root";
    $password = "123";
    $host = "localhost";
  
    $DBConnect = mysqli_connect($host, $user,$password);
    // Check connection to database
    if ($DBConnect === FALSE) echo "<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</p>";
    else {
      $DBName = "saved_routes";
      if (!mysqli_select_db($DBConnect, $DBName)) echo "<p>There are no entries in the DB!</p>";
      else {
        $TableName = "routes";
        $SQLstring = "SELECT * FROM $TableName where batch_id = $batch_id";
        $QueryResult = mysqli_query($DBConnect, $SQLstring);
        // Checks if the table is empty
  
        $batch_data = [];
        if (mysqli_num_rows($QueryResult) == 0) echo "<p>There are no entries in the DB!</p>";
        else {
          while($Row = mysqli_fetch_array($QueryResult)) {
            $batch_data = $Row;
          }
          mysqli_free_result($QueryResult);
        }
        mysqli_close($DBConnect);
      }
    }
  
    $route = str_replace('"',"",trim($batch_data["route_shelves"],'[]'));
    $route = explode(',',$route);
    $routeCounts = str_replace('"',"",trim($batch_data["shelf_occurances"],'[]{}'));
    $routeCounts = (array) json_decode($batch_data["shelf_occurances"]);
    show_route($route,$routeCounts);
  } else {
    $message = '<div class="alert alert-danger">Error with the batch ID</div>';
  }

}

select_batch($batch_id);
?>