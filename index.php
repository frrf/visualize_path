<!DOCTYPE html>
<html>
  <head>
    <title>Display Excel As HTML </title>
  </head>
<body>
  <table>
    <?php
      // LOAD PHPSPREADSHEET
      require "vendor/autoload.php";

      $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(); 

      // OPEN SPREADSHEET
      $spreadsheet = $reader->load("phpsample.xlsx");
      $worksheet = $spreadsheet->getActiveSheet();

      // READ THE CELLS
      foreach ($worksheet->getRowIterator() as $row) {
      $cellIterate = $row->getCellIterator();
      $cellIterate->setIterateOnlyExistingCells(false);
      
      // OUTPUT
      echo "<tr>";
      foreach ($cellIterate as $cell) {
        echo "<td>" . $cell->getValue() . "</td>";
      }
      echo "</tr>";
      }
    ?>
  </table>
</body>
</html>