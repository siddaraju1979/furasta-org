<?php

/**
 * JS Load, Furasta.Org
 *
 * This file loads multiple Javascript files, caches them,
 * compresses them, gzips them (if possible) and
 * advises the browser as to how long to keep a
 * client side cache.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

$files=@$_GET['files'];

if($files==''||strstr($files,'.'))
	die('filename cannot contain dots for security reasons. the ".js" is automatically appended to the name.');

ob_start('ob_gzhandler');
header('Content-type: text/javascript; charset: UTF-8');
header('Expires: '.gmdate( "D, d M Y H:i:s",time()+'35000000').' GMT');
header('Cache-Control: public, max-age=35000000');

$cache_file='FURASTA_JS_'.$files;

if(cache_exists($cache_file,'JS'))
	echo cache_get($cache_file,'JS');
else{
	$files=explode(',',$files);

	function compress($buffer){
		/* remove comments */
		$buffer=preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		/* remove tabs, spaces, newlines, etc. */
		$buffer=str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '),'', $buffer);
		return $buffer;
	}

	$content='';
	foreach($files as $file)
		$content.=compress(file_get_contents(substr(dirname(__FILE__),0,-8).$file.'.js'));

	cache($cache_file,$content,'JS');

	echo $content;
}

ob_end_flush();
?>
