<?php

/**
 * File Manager Class, Furasta.Org
 *
 * This file contains a file manager class which should
 * be used for reading/writing to the user files
 * directory.
 * 
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    file_management
 * @todo	make sure paths are used correctly everywhere - USERS_DIR . 'files/' . $path
 */

/**
 * FileManager
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
 * ==== DB_FILES DATABSE ====
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
 * @package	file_management
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com> 
 * @license	http://furasta.org/licence.txt The BSD License
 */
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
         * _rexec_fn
         *
         * default _rexecDir function - ls
         *
         * @param string &$path
         * @param string &$file
         * @param array $return
         * @access private
         * @static
         */
        private static function _rexec_fn( &$path, &$file, $return ){

                $key = str_replace( USERS_DIR . '/', $path );
                return $return[ $key ] = $file;

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
                 * path is not in USERS_DIR or
		 * path contains "..", therefore it's invalid
                 */
		if( strpos( '..', $path ) !== false )
			return $this->setError( 4, $path ); 


		return true;

	}

	/**
	 * checkFileName
	 *
	 * checks if a filename is valid. files are only allowed
	 * one extension - no extensions or multiple extensions
	 * cause the method to return false. accepts a $path
	 *
	 * @param string $path
	 * @static
	 * @access private
	 * @return bool
	 */
	private static function checkFileName( $path ){

		$file = basename( $file );

		/**
		 * if path contains more or less than one ".",
		 * it's invalid
		 */
		if( strpos( '.', $path ) != 1 )
			return $this->setError( 10, $file ); 

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
			$query = 'insert into ' . DB_FILES . ' values ( '
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
			$query = 'update ' . DB_FILES . ' set ';
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
		query( 'delete from ' . DB_FILES . ' where path=' . addslashes( $path ) );

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
	 * NOTE: this does not return the file contents,
	 * it returns the associated file data
	 *
	 * @param string $path
	 * @access public
	 * @return array|bool $file
	 */
	public function getFile( $path ){

		if( !isset( $this->files{ $path } ) ){
			$file = row( 'select * from ' . DB_FILES . ' where path="' . addslashes( $path ) . '"' );
			if( !$file ) // file is not in db
				return $this->setError( 11, $path );
			$this->files{ $path } = $file;
		}
		
		return $this->files{ $path };

	}

        /**
         * getFiles
         *
         * accepts an array of files in the format returned
         * by readDir. fetches all the database info on the
         * files at once
         *
         * @param array $files
         * @access public
         * @return bool
         */
        public function getFiles( $files ){

                $names = $this->getFileNames( $files );

                /**
                 * build query
                 */
                $query = 'select * from ' . DB_FILES . ' where';
                $c = 0;         // count hits, make sure query is nessecary
                foreach( $names as $name ){
                        if( isset( $this->files{ $name } ) )
                                continue;
                        $c++;
                        $query .= ' path="' . addslashes( $name ) . '" or';
                }

                if( $c == 0 ) // query not required
                        return true;
                
                $query = trim( $query, ' or' );
                
                /**
                 * perform query
                 */
                $files = rows( $query );

                if( !$files )
                        return $this->setError( 11, $names[ 0 ] );
        
                for( $i = 0; $i < count( $files ); ++$i ){
                        $this->files{ $files[ $i ][ 'path' ] } = $files[ $i ];
                }

                return true;

        }

        /**
         * getFileNames
         *
         * reformats the array returned by readDir to a 1D array
         * of files and directory names as they are in the database
         *
         * @param array $files
         * @access public
         * @return array
         */
        public function getFileNames( $files ){
                
                $return = array( );
                
                foreach( $files as $file => $sub ){
                
                        if( is_dir( $file ) && is_array( $sub ) )
                                $return += $this->getFileNames( $sub );
                
                        /**
                         * extract name from path
                         */
                        $name = str_replace( USERS_DIR . 'files', '', $file );
                        array_push( $return, $name );
                }
                
                return $return;

        }

	/**
	 * hasPerm
	 *
	 * checks if a user has permission to read/write
	 * a file in the user files directory.
	 *
	 * $rw defines what permissions are being checked
	 * and must be one of the following:
	 *
	 * r - read
	 * w - write
	 * v - view (checks if the file is public/private)
         *
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
	public function hasPerm( $file, $rw = 'r', $id = false ){
		
		/**
		 * make sure path is valid
		 */
		if( !self::checkPath( $file ) )
			return false;
                
		/**
		 * get user and file info
		 */
		$file = $this->getFile( $file );
		$User = User::getInstance( $id );

		/**
		 * file not found
		 */
		if( !$file )
			return false;

		/**
		 * split file permissions string from db into
		 * different arrays for easy processing
		 */
		list( $read, $write ) = split_perm( $file[ 'perm' ] );

		/**
		 * switch between different permissions checks
		 */
		switch( $rw ){
			case 'v': // check if is public
				/**
				 * if super user, return true
				 */
				if( $User && $User->isSuperUser( ) )
					return true;

				/**
				 * if file is public, return true
				 */
				if( $file[ 'public' ] == 1 )
					return true;

				return $this->setError( 3, $file[ 'name' ] );
			break;
			case 'r': // check if is readable by user

				/**
				 * must be logged in
				 */
				if( !$User )
					return $this->setError( 9 );

				/**
				 * if super user, return true
				 */
				if( $User->isSuperUser( ) )
					return true;

				/**
				 * if user does not have permission to manage
				 * files, return false
				 */
				if( !$User->hasPerm( 'f' ) )
					return $this->setError( 12 );

				/**
				 * if user is accessing their own file, return true
				 */
				if( $file[ 'owner' ] == $User->id( ) )
					return true;

				/**
				 * if user is in array of users allowed to access
				 * file, return true
				 */
				if( in_array( $User->id( ), $read[ 'users' ] ) )
					return true;

				/**
				 * if one of the user's groups is allowed to access
				 * the file, return true
				 */
				foreach( $User->groups( ) as $id ){
					if( in_array( $id, $read[ 'groups' ] ) )
						return true;
				}

				/**
				 * if user has admin permissions over the owner
				 * of the file, return true
				 */
				list( $u_read, $u_write ) = $User->filePerm( );
				if( in_array( $file[ 'owner' ], $u_read[ 'users' ] ) )
					return true;

				/**
				 * if user has admin permissions over a group of which
				 * the owner of the group is a member, return true
				 */
				$Owner = User::getInstance( $file[ 'owner' ] );
				foreach( $Owner->groups( ) as $id ){
					if( in_array( $id, $u_read[ 'groups' ] ) )
						return true;
				}

				/**
				 * permission failure
				 */
				return $this->setError( 1, $file[ 'name' ] );
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
					return $this->setError( 8, $file );

				/**
				 * if user does not have permission to manage
				 * files, return false
				 */
				if( !$User->hasPerm( 'f' ) )
					return $this->setError( 12 );

				/**
				 * if user is accessing their own file, return true
				 */
				if( $file[ 'owner' ] == $User->id( ) )
					return true;

				/**
				 * if user is in array of users allowed to access
				 * file, return true
				 */
				if( in_array( $User->id( ), $write[ 'users' ] ) )
					return true;

				/**
				 * if one of the user's groups is allowed to access
				 * the file, return true
				 */
				foreach( $User->groups( ) as $id ){
					if( in_array( $id, $write[ 'groups' ] ) )
						return true;
				}

				/**
				 * if user has admin permissions over the owner
				 * of the file, return true
				 */
				list( $u_read, $u_write ) = $User->filePerm( );
				if( in_array( $file[ 'owner' ], $u_write[ 'users' ] ) )
					return true;

				/**
				 * if user has admin permissions over a group of which
				 * the owner of the group is a member, return true
				 */
				$Owner = User::getInstance( $file[ 'owner' ] );
				foreach( $Owner->groups( ) as $id ){
					if( in_array( $id, $u_write[ 'groups' ] ) )
						return true;
				}

				/**
				 * permission check failed
				 */
				return $this->setError( 2, $file[ 'name' ] );
			break;
		}

	}

        /**
         * filesHavePerm
         *
         * parses an array of files and removes the
         * files which the user does not have permission
         * to view, returns resulting array
         *
         * @param array $files
         * @param string $rw
         * @param int $id
         * @access private
         * @return array
         */
        private function filesHavePerm( $files, $rw = 'w', $id = false ){

                $return = array( );
                
                foreach( $files as $file => $sub ){
                        if( is_array( $file ) && $this->hasPerm( $file, $rw, $id ) ){
                                $return[ $file ] = $this->filesHavePerm( $sub, $rw, $id );
                        }
                        else{
                                if( $this->hasPerm( $file, $rw, $id ) )
                                        $return[ $file ] = $sub;
                        }
                }
                
                return $return;
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

		/**
		 * a temporary array to hold error descriptions
		 * pending language support additions
		 * @todo enable language support
		 */
		$errors = array(
			0	=>	'An unknown error occured',
			1	=>	'You have insufficient privilege to read "%1"',
			2	=>	'You have insufficient privilege to write "%1"',
			3	=>	'You have insufficient privilege to view "%1"',
			4	=>	'The path "%1" is invalid',
			5	=>	'You have insufficient system permissions to perform that action. Please grant write access to the "' . USERS_DIR . '/files" directory',
			6	=>	'The "%1" at "%2" cannot be created as it already exists',
			7	=>	'There was a problem querying the database. The following query caused the error: %1',
			8	=>	'The path "%1" contains a Furasta.Org system file which cannot be written',
			9	=>	'User must be logged in to perform this action',
			10	=>	'The file name "%1" is invalid. Files must have one extension, file names with more that one extension or no extension are invalid,',
			11	=>	'The file "%1" could not be found in the database.',
			12	=>	'You do not have permission to manage files. Please contact the system administrator',
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
				        $this->error{ 'desc' } = str_replace( '%' . $i, $params[ $i ], $errors[ $id ] );

			}
			else
				$this->error{ 'desc' } = str_replace( '%1', $params, $errors[ $id ] );

		}

                //die( print_r( $this->error{ 'desc' } ) );

		return false;

	}

	/**
	 * _rreadDir
	 *
	 * private recursive function which reads all files
	 * and folders in a directory up to $level levels
	 * deep. If $level is true all sub directories
	 * are read
	 *
	 * $current tells the function which level it is
	 * currently processing
	 *
	 * @param string $path
	 * @param int $level
	 * @param int $current
	 * @return array
	 */
	private static function _rreadDir( $path, $level, $current = 0 ){

                $path = trim( $path, '/' );
		$files = array( );

		/**
		 * make sure maximum level isn't exceeded
		 */
		if( is_int( $level ) && $level == $current )
			return 0;

		/**
		 * scan directory
		 */
		$dir = scandir( $path );
		foreach( $dir as $file ){

			// exclude current dir and previous dir
			if( $file == '.' || $file == '..' )
				continue;

                        $rel = str_replace( USERS_DIR . 'files', '', $path );
			if( is_dir( $path . '/' . $file ) )
                                $files[ $rel . '/' . $file ] = self::_rreadDir( $path . '/' . $file, $level, $current + 1 );

                        else
				$files[ $rel . '/' . $file ] = 0;
			
		}

		return $files;

	}

	/**
	 * _rexecDir
	 *
	 * private recursive function which executes a
	 * function $fn on all files in a directory.
	 * this uses anonymous functions
	 *
	 * @param string $path
	 * @param function $fn
         * @param array $return
	 * @access private
	 * @return bool
	 */
	private function _rexecDir( $path, $fn = false, $return = array( ) ){

		/**
		 * scan directory
		 */
		$dir = scandir( $path );
		foreach( $dir as $file ){

			// exclude current dir and previous dir
			if( $file == '.' || $file == '..' )
				continue;

			if( is_dir( $file ) )
				$this->_rreadDir( $file, $fn, $return );

			/**
			 * call $fn on the file/directory, check
			 * for errors
			 */	
                        if( !$fn )
                                $return = self::_rexec_fn( $path, $file, $return );
                        else
                                $return = $fn( $path, $file, $return );
			if( !$return )
                                return $this->setError( 5 );

		}

		return $return;

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
         * __construct
         *
         * sets the users files directory
         *
         * @access public
         * @return void
         */
        public function __construct( ){
                $this->users_files = USERS_DIR . 'files';
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
		if( file_exists( USERS_DIR . 'files/' . $path ) )
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
	 * reeoveDir
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

		// @todo update this, not working

		/**
		 * get all files and check permissions
		 */
		if( !$this->readDir( $path, 'w', true, true ) )
			return false;

		/**
		 * create anonymous function to remove
		 * directory
		 */
//		$fn = function( $file, $dest ){
//			return rename( $file, USERS_DIR . '/' . $dest );
//		}
		
		/**
		 * call 
		 */
		$success = $this->_rexecDir( $path );

		// 	
	}

	/**
	 * readDir
	 *
	 * reads a directory and returns all files and directories
	 * if $all is true then all directory and sub directory contents
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
	 * $rw		- 	specifies what permissions to check for,
	 * 			see the hasPerm method. this way, readDir
	 * 			can be used to recursively check permissions
         *
         * example return for readDir( '/' ) :
         *      
         * array(
         *      '/' => array(
         *              '/file1' => 0,
         *              '/file2' => 0,
         *              '/users' => array(
         *                      '/users/1' => array(
         *                              '/users/1/file3' => 0,
         *                              '/users/1/file4' => 0
         *                      ),
         *                      ...
         *              }, 
         * );
         *
	 * @param string $path
	 * @param string $rw
	 * @param int/bool $level
	 * @param bool $strict
	 * @access public
	 * @return array
	 */
	public function readDir( $path = '/', $rw = 'r', $level = 1, $strict = false ){

		/**
		 * make sure path is valid
		 */
		if( !self::checkPath( $path ) )
			return false;

		// read files and dirs
		$files = self::_rreadDir( $this->users_files . $path, $level );

                /*
		 * get file database data
		 */
		$this->getFiles( $files );
                
		/**
                 * exclude items with insufficient
                 * privellages
		 */
                $files = $this->filesHavePerm( $files, 'r' );

                return $files;
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

	/**
	 * moveDir
	 *
	 * reccursively moves a directory assuming
	 * you have write permissions for all files
	 * in $path and $dest.
	 *
	 * @param string $path
	 * @param string $dest
	 * @access public
	 * @return bool
	 */
	public function moveDir( $path, $dest, $strict = false ){

		/**
		 * check permission for destination
		 */
		if( !$this->hasPerm( $dest ) )
			return false;
	
		/**
		 * get all files and check permissions
		 */
		if( !$this->readDir( $path, 'w', true, true ) )
			return false;

		/**
		 * create anonymous function to remove
		 * directory
		 */
//		$fn = function( $file, $dest ){
//			return rename( $file, USERS_DIR . '/' . $dest );
//		}
		
		/**
		 * rename all files, error check
		 */
		$success = $this->_rexecDir( $path, $fn, $dest );
		if( !$success )
			return false;

		// @todo update database
	}

	public function moveUploadedFile( $file, $path ){
		// @todo
	}


	/**
	 * zip
	 *
	 * zips the path $path which can be a
	 * directory or array of files in the archive
	 * $zip
	 *
	 * $zip		-	name of archive to be created
	 *
	 * $path	-	can be a directory or an array
	 * 			of file paths to be zipped
	 *
	 * $strict	-	if path is a directory and strict
	 * 			is true, the method will return
	 * 			false if an item cannot be read
	 *
	 * @param string $zip
	 * @param string $path
	 * @param bool $strict
	 * @access public
	 * @return bool
	 */
	public function zip( $zip, $path, $strict = false ){

		/**
		 * check permission on location of zip file
		 */
		if( !$this->hasPerm( $zip, 'w' ) )
			return false;

		/**
		 * check permission to read all files
		 * to be zipped
		 */
		$files = $this->readDir( $path, 'r', true, $strict );

		if( !$files ) // readDir failed
			return false;



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
