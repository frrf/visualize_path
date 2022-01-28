<!DOCTYPE html>
<html>
  <head>
    <title>Load Excel Sheet in Browser using PHPSpreadsheet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header>
      <h3>Upload Route</h3>
      
      <section>
        <span id="message"></span> <!-- Erro messages -->

        <form method="post" id="load_excel_form" enctype="multipart/form-data">
          <label for="enter">Select CSV File</label>
          <input id="enter" type="file" name="select_excel"/>
          <input type="submit" name="load"/>
        </form>
        <a href="#start">Go to start of path</a>;
      </section>
    </header>

    <main>
      <table id="excel_area">
        <?php
          // LOAD PHPSPREADSHEET
          require "vendor/autoload.php";
          $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(); 

          // OPEN SPREADSHEET
          $spreadsheet = $reader->load("maps/main.xlsx");
          $worksheet = $spreadsheet->getActiveSheet();

          // READ THE CELLS
          foreach ($worksheet->getRowIterator() as $row) {
          $cellIterate = $row->getCellIterator();
          $cellIterate->setIterateOnlyExistingCells(false);
          
          // OUTPUT
          echo "<tr>";
          foreach ($cellIterate as $cell) {
            if ($cell->getValue() == NULL) {
              echo "<td class=\"cell_empty\"></td>";
            } else if($cell->getValue() == 'D6-01-05') {
              echo "<td class=\"cell\">" . $cell->getValue() . "</td>";

            } else {
              echo "<td class=\"cell\">" . $cell->getValue() . "</td>";
            }
          }
          echo "</tr>";
          }
        ?>
      </table>
  </main>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="script.js"></script>
</body>
</html>