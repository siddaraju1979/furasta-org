<?php



require '../../../_inc/define.php';

$file=addslashes(@$_GET['filename']);
if($file=='')
	exit;

$file_loc=USER_FILES.'files/downloads/'.$file.'.zip';
if(!file_exists($file_loc))
	exit;

$file_info=row('select name,count from '.PREFIX.'downloadManager where filename="'.$file.'"',true);
query('update '.PREFIX.'downloadManager set count='.($file_info['count']+1).' where filename="'.$file.'"',true);

header("Content-type: application/force-download");
header("Content-Transfer-Encoding: Binary");
header("Content-length: ".filesize($file_loc));
header("Content-disposition: attachment; filename=\"".$file_info['name'].".zip\"");
readfile($file_loc);

?>
