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
 * @todo implement these functions
 */

/**
 * users_add
 * 
 * creates a new user in the database with the
 * given parameters and returns an instance of
 * the user class for the new user
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
 * @param array $groups optional
 * @param array $data optional
 * @param array $options optional
 * @param array $mail optional
 * @return int $id
 */
function users_add( $name, $email, $password, $groups = array( ), $data = array( ), $options = array( ), $mail = true ){

        query( $query );
        $id = mysql_last_insert( );
    
        /**
         * create user files directories
         * @todo update this to using file manager 
         */
	$dirs = array(
		USER_FILES . 'files/users/' . $id,
		USER_FILES . 'files/users/' . $id . '/public',
		USER_FILES . 'files/users/' . $id . '/private'
	);

	foreach( $dirs as $dir ){
		if( !is_dir( $dir ) )
			mkdir( $dir );
	}

        // default email
	if( $mail ){
            $mail[ 'subject' ] = 'User Activation - Furasta.Org';
            $mail[ 'message' ] = $name . ',<br/>
            	<br/>
                Please activate your new user by clicking on the link below:<br/>
                <br/>
                <a href="' . SITEURL . 'admin/users/activate.php?hash=' . $hash . '">'
                    . $url . '/admin/users/activate.php?hash=' . $hash . '</a><br/>
                <br/>
                If you are not the person stated above please ignore this email.<br/>
            ';
        }
        
        // send notification email to user
        email( $email, $mail[ 'subject' ], $mail[ 'message' ] );
        cache_clear( 'USERS' );

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
