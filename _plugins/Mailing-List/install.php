<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 */

switch( $version ){
	case 1:
		// add mailing list table to database
		query( 'create table if not exists ' . DB_PREFIX . 'mailing_list ('
			. 'id int auto_increment primary key,'
			. ' name text,'
			. ' email text'
		. ')' );

		// add default options to database
		query( 'insert into ' . DB_OPTIONS . ' values '
			. '( "use_names", "Yes", "mailing_list_options" ),'
			. '( "from", "", "mailing_list_options" ),'
			. '( "reply_to", "", "mailing_list_options" ),'
			. '( "template", "", "mailing_list_options" )'
		);
	break;
}

?>
