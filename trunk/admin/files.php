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

$Template->loadJavascript( '_inc/js/FileManager.js' );
$Template->loadJavascript( 'admin/files/files.js' );
$Template->add( 'title', 'Files' );

$content = '
<h1>Files</h1>
<div id="file-manager">
        <div id="files">
                <div id="directory-container">
                        <div class="th"><h3 style="color:#fff;margin-top:5px;margin-left:5px">Directory Listing</h3></div>
                        <ul id="panel-top">
                                <li><a class="link" id="file-upload"><img src="' . SITE_URL . '_inc/img/file.png"/><span>Upload File</span></a></li>
                                <li><a class="link" id="new-folder"><img src="' . SITE_URL . '_inc/img/folder.png"/><span>New Folder</span></a></li>
                                <li><a class="link" id="dir-settings"><span id="menu-configuration-img"></span><span>Folder Settings</span></a></li>
                        </ul>
                        <div id="filecrumbs"></div>
                        <ul id="directory-list"></ul>      
                        <br style="clear:both"/>  
                </div>
	</div>
</div>
<br style="clear:both"/>';
$Template->add( 'content', $content );

require 'footer.php';
?>
