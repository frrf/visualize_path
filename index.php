<!DOCTYPE html>
<html>
  <head>
    <title>Load Excel Sheet in Browser using PHPSpreadsheet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <aside id="hide">
      <header class="aside">
        <div>
          
          <form method="post" id="load_csv_form" enctype="multipart/form-data">
            <span id="message"></span> <!-- Erro messages -->
            <h3 class="upload_file">Upload Route</h3>
            <label class="upload_file label-button" for="enter">Select CSV File</label>
            <input class="upload_file" id="enter" type="file" name="select_excel" onchange="disable_radios()"/>
            <hr class="upload_file">
            <div class="saved_routes saved_routes_wrapper">
              <h3 class="saved_routes" id="db_activity">Saved Routes</h3>
              <?php
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
                  $SQLstring = "SELECT batch_id FROM $TableName";
                  $QueryResult = mysqli_query($DBConnect, $SQLstring);
                  // Checks if the table is empty
                  if (mysqli_num_rows($QueryResult) == 0) echo "<p>There are no entries in the DB!</p>";
                  else {
                    // prints out all the first_name and last_name column rows in the table
                    $count = 0;
                    while($Row = mysqli_fetch_array($QueryResult)) {
                      echo "
                        <label class=\"saved_routes\" for=\"path".$count."\">
                        <input  onclick=\"uncheck_radio()\" onchange=\"disable_radios()\"
                        type=\"radio\" 
                            name=\"batch_id\" 
                            id=\"path".$count."\" 
                            value=\"{$Row['batch_id']}\"
                          >
                          Batch #{$Row['batch_id']}
                        </label>";
                      $count++;
                    }
                    mysqli_free_result($QueryResult);
                  }
                  mysqli_close($DBConnect);
                }
              }
              ?>
            </div>
            
            <hr class="saved_routes">
            <div class="bad_flex">
              <input type="submit" name="load"/>
            </form>
            <form method="post" id="clear_DB" enctype="multipart/form-data">
              <input class="saved_routes" type="submit" value="Clear DB"/>
            </form>
          </div>
          <a id="show_start" href="#start" hidden>Go to start of path</a>
        </div>
      </header>
      
      <!-- <section class="aside">
        <form method="post" id="clear_DB" enctype="multipart/form-data">
          <input type="submit" value="Clear DB"/>
        </form>
      </section> -->
    </aside>

    <main id="table">
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
            } else {
              echo "<td class=\"cell\">" . $cell->getValue() . "</td>";
            }
          }
          echo "</tr>";
          }
        ?>
      </table>
    </main>

    <button id="collapse_button" onclick="hide()" class="collapse_aside">⛶</button>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="script.js"></script>
</body>
</html>