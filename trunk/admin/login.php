<?php

/**
 * Login, Furasta.Org
 *
 * Displays login prompt, creates session and cookies if applicable.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

$Template = Template::getInstance( );

$Template->add( 'title', 'Login' );

/**
 * Set up basic validation
 */

$conds = array(
	'Email' => array(
		'required' => true,
		'email'	=> true
	),
	'Password' => array(
		'required' => true
	)
);

$valid = validate( $conds, "#login", 'login' );

/**
 * Check if form submitted, or if cookie is present
 */
if( isset( $_POST[ 'login' ] ) && $valid == true ){
	$email = addslashes( $_POST['Email'] );
	$pass = md5( $_POST[ 'Password' ] );
	$remember = addslashes( $_POST[ 'Remember' ] );
	$check = 1;
}
elseif( isset( $_COOKIE[ 'furasta' ][ 'email' ] ) && isset( $_COOKIE[ 'furasta' ][ 'password' ] ) ){
        $email = $_COOKIE[ 'furasta' ][ 'email' ];
        $pass = $_COOKIE[ 'furasta' ][ 'password' ];
        $remember = 1;
        $check = 1;
}

/**
 * Confirm cookie/post data
 */
if( @$check == 1 ){

	$login = User::login( $email, $pass, array( 'a' ), true );

	if( $login == true ){

		/**
		 * if remember is set then set cookie
		 */
		if( $remember == 1 ){
			$User = User::getInstance( );
			$User->setCookie( );
		}

                header( 'location: ' . SITEURL . $_SERVER[ 'REQUEST_URI' ] );
		exit;
	}

}

/**
 * load javascript files
 */
$Template->loadJavascript( '_inc/js/system.js' );
$Template->loadJavascript( '_inc/js/jquery/validate.js' );
$Template->loadJavascript( 'admin/login.js' );

$content='
<div id="password-reminder-dialog" style="display:none" title="Password Reminder">
	<form method="post">
		<table style="border:none">
			<tr>
				<td>Email:</td>
				<td><input type="text" name="Email-Reminder" value=""/></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><i id="reminder-information">Please enter your email address and a link will be sent to you to reset your password.</i></td>
			</tr>
		</table>
	</form>
</div>

<div id="login-wrapper">
	<span class="header-img" id="header-Login">&nbsp;</span>
	<h1 id="login-header">Login</h1>
	<br/>
	<form id="login" method="post">
		<table id="login-table">
			<tr><td class="medium">Email:</td><td><input type="text" name="Email" value="'.@$_SESSION['email'].'"/></td></tr>
                	<tr><td class="medium">Password:</td><td><input type="password" name="Password" /></td></tr>
			<tr><td class="medium">&nbsp;</td><td class="small">Remember Me: <input type="checkbox" value="1" name="Remember" class="checkbox" class="checkbox" CHECKED/> <a class="link" id="password-reminder">Forgot your password?</a></td></tr>
			<tr><td></td><td><input type="submit" name="login" class="input" width="60px" value="Login"/></tr>
		</table>
	</form>
</div>
';

$Template->add('content',$content);

require $admin_dir.'layout/error.php';
exit;

?>
