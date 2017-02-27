// JavaScript Document
var custs;
var sales;
var quan;
var cost;
var reqDate;
var delDate;
$(function() {
	$("#dialog").dialog("destroy");
	var email = $("#email"),
		subject = $("#subject"),
			allFields = $([]).add(name).add(email).add(subject),
			tips = $(".validateTips");
			
	function updateTips(t) {
		tips
			.text(t)
			.addClass('ui-state-highlight');
		setTimeout(function() {
			tips.removeClass('ui-state-highlight', 1500);
		}, 500);
	}
	
	function checkLength(o,n,min,max) {

		if ( o.val().length > max || o.val().length < min ) {
			o.addClass('ui-state-error');
			updateTips("Length of " + n + " must be between "+min+" and "+max+".");
			return false;
		} else {
			return true;
		}

	}
	
	function checkRegexp(o,regexp,n) {

		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass('ui-state-error');
			updateTips(n);
			return false;
		} else {
			return true;
		}

	}
	
	$("#dialog-form").dialog({
			autoOpen: false,
			height: 400,
			width: 450,
			modal: true,
			buttons: {
				'Send a email': function() {
					var bValid = true;
					allFields.removeClass('ui-state-error');
					bValid = bValid && checkRegexp(email,/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"eg. ui@jquery.com");
				   bValid = bValid && checkLength(subject,"subject",3,150);
				   if (bValid) {
						//alert("submitform");
						$('#finalCustomer2').val(custs);
						$('#salesExecutive2').val(sales);
						$('#quanPeople2').val(quan);
						$('#costing2').val(cost)
						$('#requestDate2').val(reqDate);
						$('#deliveryDate2').val(delDate);
						$("#frmmailsendform").submit();
						$(this).dialog('close');
					}
				 },
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				allFields.val('').removeClass('ui-state-error');
			}
		});
	
		$("#dialog-print").dialog({
			autoOpen: false,
			height: 1200,
			width: 1000,
			modal: true,
			close: function() {
				try { $(this).dialog('close'); } catch(err) { }
			}
		});

	
	$('#send-email')
		.button()
		.click(function() {
			$('#dialog-form').dialog('open');
	 });
	/*	
	$('#btnPrint')
		.button()
		.click(function() {			
			$('#dialog-print').dialog('open');
			$('.ui-dialog').css('left',0);
			$('.ui-dialog').css('top',0);
			//setTimeout("$('.ui-dialog').animate({scrollTop:0}, 'slow');",1500);
			setTimeout("$('#btnpopprint').focus();",1500);
			setTimeout("$('#finalCustomer1').val(custs);",2500);//$('#finalCustomer1').val(custs);//
			setTimeout("$('#salesExecutive1').val(sales);",2500);//$('#salesExecutive1').val(sales);//
			setTimeout("$('#quanPeople1').val(quan);",2500);
			setTimeout("$('#costing1').val(cost);",2500);
			setTimeout("$('#requestDate1').val(reqDate);",2500);
			setTimeout("$('#deliveryDate1').val(delDate);",2500);
	 });	
	*/

});
function fnsetval() {
	custs=$('#finalCustomer').val();
	sales=$('#salesExecutive').val();
	quan=$('#quanPeople').val();
	cost=$('#costing').val();
	reqDate=$('#requestDate').val();
	delDate=$('#deliveryDate').val();
	//alert(custs);
}