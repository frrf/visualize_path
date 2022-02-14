
$(document).ready(function(){
  $('#load_saved_route').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"select.php",
      method:"GET",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data)
      {
        $('#excel_area').html(data);
        console.log("works")
      }
    })
  });
});
$(document).ready(function(){
  $('#load_excel_form').on('submit', function(event){
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
        console.log("fail")
      }
    })
  });
});