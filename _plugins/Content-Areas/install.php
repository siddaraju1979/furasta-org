<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 */

/**
 * @todo add new permissions so that all users are able to use this plugin
 */

/**
 * add content areas table to database
 */
query( 'create table ' . PREFIX . 'content_areas ( id int auto_increment primary key, name text, content text, data text )' );

?>
