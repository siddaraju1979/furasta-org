<?php

/**
 * Backup Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */


$Template = Template::getInstance( );

if( isset( $_POST[ 'create' ] ) ){
	$type = $_POST[ 'backup_type' ];
	$dir = ( $type == 'all' ) ? HOME : USER_FILES . 'files/';

	// increase memory limit
	ini_set( 'memory_limit', '32M' );

	$backup_dir = USER_FILES . 'backup/';
	if( !is_dir( $backup_dir ) )
		mkdir( $backup_dir );

	// backup database
	global $DB;	
	$dump = new MYSQL_DUMP( $DB[ 'host' ], $DB[ 'user' ], $DB[ 'pass' ] );
	$sql = $dump->dumpDB( $DB[ 'name' ] );

	// backup files
	$zip=new zipfile();
	$files=scandir(USER_FILES . 'files/');

	function zip_files($files,$dir='',$zdir='',$zip){
		foreach($files as $file){
			if($file=='.'||$file=='..'||$file=='backup')
				continue;
			if(is_dir($dir.$file)){
				$zip->addDir($zdir.$file);
				zip_files(scandir($dir.$file),$dir.$file.'/',$zdir.$file.'/',$zip);
			}
			else
				$zip->addFile(file_get_contents($dir.$file),$zdir.$file);
		}
	}

	$zdir = ( $type == 'all' ) ? '' : '_user/files/';
	zip_files($files,$dir,$zdir,$zip);
	$zip->addFile( $sql, 'database-backup.sql' );
	$zip->output($backup_dir.'file-backup.zip');

	require HOME . '_inc/function/files.php';
	download_file( $backup_dir . 'file-backup.zip' );

}

if( isset( $_POST[ 'restore' ] ) ){
	// move _user/files to USER_FILES/files
	// delete _user
	// reinstall database from dump
	// delete database-backup.sql
}

$Template->add( 'title', 'Backup' );
$Template->loadJavascript( '_plugins/Backup/admin/backup.js' );

$content = '
<h1>Backup</h1>
<form id="backup-form" method="post">
<div id="tabs">
	<ul>
		<li><a href="#tab-1">Create</a></li>
		<li><a href="#tab-2">Restore</a></li>
	</ul>
	<div id="tab-1">
		<p><i>What type of backup?</i></p>
		<table>
			<tr>
				<td><b>All User Files and Database:</b></td>
				<td><input type="radio" name="backup_type" value="partial" checked="checked"/></td>
				<td><i>This includes everything except the actual system files<i></td>
			</tr>
			<tr>
				<td><b>Everything:</b></td>
				<td><input type="radio" name="backup_type" value="all"/></td>
				<td><i>This option may not work for large systems</i></td>
			</tr>
		</table>
		<input type="submit" name="create" value="Create Backup" style="width:15%;margin:10px"/>
	</div>
	<div id="tab-2">
		<table>
			<tr>
				<td>Backup File:</td>
				<td><input type="file" name="backup_file"/></td>
				<td>This should be a zip that you have created from <b>the backup plugin</b></td>
			</tr>
		</table>
		<input type="submit" name="restore" value="Restore From Backup" style="width:15%;margin:10px"/>
	</div>
</div>
</form>
';
$Template->add( 'content', $content );

?>
