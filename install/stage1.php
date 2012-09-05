<?php

/**
 * Install Stage 1, Furasta.Org
 *
 * Requests database information from the user and stores
 * it in a $_SESSION variable.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package	   installer
 */

session_start( );

if(isset($_POST['submit'])){
	$_SESSION[ 'db' ][ 'name' ] = addslashes( $_POST[ 'DatabaseName' ] );
	$_SESSION[ 'db' ][ 'host' ] = addslashes( $_POST[ 'Hostname' ] );
	$_SESSION[ 'db' ][ 'user' ] = addslashes( $_POST[ 'Username' ] );

	if( empty( $_SESSION[ 'db' ][ 'name' ] ) || empty( $_SESSION[ 'db' ][ 'host' ] ) || empty( $_SESSION[ 'db' ][ 'user' ] ) )
		$error = 'The Database Name, Host Name and User Name fields are required.';
	else{
		$_SESSION[ 'db' ][ 'prefix' ] = addslashes( $_POST[ 'Prefix' ] );
		$_SESSION[ 'db' ][ 'pass' ]= addslashes( $_POST[ 'Password' ] );
		$_SESSION[ 'begin' ] = 2;
		header( 'location: stage2.php' );
		exit;
	}
}

require 'header.php';

echo '
<script type="text/javascript">
$( document ).ready( function( ){
	$( "#install" ).validate({
		"DatabaseName" : {
			"name" : "database name",
			"required" : true
		},
		"Hostname" : {
			"name" : "hostname",
			"required" : true
		},
		"Username" : {
			"name" : "username",
			"required" : true
		}
	},
		function( error ){
			fAlert( error );
		}
	);
	$( "#install" ).submit( function( ){
		$("#check_connection").html("<img src=\"' . SITE_URL . '/_inc/img/loading.gif\"/> Checking Details...");
		var connection=checkConnection(["Hostname","Username","DatabaseName","Password"]);
		$("input[name=DatabaseName]").removeClass("error");
		$("input[name=Hostname]").removeClass("error");
		$("input[name=Username]").removeClass("error");
		$("input[name=Password]").removeClass("error");
		if(connection=="ok")
			return true;
		if(connection=="database")
			$("input[name=DatabaseName]").addClass("error");
		else{
			$("input[name=Hostname]").addClass("error");
			$("input[name=Username]").addClass("error");
			$("input[name=Password]").addClass("error");
		}
		fAlert("The details that you have supplied are invalid. Please correct them to continue.");
		$("#check_connection").html("&nbsp;");

		return false;
	} );
} );
</script>
';

$prefix=(isset($_SESSION['db']['prefix']))?$_SESSION['db']['prefix']:'fr_';

echo '
<h2>' . @$error . '</h2>
<div id="install-center">
	<p id="check_connection" class="right">&nbsp;</p>
	<h1 style="text-align:left">Stage 1 / 3</h1>
	<form id="install" method="post" >
		<table class="row-color">
			<tr><th colspan="2">Database Details</th></tr>
			<tr><td>Database Name:</td><td><input type="text" name="DatabaseName" value="'.@$_SESSION['db']['name'].'" class="input right" /></td></tr>
			<tr><td>Host Name:</td><td><input type="text" name="Hostname" value="'.@$_SESSION['db']['host'].'" class="input right" /></td></tr>
			<tr><td>User Name:</td><td><input type="text" name="Username" value="'.@$_SESSION['db']['user'].'" class="input right" /></td></tr>
			<tr><td>Password:</td><td><input type="password" name="Password" value="" class="input right" /></td></tr>
			<tr><td>Optional Table Prefix:</td><td><input type="text" name="Prefix" value="'.$prefix.'" class="input right"/></td></tr>
		</table>
	<br/>
	<input type="submit" name="submit" id="form-submit" class="submit right" value="Next"/>
	</form>
	<br style="clear:both"/>
</div>
';

require 'footer.php';
?>
