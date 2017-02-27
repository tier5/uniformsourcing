
function IsSaved(val)
{
	document.getElementById('saveid').value = val;
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
if($("#order_on")) 
{
	$(function() {
		$("#order_on").datepicker();
	});
}



$("#validationForm").validate({
	rules: {
			quanPeople:  {digits: true},
			ptrnsetup:  {number: true}, 
			grdngsetup:  {number: true}, 
			smplefeesetup:  {number: true}, 
			fabric:  {number: true}, 
			trimfee:  {number: true}, 
			labour:  {number: true}, 
			duty:  {number: true}, 
			frieght:  {number: true}, 
			other:  {number: true}, 
			totalGarments:  {digits: true},
			targetPriceunit:  {number: true},
			targetRetailPrice:  {number: true},
			projectQuote:  {number: true},
			shipping_cost:  {number: true},
			pt_invoice:  {number: true},
			taxes:  {number: true},
			pcost:  {number: true},
			pestimate:  {number: true},
			elementcost: {number: true},
			order_on: {date: true},
			pcompcost:  {number: true}
		},
		messages: {
			quanPeople: "Please enter in digits",
			ptrnsetup: "Please enter in digits",
			grdngsetup: "Please enter in digits",
			smplefeesetup: "Please enter in digits",
			fabric: "Please enter in digits",
			trimfee: "Please enter in digits",
			labour: "Please enter in digits",
			duty: "Please enter in digits",
			frieght: "Please enter in digits",
			other: "Please enter in digits",
			targetPriceunit : "Please enter in digits",
			targetRetailPrice : "Please enter in digits",
			shipping_cost: "Please enter in digits",
			pt_invoice : "Please enter in digits",
			taxes : "Please enter in digits",
			projectQuote : "Please enter in digits",
			pcost: "Please enter in digits",
			pestimate: "Please enter in digits",
			elementcost: "Please enter in digits",
			order_on: "Please select a valid date",
			pcompcost : "Please enter in digits"
			}
	});



function sampleChange()
{
	var sel = document.getElementById('sampleNmbr');
	var hdn = document.getElementById('hdn_sampleNum');
	hdn.value = sel.options[sel.selectedIndex].value;
	if(hdn.value != "")
		document.getElementById('sample_a').style.display="";
	else
		document.getElementById('sample_a').style.display="none";
}

function CheckVendor()
{
	var vendor_sel = document.getElementById('vendorID');
	var vendorid = vendor_sel.options[vendor_sel.selectedIndex].value;
	if(vendorid !="" && vendorid > 0)
	{
		var table = document.getElementById('tbl_vendor');
		var rowCount = table.rows.length;
		for(i=0; i<rowCount ;i++)
		{
			if(vendorid ==document.getElementById("vendorinp"+i).value)
			{
				alert('You have selected a vendor which is already in list');
				vendor_sel.selectedIndex = 0;
				return;
			}
		}
	}
}


function FillEstimatedCost()
{	
	if($("#validationForm").valid())
	{
		var patern = document.getElementById('ptrnsetup').value;
		var grading = document.getElementById('grdngsetup').value;
		var samplefee = document.getElementById('smplefeesetup').value;
		var fabric = document.getElementById('fabric').value;
		var trim = document.getElementById('trimfee').value;
		var labour = document.getElementById('labour').value;
		var duty = document.getElementById('duty').value;
		var frieght = document.getElementById('frieght').value;
		var other = document.getElementById('other').value;
		var total_garment = document.getElementById('totalGarments').value;
		var first_sum = 0;
		var sub_value = 0;
		var estimated_cost = 0;
		if((patern != '' || grading != '' || samplefee != '' )&& (total_garment==0 || total_garment == ""))
		{
			alert("Please enter Total No. of Garments in the 'Purchase' tab before saving");
		}
		if(patern == '')
		{
			patern =0;
		}
		if(grading == '')
		{
			grading = 0;
		}
		if(samplefee == '')
		{
			samplefee = 0;
		}
		if(fabric == '')
		{
			fabric = 0;
		}
		if(trim == '')
		{
			trim = 0;
		}
		if(labour == '')
		{
			labour = 0;
		}
		if(duty == '')
		{
			duty = 0;
		}
		if(frieght == '')
		{
			frieght = 0;
		}
		if(other == '')
		{
			other = 0;
		}
		first_sum = parseFloat(patern) + parseFloat(grading) + parseFloat(samplefee);
		if(total_garment !=0 && total_garment !='')
		{
			sub_value = parseFloat(first_sum)/total_garment;
			estimated_cost = parseFloat(sub_value) + parseFloat(fabric) + parseFloat(trim) + parseFloat(labour) + parseFloat(duty) + parseFloat(frieght) + parseFloat(other);
			document.getElementById('pestimate').value = estimated_cost.toFixed(2);
		}
		else
		{
			document.getElementById('pestimate').value = 0;
		}
	}
}
function ClearAllFields()
{
	document.getElementById('ptrnsetup').value ="";
	document.getElementById('grdngsetup').value ="";
	document.getElementById('smplefeesetup').value ="";
	document.getElementById('fabric').value ="";
	document.getElementById('trimfee').value ="";
	document.getElementById('labour').value ="";
	document.getElementById('duty').value ="";
	document.getElementById('frieght').value ="";
	document.getElementById('other').value ="";
}
function stripslashes (str) {
    return (str + '').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
        case '\\':
            return '\\';
        case '0':
            return '\u0000';
        case '':
            return '';
        default:
            return n1;
        }
    });
}
