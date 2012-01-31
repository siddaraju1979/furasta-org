<?php

/**
 * User Files Access, Furasta.Org
 *
 * This file is loaded whenever a file contained in the _user
 * directory is accessed. It loads the file, even if the _user
 * dir is placed outside the web root.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @todo update this file
 */

require '../_inc/define.php';

$name=@$_GET['name'];

if($name=='')
	error( 'You must select an item to view.','No File Selected', false );

if(file_exists(USER_FILES . 'files/' . $name) && strpos( $name, '..' ) === false){
	$content=file_get_contents(USER_FILES . 'files/' . $name);
	echo $content;
}
else
	error( 'File does not exist.','404 - Not Found', false);

exit;

?>
