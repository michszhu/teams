<!DOCTYPE html>
<html>

<head>	
	
	
	<style>
/* Paste this css to your style sheet file or under head tag */
/* This only works with JavaScript, 
if it's not present, don't show loader */
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url('https://d13yacurqjgara.cloudfront.net/users/160117/screenshots/3197970/main.gif') center no-repeat #fff;
}
	</style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>

//paste this code under the head tag or in a separate js file.
	// Wait for window load
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});
	

</head>

<body>
	<div class="se-pre-con"></div>
		
	
<form action="start.php" method="post">
google sheets ID : <input type="text" name="name"><br>
use format https://docs.google.com/spreadsheets/d/1LhCT9KRfMrXinRyphcBn1jz3JIUh5LQSli9mQFmOc7w/edit#gid=1408926158
	e.g. ID -> 1LhCT9KRfMrXinRyphcBn1jz3JIUh5LQSli9mQFmOc7w
<input type="submit">
</form>

</body>
</html>
