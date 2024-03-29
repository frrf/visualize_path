<?php
include 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

function show_route($route, $routeCounts, $available = []) {
  // OPEN MAP
  $reader = IOFactory::createReader('Xlsx');
  $spreadsheet = $reader->load("maps/main.xlsx");
  $map = $spreadsheet->getActiveSheet();

  // READ THE CELLS
  foreach ($map->getRowIterator() as $row) {
    $cellIterate = $row->getCellIterator();
    $cellIterate->setIterateOnlyExistingCells(false);

    // OUTPUT
    echo "<tr>";
    foreach ($cellIterate as $cell) {
      $occurances = $routeCounts[$cell->getValue()];
  // If cell is empty/null then cell is printed with empty styling class
      if ($cell->getValue() == NULL) {
        echo "<td class=\"cell cell_empty\"></td>";
  // If cell is not empty then check if the shelf value is in the route/path submitted by user
      } else if(in_array($cell->getValue(), $route)) { // hard check, if cell value is in route array proceed...
        // Returns key/index of cell value that is in route array
        $routeCellIndex = array_search($cell->getValue(), $route); 


  // Check for starting shelf, add the "start" id tag to the starting element 
        if ($routeCellIndex == 0) { 
          echo "<td id=\"start\" 
          class=\"cell cell_match\" 
          style=\"background-color: rgb(0, " . 255 - ($routeCellIndex * 1.5) . "," . 255 - ($routeCellIndex * 255/count($route)) . ");\">
          <span class=\"route\">#" . $routeCellIndex+1 . "</span>
          <span class=\"route route_occurances\">x" . $occurances . "</span>
          </td>";
  // If not first cell then only add cell_match class to element
        } else if ($routeCellIndex+1 == count($route)) {
          echo "<td id=\"end\" 
          class=\"cell cell_match\" 
          style=\"background-color: rgb(0, " . 255 - ($routeCellIndex * 1.5) . "," . 255 - ($routeCellIndex * 255/count($route)) . ");\">
          <span class=\"route\">#" . $routeCellIndex+1 . "</span>
          <span class=\"route route_occurances\">x" . $occurances . "</span>
          </td>";
        }else {
          echo "<td 
            class=\"cell cell_match\" 
            style=\"background-color: rgb(0, " . 255 - ($routeCellIndex * 1.5) . "," . 255 - ($routeCellIndex * 255/count($route)) . ");\">
            <span class=\"route\">#" . $routeCellIndex+1 . "</span>
            <span class=\"route route_occurances\">x" . $occurances . "</span>
            </td>";
        }
      } else if(in_array($cell->getValue(), $available)) { // hard check, if cell value is in route array proceed...
        echo "<td class=\"cell\"
        style=\"background-color:RGB(255, 119, 255);\">" . $cell->getValue() . "</td>";
      //   echo "<td class=\"cell\">" . $cell->getValue() . "</td>";

      } else {
        echo "<td class=\"cell\">" . $cell->getValue() . "</td>";
      } // end of IF / Else 
    } // end of foreach loop
    echo "</tr>";
  }
}

function upload_route() {
  if($_FILES["select_excel"]["name"] != '') {
      $allowed_extension = array('xls', 'xlsx','csv');
      // split name of file by '.' to get file extension
      $file_array = explode(".", $_FILES['select_excel']['name']);
      $file_extension = end($file_array);
  
      // validate file type via extension
      if(in_array($file_extension, $allowed_extension)) {
        // convert list input to array
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        // reader for excel extensions maybe expand via if/else statements
        // $reader = IOFactory::createReader('Xlsx');
  
        $available_csv = $reader->load("maps/nda_available.csv");
        $available = $available_csv->getActiveSheet()->toArray();
        $available = array_flatten($available, -1);
        $available= array_flatten((array_unique($available)),-1);
  
  
  
        $spreadsheet = $reader->load($_FILES['select_excel']['tmp_name']);
        $route = $spreadsheet->getActiveSheet()->toArray();
        $route = array_flatten($route, -1);
        $routeCounts = array_count_values($route);
        // non-duplicate route to fix shelf sequence 1,2,3,4...
        $route= array_flatten((array_unique($route)),-1);
        // echo "<pre>" . print_r(array_flatten(array_unique($route)),-1) . "</pre>";
  
        show_route($route,$routeCounts,$available);
  
        // -- UPLOAD ROUTE --
        // (array) + json_decode(array) to download from MySQL
        $user = "root";
        $password = "123";
        $host = "localhost";
        $DBConnect = mysqli_connect($host, $user,$password);
        // Checks if mysqli_connect was succesful
        if($DBConnect===FALSE) {
          // echo "<td>Unable to connect to the databse server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</td>";
        } else { 
          $DBName = "saved_routes";
          // Check if saved_routes database exists
          if (!mysqli_select_db($DBConnect,$DBName)) {
            $SQLstring = "CREATE DATABASE $DBName";
            $QueryResult = mysqli_query($DBConnect, $SQLstring);
            // Checks if the create database query was succesful 
            if ($QueryResult === FALSE) {
              // echo "<td>Unable to execute the query.</td>" . "<td>Error code " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</td>";
            } else {
              // Confirms that the candidates database has been created 
              echo "<td>First route!</td>";
            }
          }
          mysqli_select_db($DBConnect,$DBName);
          $TableName = "routes";
          $SQLstring = "SHOW TABLES LIKE '$TableName'";
          $QueryResult = mysqli_query($DBConnect, $SQLstring);
          // checks if the routes table exists, creates table if it doesn't it creates the table
          if (mysqli_num_rows($QueryResult)==0) {
              $SQLstring = "CREATE TABLE $TableName (
                batch_id varchar(10) PRIMARY KEY
                , route_shelves text
                , shelf_occurances text
                )";
              $QueryResult = mysqli_query($DBConnect,$SQLstring);
              // since table doesn't exist, check if table can be created succesfully
              if ($QueryResult === FALSE) {
                // echo "<td>Unable to create the table.</td>" . "<td>Error code " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</td>";
              } else {
                echo "<td>Created table</td>";
              }
          }
          
          // Ready inputs
          // batch ID is presumed to be the first sequence of chars of file before _ chars 
          $batch_id = explode('_', $file_array[0])[0]; 
          // json_encode(array) to upload to MySQL
          $route_shelves = json_encode($route); 
          $shelf_occurances = json_encode($routeCounts); 
  
          //inserts route data into routes table
          $SQLstring = "INSERT INTO $TableName VALUES (
            '$batch_id'
            , '$route_shelves'
            , '$shelf_occurances'
            )";
          $QueryResult = mysqli_query($DBConnect,$SQLstring);
          // checks if the insert query can run successfully
          if ($QueryResult === FALSE) {
              // echo "<td>Unable to execute the query.</td>" . "<td>Error code " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</td>";
          } else {
              echo "<td>Route logged!</td>";
          }
        }
      
      } else {
        $message = '<div class="alert alert-danger">Only .xls or .xlsx file allowed</div>';
      }
  }
  else {
    $message = '<div class="alert alert-danger">Please Select File</div>';
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

$file = $_FILES["select_excel"]["name"];

if ($file != "") {
  // echo "<h1>file present</h1>";
  upload_route();
} else {
  if(isset($_POST["batch_id"]) == false) {
    echo "<h1>file not present <br> no saved route selection<h1>";
  } else {
    $batch_id = $_POST["batch_id"];
    // echo "<h1>saved route selection</h1>";
    select_batch($batch_id);
  }
}



?>