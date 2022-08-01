
$(document).ready(function(){
  $('#load_csv_form').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"upload.php",
      method:"post",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data)
      {
        show_start_link();
        $('#excel_area').html(data);
        // $('table');
      }
    })
  });
});

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
        show_start_link();
        $('#excel_area').html(data);
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
        show_start_link();
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

function disable_radios() {
  let radio_buttons = document.querySelectorAll('input[type="radio"]');
  let saved_route_area = document.getElementsByClassName("saved_routes");
  let upload_area = document.getElementsByClassName("upload_file");
  let checked = false;
  for (const radioButton of radio_buttons) {
    if (radioButton.checked) {
        checked = true;
    }
  }
  if (document.querySelector('input[type="file"]').files.length != 0) {
    radio_buttons.forEach(cb => cb.hidden = true);
    for (const sections of saved_route_area) {
      sections.hidden = true 
    }
    console.log("radio buttons disabled");
  }
  else if (checked == true) {
    document.querySelector('input[type="file"]').hidden = true;
    for (const sections of upload_area) {
      sections.hidden = true 
    }
    console.log("file disabled");
  }
  else {
    radio_buttons.forEach(cb => cb.hidden = false);
    for (const sections of saved_route_area) {
      sections.hidden = false 
    }
    console.log("radio buttons enabled");

  }
}

function uncheck_radio() {
  let radio_buttons = document.querySelectorAll('input[type="radio"]');
  for (const radioButton of radio_buttons) {
    if (document.querySelector('input[type="file"]').hidden === true && radioButton.checked) {
      radioButton.checked = false;  
      document.querySelector('input[type="file"]').hidden = false;
      let upload_file = document.getElementsByClassName('upload_file');
      for (const sections of upload_file) {
        sections.hidden = false; 
      }
      console.log("radio unchecked \nfile upload enabled")
    }
    break;
  }
}

function show_start_link() {
  let start_link = document.getElementById("show_start");
  start_link.hidden = false;
}