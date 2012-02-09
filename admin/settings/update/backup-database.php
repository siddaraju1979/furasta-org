<?php

/**
 * Database Backup, Furasta.Org
 *
 * Dumps the content of the current database to /backup/db-backup.sql
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_settings
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        exit;

if(!is_dir(USER_FILES.'backup'))
	mkdir(USER_FILES.'backup');

$dump=new MYSQL_DUMP($DB['host'],$DB['user'],$DB['pass']);

$sql=$dump->dumpDB($DB['name']);

$dump->save_sql($sql,USER_FILES.'backup/db-backup.sql');

die('ok');
?>
