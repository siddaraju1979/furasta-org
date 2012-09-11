<?php

/**
 * Users Functions, Furasta.Org
 *
 * Contains function which are useful for dealing with
 * users and groups.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * user_create
 * 
 * creates a new user in the database with the
 * given parameters
 * 
 * $data    -   an array of items to be JSON encoded in
 *              the data field
 * $options -   an array of options to be added to the
 *              options database table for the user
 * $mail    -   an array with the keys 'subject', 'message',
 *              for the users notification email         
 * 
 * @param string $name
 * @param string $email
 * @param string $password
 * @param array $groups
 * @param array $data optional
 * @param array $options optional
 * @param array $mail optional
 * @return int|bool $id
 */
function user_create( $name, $email, $password, $groups, $data = array( ), $options = array( ), $mail = true ){

	/**
	 * if email is in use, return false
	 * note; one account per email
	 */
	if( num( 'select id from ' . DB_USERS . ' where email="' . $email .'"' ) != 0 )
		return false;

	/**
	 * add to users table
	 */
	$hash = md5( mt_rand( ) );
	query( 'insert into ' . DB_USERS . ' values ('
		. '"",'
		. '"' . $name . '",'
		. '"' . $email . '",'
		. '"' . md5( $password ) . '",'
		. '"' . $hash . '",'
		. '"",'
		. '"' . json_encode( $data ) . '"'
	. ')' );
        $id = mysql_insert_id( );

	/**
	 * add to groups table for each group
	 */
	foreach( $groups as $group )
		query( 'insert into ' . DB_USERS_GROUPS . ' values( ' . $id . ', ' . $group . ' )' );	

        /**
         * create user files directory
         */
	$FileManager = FileManager::getInstance( );
	$FileManager->addDir( 'users/' . $id );

	/**
	 * add options to options table if nessecary
	 */
	if( !empty( $options ) ){
		foreach( $options as $name => $value ){
			query( 'insert into ' . DB_OPTIONS . ' values( "' . $name . '", "' . $value . '", "user_' . $id . '"' );
		}
	}

        // default email
	if( $mail ){
		$mail = array( );	
		$mail[ 'subject' ] = 'User Activation - Furasta.Org';
		$mail[ 'message' ] = $name . ',<br/>
		<br/>
		Please activate your new user by clicking on the link below:<br/>
		<br/>
		<a href="' . SITE_URL . 'admin/users/activate.php?hash=' . $hash . '">'
		    . $url . '/admin/users/activate.php?hash=' . $hash . '</a><br/>
		<br/>
		If you are not the person stated above please ignore this email.<br/>
		';
        }
        
        // send notification email to user
        email( $email, $mail[ 'subject' ], $mail[ 'message' ] );
        cache_clear( 'DB_USERS' );

	return $id;
}

/**
 * users_delete
 * 
 * removes a user from the database and deletes
 * all files associated with the user. returns
 * false on failure
 * 
 * @param int $id
 * @return bool 
 */
function users_delete( $id, $message = '' ){
  
}

/**
 * group_add
 * 
 * creates a new group, adds it to the
 * database and returns an instance of
 * the group class for the new group
 * 
 * @param
 * @return Group 
 */
function group_add( ){
    
}

/**
 * group_delete
 * 
 * removes a group from the database, removes all
 * members of the group from the group, deletes
 * all group files. returns false on failure
 * 
 * @param int $id
 * @return bool 
 */
function group_delete( $id ){
    
}
?>
