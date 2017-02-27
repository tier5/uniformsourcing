function SendMail()
{
  var email = '';
  var subject = '';
  var mailBody = '';
  dataString = $("#pop").serialize();
  //mailBody = document.getElementById('divBody').innerHTML;
  //dataString += "&email="+email+"&subject="+subject;
  $.ajax({
		 type: "POST",
		 url: "mail_submit.php",
		 data: dataString,
		 dataType: "json",
		 success: function(data) 
		 {	
			 if(data!=null)
			 {	
				 if(data.name || data.error)
				 {
					 $("#msg_email").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
				 } 
				 else if(data.email != "")
				 {
					 $("#msg_email").html("<div class='errorMessage'><strong>Email were not send to following email Id's "+ data.email +" </strong></div>"); 
				 }
				 else 
				 {
					 $("#message").html("<div class='successMessage'><strong>Email Send Successfully.</strong></div>");
					 Fade();
				 }	
			 }
			 else
			 {
				 $("#msg_email").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>"); 
			 }
		 }
		 });
  return false;
}

function getDiv(pid){
	$.ajax({
		   type: "POST",
		   url: "pop_up_div.php",
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.div)
					{						
						//project_pop.innerHTML='HELLOW';
						$("#project_pop").html(data.div);
						popOpen(pid);
					} 
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});	
}

function popOpen(pid)
{
	var popID = "dialog-form";
	document.getElementById('email_pid').value=pid;
	//alert(popID);
	popWidth = '500'; $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="../../images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
	   
	//Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;
	//Apply Margin to Popup
	$('#' + popID).css({ 
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});	
	//Fade in Background
	$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 		
	}
	//Close Popups and Fade Layer
	$('span.close, #fade, #cancel').live('click', function() { //When clicking on the close or fade layer...
	Fade();
	return false;
});
function Fade()
{
	$("#msg_email").html('');
	document.getElementById('email').value="";
	$('#fade , .popup_block').fadeOut(); //fade them both out
	$('#fade').remove();
}
