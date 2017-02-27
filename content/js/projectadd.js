function ajaxFileUpload(filefield, pro_id, img_cnt)
	{		
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});
		var hdnfield="hdnimage"+img_cnt;		
		if(document.getElementById(hdnfield) && document.getElementById(hdnfield).value != "")
		{
			hdnfield = document.getElementById(hdnfield).value;
			//alert(hdnfield);
		}
		else
			hdnfield = "null";		
		$.ajaxFileUpload
		(
			{						
				url:'doajaxfileupload.php?field='+filefield+'&pro_id='+pro_id+'&img_cnt='+img_cnt+'&proj_img='+hdnfield,
				secureuri:false,
				fileElementId:filefield,
				dataType: 'json',
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
						}else
						{
							alert("Upload success");//alert(data.msg);
							document.getElementById(filefield).value="";
							var hdnfield="hdnimage"+img_cnt;
							document.getElementById(hdnfield).value=data.msg;
							var thmbfield="thumb_image"+img_cnt;
							if(img_cnt<=15)
							{
							document.getElementById(thmbfield).style.display="";							
							document.getElementById(thmbfield).style.width="129px";
							document.getElementById(thmbfield).style.height="96px";
							document.getElementById(thmbfield).src='../../projectimages/'+data.msg;
							}
							else
							{
								var newDiv=document.getElementById(thmbfield);
								newDiv.style.display="";	
								var projID = ""+ pro_id;
								var len = projID.length + 9;
								//alert(len);
								var str=data.msg.substr(len);
								//alert(str);
								newDiv.innerHTML='<a href ="../../projectimages/'+data.msg+'">'+str+'</a>';	
							}
							document.getElementById('alink'+img_cnt).style.display="block";
							return true;
						}
					}
				},
				error: function (data, status, e)
				{					
					alert(e);
				}
			}
		)
		
		return false;
	}
	
	function ajaxFileUploadGeneral(filefield, rowid,img_cnt) {
		$("#loading").ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload
		(
			{
				url:'doajaxfileupload.php?field='+filefield+'&id='+rowid+'&img_cnt='+img_cnt,
				secureuri:false,
				fileElementId:filefield,
				dataType: 'json',
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
						}else
						{
							alert("Upload success");//alert(data.msg);
							document.getElementById(filefield).value="";
							var hdnfield="hdn"+filefield;
							//if(img_cnt) { hdnfield=hdnfield+img_cnt; }							
							var thmbfield="thumb_"+filefield;
							//if(img_cnt) { thmbfield=thmbfield+img_cnt; }
							//alert(hdnfield+data.msg);
							//alert(thmbfield+'../../projectimages/'+data.msg);
							document.getElementById(hdnfield).value=data.msg;
							document.getElementById(thmbfield).style.display="";
							document.getElementById(thmbfield).src='../../projectimages/'+data.msg;
						}
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)		
		return false;
	}
	
if($("#prdctDate"))
{
	$(function() 
	 {
	  $("#prdctDate").datepicker();
	 });
}
if($("#sampleprdctDate"))
{
	$(function() 
	 {
	  $("#sampleprdctDate").datepicker();
	 });
}
if($("#prj_poDueDate"))
{
	$(function() 
	 {
	  $("#prj_poDueDate").datepicker();
	 });
}
if($("#poDueDate"))
{
	$(function() {
	 	$("#poDueDate").datepicker();
			   });
}
if($("#prdctnSample"))
{
	$(function() {
	 	$("#prdctnSample").datepicker();
			   });
}
if($("#lpDip"))
{
	$(function() {
	 	$("#lpDip").datepicker();
			   });
}
if($("#etaPrdctn"))
{
	$(function() {
	 	$("#etaPrdctn").datepicker();
			   });	
}
if($("#lapDip")) 
{
	$(function() {
		$("#lapDip").datepicker();
	});
}
if($("#lapDipApprvl")) 
{
	$(function() {
		$("#lapDipApprvl").datepicker();
	});
}
if($("#estDelvry")) 
{
	$(function() {
		$("#estDelvry").datepicker();
	});
}
if($("#pdctSampl")) 
{
	$(function() {
		$("#pdctSampl").datepicker();
	});
}
if($("#pdctSamplApprvl")) 
{
	$(function() {
		$("#pdctSamplApprvl").datepicker();
	});
}
if($("#szngLine")) 
{
	$(function() {
		$("#szngLine").datepicker();
	});
}
if($("#prdctnTrgtDelvry")) 
{
	$(function() {
		$("#prdctnTrgtDelvry").datepicker();
	});
}

if($("#dateFrom")) {
	/*$(document).ready(function() {
		$("#dateFrom").datepicker();
	});*/
	$(function() {
		$("#dateFrom").datepicker();
	});
} 
if($("#dateTo")) {
	$(function() {
		$("#dateTo").datepicker();
	});
}
if($("#requestDate")) {
	$(function() {
		$("#requestDate").datepicker();
	});
}
if($("#deliveryDate")) {
	$(function() {
		//$("#deliveryDate").datepicker();
		$('#deliveryDate').datepicker( {
			onSelect: function(date) {
				var reDt = $("#requestDate").val();
            	if( !( Date.parse(reDt) <= Date.parse(date)) ){
					alert("Enter a date greater than Request date");
					$("#deliveryDate").val("");
				}				
			}
		});
	});
}

function fnvalidate(){
	if(document.getElementById('projectName').value=="") {
		alert("Enter Project name");
		document.getElementById('projectName').focus();	
		return false;
	}
	if(document.getElementById('quanPeople').value!="") {
		if(isNaN(document.getElementById('quanPeople').value)) {
			alert("Enter Quantity of People in Digits");
			document.getElementById('quanPeople').focus();	
			return false;
		}
	}
	if(document.getElementById('totalGarments').value!="") {
		if(isNaN(document.getElementById('totalGarments').value)) {
			alert("Enter Total No. of Garments in Digits");
			document.getElementById('totalGarments').focus();	
			return false;
		}
	}
	if(document.getElementById('targetPriceunit').value!="") {
		if(isNaN(document.getElementById('targetPriceunit').value)) {
			alert("Enter Target Price Unit in Digits");
			document.getElementById('targetPriceunit').focus();	
			return false;
		}
	}
	if(document.getElementById('targetRetailPrice').value!="") {
		if(isNaN(document.getElementById('targetRetailPrice').value)) {
			alert("Enter Target Retail Price in Digits");
			document.getElementById('targetRetailPrice').focus();	
			return false;
		}
	}
	if(document.getElementById('projectQuote')) {
			if(isNaN(document.getElementById('projectQuote').value)) {
				alert("Enter Project Quote in digits");
				document.getElementById('projectQuote').focus();	
				return false;
			}	
	}
	if(document.getElementById('pcost')) {
			if(isNaN(document.getElementById('pcost').value)) {
				alert("Enter Project Cost in digits");
				document.getElementById('pcost').focus();	
				return false;
			}	
	}	
	if(document.getElementById('pestimate')) {
			if(isNaN(document.getElementById('pestimate').value)) {
				alert("Enter Project Estimate Cost in digits");
				document.getElementById('pestimate').focus();	
				return false;
			}	
	}
	if(document.getElementById('pcompcost')) {
			if(isNaN(document.getElementById('pcompcost').value)) {
				alert("Enter Project Completion Cost in digits");
				document.getElementById('pcompcost').focus();	
				return false;
			}	
	}
	return true;
}


function fnvalidatesamplerequest()
{
	if(document.getElementById('srID').value=="")
		{
			alert("Please enter a sampleID");
			document.getElementById('srID').focus();	
			return false;
		}
	if(document.getElementById('customerTargetprice').value!="")
	{
		if(isNaN(document.getElementById('customerTargetprice').value))
		{
			alert("Enter Customer Target Price in Digits");
			document.getElementById('customerTargetprice').focus();	
			return false;
		}
	}
	return true;
}
