if($("#fromDate")) {
	$(function() {
		$("#fromDate").datepicker();
	});
}
if($("#toDate")) {
	$(function() {
		$('#toDate').datepicker( {
			onSelect: function(date) {
				var toDate = $('#toDate').val();
            	if(! ( Date.parse(toDate) <= Date.parse(date)) ){
					alert("Enter a date greater than from date");
					$("#toDate").val("");
				}				
			}
		});
	});
}