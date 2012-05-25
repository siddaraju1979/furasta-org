<?php

/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

switch( $version ){
	case 0:
		query( 'create table ' . PREFIX . 'stats ('
			. 'id int auto_increment primary key,'
			. 'ip text,'
			. 'page text,'
			. 'referrer text,'
			. 'os text,'
			. 'browser text,'
			. 'lang text,'
			. 'country text,'
			. 'viewed date'
		. ')' );

		/**
		 * mkdir for stats files
		 */
		$sdir = USER_FILES . 'stats';
		if( !is_dir( $sdir ) )
			mkdir( $sdir );
	break;
}

?>
