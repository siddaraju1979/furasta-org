<?php

/**
 * Files List, Furasta.Org
 *
 * Lists Files in the user files dir that the user has access to
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

$User = User::getInstance( );
$Template = Template::getInstance( );
$Template->loadJavascript( 'admin/files/list.js' );

$files = array(
	USER_FILES . 'users/' . $User->id( ),
	USER_FILES . 'groups/' . implode( '', $User->groups( ) )
);

$content = '
<h1>Files</h1>
<div id="file-manager">
	<div id="files">
		<div id="filecrumbs">
			<p>this is where the breadcrumbs go</p>
		</div>
		<p>this is where the files go</p>
	</div>
	<div id="shortcuts">
		<p>this is where the shortcut links to home, groups etc go</p>
	</div>
</div>';
$Template->add( 'content', $content );
?>
