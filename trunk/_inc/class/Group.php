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

interface Grouperface{

	public function getInstance( $new );
	public function __construct( $id );

	public function perm( ); // returns group perms
	public function name( ); // returns group name

	public function createGroupDirs( $id ); // use filemanager to add dirs
	public function removeGroupDirs( $id ); // use filemanager
}

class Group{

	/**
	 * id
	 *
	 * @var int
	 * @access private
	 */
	private $id;

	/**
	 * name
	 *
	 * @var string
	 * @access private
	 */
	private $name;

	/**
	 * perm
	 *
	 * @var array
	 * @access private
	 */
	private $perm;

	/**
	 * file_perm
	 *
	 * @var array
	 * @access private
	 */
	private $file_perm;

	/**
	 * instance
	 *
	 * @var object
	 * @static
	 * @access private
	 */
	private static $instances = array( );

	/**
	 * getInstance
	 *
	 * gets an instance by id
	 *
	 * @param int $id
	 * @access public
	 * @static
	 * @return instance
	 */
	public function getInstance( $id ){

		// if there isn't an instance, create one
		if( !isset( self::$instances{ $id } ) )		
			self::$instances{ $id } = new Group( $id );

		return self::$instances{ $id };

	}

	/**
	 * __construct
	 *
	 * constrcutor for the group class
	 *
	 * @param int $id
	 * @access private
	 * @return void
	 */
	private function __construct( $id ){

		// get a few properties from database
		$group = row( 'select * from ' . GROUPS . ' where id=' . $id );
		$this->id = $id;
		$this->name = $group[ 'name' ];
		$this->perm = User::permToArray( $group[ 'perm' ] );

	}

	/**
	 * id
	 *
	 * accessor method for the group id
	 *
	 * @access public
	 * @return int
	 */
	public function id( ){
		return $this->id;
	}

	/**
	 * name
	 *
	 * accessor method for the group
	 * name
	 *
	 * @access public
	 * @return string
	 */
	public function name( ){
		return $this->name;
	}

	/**
	 * perm
	 *
	 * accessor method for the group
	 * perm, note: returns perm in array
	 * form
	 *
	 * @access public
	 * @return array
	 */
	public function perm( ){
		return $this->perm;
	}

	/**
	 * filePerm
	 *
	 * accessor method for the file_perm
	 * variable
	 *
	 * @access public
	 * @return array
	 */
	public function filePerm( ){
		return $this->file_perm;
	}

	/**
	 * hasFilePerm
	 *
	 * checks if the group has permission to access a
	 * file in the user files directory
	 *
	 * @param string $path
	 * @access public
	 * @return bool
	 */
	public function hasFilePerm( $path ){


	}

	}

	/**
	 * groupsHaveFilePerm
	 *
	 * checks if an array of groups have file permissions
	 * to access a file in the user files directory
	 *
	 * @param array $ids
	 * @param string $path
	 * @access public
	 * @static
	 * @return bool
	 */
	public static function groupsHaveFilePerm( $ids, $path ){

		foreach( $ids as $id ){

			$group = Group:getInstance( $id );

			if( !$group->hasFilePerm( $path ) )
				return false;

		}

		return true;

	}

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
	public static function createGroupDirs( $id ){
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
