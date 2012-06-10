<?php

/**
 * Permissions, Furasta.Org
 *
 * defines default permissions
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

/**
 * normal permissions, default
 * then filter by plugins
 */
$perms = array(
		'a' => 'Login To Admin Area',
                'e' => 'Edit Pages',
                'c' => 'Create Pages',
                'd' => 'Delete Pages',
                't' => 'Manage Trash',
		'f' => 'Manage Files',
                's' => 'Edit Settings',
                'u' => 'Edit Users'
);

if( !isset( $Plugins ) )
	$Plugins = Plugins::getInstance( );

$perms = $Plugins->filter( 'admin', 'filter_group_permissions', $perms );
