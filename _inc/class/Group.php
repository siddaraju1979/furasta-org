<?php

/**
 * Group Class, Furasta.Org
 *
 * This file contains a class which enables easy access
 * to info on groups.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @todo a lot of development to be done here. The end goal is for
 * this class to manage everything to do with the group, taking
 * some of the responsibilites from the user class but the group
 * class will be initialised by the user class
 */

class Group{

	/**
	 * createGroupDirs
	 *
	 * create the group directory structure for a 
	 * given group
	 *
	 * @params int $id
	 * @return bool
	 * @access public
	 */
	public static createGroupDirs( $id ){
		$dirs = array(
			USER_FILES . 'files/groups/' . $id,
			USER_FILES . 'files/groups/' . $id . '/public',
			USER_FILES . 'files/groups/' . $id . '/private'
		);

		$mkdir = true;
		foreach( $dirs as $dir ){
			if( !is_dir( $dir ) )
				mkdir( $dir ) || $mkdir == false;
		}

		return $mkdir;
	}

	/**
	 * removeGroupDirs
	 * 
	 * removes the directory structure for a given group
	 *
	 * @params int $id
	 * @return bool
	 * @access public
	 */
	public static function removeGroupDirs( $id ){
		$dir = USER_FILES . 'files/groups/' . $id;
		$rmdir = true;
		if( is_dir( $dir ) )
			remove_dir( $dir ) || $rmdir = false;
		return $rmdir;
	}

}

?>
