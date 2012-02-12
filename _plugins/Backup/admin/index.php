<?php

/**
 * Backup Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */


$Template = Template::getInstance( );

if( isset( $_POST[ 'create' ] ) ){ // create backup
	$type = $_POST[ 'backup_type' ];
	$dir = ( $type == 'all' ) ? HOME : USER_FILES . 'files/';

	// increase memory limit
	ini_set( 'memory_limit', '32M' );

	$backup_dir = USER_FILES . 'backup/';
	if( !is_dir( $backup_dir ) )
		mkdir( $backup_dir );

	// json encode settings
	global $DB, $SETTINGS, $PLUGINS;	
	$settings = array( 'SETTINGS' => $SETTINGS );
	if( $type == 'all' )
		$settings[ 'PLUGINS' ] = $PLUGINS;
	$settings = json_encode( $settings );

	// backup database
	$dump = new MYSQL_DUMP( $DB[ 'host' ], $DB[ 'user' ], $DB[ 'pass' ] );
	$sql = $dump->dumpDB( $DB[ 'name' ] );

	// backup files
	$zip=new zipfile();
	$files=scandir($dir);

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
	if( $type == 'all' ){
		zip_files( $files, USER_FILES, '_user/files', $zip ); 
	}
	$zip->addFile( $settings, '.settings.php' );
	$zip->addFile( $sql, 'database-backup.sql' );
	$zip->output($backup_dir.'file-backup.zip');

	require HOME . '_inc/function/files.php';
	download_file( $backup_dir . 'file-backup.zip', $SETTINGS[ 'site_title' ] . '-' . date( 'd-m-y-H:i' ) . '.zip' );
}

if( isset( $_FILES[ 'backup_file' ] ) && $_FILES[ 'backup_file' ][ 'error' ] == 0 ){ // restore from backup

	// move file to home
	$zip_file = HOME  . $_FILES[ 'backup_file' ][ 'name' ];
	move_uploaded_file( $_FILES[ 'backup_file' ][ 'tmp_name' ], $zip_file ) 
		|| error( 'please grant write permission to the / dir to perform a restore' );	

	// unzip file in home
	$unzip = new dUnzip2( $zip_file );
	$unzip->unzipAll( HOME );

	// restore settings file
	global $SETTINGS, $DB, $PLUGINS;
	$settings = json_decode( file_get_contents( HOME . 'settings.php' ), true );
	foreach( $settings as $name => $value ){
		switch( $setting ){
			case 'SETTINGS': // extract settings
				foreach( $value as $setting => $v )
					$SETTINGS[ $setting ] = $v;
			break;
			case 'PLUGINS': // extract plugins
				$PLUGINS = $value;
			break;
		}
	}
	$constants = array( 'USER_FILES', HOME . '_user' ); 
	settings_rewrite( $SETTINGS, $DB, $PLUGINS, $constants ); 

	// reinstall database from dump
	$templine = '';
	$lines = file( HOME . 'database-backup.sql' );

	foreach( $lines as $line ){ // loop through lines
		if( substr( $line, 0, 2 ) == '--' || $line == '' ) // skip comments
			continue;

		$templine .= $line;

		if( substr( trim( $line ), -1, 1 ) == ';' ){ // its a query
			// run query
			mysql_query( $templine );
			$templine = '';
		}
	}

	// delete database-backup.sql and zip file
	unlink( HOME . 'database-backup.sql' );
	unlink( $zip_file );

	$Template->runtimeError( 'Backup Restored' ); 
}

$Template->add( 'title', 'Backup' );
$Template->loadJavascript( '_plugins/Backup/admin/backup.js' );

$content = '
<h1>Backup</h1>
<form id="backup-form" method="post" enctype="multipart/form-data">
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
