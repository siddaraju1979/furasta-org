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

/**
 * Group
 *
 * Used for group management, similar to the
 * Users class.
 *
 * @package user_management
 * @author Conor Mac Aoidh <conormacaoidh@gmail.com> 
 * @license http://furasta.org/licence.txt The BSD License
 */
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
	 * createInstances
	 *
	 * Used to create multiple instances of groups
	 * at once. Accepts an array of group ids and
	 * creates instances of them, they can then be
	 * reterived using the getInstance method.
	 * This method uses one database query as opposed
	 * to many.
	 *
	 * @param array $group_ids
	 * @access public
	 * @static
	 * @return void
	 */
	public static function createInstances( $group_ids ){

		/**
		 * check instance isn't already loaded
		 */
		$ids = array( );
		foreach( $group_ids as $key => $id ){
			if( !isset( self::$instances{ $id } ) )
				array_push( $ids, $id );
		}
		
		if( empty( $ids[ 0 ] ) )
			return;

		/**
		 * get groups info from db
		 */
		$query = 'select * from ' . DB_GROUPS . ' where id=';
		$query .= implode( ' or id=', $ids );
		$groups = rows( $query );

		/**
		 * create instances
		 */
		foreach( $groups as $group )
			self::$instances{ $group[ 'id' ] } = new Group( $group );

	}

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
	 * @param int|array $group
	 * @access private
	 * @return void
	 */
	private function __construct( $group ){

		if( !is_array( $group ) )
			$group = row( 'select * from ' . DB_GROUPS . ' where id=' . $id );

		$this->id = $id;
		$this->name = $group[ 'name' ];
		$this->perm = explode( ',', $group[ 'perm' ] );	
		$this->file_perm = split_perm( $group[ 'file_perm' ] );

	}

	/**
	 * hasPerm
	 *
	 * used to check if group has permission
	 * to access a certain area of the cms
	 * 
	 * @param string $perm 
	 * @access public
	 * @return bool
	 */
	public function hasPerm( $perm ){

		/**
		 * searches the perm array to check
		 * if user has perm and returns bool
		 * value
		 */
		if( in_array( $perm, $this->perm ) )
			return true;

		return false;
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
			USERS_FILES . 'files/groups/' . $id,
			USERS_FILES . 'files/groups/' . $id . '/public',
			USERS_FILES . 'files/groups/' . $id . '/private'
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
		$dir = USERS_FILES . 'files/groups/' . $id;
		$rmdir = true;
		if( is_dir( $dir ) )
			remove_dir( $dir ) || $rmdir = false;
		return $rmdir;
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

}

?>
