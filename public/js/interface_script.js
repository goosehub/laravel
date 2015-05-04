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
        // Enter input to response div
        $('#response_cnt').append('<div class="user_talking">' + text_input + '</div>');
        // Scroll to bottom
        $("#response_cnt").scrollTop($("#response_cnt")[0].scrollHeight);
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
                $('#response_cnt').append('<div class="computer_talking">' + data[0]['text'] + '</div>');
                // Scroll to bottom
                $("#response_cnt").scrollTop($("#response_cnt")[0].scrollHeight);
            }
        });
    });

});