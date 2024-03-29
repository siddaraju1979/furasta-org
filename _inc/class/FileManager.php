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
 */

/**
 * File Type Constants
 *
 * a list of constants used by the file manager class when working with
 * mime types
 * 
 * @package	file_management
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com> 
 * @license	http://furasta.org/licence.txt The BSD License
 * @todo        build a more comprehensive list of types
 */
define( 'UNKNOWN',      'mg_unknown' );
define( 'BITMAP',       'mg_bitmap' );
define( 'IMAGE',        'mg_image' );
define( 'DRAWING',      'mg_drawing' );
define( 'AUDIO',        'mg_audio' );
define( 'VIDEO',        'mg_video' );
define( 'MULTIMEDIA',   'mg_multimedia' );
define( 'OFFICE',       'mg_office' );
define( 'TEXT',         'mg_text' );
define( 'EXECUTABLE',   'mg_executable' );
define( 'ARCHIVE',      'mg_archive' );

/*
 * new spec

 addInfo( $path/$files )
 updateInfo( $path/$files )
 deleteInfo( $path/$files )
 getInfo( $path/$files )
 
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
                        return false;

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
	 * @return bool
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

			$data[ 'id' ] = mysql_insert_id( );

		}
		else{ // update db and files array
                        $query = 'update ' . DB_FILES . ' set '
                                . 'name="' . $data[ 'name' ] . '",'
                                . 'path="' . $data[ 'path' ] . '",'
                                . 'owner=' . $data[ 'owner' ] . ','
                                . 'perm="' . $data[ 'perm' ] . '",'
                                . 'type="' . $data[ 'type' ] . '",'
                                . 'public="' . $data[ 'public' ] . '",'
                                . 'hash="' . $data[ 'hash' ] . '"'
			        . 'where path="' . $data[ 'path' ] . '"';

			$success = query( $query );

			if( !$success )
				return $this->setError( 7, $query );
                }

                $this->files{ $data[ 'path' ] } = $this->filesExtraParams( $data );

                return true;

	} 

	/**
	 * deleteInfo
	 *
	 * removes a file by the given path from the
	 * database and the files array
         *
         * $path        -       can be a string - 1 path or
         *                      and array of files
         *
	 * @param string/array $path
	 * @access private
	 * @return void
	 */
	private function deleteInfo( $path ){

                

		/**
		 * delete from db
		 */		
		query( 'delete from ' . DB_FILES . ' where path="' . addslashes( $path ) . '"' );

		/**
		 * remove from files array
		 */
		if( isset( $this->files{ $path } ) )
			unset( $this->files{ $path } );

	}

        /**
         * filesExtraParams
         *
         * adds additional parameters to the file data
         * which is calculated at runtime rather than
         * stored in the db
         *
         * @param array $fdata
         * @access private
         * @return void
         */
        private function filesExtraParams( $fdata ){

                $fdata[ 'mimegroup' ] = self::getType( $fdata[ 'type' ] );

                return $fdata;

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
			$this->files{ $path } = $this->filesExtraParams( $file );
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
                        $this->files{ $files[ $i ][ 'path' ] } = $this->filesExtraParams( $files[ $i ] );
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
         * getType
         *
         * categorizes the file based on its mime
         * type and returns a constant to identify
         * that category. see the top of this file
         * for list of constants
         *
         * @param string $type
         * @static
         * @return string
         */
        public static function getType( $type ){

                /**
                 * switch by filetype and return
                 * constants to identify type group
                 */
                switch( $type ){
                        // text
                        case 'text/plain':
                        case 'text/html':
                        case 'text/css':
                        case 'text-xphp':
                        case 'application/javascript':
                                return TEXT;
                        break;
                        // office
                        case 'application/msword':
                        case 'application/pdf':
                        case 'application/rtx':
                        case 'application/xls':
                        case 'application/vnd.ms-powerpoint':
                                return OFFICE;
                        break;
                        // audio
                        case 'audio/mp3':
                        case 'audio/mpeg3':
                        case 'audio/x-aiff':
                        case 'audio/x-wav':
                                return AUDIO;
                        break;
                        // video
                        case 'video/mpeg':
                        case 'video/quicktime':
                        case 'video/x-msvideo':
                                return VIDEO;
                        break;
                        // flash
                        case 'application/shockwave-flash':
                                return FLASH;
                        break;
                        // image
                        case 'image/bmp':
                        case 'image/png':
                        case 'image/jpeg':
                        case 'image/gif':
                        case 'image/jpg':
                                return IMAGE;
                        break;
                        // archive
                        case 'application/zip':
                        case 'application/x-tar':
                        case 'application/x-compressed':
                        case 'application/x-gzip':
                                return ARCHIVE;
                        break;
                        // executable
                        case 'application/octet-stream':
                        case 'application/x-sh':
                                return EXECUTABLE;
                        break;        
                        // unknown
                        default:
                                return UNKNOWN;

                }

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
	 * @param string/int $path
	 * @param string $rw
	 * @param int $id
	 * @access public
	 * @return bool
	 */
	public function hasPerm( $path, $rw = 'r', $id = false ){
		
		/**
		 * make sure path is valid
		 */
		if( !self::checkPath( $path ) )
			return $this->setError( 4, $path ); 
                
		/**
		 * get user and file info
                 */
		$file = $this->getFile( $path );
		$User = User::getInstance( $id );

		/**
                 * file not in db, therefore is a check
                 * to create file
                 */
                $new = false;
                if( !$file ){
                        $new = true;
                        $file = $this->getFile( dirname( $path ) );
                        if( !$file )
                                return $this->setError( 4, $path );
                }

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
                                if( !$new && $file[ 'owner' ] == 0 )
					return $this->setError( 8, $file );

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
                        if( $this->hasPerm( $file, $rw, $id ) ){
                                $data = $this->getFile( $file );
                                if( is_array( $file ) )
                                        $data[ 'files' ] = $this->filesHavePerm( $sub, $rw, $id );
                                array_push( $return, $data );
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
                        13      =>      'The path "%1" is a directory.',
                        14      =>      'There was an error uploading the file "%1". Please try again.',
                        15      =>      'The image at "%1" cannot be displayed as it is in an unsupported format.',
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

                //die( print_r( debug_backtrace( ) ) );

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
			'name' => basename( $path ),
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
		$success = mkdir( $this->users_files . $path ); 

                if( !$success )
			return $this->setError( 5, $path );

      		/**
		 * add to database
		 */
		return $this->updateInfo( $data, true );

        }

        /**
         * removeFile
         *
         * removes a file from the file manager. checks
         * write permission before deletion
         *
         * @param string $path
         * @access public
         * @return void
         */
        public function removeFile( $path ){

                /**
                 * check permission
                 */
                if( !$this->hasPerm( $path, 'w' ) )
                        return false;

                /**
                 * delete from files array, db
                 */
                $this->deleteInfo( $path );

                /**
                 * delete from filesystem
                 */
                $success = unlink( $this->users_files . $path );
                if( !$success )
                        return $this->setError( 5, $path );

                return false;

        }

	/**
	 * removeDir
	 *
	 * removes a directory, and all of it's contents if
	 * non-empty. must have permission to remove all files in
	 * directory
	 *
	 * @param string $path
	 * @access public
	 * @return void
	 */
	public function removeDir( $path ){	

		// @todo update this, not working

		/**
		 * get all files and check permissions
                 */
                $files = $this->readDir( $path, 'w', true, true );
		if( !$files )
			return false;

		/**
		 * recursive remove dir 
                 */
                self::_rremoveDir( $this->users_files . $path );

                /**
                 * delete info for all files
                 */
                return $this->deleteInfo( $files );
	
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
         * // return array in following format:
         * array(
         *      '/' => array(
         *              'type'  =>      'mimetype',
         *              'mimegroup' =>  'mg_unknown',   // mimegroup, see constants at top of file
         *              'files' =>      array( ... ),   // if is dir and $level is greater than 1 then file list is included
         *              ..
         *              ..                              // other database properties
         *              ..
         *      )
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

                /**
                 * if no files, check directory permissions and
                 * return empty array
                 */
                if( empty( $files ) ){
                        if( !$this->hasPerm( $path, $rw ) )
                                return false;
                        return array( );
                }

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
		$success = file_put_contents( $this->users_files . $path, $contents );

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

                /**
                 * make sure its a file
                 */
                if( is_dir( $path ) )
                        return $this->setError( 13, $path );

                /**
                 * check permissions
                 */
                if( !$this->hasPerm( $path, 'r' ) )
			return $this->setError( 2, $path );

                /**
                 * get file contents, else file system permission
                 * error
                 */

		if( is_readable( $this->users_files . $path ) )
                        $contents = file_get_contents( $this->users_files . $path );
                else
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

        /**
         * uploadFile
         *
         * adds a file to the file manager which has
         * just been uploaded and resides in the temp
         * folder
         *
         * $file        -       a member of the php $_FILES array
         * $path        -       the directory the file should be
         *                      uploaded to
         *
         *
         * @param array $file
         * @param string $path
         * @param array $perm optional
         * @param bool $public optional
         * @return bool
         */
	public function uploadFile( $file, $path, $perm = array( ), $public = false ){

		/**
		 * permission failure
                 */
                $fullpath = $path . $file[ 'name' ];
		if( !$this->hasPerm( $fullpath, 'w' ) )
			return $this->setError( 1, $path );

                /**
                 * throw error if upload error occured
                 */
                if( $file[ 'error' ] != UPLOAD_ERR_OK )
                        return $this->error( 14, $file[ 'name' ] );

                /**
                 * move uploaded file
                 */
                $success = move_uploaded_file( $file[ 'tmp_name' ], $this->users_files . $fullpath );

		if( !$success ) 
			return $this->setError( 5, $path );

		/**
		 * build data
		 */
                $User = User::getInstance( );
		$data = array(
			'name' => $file[ 'name' ],
			'path' => $fullpath,
			'owner' => $User->id( ),
			'type' => $file[ 'type' ],
			'perm' => ( empty( $perm ) ) ? '' : implode( ',', $perm ),
			'public' => $public ? 1 : 0,
			'hash' => md5( mt_rand( ) )
                );

		/**
		 * add file to database
		 */
		return $this->updateInfo( $data, true );

	}

        /**
         * downloadFile
         *
         * force downloads a file and then
         * exits
         *
         * @param string $path
         * @return void
         */
        public function downloadFile( $path ){

                /**
                 * check permissions
                 */
                if( !$this->hasPerm( $path, 'v' ) )
                        return false;                

                /**
                 * get file details in db or throw
                 * error
                 */
                $file = $this->getFile( $path );
                if( !$file )
                        return false;

                header( 'Content-type: ' . $file[ 'type' ] );
                header( 'Content-Disposition: attachment; filename="' . $file[ 'name' ] . '"' );

                echo file_get_contents( $this->users_files . $path );

                exit;

        }

        /**
         * displayImage
         *
         * displays a file as an image and then exits
         *
         * @param string $path
         * @param int/bool $width
         * @param int/bool $height
         * @return void
         */
        public function displayImage( $path, $width = false, $height = false ){

                /**
                 * permission check
                 */
                if( !$this->hasPerm( $path, 'v' ) )
                        return false;

                // temporary - might move this functionality entirely
                // into the file manager
                require_once HOME . '_inc/function/files.php';

                // create WBMP
                $file = $this->getFile( $path );
                switch( $file[ 'type' ] ){
                        case 'image/png':
                                $image = imagecreatefrompng( $this->users_files . $path );
                        break;
                        case 'image/jpeg':
                        case 'image/jpg':
                        case 'image/JPG':
                        case 'image/JPEG':
                                $image = imagecreatefromjpeg( $this->users_files . $path );
                        break;
                        case 'image/gif':
                                $image = imagecreatefromgif( $this->users_files . $path );
                        break;
                        default:
                                return $this->setError( 15, $path );
                }

                if( $width != false && $height != false ) // crop WBMP
                        $image = crop_image( $image, $width, $height, true, $this->users_files . $path );
                else if( $width != false ) // resize WBMP
                        $image = resize_image( $image, $width, false, true, $this->users_files . $path );

                // display WBMP as png
                header( 'Content-Type: image/png' );
                imagepng( $image );
                imagedestroy( $image );

                exit;

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
		return $this->error;
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
