<?php

/**
 * Install Stage 2, Furasta.Org
 *
 * Requests user details and stores them in the
 * $_SESSION variable.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package	   installer
 */

session_start( );

if(@$_SESSION['begin']!=2)
        header('location: stage1.php');

if(isset($_POST['submit'])){

	$_SESSION[ 'user' ][ 'name' ] = addslashes( @$_POST[ 'Name' ] );
	$_SESSION[ 'user' ][ 'email' ] = addslashes( @$_POST[ 'Email' ] );
	$pass = addslashes( @$_POST[ 'Password' ] );
	$repeat = addslashes( @$_POST[ 'Repeat-Password' ] );

	if( empty( $_SESSION[ 'user' ][ 'name' ] ) || empty( $_SESSION[ 'user' ][ 'email' ] ) || empty( $pass ) || empty( $repeat ) )
		$error = 'The Name, Email, Password and Repeat Password fields are required';
	elseif( $pass != $repeat )
		$error = 'The Password and Repeat Password fields must match';
	else{
		$_SESSION[ 'user' ][ 'pass' ] = md5( $pass );
		$_SESSION[ 'begin' ] = 3;
		header( 'location: stage3.php' );
	}
}

require 'header.php';

echo '
<script type="text/javascript">
$(function(){
	$("#install").validate({
		"Name" : {
			"name" : "name",
			"required" : true
		},
		"Email" : {
			"name" : "email",
			"required" : true,
			"email" : true
		},
		"Password" : {
			"name" : "password",
			"required" : true,
			"minlength" : 6,
			"match" : "Repeat-Password"
		},	
		"Repeat-Password" : {
			"name" : "repeat password",
			"required" : true
		},	
	});	
});
</script>
<h2>' . @$error . '</h2>
<div id="install-center">
	<h1 style="text-align:left">Stage 2 / 3</h1>
	<form id="install" method="post">
		<table class="row-color">
			<tr><th colspan="2">Personal Details</th></tr>
			<tr><td>Name:</td><td><input type="text" name="Name" value="'.@$_SESSION['user']['name'].'" class="input right" /></td></tr>
			<tr><td>Email Address:</td><td><input type="text" name="Email" value="'.@$_SESSION['user']['email'].'" class="input right" /></td></tr>
			<tr><td>Password:</td><td><input type="password" name="Password" class="input right" /></td></tr>
		        <tr><td>Repeat Password:</td><td><input type="password" name="Repeat-Password" class="input right" /></td></tr>
		</table>
	        <br/>
        	<input type="submit" name="submit" id="form-submit" class="submit right" value="Next"/>
	        </form>
	        <br style="clear:both"/>
</div>
';

require 'footer.php';
?>
