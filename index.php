<!DOCTYPE html>
<html>
  <head>
    <title>Load Excel Sheet in Browser using PHPSpreadsheet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <aside>
      <header class="aside">
        <h3>Upload New Route</h3>
        <div>
          <span id="message"></span> <!-- Erro messages -->
  
          <form method="post" id="load_excel_form" enctype="multipart/form-data">
            <label class="label-button" for="enter">Select CSV File</label>
            <input type="submit" name="load"/>
            <input id="enter" type="file" name="select_excel"/>
          </form>
          <a href="#start">Go to start of path</a>
        </div>
      </header>

      <section class="aside">
        <h3>Saved Routes</h3>
        <form action="/">
          <input type="radio" name="path1" id="path1"><label for="path1">test</label>
          <input type="radio" name="path2" id="">
          <input type="radio" name="path3" id="">
        </form>
      </section>


    </aside>


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