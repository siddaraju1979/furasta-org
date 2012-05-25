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

require 'header.php';
require HOME . '_inc/function/system.php';

if(@$_SESSION['complete']!=1)
	header('location: stage3.php');

$connect=mysql_connect($_SESSION['db']['host'],$_SESSION['db']['user'],$_SESSION['db']['pass']);
$select=mysql_select_db($_SESSION['db']['name'],$connect);

$prefix=$_SESSION['db']['prefix'];
$pages=$prefix.'pages';
$users=$prefix.'users';
$trash=$prefix.'trash';
$groups=$prefix.'groups';
$options= $prefix . 'options';
$files = $prefix . 'files';
$hash=md5(mt_rand());
$site_url=$_SESSION['settings']['site_url'];
$user_files=$_SESSION['settings']['user_files'];

$pagecontent='
<h1>Welcome to Furasta CMS</h1>
<p>This is your new installation of Furasta CMS.</p>
<p>To log in to your CP <a href=\'/admin\'>Click Here</a></p>
';

// pages
mysql_query('drop table if exists '.$pages);
mysql_query('create table '.$pages.' (id int auto_increment primary key,name text,content text,slug text,template text,type text,edited date,user text,position int,parent int,perm text, home int,display int)');
mysql_query('insert into '.$pages.' values(0,"Home","'.$pagecontent.'","Home","Default","Normal","'.date('Y-m-d').'","Installer",1,0,"|",1,1)');

// user
mysql_query('drop table if exists '.$users);
mysql_query('create table '.$users.' (id int auto_increment primary key,name text,email text,password text,groups text,hash text,reminder text, data text)');
mysql_query('insert into '.$users.' values(0,"'.$_SESSION['user']['name'].'","'.$_SESSION['user']['email'].'","'.$_SESSION['user']['pass'].'","_superuser","'.$hash.'","","")');
$user_id = mysql_insert_id( );


// groups
mysql_query( 'drop table if exists ' . $groups );
mysql_query( 'create table ' . $groups . ' ( id int auto_increment primary key, name text, perm text, file_perm text )' );
mysql_query( 'insert into ' . $groups . ' values ( "", "Users", "a,e,c,d,t,o,s,u", "|" )' );

// trash
mysql_query('drop table if exists '.$trash);
mysql_query('create table '.$trash.' (id int auto_increment primary key,name text,content text,slug text,template text,type text,edited date,user text,position int,parent int,perm text,home int,display int)');
mysql_query('insert into '.$trash.' values(0,"Example Page","Sample page content.","Example-Page","Default","Normal","'.date('Y-m-d').'","Installer",1,"","|",1,1)');

// options
mysql_query( 'drop table if exists ' . $options );
mysql_query( 'create table ' . $options . ' (name text,value text)' );

// file manager
mysql_query( 'drop table if exists ' . $files );
mysql_query( 'create table ' . $files . ' ( id int auto_increment primary key, name text, owner int, perm text, type text, public int, hash text )' );
// file manager add initial directory structure
mysql_query( 'insert into ' . $files . ' values( "", "users", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )') ; 
$fm_users_id = mysql_insert_id( );
mysql_query( 'insert into ' . $files . ' values( "", "1", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
$fm_users_su_id = mysql_insert_id( );
mysql_query( 'insert into ' . $files . ' values( "", "groups", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
$fm_groups_id = mysql_insert_id( );
mysql_query( 'insert into ' . $files . ' values( "", "_superuser", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
$fm_groups_su_id = mysql_insert_id( );

$filecontents='<?php
define(\'PAGES\',\''.$pages.'\');
define(\'USERS\',\''.$users.'\');
define(\'TRASH\',\''.$trash.'\');
define(\'GROUPS\',\''.$groups.'\');
define(\'OPTIONS\',\''.$options.'\');
define(\'FILES\',\'' . $files . '\');
define(\'TEMPLATE_DIR\',\''.HOME.'_www/\');
define(\'PREFIX\',\''.$prefix.'\');
define(\'VERSION\',\'0.9.2\');
define(\'SITEURL\',\''.$site_url.'\');
define(\'USER_FILES\',\''.$user_files.'\');
define(\'DIAGNOSTIC_MODE\',\'0\');
define(\'LANG\', \'English\' );

$PLUGINS=array();

$SETTINGS=array(
	\'site_title\'=>\''.addslashes( $_SESSION['settings']['title'] ).'\',
        \'site_subtitle\'=>\''.addslashes( $_SESSION['settings']['sub_title'] ).'\',
        \'index\'=>\''.$_SESSION['settings']['index'].'\',
	\'maintenance\'=>\''.$_SESSION['settings']['maintenance'].'\',
	\'system_alert\'=>\'\'
);

$DB=array(
        \'name\'=>\''.$_SESSION['db']['name'].'\',
        \'host\'=>\''.$_SESSION['db']['host'].'\',
        \'user\'=>\''.$_SESSION['db']['user'].'\',
        \'pass\'=>\''.$_SESSION['db']['pass'].'\'
);

?>
';

file_put_contents(HOME.'.settings.php',$filecontents) or error('Please grant <i>0777</i> write access to the <i>'.HOME.'</i> directory then reload this page to complete installation.');

// dirs to create
$dirs = array(
	// system dirs
	$user_files . 'cache',
	$user_files . 'backup',
	$user_files . 'smarty',
	$user_files . 'smarty/templates',
	$user_files . 'smarty/template_c',
	
	// file manager dirs
	$user_files . 'files',
	$user_files . 'files/users',
	$user_files . 'files/users/' . $user_id,
	$user_files . 'files/groups',
	$user_files . 'files/groups/_superuser',
	$user_files . 'db',
	$user_files . 'db/' . $fm_users_id,
	$user_files . 'db/' . $fm_users_id . '/' . $fm_users_su_id,
	$user_files . 'db/' . $fm_groups_id,
	$user_files . 'db/' . $fm_groups_id . '/' . $fm_groups_su_id
);

// symlinks to create for the file manager 
$symlinks = array(
	$user_files . 'files/.users.lnk' => $user_files . 'db/' . $fm_users_id . '/',
	$user_files . 'files/users/.' . $user_id . '.lnk' => $user_files . 'db/' . $fm_users_id . '/' . $fm_users_su_id . '/',
	$user_files . 'files/.groups.lnk' => $user_files . 'db/' . $fm_groups_id . '/',
	$user_files . 'files/groups/._superuser.lnk' =>  $user_files . 'db/' . $fm_groups_id . '/' . $fm_groups_su_id . '/'
);

// create dirs
$match = true;
foreach( $dirs as $dir ){
	if( !is_dir( $dir ) )
		mkdir( $dir ) || $match = false;
}
if( $match == false )
	die( 'could not create some of the required directories in the user dir. make sure your user dir is writable' );

// create symlinks
foreach( $symlinks as $link => $target ){
	if( !file_exists( $link ) )
		symlink( $target, $link );
}

$htaccess=
	"# .htaccess - Furasta.Org\n".
	"<IfModule mod_deflate.c>\n".
	"       SetOutputFilter DEFLATE\n".
	"       Header append Vary User-Agent env=!dont-vary\n".
	"</IfModule>\n\n".

	"php_flag magic_quotes_gpc off\n\n".

	"RewriteEngine on\n".
	"RewriteCond %{SCRIPT_NAME} !\.php\n".
	"RewriteRule ^admin$ /admin/ [L]\n".
	"RewriteRule ^files/(.*)$ /_user/files.php?name=$1 [L]\n".
	"RewriteRule ^([^./]{3}[^.]*)$ /index.php?page=$1 [QSA,L]\n\n".

	"AddCharset utf-8 .js\n".
	"AddCharset utf-8 .xml\n".
	"AddCharset utf-8 .css\n".
	"AddCharset utf-8 .php";

file_put_contents(HOME.'.htaccess',$htaccess);

if($_SESSION['settings']['index']=='0')
	$robots=
	"# robots.txt - Furasta.Org\n".
	"User-agent: *\n".
	"Disallow: /admin\n".
	"Disallow: /install\n".
	"Disallow: /_user";
else
	$robots=
	"# robots.txt - Furasta.Org\n".
	"User-agent: *\n".
	"Disallow: /\n";

file_put_contents(HOME.'robots.txt',$robots);

$url='http://'.$_SERVER['SERVER_NAME'];
$subject='User Activation - Furasta.Org';
$message='Hi ' . $_SESSION['user']['name'].',<br/>
	<br/>
	Please activate your new user by clicking on the link below:<br/>
	<br/>
	<a href="'.$url.'/admin/users/activate.php?hash='.$hash.'">'.$url.'/admin/users/activate.php?hash='.$hash.'</a><br/>
	<br/>
	If you are not the person stated above please ignore this email.<br/>
';
email( $_SESSION['user']['email'], $subject, $message );

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
