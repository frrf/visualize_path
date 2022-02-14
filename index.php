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
        <h3>Upload Route</h3>
        <div>
          <span id="message"></span> <!-- Erro messages -->
  
          <form method="post" id="load_excel_form" enctype="multipart/form-data">
            <label class="label-button" for="enter">Select CSV File</label>
            <input id="enter" type="file" name="select_excel"/>
            <input type="submit" name="load"/>
          </form>
          <a href="#start">Go to start of path</a>
        </div>
      </header>

      <section class="aside">
        <h3>Saved Routes</h3>
        <form id="select_area" method="post" id="load_saved_route" enctype="multipart/form-data" class="radio">
          <?php
            $user = "root";
            $password = "123";
            $host = "localhost";

            $DBConnect = mysqli_connect($host, $user,$password);
            // Check connection to database
            if ($DBConnect === FALSE) echo "<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</p>";
            else {
              $DBName = "saved_routes";
              if (!mysqli_select_db($DBConnect, $DBName)) echo "<p>There are no entries in the interview logs!</p>";
              else {
                $TableName = "routes";
                $SQLstring = "SELECT batch_id FROM $TableName";
                $QueryResult = mysqli_query($DBConnect, $SQLstring);
                // Checks if the table is empty
                if (mysqli_num_rows($QueryResult) == 0) echo "<p>There are no entries in the interview logs!</p>";
                else {
                  // prints out all the first_name and last_name column rows in the table
                  $count = 0;
                  while($Row = mysqli_fetch_array($QueryResult)) {
                    echo "<label for=\"path".$count."\"><input type=\"radio\" name=\"path\" id=\"path".$count."\">Batch #{$Row['batch_id']}</label>";
                    $count++;
                  }
                  mysqli_free_result($QueryResult);
                }
                mysqli_close($DBConnect);
              }
            }
          ?>
          <input type="submit" name="select"/>
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