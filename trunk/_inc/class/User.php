<?php

/**
 * User Class, Furasta.Org
 *
 * This file contains a class which enables easy access
 * to info on users.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */
 
/**
 * User 
 * 
 * Used for user management. Some examples below:
 * 
 * == GENERAL USAGE ===
 *
 * This class can be used to simply get access to
 * details on a user. Using this code:
 *
 * $User = User::getInstance( $id );
 *
 * you can gain access to user info. If no $id is
 * provided then the constructor will check for
 * currently a currently logged in user id
 *
 * === VERIFY LOGIN ===
 * 
 * if( User::verify( ) ){
 *   // user is logged in
 * }
 *
 * === LOGIN USER ===
 *
 * This is usefull in the case of creating another
 * login area for the CMS, through the use of a
 * plugin. You can see how it works in admin/login.php
 * Basically you can use the static method "login" to log
 * the user in:
 *
 * if( User::login( $email, $password, $permissions, $instance ) ){
 *	$User = User::getInstance( );
 * 	// user is logged in
 * }
 * else
 * 	// user login failed
 *
 * An array of permissions required to log into the
 * area can also be passed. The last paramater decides
 * whether to create a new instance of the class for the
 * user if login is successful
 * 
 * === CHECK USER PERMISSIONS ===
 *
 * This class also manages group permissions for the
 * user. To check wether the user has permission to do
 * something use the following syntax, where $section
 * is a permission string ( see the key table below ):
 * 
 * $User = User::getInstance( );
 * 
 * if( $User->hasPerm( $string ){
 *   // user has permission to access this of the system
 * }
 * 
 * The string above can be anything from the table below,
 * or if plugins are installed there may be other
 * keys - refer to the plugin documentation for details on
 * that.
 * 
 * +---------------+----------------------------+
 * |      a        |      login to admin area   |    
 * |      e        |      edit pages            |
 * |      c        |      create pages          |
 * |      d        |      delete pages          |
 * |      t        |      manage trash          |
 * |      o        |      order pages           | // @todo make order pages perms work
 * |      s	   |	  edit settings	        |
 * |      u	   |      edit users	        |
 * +---------------+----------------------------+
 * 
 * The class can also be used to check if the user has
 * permission to edit a certain page. The function accepts
 * an array of page permissions, or the string of perms
 * directly from the database.
 *
 * if( $User->adminPagePerm( $perm_array ) ){
 *   // user has permission to edit page
 * }
 *
 * When using the pagePerm function it handles group
 * permissions as well internally, so there is no need
 * to run hasPerm if pagePerm is run.
 *
 * === DATA & OPTIONS ===
 * 
 * Not all required data on users can be stored in the users table.
 * This class provides two alternatives to storing data for users:
 * 
 * Data can be stored in the data column in the users table -
 * a JSON encoded string of user data, so it's sufficient for data
 * that does not need to be indexed.
 *
 * It can also be stored using the options methods which use the
 * options table (for general CMS options/settings) to store data.
 * This table takes the form of name, value and stores data very simply.
 * Note: It is advisable to take a look at the database structure.
 * Using this method extra data can be stored and indexed.
 * 
 * @package user_management
 * @author Conor Mac Aoidh <conormacaoidh@gmail.com> 
 * @license http://furasta.org/licence.txt The BSD License
 */
class User{

	/**
	 * instances
	 *
	 * holds User class instances
	 * 
	 * @var object
	 * @static
	 * @access private
	 */
	private static $instances = array( );

	/**
	 * id 
	 * 
	 * @var integer
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
	 * email 
	 * 
	 * @var string
	 * @access private
	 */
	private $email;

	/**
	 * password 
	 * 
	 * @var integer
	 * @access private
	 */
	private $password;

	/**
	 * groups 
	 * 
	 * @var array
	 * @access private
	 */
	private $groups;

	/**
	 * hash
	 *
	 * @var string
	 * @access private
	 */
	private $hash;

	/**
	 * perm 
	 * 
	 * @var array
	 * @access private
	 */
	private $perm = array( );

	/**
	 * filePerm
	 *
	 * holds a combination of all
	 * file perm for the groups of which
	 * the user is a member
	 *
	 * @var array
	 * @access private
	 */
	private $filePerm = array( );

	/**
	 * data
	 *
	 * @var array
	 * @access private
	 */
	private $data = array( );

	/**
	 * options
	 *
	 * holds user options which are stored in the
	 * options table
	 *
	 * @var array
	 * @access private
	 */
	private $options;

	/**
	 * _superuser
	 *
	 * true if the user is the superuser. this means
	 * that all permission checks are bypassed
	 *
	 * @var bool
	 * @access private
	 */
	private $_superuser = false;

	/**
	 * performLogin
	 *
	 * does the bulk of the login work, called
	 * by the login and loginHash functions
	 *
	 * NOTE : If $instance is set to true, creates
	 * instance of user after login, stores it
	 * in instance var, use User::getInstance( ) to 
	 * access the now logged in user
	 * 
	 * $permissions should be in the form of an array
	 * of group names such as array( 'users' )
	 *
	 * @todo consider creating second constructor to save a database query
	 * on login
	 * @param
	 * @access private
	 * @return bool
	 * @static
	 */
	private static function performLogin( $user, $permissions, $instance ){

		// already logged in, so destroy old login
		if( User::verify( ) ){
			unset( $_SESSION[ 'user' ] );
			self::destroyCookie( );
		}

		// user not in db
		if( !$user ){
			$Template = Template::getInstance( );
			$Template->runtimeError( '12' );
			return false;
		}

		/**
		 * get instance of template class and show error
		 * from lang files
		 */
		if( $user[ 'hash' ] != 'activated' ){
			$Template = Template::getInstance( );
			$Template->runtimeError( '11' );
			return false;
		}

		/**
	 	 * check permissions if not super user and permissions
		 * check is required
		 */
		if( !empty( $permissions ) ){
			$groups = explode( ',', $user[ 'groups' ] );
			$perm = array( );
			$match = false;

			/**
			 * if super user, has permission
			 */
			if( in_array( 1, $groups ) )
				$match = true;
	
			foreach( $groups as $group ){
				$Group = Group::getInstance( $group );
				$perm = array_merge( $perm, $Group->perm( ) );
			} 

			// make sure user has login perms
			if( !is_array( $permissions ) )
				$permissions = array( $permissions );

			foreach( $permissions as $p ){
				if( in_array( $p, $perm ) )
					$match = true;
			}

			if( !$match ){
				$Template = Template::getInstance( );
				$Template->runtimeerror( '19' );
				return false;
			}
		}

		$_SESSION[ 'user' ][ 'id' ] = $user[ 'id' ];
		$_SESSION[ 'user' ][ 'email' ] = $user[ 'email' ];
		$_SESSION[ 'user' ][ 'password' ] = $user[ 'password' ];
		$_SESSION[ 'user' ][ 'perm' ] = @$perm;
		$_SESSION[ 'user' ][ 'login' ] = true;

		// create new instance of user if required
		if( $instance )
			self::$instances{ $user[ 'id' ] } = new User( $user[ 'id' ] );	

		// trigger onlogin event
		$Plugins = Plugins::getInstance( );
		$Plugins->hook( 'general', 'on_login' );

		return true;
	}

	/**
	 * createInstances
	 *
	 * Used to create multiple instances of users
	 * at once. Accepts an array of users ids and
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
	public static function createInstances( $user_ids ){

		/**
		 * check instance isn't already loaded
		 */
		$ids = array( );
		foreach( $user_ids as $key => $id ){
			if( !isset( self::$instances{ $id } ) )
				array_push( $ids, $id );
		}

		if( empty( $ids[ 0 ] ) )
			return;

		/**
		 * get users info from db
		 */
		$query = 'select *,group_concat(' . USERS_GROUPS . '.group_id) as groups  from ' . USERS . ',' . USERS_GROUPS . ' where (' . USERS . '.id=';
		$query .= implode( ' or ' . USERS . '.id=', $ids );
		$query .= ') and ' . USERS_GROUPS . '.user_id=' . USERS . '.id group by ' . USERS . '.id';
		$users = rows( $query );

		/**
		 * create instances
		 */
		foreach( $users as $user )
			self::$instances{ $user[ 'id' ] } = new User( $user );

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
	public static function getInstance( $id = false ){

		// if no id, use session user id
		// if logged in else return false
		if( !$id ){
			if( self::verify( ) )
				$id = $_SESSION[ 'user' ][ 'id' ];
			else
				return false;
		}

		// if there isn't an instance, create one
		if( !isset( self::$instances{ $id } ) )		
			self::$instances{ $id } = new User( $id );

		return self::$instances{ $id };

	}

	/**
	 * __construct
	 *
	 * sets up the user. will take an id
	 * or an array of user info
	 *
	 * @params int|array $user
	 * @return bool
	 * @access private
	 */
	private function __construct( $user ){

		if( !is_array( $user ) )
			$user = row( 'select *,group_concat(' . USERS_GROUPS . '.group_id) as groups from ' . USERS . ',' . USERS_GROUPS . ' where ' . USERS_GROUPS . '.user_id=' . $user . ' group by ' . USERS . '.id' );
			
		// user doesn't exist
		if( count( $user ) == 0 )
			return false;		

		$this->id = $user[ 'id' ];
		$this->name = $user[ 'name' ];
		$this->email = $user[ 'email' ];
		$this->password = $user[ 'password' ];
		$groups = $user[ 'groups' ];
		$this->groups = array( );
		$this->hash = $user[ 'hash' ];
		$this->data = json_decode( stripslashes( $user[ 'data' ] ), true );	

		/**
		 * create an instance of each group of which
		 * the user is a member in order to inherit
		 * permissions
		 */
		$groups = explode( ',', $groups );

		/**
		 * if superuser, set in _superuser field
		 */
		if( in_array( '_superuser', $groups ) ){
			$this->_superuser = true;
			unset( $groups[ array_search( '_superuser', $groups ) ] );
		}

		Group::createInstances( $groups );
		foreach( $groups as $id ){

			array_push( $this->groups, $id );
			$group = Group::getInstance( $id );

			$this->perm = array_merge( $this->perm, $group->perm( ) );
			$this->filePerm = merge_perm( $this->filePerm, $group->filePerm( ) );
		}
	
		return true;
	}

	/**
	 * login
	 *
	 * attempts to login the user, returns
	 * false on failure
	 *
	 * uses the performLogin method, for more detail
	 * on parameters see that method
	 *
	 * @param string $email
	 * @param string $password
	 * @param array $permissions ( groups in array )
	 * @param bool $instance, create new instance of user after successful login
	 * @access public
	 * @return bool
	 */
	public static function login( $email, $password, $permissions = array( ), $instance = false ){

		$user = row(
			'select *,group_concat(' . USERS_GROUPS . '.group_id) as groups from ' . USERS . ',' . USERS_GROUPS
			. ' where ' . USERS . '.email="' . $email . '" and ' . USERS . '.password="' . $password . '"'
			. ' and ' . USERS_GROUPS . '.user_id=' . USERS . '.id group by ' . USERS . '.id limit 1'
		);
	
		return self::performLogin( $user, $permissions, $instance );

	}

	/**
	 * loginHash
	 *
	 * allows users to login using a hash key, works
	 * much the same as the login function
	 * 
	 * @param int $userid
	 * @param int $hash
	 * @param array $permissions
	 * @param bool $instance
	 * @access public
	 * @return bool
	 * @static
	 */
	public static function loginHash( $userid, $hash, $permissions = array( ), $instance = false ){

		$user = row(
			'select *,group_concat(' . USERS_GROUPS . '.group_id) as groups from ' . USERS . ',' . USERS_GROUPS
			. ' where ' . USERS . '.password="' . $hash . '"'
			. ' and ' . USERS_GROUPS . '.user_id=' . $userid . '.id group by ' . USERS . '.id limit 1'
		);

		return self::performLogin( $user, $permissions, $instance );

	}

	/**
	 * verify
	 *
	 * checks if the user is logged in
	 * 
	 * @access public
	 * @return bool
	 */
	public static function verify( ){

		if( isset( $_SESSION[ 'user'][ 'login' ] ) && $_SESSION[ 'user' ][ 'login' ] == true )
			return true;

		return false;
	}

	/**
	 * setCookie
	 *
	 * sets a cookie to store the current users
	 * login details on the client side 
	 * 
	 * @access public
	 * @return void
	 */
	public function setCookie( ){

		/**
		 * set expire time to one week
		 */
		$expire_time = time( ) + ( 3600 * 24 * 7 );

		/**
		 * set cookies for email and password
		 */
		setcookie( 'furasta[email]', $this->email, $expire_time );
		setcookie( 'furasta[password]', $this->password, $expire_time );

		return true;

	}

	/**
	 * logout
	 *
	 * logs out the current user 
	 * 
	 * @access public
	 * @return void
	 */
	public static function logout( ){

		// trigger onlogout event
		$Plugins = Plugins::getInstance( );
		$Plugins->hook( 'general', 'on_logout' );

		@session_start( );
		session_destroy( );

		self::destroyCookie( );

	}

	/**
	 * destroyCookie
	 *
	 * Destroys the users cookie if one is set 
	 * 
	 * @access public
	 * @return bool
	 */
	public static function destroyCookie( ){

		/**
		 * set negative expire time
		 */
		$expire_time = time( ) - 3600;

		/**
		 * check if cookie isset then destroy it
		 */
		if( isset( $_COOKIE[ 'furasta' ][ 'email' ] ) && isset( $_COOKIE[ 'furasta' ][ 'password' ] ) ){
		        setcookie( 'furasta[email]', '', $expire_time );
		        setcookie( 'furasta[password]', '', $expire_time );
			unset( $_COOKIE[ 'furasta' ][ 'email' ] );
			unset( $_COOKIE[ 'furasta' ][ 'password' ] );
		}

		return true;

	}

	/**
	 * hasPerm 
	 * 
	 * used to check if user has permission
	 * to access certain areas of the cms
	 * 
	 * @param string $perm 
	 * @access public
	 * @return bool
	 */
	public function hasPerm( $perm ){

		/**
		 * if user is super user bypass all permissions 
		 */
		if( $this->_superuser )
			return true;

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
	 * adminPagePerm
	 * 
	 * used to check if the user has permission
	 * to edit the page, pass the pages permissions 
	 * string
	 * 
	 * @param string $perm 
	 * @access public
	 * @return bool 
	 */
	public function adminPagePerm( $perm ){

		/**
		 *  if user is super user bypass all permissions
		 */
		if( $this->_superuser )
			return true;

                /**
                 * if no perms are set for page, then everyone
                 * can edit it assuming they have group edit
                 * perms
                 */
                if( empty( $perm ) )
                        return $this->hasPerm( 'e' );

		/**
		 * check if groups are used, if so add
		 * all users from that group to the array
		 * of users
		 */
		if( strpos( $perm, "#" ) !== false ){

			$perm = explode( '#', $perm );

			/**
			 *  users and groups arrays
			 */
			$users = explode( ',', $perm[ 0 ] );

			$groups = explode( ',', $perm[ 1 ] );
			
			foreach( $groups as $group ){

				/**
				 * if user is in group, then he has
				 * permission
				 */
				if( in_array( $group, $this->groups ) )
					return true;
		
			}


		}
		else
			$users = explode( ',', $perm );

		/**
		 * check if user id is in the array of allowed
		 * ids to edit page 
		 */
		if( in_array( $this->id, $users ) )
			return true;

		/**
		 * user cannot edit page
		 */
		return false;
	}

	/**
	 * frontendPagePerm
	 * 
	 * used to check if the user has permission
	 * to view the page, pass the pages permissions 
	 * string
	 * 
	 * @param string $perm 
	 * @access public
	 * @return bool 
	 */
	public function frontendPagePerm( $perm ){

		/**
		 *  if user is super user bypass all permissions
		 */
		if( $this->_superuser )
			return true;

                /**
                 * if no perms are set for page, then everyone
                 * can view it
                 */
                if( empty( $perm ) )
			return true;

		/**
		 * check if groups are used, if so add
		 * all users from that group to the array
		 * of users
		 */
		if( strpos( $perm, "#" ) !== false ){

			/**
			 * user is not logged in
			 */
			if( !User::verify( $this->id ) )
				return false;

			$perm = explode( '#', $perm );

			/**
			 *  users and groups arrays
			 */
			$users = explode( ',', $perm[ 0 ] );

			$groups = explode( ',', $perm[ 1 ] );
			
			foreach( $groups as $group ){

				/**
				 * if user is in group, then he has
				 * permission
				 */
				if( in_array( $group, $this->groups ) )
					return true;
		
			}

		}
		else
			$users = explode( ',', $perm );

		/**
		 * check if user id is in the array of allowed
		 * ids to edit page 
		 */
		if( in_array( $this->id, $users ) )
			return true;

		/**
		 * user cannot edit page
		 */
		return false;
	}

	/**
	 * createUserDirs
	 * 
	 * creates the directory structure for users
	 *
	 * @todo update to file manager
	 * @params int $id
	 * @return bool
	 * @access public
	 */
	public static function createUserDirs( $id ){
		$dirs = array(
			USER_FILES . 'files/users/' . $id,
			USER_FILES . 'files/users/' . $id . '/public',
			USER_FILES . 'files/users/' . $id . '/private'
		);

		$mkdir = true;
		foreach( $dirs as $dir ){
			if( !is_dir( $dir ) )
				mkdir( $dir ) || $mkdir == false;
		}

		return $mkdir;
	}

	/**
	 * removeUserDirs
	 * 
	 * removes the directory structure for users
	 *
	 * @params int $id
	 * @return bool
	 * @access public
	 * @todo update to file manager
	 */
	public static function removeUserDirs( $id ){
		$dir = USER_FILES . 'files/users/' . $id;
		$rmdir = true;
		if( is_dir( $dir ) )
			remove_dir( $dir ) || $rmdir = false;
		return $rmdir;
	}

	/**
	 * getOptions
	 *
	 * this function gets options for this user from the
	 * options table and stores them in the options array
	 *
	 * @access public
	 * @return array
	 */
	public function getOptions( ){

		$options = rows( 'select * from ' . OPTIONS . ' where category="user_' . $this->id . '"' );

		$this->options = array( );		
		foreach( $options as $name => $value ){
			$name = end( explode( '_', $name ) );
			$this->options{ $name } = $value;
		}

		return $this->options;

	}

	/**
	 * getOption
	 *
	 * gets the value of a specified user option. these methods
	 * manages the storage of the options so a simple option name
	 * is required like "user_rating" or "address"
	 *
	 * @param string $name
	 * @access public
	 * @return string
	 */
	public function getOption( $name ){
		if( !is_array( $this->options ) )
			$this->getOptions( );

		return @$this->options{ $name };
	}

	/**
	 * setOption
	 *
	 * sets an option to a specified value. updates the database
	 * and the class's record of the options
	 *
	 * @param string $name
	 * @param string $value
	 * @access public
	 * @return bool
	 */
	public function setOption( $name, $value ){
		if( !is_array( $this->options ) )
			$this->getOptions( );

		$name = addslashes( $name );
		$value = addslashes( $value );

		if( isset( $this->options{ $name } ) ) // if isset, update db
			query( 'update ' . OPTIONS . ' set value="' . $value . '" where name="' . $name . '" and category="user_' . $this->id . '"' );
		else // else create db entry
			query( 'insert into ' . OPTIONS . ' values( "' . $name . '", "' . $value . '", "user_' . $this->id . '" )' );

		$this->options{ $name } = $value;

		return true;
	}

	/**
	 * id
	 *
	 * accessor method for user id
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
	 * accessor method for user name
	 *
	 * @access public
	 * @return string
	 */
	public function name( ){
		return $this->name;
	}

	/**
	 * email
	 *
	 * accessor method for user email
	 *
	 * @access public
	 * @return int
	 */
	public function email( ){
		return $this->email;
	}


	/**
	 * password
	 *
	 * accessor method for user password
	 *
	 * @access public
	 * @return string md5
	 */
	public function password( ){
		return $this->password;
	}

	/**
	 * hash
	 *
	 * accessor method for user hash
	 *
	 * @access public
	 * @return int
	 */
	public function hash( ){
		return $this->hash;
	}

	/**
	 * perm
	 *
	 * accessor method for user permissions
	 *
	 * @access public
	 * @return perm
	 */
	public function perm( ){
		return $this->perm;
	}

	/**
	 * groups
	 *
	 * accessor method for groups, returns an array
	 * of group ids of which this user is a member
	 *
	 * @access public
	 * @return array
	 */
	public function groups( ){
		return $this->groups;
	}

	/**
	 * getData
	 *
	 * accessor method for the data array
	 *
	 * @return array
	 * @access public
	 */
	public function getData( ){
		return $this->data;
	}

	/**
	 * isSuperUser
	 *
	 * returns true if the users is the
	 * _superuser
	 *
	 * @access public
	 * @return bool
	 */
	public function isSuperUser( ){
		return $this->_superuser;
	}
}
?>
