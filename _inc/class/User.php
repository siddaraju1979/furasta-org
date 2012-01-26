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
 * VERIFY LOGIN
 * 
 * $User = User::getInstance( );
 * 
 * if( $User->verify( ) ){
 *   // user is logged in
 * }
 * 
 * CHECK USER PERMISSIONS
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
 *   // user has permission to access page
 * }
 *
 * When using the pagePerm function it handles group
 * permissions as well internally, so there is no need
 * to run hasPerm if pagePerm is run.
 *
 * @package user_management
 * @author Conor Mac Aoidh <conormacaoidh@gmail.com> 
 * @license http://furasta.org/licence.txt The BSD License
 * @todo change public vars to private
 */
class User{

	/**
	 * instance 
	 *
	 * holds the User object
	 * 
	 * @var object
	 * @static
	 * @access private
	 */
	private static $instance;

	/**
	 * id 
	 * 
	 * @var integer
	 * @access public
	 */
	public $id;

	/**
	 * name 
	 * 
	 * @var string
	 * @access public
	 */
	public $name;

	/**
	 * email 
	 * 
	 * @var string
	 * @access public
	 */
	public $email;

	/**
	 * password 
	 * 
	 * @var integer
	 * @access public
	 */
	public $password;

	/**
	 * group 
	 * 
	 * @var string
	 * @access public
	 */
	public $group;

	/**
	 * group_name 
	 * 
	 * @var string
	 * @access public
	 */
	public $group_name;

	/**
	 * perm 
	 * 
	 * @var array
	 * @access public
	 */
	public $perm = array( );

	/**
	 * _group
	 *
	 * @var bool/array
	 * @access private
	 */
	public $_group = false;

        /**
         * getInstance 
         * 
         * @static
         * @param string $value , true if new instance should be made
         * @access public
         * @return instance
         */
        public static function getInstance( $value = false ){

                /**
                 * if value is true a new instance is created
                 */
                if( $value == true ){
                        if( self::$instance )
                                self::$instance == '';

                        self::$instance = new User( );
                        return self::$instance;
                }

                /**
                 *  checks if an instance exists
                 */
                if( !self::$instance )
                        self::$instance = new User( );

                return self::$instance;
        }

	/**
	 * login
	 *
	 * attempts to login the user, returns
	 * false on failure
	 * 
	 * @param string $email 
	 * @param md5 string $password 
	 * @access public
	 * @return bool
	 */
	public function login( $email, $password, $permissions ){

		/**
		 * store user data in class and session
		 */
		$_SESSION[ 'user' ][ 'email' ] = $email;
		$_SESSION[ 'user' ][ 'password' ] = $password;
		$this->email = $email;
		$this->password = $password;

		/**
		 * check if user is in database and activated
		 */
	        $query = mysql_query( 'select * from ' . USERS . ' where email="' . $email . '" and password="' . $password . '" and hash="activated"' );
	        $num = mysql_num_rows( $query );

		/**
		 * if no rows match query check if user exists or is not activated 
		 */
	        if( $num != 1 ){
	                $result = mysql_query( 'select id from ' . USERS . ' where email="' . $email . '" and password="' . $password . '"' );
	                $num_res = mysql_num_rows( $result );

			/**
			 * get instance of template class and show error
			 * from lang files
			 */
			$Template = Template::getInstance( );

	                if( $num_res == 1 )
	                        $Template->runtimeError( '11' );
	                else
	                        $Template->runtimeError( '12' );

			return false;

	        }

		/**
		 * get further user details from db
		 */
		$array = mysql_fetch_array( $query );

		$_SESSION[ 'user' ][ 'id' ] = $array[ 'id' ];
		$this->id = $array[ 'id' ];
		$this->name = $array[ 'name' ];
		$this->group = $array[ 'user_group' ];

		if( $array[ 'user_group' ] != '_superuser' ){
			/**
			 * get user group permissions and group name
			 */
			$group = row( 'select name, perm from ' . GROUPS . ' where id="' . $array[ 'user_group' ] . '"' );

			$perm = self::permToArray( $group[ 'perm' ] );

			// make sure user has login perms
			if( !is_array( $permissions ) )
				$permissions = array( $permissions );
			foreach( $permissions as $p ){
				if( in_array( $p, $perm ) ){
					$Template = Template::getInstance( );
					$Template->runtimeError( '19' );
					return false;
				}
			}

			$group_name = $group[ 'name' ];
		}
		else{
			$perm = '';
			$group_name = '_superuser';
		}

		$_SESSION[ 'user' ][ 'perm' ] = $perm;
		$this->perm = $perm;

		$_SESSION[ 'user' ][ 'group_name' ] = $group_name;
		$this->group_name = $group_name;

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
	public function verify( ){

		/**
		 * check session vars are set
		 */
		if( !isset( $_SESSION[ 'user' ][ 'email' ] ) || !isset( $_SESSION[ 'user' ][ 'password' ] ) || !isset( $_SESSION[ 'user' ][ 'perm' ] ) )
			return false;

		$this->email = $_SESSION[ 'user' ][ 'email' ];
		$this->password = $_SESSION[ 'user' ][ 'password' ];

		/**
		 * get user data from db
		 */
		$verify = row( 'select * from ' . USERS . ' where email="' . $this->email . '" && password="' . $this->password . '"' );

		/**
		 * return false if user is not in db
		 */
		if( $verify == false )
			return false;

		/**
		 * set other vars
		 */
		$this->id = $verify[ 'id' ];
		$this->name = $verify[ 'name' ];
		$this->group = $verify[ 'user_group' ];
		$this->group_name = $_SESSION[ 'user' ][ 'group_name' ];
		$this->perm = $_SESSION[ 'user' ][ 'perm' ];

		return true;
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
	 * about
	 * 
	 * accessor method for class vars
	 *
	 * @todo change class vars to private
	 * @param string $var
	 * @return void/string
	 */
	public function about( $var ){
		if( isset( $this->$var ) )
			return $this->$var; 
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
	 * @params string $name
	 * @return int
	 * @access public
	 */
	public function getGroupId( $name ){
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
	 * @params int $id
	 * @return string
	 * @access public
	 */
	public function getGroupName( $id ){
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
		return explode( ',', $permstring );
	}
}
?>
