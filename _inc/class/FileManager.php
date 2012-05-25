<?php

/**
 * File Manager Class, Furasta.Org
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */

/**
 * FileManager
 *
 * this class will be used for manipulation of files
 * in the user files directory
 */
interface FileManagerFace{

	public function getInstance( $new );
	public function __construct( ); // load database of protected files

	private function checkPath( $path ); // checks if path is in user files

	public function addFile( $path, $contents );
	public function moveFile( $file, $path );
	public function copyFile( $file, $path );
	public function removeFile( $path );
	public function filePerm( $path );
//	public function hasFilePerm( $file ); --> put this in users class
	public function addDir( $path );
	public function moveDir( $dir, $path ); // reccursive
	public function copyDir( $dir, $path );
	public function removeDir( $path ); // recurrsive remove function
	
}

class FileManager{

	/**
	 * instance
	 *
	 * @static
	 * @var object
	 * @access private
	 */
	private static $instance;

	/**
	 * protected_files
	 *
	 * @access private
	 * @var array
	 */
	private $protected_files = array( );
	
	/**
	 * getInstance
	 *
	 * if an instance exists, it returns it,
	 * if not, it creates a new one and returns
	 * it
	 *
	 * @access public
	 * @static
	 * @return instance
	 */
	public static function getInstance( ){

		if( !self::$instance )
			self::$instance = new FileManager( );

		return self::$instance; 

	}

	private static function checkPath( ){

		$User = User::getInstance( );

//		if( !$this->hasFilePerm( $path ) )
//			return false;

		if( !$User->hasFilePerm( $path ) )
			return false;

		return true;

	}

	public function addFile( $path, $contents ){

		if( !self::checkPath( ) )
			return false;

		file_put_contents( $path, $contents );

	}

}

?>
