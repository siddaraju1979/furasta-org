<?php

/**
 * Upgrade File, Furasta.Org
 *
 * This file runs upgrades for svn revisions of the
 * CMS. For major updates see _inc/update.php
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

if( !defined( 'SET_REVISION' ) )
  define( 'SET_REVISION', 100 );

switch( SET_REVISION ){
	case 100:
		// update admin area users & groups
		// create users_groups database
		// add group entries to users_groups
		// delete groups column from users

}

?>
