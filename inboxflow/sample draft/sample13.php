<!DOCTYPE html>
<html>
<head>
	<style>
		body {
			text-align: center;
			font-size: 20px;
		}
		button {
			background-color: #4CAF50;
			border: none;
			color: white;
			padding: 5px 32px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
		}
	</style>
</head>
<body>
	<h1 style="color:green">
		GeeksForGeeks
	</h1>
	<p>
		The form attribute specifies which
		form the drop-down list belongs to:
	</p>

	<form id="countryName" action="sample13.php" method="post">
		<label for="fname">First Name:</label>
		<input type="text" id="fname" name="fname">
		<button>Submit</button>
	</form>
	<br>
	<label for="country">Choose a country:</label>
	<select id="country" name="carlist" form="countryName">
		<option value="India">India</option>
		<option value="US">US</option>
		<option value="Germany">Germany</option>
		<option value="Australia">Australia</option>
	</select>
</body>
</html>
<?php
if($_SERVER['REQUEST_METHOD']=="POST"){
    $fname=$_POST['fname'];
    $country=$_POST['carlist'];
    echo $fname,$country;
}
