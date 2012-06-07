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
 * ==== ERRORS ====
 *
 * The following map numbers to errors:
 * 
 * +--------+--------------------------------+
 * | Number | Error Description              |
 * +--------+--------------------------------+
 * |  0     | unknown error                  |
 * |  1     | read permission failure        |
 * |  2     | write permission failure       |
 * |  3     | view permission failure        |
 * |  4     | invalid path                   |
 * |  5     | system permission failure      |
 * |  6     | file/dir already exists        |
 * +--------+--------------------------------+
 *
 * The error function can also be used to get further information on the
 * last error to occur.
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
	 * error
	 *
	 * holds information on the last error. ie
   * error number and description
   *
	 * @access private
	 * @var array
	 */
	private $error = array( );

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
			return $this->setError( 4, $path ); 

		return true;

	}

	/**
	 * updateInfo
	 *
	 * updates info on a file keeping the database
	 * and file manager in sync
	 *
	 * $data	-	the only required memeber of this array
	 *			is "path"
	 *
	 * @param array $data
	 * @param bool $new
	 * @access private
	 * @return void
	 */
	private function updateInfo( $data, $new = false ){

		if( $new ){ // insert db and files array
			$query = 'insert into ' . FILES . ' values ( '
				. '"",'
				. '"' . $data[ 'name' ] . '",'
				. '"' . $data[ 'path' ] . '",'
				. $data[ 'owner' ] . ','
				. '"' . @$data[ 'perm' ] . '",'
				. '"' . $data[ 'type' ] . '",'
				. '"' . $data[ 'public' ] . '",'
				. '"' . $data[ 'hash' ] . '"'
			. ')';
			
			$success = query( $query );

			if( !$success )
				return $this->setError( 7, $query );

			$data[ 'id' ] = $id;
			$this->files{ $data[ 'path' ] } = $data;
		}
		else{ // update db and files array
			$query = 'update ' . FILES . ' set ';
			foreach( $data as $name => $value ){
				$query .= $name . '="' . $value . '",';
				$this->files{ $data[ 'path' ] }{ $name } = $value;
			}
			$query = substr( $query, 0, 1 ) . ' where path="' . $data[ 'path' ] . '"';

			$success = query( $query );

			if( !$success )
				return $this->setError( 7, $query );
		}

	} 

	/**
	 * deletInfo
	 *
	 * removes a file by the given path from the
	 * database and the files array
	 *
	 * @param string $path
	 * @access private
	 * @return void
	 */
	private function deleteInfo( $path ){

		/**
		 * delete from db
		 */		
		query( 'delete from ' . FILES . ' where path=' . addslashes( $path ) );

		/**
		 * remove from files array
		 */
		if( isset( $this->files{ $path } ) )
			unset( $this->files{ $path } );

	}

	/**
	 * getFile
	 *
	 * returns a file from the files array
	 * if the file is not present it is fetched
	 * from the db and added to the array
	 *
	 * @param string $path
	 * @access private
	 * @return array $file
	 */
	private function getFile( $path ){

		if( !isset( $this->files{ $path } ) ){
			$file = row( 'select * from ' . FILES . ' where path="' . addslashes( $path ) . '"' );
			if( !$file ) // file is not in db
				return false;
			$this->files{ $path } = $file;
		}
		
		return $this->files{ $path };

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
	 * @access public
	 * @return bool
	 */
	public function hasPerm( $path, $rw = 'r', $id = false ){
		
		/**
		 * get file details
		 */

		if( !self::checkPath( $path ) )
			return false;

		/**
		 * get user instance
		 */
		$User = User::getInstance( $id );

		/**
		 * if super user, bypass all permissions
		 */
		if( $User && $User->isSuperUser( ) )
			return true;

		$file = $this->getFile( $path );

		/**
	 	 * if user/group administers files for this user, then
		 * don't bother checking file permissions just return true
		 */
		if( $User && $User->hasFilePerm( $file[ 'owner' ] ) )
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
					 * must be logged in
					 */
					if( !$User )
						return $this->setError( 9 );

					/**
					 * if user does not have permission to manage
					 * files, return false
					 */
					if( !$User->hasPerm( 'f' ) )
						return $this->setError( 1, $path );

					// if not in users array, check groups array
					if( !in_array( $User->id( ), $read[ 'users' ] ) ){
						foreach( $User->groups( ) as $Group ){
							if( in_array( $Group->id( ), $read[ 'groups' ] ) ){
								$match = true;
								break;
							}
						}
						if( !$match ) // user does not have permission to view file
							return $this->setError( 1, $path );
					}
					
				break;
				case 'w': // check if is writable by user

					/**
					 * must be logged in
					 */
					if( !$User )
						return $this->setError( 9 );

					/**
					 * if is system file/folder (ie. owner=0) then
					 * return false
					 */
					if( $file[ 'owner' ] == 0 )
						return $this->setError( 8, $path );

					/**
					 * if user does not have permission to manage
					 * files, return false
					 */
					if( !$User->hasPerm( 'f' ) )
						return $this->setError( 2, $path );

					// if not in users array, check groups array
					if( !in_array( $User->id( ), $write[ 'users' ] ) ){
						foreach( $User->groups( ) as $Group ){
							if( in_array( $Group->id( ), $write[ 'groups' ] ) ){
								$match = true;
								break;
							}
						}
						if( !$match ) // user does not have permission to view file
							return $this->setError( 2, $path );
					}
				break;
			}

		}

		return true;

	}

	/**
	* setError
	*
	* logs an error by id. The error description is 
	* calculated and logged
	*
	* $params  -   array of arguments to the error
	*              description, will replace %1 - %n
	*              in the form array( %1, %2 .. %n )
	*
	* @param int $id optional
	* @param array $params optional
	* @access private
	* @return false
	*/
	private function setError( $id = 0, $params = false ){

		// do not rely on this, a temporary array to
		// hold error descriptions pending language
		// support additions
		$errors = array(
			0 => 'An unknown error occured',
			1 => 'You have insufficient privilege to read %1',
			2 => 'You have insufficient privilege to write %1',
			3 => 'You have insufficient privilege to view %1',
			4 => 'The path %1 is invalid',
			5 => 'You have insufficient system permissions to perform that action. Please grant write access to the ' . USER_FILES . 'files directory',
			6 => 'The %1 at %2 cannot be created as it already exists',
			7 => 'There was a problem querying the database. The following query caused the error: %1',
			8 => 'The path %1 contains a Furasta.Org system file which cannot be written',
			9 => 'User must be logged in to perform this action',
		);

		$this->error{ 'id' } = $id;

		/**
		* if params var is present replace occurances
		* of %i with var. can take an array of vars 
		*/
		if( $params != false ){

			if( is_array( $params ) ){
				/**
				* reindex array to start with 1 instead of 0
				*/
				$params = array_combine( range( 1, count( $params ) ), array_values( $params ) );

				for( $i = 1; $i <= count( $params ); $i++ )
				$this->error{ 'desc' } = str_replace( '%' . $i, $params[ $i ], $error );

			}
			else
				$this->error{ 'desc' } = str_replace( '%1', $params, $error );

		}

		return false;

	}

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
		if( file_exists( USER_FILES . 'files/' . $path ) )
			return $this->setError( 6, array( 'dir', $path ) );

		/**
		 * permission failure
		 */
		if( !$this->hasPerm( $path, 'w' ) )
			return $this->setError( 2, $path );

		/**
		 * build data
		 */
		$User = User::getInstance( );
		$data = array(
			'name' => end( explode( '/', $path ) ),
			'path' => $path,
			'owner' => $User->id( ),
			'type' => 'dir',
			'perm' => ( empty( $perm ) ) ? '' : implode( ',', $perm ),
			'public' => $public ? 1 : 0,
			'hash' => md5( mt_rand( ) )
		);

		/**
		 * create directory
		 */
		$success = mkdir( $path ); 

		if( !$success )
			return $this->setError( 5, $path );


		/**
		 * add to database
		 */
		return $this->updateInfo( $data, true );
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
			return $this->setError( 2, $path );

		/**
		* loop through elements in directory
		*/
		$query = 'select * from ' . FILES . ' where';
		$it = new RecursiveDirectoryIterator( $path );
		$file_ids = array( );
		$count = 0;

		foreach( new RecursiveIteratorIterator( $it ) as $file ){

			if( $file->isDot( ) )
				continue;

			$path = $file->getSubPathname( );
			die( $path );

		}
	}

	/**
	 * readDir
	 *
	 * reads a directory and returns it's contents. if $all
	 * is true then all directory and sub directory contents
	 * are returned. if strict is true then will return false
	 * if permission is denied to read any file
	 *
	 * $level	-	can be an integer of how many levels
	 *			of directories to read or true to read
	 *			all levels. reads one level by default
	 * $strict	-	if strict is true and any files fail to
	 *			be read the method will generate an error
	 *			and return false, otherwise those files
	 *			will just be excluded from the list
	 *
	 * @param string $path
	 * @param int/bool $level
	 * @param bool $strict
	 * @access public
	 * @return array
	 */
	public function readDir( $path, $level = 1, $strict = false ){

		/**
		 * permission failure
		 */
		if( !$this->hasPerm( $path, 'r' ) )
			return $this->setError( 1, $path );

		// @todo implement this

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
			$this->setError( 1, $path );
                
		/**
		 * build data
		 */
		$User = User::getInstance( );
		$data = array(
			'name' => end( explode( '/', $path ) ),
			'path' => $path,
			'owner' => $User->id( ),
			'type' => 'file',
			'perm' => ( empty( $perm ) ) ? '' : implode( ',', $perm ),
			'public' => $public ? 1 : 0,
			'hash' => md5( mt_rand( ) )
		);

		/**
		 * add file
		 */
		$success = file_put_contents( $link, $contents );

		if( !$success ) 
			return $this->setError( 5, $path );

		/**
		 * add file to database
		 */
		return $this->updateInfo( 0, $data );

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
		 * check permissions
		 */
		if( !$this->hasPerm( $file, 'w' ) )
			return $this->setError( 2, $file );
 
		if( !$this->hasPerm( $path, 'w' ) )
			return $this->setError( 2, $path );

		/**
		 * move file
		 */
		$success = rename( $file, $path );

		if( !$success ) 
			return $this->setError( 5, $path );

		/**
		 * update database
		 */
		return $this->updateInfo( array( 'path', $path ) );

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
		 * note: permissions checks are done by the
		 * readFile and addFile methods
	 	 */

		/**
		 * get files db details
		 */
		$file = $this->getFile( $file );

		/**
		 * read file to be copied
		 */	
		$contents = $this->readFile( $file );

		if( !$contents )
			return false;

		/**
		 * add new file with same contents
		 */
		return $this->addFile( $path, $contents, $file[ 'perm' ], $file[ 'public' ] );
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
			return $this->setError( 2, $path );

		$contents = file_get_contents( $file );

		if( !$contents )
			return $this->setError( 5, $path );

		return $contents;

	}			

	public function moveDir( $path, $dest ){
		// @todo
	}

	public function moveUploadedFile( $file, $path ){
		// @todo
	}

	// path is directory or array of filenames
	public function zip( $zip, $path ){
		// @todo
	}

	public function unzip( $zip, $path ){
		// @todo
	}

	/**
 	 * error
  	 *
	 * returns information on the last recorded error.
	 * for the form of the array, see the private error
	 * variable
	 *
	 * @access public
	 * @return array
	 */
	public function error( ){
		return $this->error( );
	}

	/**
	 * cleanUp
	 *
	 * scans the files directory for files which are not in
	 * the database and removes them. it is rare for any files
	 * to be in this directory without being in the database,
	 * with the exception of when a database query fails.
	 *
	 * @access public
	 * @return bool
	 */
	public function cleanUp( ){
		// @todo
	}

}

?>
