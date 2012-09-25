<?php

/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

// weekly job to update geo ip data, should be daily but thats not really nessecary
// info should only be kept in db for a month, run job daily to delete data
// summarys should be held for previous months in db for the last year, total views is count of this table

$plugin = array(

	'name' => 'Stats',
	'description' => 'Gathers statistical information about the website\'s visitors.',
	'version' => 1,

	'admin' => array(
		'filter_menu' => 'stats_admin_filter_menu',
		'filter_group_permissions' => 'stats_filter_group_permissions',
		'page' => 'stats_admin_page',
		'on_load' => 'stats_admin_on_load',
	),
	'frontend' => array(
		'filter_metadata' => 'stats_frontend_filter_metadata',
	),
	'general' => array(
		'on_login' => 'stats_on_login',
		'on_logout' => 'stats_on_logout',
		'jobs' => array(
//			array( '01', 'stats_delete_data' ), // executes the first day of every month
//			array( '+1 month', 'stats_update_geoip' )
		)
	)
);

function stats_filter_group_permissions( $perms ){

	$perms[ 'st' ] = 'Website Stats';

	return $perms;

}

/**
 * stats_admin_filter_menu
 *
 * adds the stats menu item
 *
 * @param array $menu_items
 * @param string $url
 * @return array
 */
function stats_admin_filter_menu( $menu_items, $url ){

	$User = User::getInstance( );

	if( $User->hasPerm( 'st' ) )
		$menu_items[ 'stats_plugin' ] = array( 'name' => 'Stats', 'url' => $url );

	return $menu_items;
}

/**
 * stats_frontend_filter_metadata
 *
 * adds a bit of javascript that send a request to the process.php file
 *
 * @param string $metadata
 * @return string
 */
function stats_frontend_filter_metadata( $metadata ){
	// add javascript to frontend which sends a request to the
	// stats program
	$metadata .= '<script type="text/javascript">$(function(){$.ajax({type:"post",url:window.furasta.site.url+"ajax.php?file=_plugins/Stats/frontend/process.php",data:{page:"' . $_SERVER[ 'REQUEST_URI' ] . '",referrer:"' . $_SERVER[ 'HTTP_REFERER' ] . '"}});});</script>';

	return $metadata;
}

/**
 * stats_admin_page
 * 
 * displays the admin stats page
 *
 * @return void
 */
function stats_admin_page( ){
	require HOME . '_plugins/Stats/admin/index.php';
}

/**
 * stats_update_geoip
 * 
 * this function executes every month in order to update
 * the geo ip database. it executes in parts as to not
 * exceed the PHP memory limit - the db is very big,
 * around 8MB
 *
 * @return bool
 */
function stats_update_geoip( ){
	
	/**
	 * part 1, download zip
	 */
	$zip = USERS_DIR . 'stats/IpToCountry.zip';
	if( !file_exists( $zip ) ){
		$contents = file_get_contents( 'http://software77.net/geo-ip/?DL=2' );
		file_put_contents( $zip, $contents );
		return false;
	}

	/**
	 * part 2, unzip
	 */
	$file = USERS_DIR . 'stats/IpToCountry.csv';
	if( !file_exists( $file ) ){
		$unzip = new dUnzip2( $zip );
		$unzip->unzip( 'IpToCountry.csv', $file );
		return false;
	}

	/**
	 * part3, parse file and put output in temp file
	 */
	$newfile = USERS_DIR . 'stats/IpToCountry.tmp';
	if( !file_exists( $newfile ) ){
		$handle = @fopen( $file, 'r' );
		$new = @fopen( $newfile, 'w' );
		if( !$handle || !$new )
			return false;

		while( ( $buffer = fgets( $handle, 4096 ) ) !== false ){
			if( $buffer[ 0 ] == '#' )
				continue;

			$arr = explode( ',', $buffer );
			$arr = implode( "\t", array(
				trim( $arr[ 0 ], "\"" ),
				trim( $arr[ 1 ], "\"" ),
				trim( $arr[ 4 ], "\"" ),
				str_replace( '"', '', $arr[ 6 ] )
			));
			fputs( $new, $arr );
		}

		fclose( $handle );
		return false;
	}

	/**
	 * part4, split temp file into 9 different files
	 * with each file containing ips starting with
	 * the appropriate number
	 */
	$dir = USERS_DIR . 'stats/';

	/**
	 * files to be created
	 */
	$files = array(
		'0' => array( '00', '01', '02', '03', '04', '05', '06', '07', '08', '09' ),
		'1' => array( '10', '11', '12', '13', '14', '15', '16', '17', '18', '19' ),
		'2' => array( '20', '21', '22', '23', '24', '25', '26', '27', '28', '29' ),
		'3' => array( '30', '31', '32', '33', '34', '35', '36', '37', '38', '39' ),
		'4' => array( '40', '41', '42', '43', '44', '45', '46', '47', '48', '49' ),
		'5' => array( '50', '51', '52', '53', '54', '55', '56', '57', '58', '59' ),
		'6' => array( '60', '61', '62', '63', '64', '65', '66', '67', '68', '69' ),
		'7' => array( '70', '71', '72', '73', '74', '75', '76', '77', '78', '79' ),
		'8' => array( '80', '81', '82', '83', '84', '85', '86', '87', '88', '89' ),
		'9' => array( '90', '91', '92', '93', '94', '95', '96', '97', '98', '99' ),
	);

	error_log( 'test1' );
	// open temp file for processing
	$new = @fopen( $newfile, 'r' );
	if( !$new )
		return false;
	error_log( 'test2' );
	while( ( $buffer = fgets( $new, 4096 ) ) !== false ){
		$line = explode( "\t", $buffer );

		$lines = stats_split_ips( $line, 0 );
		file_put_contents( HOME . 'testtest', print_r( $lines, true ) );
		return false;
		
	}

	fclose( $new );

	// close fps
	foreach( $fp as $p )
		fclose( $p );

	return false;

	/**
	 * part4, split second quarter of file
	 */

	/**
	 * part5, split third quarter of file
	 */

	/**
	 * part6, split fourth quarter of file
	 */

	// @todo cleanup!

	/**
	 * finished! return true to mark as done
	 */
	return true;
}

function stats_write_geoip_to_file( $fp, $line ){
	error_log( 'writing to file..' );
	fputs( $fp, implode( "\t", $line ) );	
}

function stats_split_ips( $line, $layer ){

	$ip1 = substr( $line[ 0 ], 0, 1 );
	$ip2 = substr( $line[ 1 ], 0, 1 );

	error_log( 'from ' . $line[ 0 ] . ' to ' . $line[ 1 ] );

	$newlines = array( );

	/**
	 * if the first digits of each number are not e
	 */
	if( $ip1 != $ip2 ){
		$digits = strlen( $line[ 1 ] );
		$newip = $ip1;
		for( $i = 1; $i < $digits; $newip .= '9', $i++ );

		// new line1
		array_push( $newlines, array(
			$line[ 0 ],
			$newip,
			$line[ 2 ],
			$line[ 3 ]
		) );

		$start = 0;
		$fin = substr( $newip, 1 );
		for( $i = 9; $i > 0; --$i ){
			$start = 0;
			$fin = 0;
			array_push( $newlines, array(
				$start,
				$fin,
				$line[ 2 ],
				$line[ 3 ]
			) );
		}

		return $newlines;

		$newip = $ip2;
		for( $i = 1; $i < $digits; $newip .= '0', $i++ );

		array_push( $newlines, array(
			$newip,
			$line[ 1 ],
			$line[ 2 ],
			$line[ 3 ]
		) );
	}

	return $newlines;
}

?>
