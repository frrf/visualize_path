
$(document).ready(function(){
  $('#load_saved_route').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"select.php",
      method:"post",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data)
      {
        $('#excel_area').html(data);
      }
    })
  });
});

$(document).ready(function(){
  $('#load_csv_form').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"upload.php",
      method:"POST",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data)
      {
        $('#excel_area').html(data);
        // $('table');
      }
    })
  });
});


$(document).ready(function(){
  $('#clear_DB').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"db_man.php",
      method:"POST",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data)
      {
        $('#db_activity').html(data);
        location.reload();
        // $('table');
      }
    })
  });
});

function hide() {
  var aside = document.getElementById("hide");
  var table = document.getElementById("table");
  var col_button = document.getElementById("collapse_button");
  if (aside.style.display === "none") {
    aside.style.display = "block";
    col_button.innerHTML = "⛶";
    col_button.style.backgroundColor = "rgb(25, 38, 70)";
  } else {
    aside.style.display = "none";
    col_button.innerHTML = "⛶";
    col_button.style.backgroundColor = "rgb(89, 139, 255)";
  }
};