$(document).ready( function() {

	// Load page with focus on text input
    $('#text_input').focus();

    // Input send and response
	var text_input = '';
	$('form').submit(function (e) {
		//prevent default form submit
        e.preventDefault();
        // Get input
	    text_input = $("#text_input").val();
        // Clear input and refocus
        $('#text_input').val('');
        $('#text_input').focus();
        // Send data
        $.ajax({
            url: 'speak',
            data: { text_input: text_input },
	        cache: false,
            success: function (data) {
            	// Receive response and put to response div
                $('#response_cnt').html(data[0]['text']);
            }
        });
    });

});