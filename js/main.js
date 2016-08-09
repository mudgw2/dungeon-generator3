//callback handler for form submit
$('form[id^="move_form_"]').submit(function(e)
{
    var postData = $(this).serializeArray();		
    $.ajax(
    {
        url : "process-move.php",
        type: "POST",
        data : postData,
		datatype: 'json',
		cache: false,
		async: true,
		
		beforeSend: function(){
        // Code to display spinner
		},
        success:function(response, textStatus, jqXHR) 
        {	
			var json = $.parseJSON(response);
			console.log(json);
			//Append to existing room
			 $('#view_area').append("<div class='row' id='view_area2'><div class='panel panel-primary'><div class='panel-heading'><h3 class='panel-title'>Chamber</h3></div><div class='panel-body'><div class='col-md-6'></div><div class='col-md-6'>"+json.desc+"</div><div class='col-md-12'><strong>Passages</strong><br>"+json.doors+"</div></div></div></div>");		 

		},
		complete: function(){

		},
        error: function(jqXHR, textStatus, errorThrown) 
        {
            //if fails      
        }
    }).done(function () {
        
      });
    e.preventDefault(); //STOP default action
});


	
