$( document ).ready(function() {
	$('input:text').click(
	    function(){
	        $(this).val('');
	    }
	);

	$('#tweet_toggle').click(function(){
		$('#tweets').slideToggle('slow');
	});
});
