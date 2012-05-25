<?php

/**
 * File Manager Class, Furasta.Org
 *
 * This is the Furasta.Org File Manager Class. It is intended to be used
 * to manage all of the writing to / reading from the user files
 * directory. The advantage of using this class over standard PHP functions
 * is that it accounts for all kinds of extra things like security, file
 * permissions (for furasta.org users, not system permissions) etc.
 *
 * ==== FILE PERMISSIONS ====
 *
 * Furasta.Org file permissions work using two methods of approach:
 *
 *	1. All users/groups have a file perm field in the database. This works
 * 	   along the same lines as the perm field in pages. This field
 * 	   is intended to overwrite the other file permissions and
 *	   consists of a list of users and groups who's files the user/group
 *	   can view/edit.
 *	2. Each file has its own field in the database which states the name
 * 	   of the files, the type (dir or file), the permissions string for
 *	   who can view/edit it, the public status (whether its a publicly
 *	   viewable or private file) and the hash which is a unique md5 string
 * 	   which if the file is viewed in the browser by anyone, even if private,
 * 	   and this string is correctly added to the URL the file will be displayed.
 *
 *
 * ==== FILES DATABSE ====
 *
 * The file manager class keeps a record of all files in the database.
 * The files are then named according to their unique identifier in the db,
 * that's how the class keeps track of the files database info. When getting
 * a directories contents an array of information on each file is returned
 * which contains the file name, id, permissions and location among other things.
 * 
 *
 * 
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 * @todo return errors in number format instead of boolean for better de-bugging and for language support
 *  also add lastError function or something like that to see errors
 */

/**
 * FileManager
 *
 * this class will be used for manipulation of files
 * in the user files directory
 *
interface FileManagerFace{

	public function getInstance( $new );
	public function __construct( ); // load database of protected files

	private function checkPath( $path ); // checks if path is in user files

	public function addFile( $path, $contents );
	public function moveFile( $file, $path );
	public function copyFile( $file, $path );
	public function getFile( $path );
	public function removeFile( $path );
	public function filePerm( $path );
	public function hasPerm( $file, $perm );
	public function addDir( $path );
	public function moveDir( $dir, $path ); // reccursive
	public function copyDir( $dir, $path ); // reccursive
	public function readDir( $path ); // filter for permissions
	public function removeDir( $path, $recursive ); // recurrsive remove function

	public function updateInfo( $file );// updates the database info and the files array on a file
	// accepts a string of database info
	public function search( $string, $location ); // searches for a file by name or string in contents, optional in a certain location
	// keep symbolic links for groups/files
	private function addLink( $target, $link );
	private function readLink( $path );
	private function removeLink( $path );
}*/

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
	 * files
	 *
	 * @access private
	 * @var array
	 */
	private $files = array( );

	/**
	 * fp
	 *
	 * @access private
	 * @var string
	 */
	private $fp;

	/**
	 * db
	 *
	 * @access private
	 * @var string
	 */
	private $db;

	/**
	 * errors
	 *
	 * holds all errors executed in
	 * this session
	 *
	 * @todo
	 * @access private
	 * @var array
	 */
	private $errors = array( );

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

	/**
	 * construct
	 */
	public function __construct( ){
		$this->fp = USER_FILES . 'files/';
		$this->db = USER_FILES . 'db/';
	}

	/**
	 * checkPath
	 *
	 * checks if a path is valid/secure. does not check file
	 * permissions, for that see the hasFilePerm method
	 *
	 * @param string $path
	 * @static
	 * @access private
	 * @return bool
	 */
	private static function checkPath( $path ){

		/**
		 * path contains "..", therefore it's invalid
		 */
		if( strpos( '..', $path ) !== false )
			return false;

		return true;

	}

	/**
	 * addLink
	 *
	 * adds a symlink
	 *
	 * @param string $target
	 * @param string $link
	 * @access private
	 * @return bool
	 */
	private function addLink( $target, $link ){
		// delete file if exists
		if( file_exists( $link ) )
			unlink( $link );

		return symlink( $this->db . $link, $this->fp . $target );
	}

	/**
	 * readLink
	 *
	 * returns the location the symlink points
	 * to
	 * 
	 * @param string $path
	 * @access private
	 * @return string
	 */
	private function readLink( $path ){

		/**
		 * if is dir, find the link for that dir
		 * and return it. Note: directory links are
		 * stored as .dirname.lnk
		 */
		if( is_dir( $this->fp . $path ) ){
			$path = array_filter( explode( '/', $path ) );
			$end = array_pop( $path );
			$path = implode( '/', $path ) . '/';
			error_log( $this->fp . $path . '.' . $end . '.lnk   ' . readlink( $this->fp . $path . '.' . $end . '.lnk' ));
			return readlink( $this->fp . $path . '.' . $end . '.lnk' );
		}

		return readlink( $this->fp . $path );
	}

	/**
	 * updateInfo
	 *
	 * updates info on a file keeping the database
	 * and file manager in sync
	 *
	 * @param int $id
	 * @param array $data
	 * @access private
	 * @return int/bool
	 */
	private function updateInfo( $id, $data ){

		if( $id == 0 ){ // insert db and files array
			query( 'insert into ' . FILES . ' values ( '
				. '"",'
				. '"' . $data[ 'name' ] . '",'
				. $data[ 'owner' ] . ','
				. '"' . @$data[ 'perm' ] . '",'
				. '"' . $data[ 'type' ] . '",'
				. '"' . $data[ 'public' ] . '",'
				. '"' . $data[ 'hash' ] . '"'
			. ')' );
			$id = mysql_insert_id( );
			$data[ 'id' ] = $id;
			$this->files{ $id } = $data;

			return $id;
		}
		else{ // update db and files array
			$query = 'update ' . FILES . ' set ';
			foreach( $data as $name => $value ){
				$query .= $name . '="' . $value . '",';
				$this->files{ $id }{ $name } = $value;
			}
			$query = substr( $query, 0, 1 ) . ' where id=' . $id;
			query( $query );

			return true;
		}

	} 

	/**
	 * deletInfo
	 *
	 * removes a file by the given id from the
	 * database and the files array
	 *
	 * @param int $id
	 * @access private
	 * @return void
	 */
	private function deleteInfo( $id ){

		/**
		 * delete from db
		 */		
		query( 'delete from ' . FILES . ' where id=' . addslashes( $id ) );

		/**
		 * remove from files array
		 */
		if( isset( $this->files{ $id } ) )
			unset( $this->files{ $id } );

	}

	/**
	 * getFile
	 *
	 * returns a file from the files array
	 * if the file is not present it is fetched
	 * from the db and added to the array
	 *
	 * @param int $id
	 * @access private
	 * @return array $file
	 */
	private function getFile( $id ){

		if( !isset( $this->{ $id } ) ){
			$file = row( 'select * from ' . FILES . ' where id="' . $id );
			if( !$file ) // file is not in db
				return false;
			$this->{ $id } = $file;
		}
		
		return $this->{ $id };

	}

	/**
	 * hasPerm
	 *
	 * checks if a user has permission to read/write
	 * a file in the user files directory.
	 *
	 * $rw defines what permissions are being checked
	 * and must be one of or a combination of (such as
	 * rw or wv) the following:
	 * r - read
	 * w - write
	 * v - view (checks if the file is public/private)
	 *
	 * checks can be performed on a specific user using
	 * $id, defaults to current user
	 *
	 * @param string/int $file
	 * @param string $rw
	 * @param int $id
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function hasPerm( $path, $rw = 'r', $id = false ){
		
		/**
		 * get user instance and real link to file
		 */
		$User = User::getInstance( $id );

		/**
		 * get file details
		 */
		if( is_int( $path ) ) // id given
			$id = $path;
		else{ // path given, check path, find id

			if( !self::checkPath( $path ) )
				return false;

			$link = $this->readLink( $path );
			$id = end( array_filter( explode( '/', $link ) ) );
		}
		$file = $this->getFile( $id );

		/**
		 * file is not in db
		 */
		if( !$file )
			return false;

		/**
		 * if is system file/folder (ie. owner=0) then
		 * return false
		 */
		if( $file[ 'owner' ] == 0 )
			return false;

		/**
	 	 * if user/group administers files for this user, then
		 * don't bother checking file permissions just return true
		 */
		if( $User->hasFilePerm( $file[ 'owner' ] ) )
			return true;

		/**
		 * split file permissions string from fb into
		 * different arrays for easy processing
		 */
		list( $read, $write ) = split_perms( $perm );

		/**
		 * switch between different permissions checks
		 */
		$rw = ( strlen( $rw ) > 1 ) ? explode( '', $rw ) : array( $rw );
		foreach( $rw as $check ){

			switch( $check ){
				case 'v': // check if is public
					if( $file[ 'public' ] == 0 )
						return false;
				break;
				case 'r': // check if is readable by user

					/**
					 * if user does not have permission to manage
					 * files, return false
					 */
					if( !$User->hasPerm( 'f' ) )
						return false;

					// if not in users array, check groups array
					if( !in_array( $User->id( ), $read[ 'users' ] ) ){
						foreach( $User->groups( ) as $Group ){
							if( in_array( $Group->id( ), $read[ 'groups' ] ) ){
								$match = true;
								break;
							}
						}
						if( !$match ) // user does not have permission to view file
							return false;
					}
					
				break;
				case 'w': // check if is writable by user

					/**
					 * if user does not have permission to manage
					 * files, return false
					 */
					if( !$User->hasPerm( 'f' ) )
						return false;

					// if not in users array, check groups array
					if( !in_array( $User->id( ), $write[ 'users' ] ) ){
						foreach( $User->groups( ) as $Group ){
							if( in_array( $Group->id( ), $write[ 'groups' ] ) ){
								$match = true;
								break;
							}
						}
						if( !$match ) // user does not have permission to view file
							return false;
					}
				break;
			}

		}

		return true;

	}

	/**
	 * addDir
	 *
	 * adds a directory to the file manager
	 *
	 * @param string $path
	 * @param array $perm
	 * @param bool $public
	 * @access public
	 * @return bool
	 */
	public function addDir( $path, $perm = array( ), $public = false ){

		// file exists, return false
		if( file_exists( $this->fp . $path ) )
			return false;

		/**
		 * check if user has permission
		 * to create the directory
		 */
		if( !$this->hasPerm( $path, 'w' ) )
			return false;

		/**
		 * build data
		 */
		$User = User::getInstance( );
		$data = array(
			'name' => end( explode( '/', $path ) ),
			'owner' => $User->id( ),
			'type' => 'dir',
			'perm' => ( empty( $perm ) ) ? '' : implode( ',', $perm ),
			'public' => $public ? 1 : 0,
			'hash' => md5( mt_rand( ) )
		);

		/**
		 * add to database
		 */
		$id = $this->updateInfo( 0, $data );

		/**
		 * add symlink
		 */
		$link = explode( '/', $path );
		array_pop( $link );
		$link = $this->readLink( implode( '/', $link ) ) . $id;
		$this->addLink( $path, $link );
	
		/**
		 * create directory
		 */
		mkdir( $link );

		return true;
	}

	/**
	 * removeDir
	 *
	 * removes a directory, and all of it's contents if
	 * non-empty. must have permission to remove all files in
	 * directory
	 *
	 * @params string $path
	 * @access public
	 * @return void
	 */
	public function removeDir( $path ){	

		/**
		 * permission failure
		 */
		if( !$this->hasPerm( $path, 'w' ) )
			return false;

		$link = $this->readLink( $path );
		$id = end( array_filter( explode( '/', $path ) ) ); // get item id
		
		/**
		 * attempt to recursively read all files in
		 * the directory. if count is 0 or all files
		 * can be deleted (permissions check), deletion
		 * will be attempted
		 */
		$files = $this->readDir( $link );

		if( count( $files ) == 0 ){ // remove dir

		}
		else{
			foreach( $files as $file => $data ){
				if( !$this->checkFilePerm( $file ) )
					return false;
			}
		}

	}

	/**
	 * readDir
	 *
	 * reads a directory and returns it's contents. if $all
	 * is true then all directory and sub directory contents
	 * are returned
	 *
	 * @param string $path
	 * @param bool $all
	 * @access public
	 * @return array
	 */
	public function readDir( $path, $all = false ){

		/**
		 * permission failure
		 */
		if( !$this->hasPerm( $path, 'r' ) )
			return false;

		

	}

	/**
	 * addFile
	 *
	 * creates a file at the given location. returns
	 * false if permission failure
	 * 
	 * @param string $path
	 * @param string $contents
	 * @access public
	 * @return bool
	 */
	public function addFile( $path, $contents, $perm = array( ), $public = false ){

		/**
		 * permission failure
		 */
		if( !$this->hasPerm( $path, 'w' ) )
			return false;

		// @todo check if file exists and if user has
		// permission to replace it

		/**
		 * build data
		 */
		$User = User::getInstance( );
		$data = array(
			'name' => end( explode( '/', $path ) ),
			'owner' => $User->id( ),
			'type' => 'file',
			'perm' => ( empty( $perm ) ) ? '' : implode( ',', $perm ),
			'public' => $public ? 1 : 0,
			'hash' => md5( mt_rand( ) )
		);

		/**
		 * add file to database
		 */
		$id = $this->updateInfo( 0, $data );

		/**
		 * add symlink
		 */
		$link = explode( '/', $path );
		array_pop( $link );
		die( $this->readLink( implode( '/', $link ) ) . $id );
		$this->addLink( $this->fp . $path, $link );
	
		/**
		 * add file
		 */
		file_put_contents( $link, $contents );

		return true;

	}

	/**
	 * moveFile
	 * 
	 * moves a file from one location to another
	 * while keeping the database in sync
	 *
	 * @param string $file
	 * @param string $path
	 * @access public
	 * @return bool
	 */
	public function moveFile( $file, $path ){

		/**
		 * one of the locations is invalid
		 */
		if( !$this->hasPerm( $file, 'w' ) || !$this->hasPerm( $path, 'w' ) )
			return false;

		// @todo update database, move file

		return true;
	}

	/**
	 * copyFile
	 *
	 * copys a file from one location to another. returns
	 * an array of new database info on the file
	 *
	 * @param string $file
	 * @param string $path
	 * @access public
	 * @return bool
	 */
	public function copyFile( $file, $path ){

		/**
		 * one of the locations is invalid
		 */
		if( !$this->hasPerm( $file, 'r' ) || !$this->hasPerm( $path, 'w' ) )
			return false;

		// @todo copy file, update db

		return true;
	}

	/**
	 * readFile
	 *
	 * returns the contents of a file
	 *
	 * @param string $path
	 * @access public
	 * @return string/bool
	 */
	public function readFile( $path ){

		if( !$this->hasPerm( $path, 'r' ) )
			return false;

		// @todo	

	}

}

?>
