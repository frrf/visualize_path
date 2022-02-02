<?php
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

include 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
      $spreadsheet = $reader->load($_FILES['select_excel']['tmp_name']);
      $route = $spreadsheet->getActiveSheet()->toArray();
      $route = array_flatten($route, -1);
      $routeCounts = array_count_values($route);
      // non-duplicate route to fix shelf sequence 1,2,3,4...
      $route= array_flatten((array_unique($route)),-1);
      // echo "<pre>" . print_r(array_flatten(array_unique($route)),-1) . "</pre>";


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
              style=\"background-color: rgb(0, 251," . 251 - ($routeCellIndex * 251/count($route)) . ");\">
              <span class=\"route\">#" . $routeCellIndex+1 . "</span>
              <span class=\"route route_occurances\">x" . $occurances . "</span>
              </td>";
      // If not first cell then only add cell_match class to element
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
          } // end of IF / Else 
        } // end of foreach loop
        echo "</tr>";
      }
    } else {
      $message = '<div class="alert alert-danger">Only .xls or .xlsx file allowed</div>';
    }
}
else {
  $message = '<div class="alert alert-danger">Please Select File</div>';
}
?>