<?php	
require 'includes.php';
?>
<html>
	<head>
	<title>Thank you!</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/app.css">
	
	<script src="js/jquery.min.js"></script>
	
	<style>
		body {
			text-align: center;
			padding-top: 20px;
		}
	</style>
	</head>
	<body>
		
	<h1>Thanks for completing the survey!</h1>
	<h2>Your responses have been recorded.</h2>
	<h3>Please hand this tablet back to a member of the Clinic staff.</h3>
<script>
	// Disable back functionality
(function ($, global) {


    var _hash = "!",
    noBackPlease = function () {
        global.location.href += "#";

        setTimeout(function () {
            global.location.href += "!";
        }, 50);
    };

    global.setInterval(function () {
        if (global.location.hash != _hash) {
            global.location.hash = _hash;
        }
    }, 100);

    global.onload = function () {
        noBackPlease();

        // disables backspace on page except on input fields and textarea..
        $(document.body).keydown(function (e) {
            var elm = e.target.nodeName.toLowerCase();
            if (e.which == 8 && elm !== 'input' && elm  !== 'textarea') {
                e.preventDefault();
            }
            // stopping event bubbling up the DOM tree..
            e.stopPropagation();
        });
    }

})(jQuery, window);	
</script>
	</body>
</html>