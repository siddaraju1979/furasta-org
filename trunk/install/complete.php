<?php

/**
 * Install Completion, Furasta.Org
 *
 * Completes the install process. Installs the database,
 * creates the settings.php file and emails the user
 * with a validation link.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    installer
 */

session_start( );

if(@$_SESSION['complete']!=1)
	header('location: stage3.php');

require 'header.php';
require HOME . '_inc/function/defaults.php';
require HOME . 'install/install-core.php';

/**
 * connect to mysql database
 */
$connect = mysql_connect( $_SESSION[ 'db' ][ 'host' ], $_SESSION[ 'db' ][ 'user' ], $_SESSION[ 'db' ][ 'pass' ] );
$select  = mysql_select_db($_SESSION[ 'db' ][ 'name' ], $connect );

/**
 * run install, variables below
 * are used for sharing of information
 * between install functions
 */
$constants = array( );
$settings  = array( );
$hash      = md5( mt_rand( ) );
$user_id   = 0;
install_settings( );
install_db( );
install_dirs( );
install_email( );

echo '
<h1>Complete! Verification Email Sent to ' . $_SESSION[ 'user' ][ 'email' ] . '</h1>
<p>Your Furasta CMS installation has been performed successfully. A user verification email has been sent to your email address. You must verify this address before you can log in. Please now finish and configure your website.</p>
<br/>
<a href="../admin/index.php" class="grey-submit right">Finish</a>
</form>
<br style="clear:both"/>
';

require 'footer.php';
?>
