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
	 * group 
	 * 
	 * @var string
	 * @access private
	 */
	private $group;

	/**
	 * group_name 
	 * 
	 * @var string
	 * @access private
	 */
	private $group_name;

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
	 * data
	 *
	 * @var array
	 * @access private
	 */
	private $data = array( );

	/**
	 * _group
	 *
	 * @var bool/array
	 * @access private
	 */
	private $_group = false;

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
	 * but will use session id if isset and
	 * no id given
	 *
	 * @params int $id
	 * @return bool
	 * @access public
	 */
	public function __construct( $id ){

		$user = row( 'select * from ' . USERS . ' where id=' . $id );

		// user doesn't exist
		if( count( $user ) == 0 )
			return false;		

		$this->id = $user[ 'id' ];
		$this->name = $user[ 'name' ];
		$this->email = $user[ 'email' ];
		$this->password = $user[ 'password' ];
		$this->group = $user[ 'user_group' ];
		$this->hash = $user[ 'hash' ];
		$this->data = json_decode( stripslashes( $user[ 'data' ] ), true );

		// if not superuser check perms
		if( $this->group != '_superuser' ){
			$group = Group::getInstance( $this->group );
			$this->perm = $group->perm( );
			$this->group_name = $group->name( );
		}
		
		return true;
	}

	/**
	 * login
	 *
	 * attempts to login the user, returns
	 * false on failure
	 *
	 * NOTE : If $instance is set to true, creates
	 * instance of user after login, stores it
	 * in instance var, use User::getInstance( ) to 
	 * access the now logged in user
	 * 
	 * $permissions should be in the form of an array
	 * of group names such as array( 'users' )
	 *
	 * @param string $email
	 * @param md5 string $password
	 * @param array $permissions ( groups in array )
	 * @param bool $instance, create new instance of user after successful login
	 * @access public
	 * @return bool
	 */
	public static function login( $email, $password, $permissions = array( ), $instance = false ){

		// already logged in, so destroy old login
		if( User::verify( ) )
			User::logout( );

		$user = row( 'select id,hash,user_group from ' . USERS . ' where email="' . $email . '" and password="' . $password . '"' );

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
		if( $user[ 'user_group' ] != '_superuser' && !empty( $permissions ) ){
			$perm = single( 'select perm from ' . GROUPS . ' where id=' . $user[ 'user_group' ], 'perm' );
			$perm = self::permToArray( $perm );

			// make sure user has login perms
			if( !is_array( $permissions ) )
				$permissions = array( $permissions );

			$match = false;
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
		$_SESSION[ 'user' ][ 'email' ] = $email;
		$_SESSION[ 'user' ][ 'password' ] = $password;
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

		session_start( );
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
		if( $this->group == '_superuser' )
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
	 * hasFilePerm
	 *
	 * checks if a user has permission to access a file
	 * in the user files directory
	 *
	 * @param string $path
	 * @access public
	 * @return bool
	 */
	public function hasFilePerm( $path ){

		// if user is superuser bypass all permissions
		if( $this->group == '_superuser' )
			return true;

		// split path into directories
		$path = explode( '/', $path );
		while( $path != 'files' )
			array_shift( $path );

		$section = ( @$path[ 0 ] == 'users' ) ? 'users' : 'groups';
		$id = ( int ) @$path[ 1 ];
		$filename = @$path[ 2 ];

		die( $section . $id . $filename );

		// check user file perm

		if( !Group::groupsHaveFilePerm( $this->id, $path ) )
			return false;

		return true;
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
		if( $this->group == '_superuser' )
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
				if( $this->group == $group )
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
		if( $this->group == '_superuser' )
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
			if( empty( $this->group ) )
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
				if( $this->group == $group )
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
	 * isInGroup
	 * 
	 * checks if a user is in a given group
	 *
	 * @todo add multiple groups per person
	 * @params name
	 * @return public
	 */
	public function isInGroup( $name ){
		$id = $this->getGroupId( $name );
		if( $this->group == $id )
			return true;
		return false;
	}

	/**
	 * getGroupId
	 *
	 * returns a group id if given a group
	 * name
	 *
	 * @params string $name optional
	 * @return int
	 * @access public
	 */
	public function getGroupId( $name = 'false' ){
		if( !$name )
			return $this->group;

		if( !$this->_groups )
			$this->_groups = rows( 'select id,name from ' . GROUPS );

		foreach( $this->_groups as $group ){ // find group id
			if( $group[ 'name' ] == $name )
				return $group[ 'id' ];
		}
		// no matches
		return false;
	}

	/**
	 * getGroupName
	 *
	 * returns a group name if given a group
	 * id
	 *
	 * @params int $id optional
	 * @return string
	 * @access public
	 */
	public function getGroupName( $id = 'false' ){

		if( !$id )
			return $this->group_name;

		if( !$this->_groups )
			$this->_groups = rows( 'select id,name from ' . GROUPS );

		foreach( $this->_groups as $group ){ // find group name
			if( $group[ 'id' ] == $id )
				return $group[ 'name' ];
		}
		// no matches
		return false;
	}

	/**
	 * permToArray
	 *
	 * converts a perm string ( comma delimited ) to
	 * an array of perms, example:
	 *
	 * input: "a,c,p,t"
	 * output: array( 'a', 'c', 'p', 't' )
	 *
	 * @params string $permString
	 * @return array
	 * @access public
	 */
	public static function permToArray( $permString ){
		if( strpos( ',', $permString ) == -1 )
			return array( $permString );
		return explode( ',', $permString );
	}

	/**
	 * createUserDirs
	 * 
	 * creates the directory structure for users
	 *
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
	 */
	public static function removeUserDirs( $id ){
		$dir = USER_FILES . 'files/users/' . $id;
		$rmdir = true;
		if( is_dir( $dir ) )
			remove_dir( $dir ) || $rmdir = false;
		return $rmdir;
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
	 * accessor method for group ids
	 *
	 * @access public
	 * @return array
	 */
	public function groups( ){
		return array( $this->group );
	}

	/**
	 * groupName
	 *
	 * accessor method for user group name
	 *
	 * @access public
	 * @return int
	 */
	public function groupName( ){
		return $this->group_name;
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
}
?>
