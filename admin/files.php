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

require 'header.php';

$Template->loadJavascript( 'admin/files/files.js' );
$Template->add( 'title', 'Files' );
$FileManager = FileManager::getInstance( );
$files = $FileManager->readDir( '/users/1/' );

$content = '
<h1>Files</h1>
<div id="file-manager">
	<div id="files">
		<h3 id="filecrumbs"></h3>
                <ul id="directory-list">
                ';

foreach( $files as $file => $sub ){
        $img = ( is_dir( $FileManager->users_files . $file ) ) ? '_inc/img/folder.png' : '_inc/img/file.png';
        $content .= '<li><img src="' . SITE_URL . $img . '"/>' . $file . '</li>';
}

$files = $FileManager->readDir( '/', 2 );

$content .= '
	        </ul>	
	</div>
</div>
<br style="clear:both"/>';
$Template->add( 'content', $content );

require 'footer.php';
?>
