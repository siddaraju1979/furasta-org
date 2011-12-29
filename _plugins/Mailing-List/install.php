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
query( 'create table ' . PREFIX . 'mailing_list ( id int auto_increment primary key, name text, email text )' );

?>
