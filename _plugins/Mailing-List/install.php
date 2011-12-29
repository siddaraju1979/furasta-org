<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 */

/**
 * add mailing list table to database
 */
query( 'create table if not exists ' . PREFIX . 'mailing_list ( id int auto_increment primary key, name text, email text )' );

/**
 * add default options to database
 */
query( 'insert into ' . OPTIONS . ' values '
	. '( "use_names", "Yes", "mailing_list_options" )'
);

?>
