<?php

/**
 * Install Core, Furasta.Org
 *
 * Contains functions used in install/complete.php to
 * perform the install
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    installer
 */

/**
 * install_settings
 *
 * formats the information collected in the install
 * stages and outputs it to the .settings.php file
 * this function also creates the .htaccess and
 * robots.txt files
 *
 * @return void
 */
function install_settings( ){

        global $constants;
        global $settings;

        $prefix = $_SESSION[ 'db' ][ 'prefix' ];

        // constants for .settings.php
        $constants = array(
                'DB_PAGES'                 => $prefix . 'pages',
                'DB_USERS'                 => $prefix . 'users',
                'DB_TRASH'                 => $prefix . 'trash',
                'DB_GROUPS'                => $prefix . 'groups',
                'DB_USERS_GROUPS'          => $prefix . 'users_groups',
                'DB_OPTIONS'               => $prefix . 'options',
                'DB_FILES'                 => $prefix . 'files',
                'TEMPLATE_DIR'          => HOME . '_www/vitruvius/',     // @todo update
                'DB_PREFIX'                => $prefix,
                'VERSION'               => '0.9.2',                     // @todo define version in install header
                'SITE_URL'               => $_SESSION[ 'settings' ][ 'site_url' ],
                'USERS_DIR'            => $_SESSION[ 'settings' ][ 'user_files' ],
                'DIAGNOSTIC_MODE'       => 0,
                'LANG'                  => 'English',
                'SET_REVISION'          => 100
        );
        $constants = defaults_constants( $constants );

        // no plugins installed by default
        $plugins = array( );

        /*
         * settings array for .settings.php
         */
        $settings = array(
                'site_title'            => addslashes( $_SESSION[ 'settings' ][ 'title' ] ),
                'site_subtitle'         => addslashes( $_SESSION[ 'settings' ][ 'sub_title' ] ),
                'index'                 => ( int ) $_SESSION[ 'settings' ][ 'index' ],
                'maintenance'           => $_SESSION[ 'settings' ][ 'maintenance' ],
                'system_alert'          => ''
        );

        $db = array(
                'name' => addslashes( $_SESSION[ 'db' ][ 'name' ] ),
                'host' => addslashes( $_SESSION[ 'db' ][ 'host' ] ),
                'user' => addslashes( $_SESSION[ 'db' ][ 'user' ] ),
                'pass' => addslashes( $_SESSION[ 'db' ][ 'pass' ] )
        );

        $filecontents = defaults_settings_content( $settings, $db, $plugins, $constants );


        /**
         * write settings file
         */
        file_put_contents( HOME . '.settings.php', $filecontents )
                or error( 'Please grant <i>0777</i> write access to the <i>' . HOME
                . '</i> directory then reload this page to complete installation.');


        /**
         * create htaccess file
         */
        $rules = defaults_htaccess_rules( $_SESSION[ 'settings' ][ 'site_url' ] );

        $htaccess = defaults_htaccess_content( $rules );

        file_put_contents( HOME . '.htaccess', $htaccess );


        /**
         * create robots file
         */
        $robots = defaults_robots_content( $settings[ 'index' ], $_SESSION[ 'settings' ][ 'site_url' ] );

        file_put_contents( HOME . 'robots.txt', $robots );

        return array( $settings, $constants );
        
}

/**
 * install_db
 *
 * installs the database of the default system
 *
 * @return void
 */
function install_db( ){

        /**
         * if this setting is enabled, skip the db install
         */
        if( $_SESSION[ 'settings' ][ 'database_install' ] == 1 )
                return;

        global $settings;
        global $constants;
        global $hash;
        global $user_id;

        /**
         * pages table. where general information on
         * pages is stored
         */
        $pagecontent = '<h1>Welcome to Furasta.Org</h1>'
                . '<p>Welcome to your new installation of Furasta.Org'
                . 'Content Management System.</p>'
                . '<p>To log in to the administration area <a href=\''
                . $constants[ 'SITE_URL' ] . '/admin\'>Click Here</a></p>';
        mysql_query( 'drop table if exists ' . $constants[ 'DB_PAGES' ] );
        mysql_query( 'create table ' . $constants[ 'DB_PAGES' ] . ' ('
                . 'id int auto_increment primary key,' 
                . 'name text,' 
                . 'content text,' 
                . 'slug text,' 
                . 'template text,' 
                . 'type text,' 
                . 'edited date,' 
                . 'user text,' 
                . 'position int,' 
                . 'parent int,' 
                . 'perm text,' 
                . 'home int,' 
                . 'display int' 
        . ')' );
        mysql_query( 'insert into ' . $constants[ 'DB_PAGES' ] . ' values('
                . '0,'
                . '"Home",'
                . '"' . $pagecontent . '",'
                . '"Home",'
                . '"Default",'
                . '"Normal",'
                . '"' . date( 'Y-m-d' ) . '",'
                . '"Installer",'
                . '1,'
                . '0,'
                . '"|",'
                . '1,'
                . '1'
        . ')' );
        
        /**
         * users tables, where user information
         * is stored
         */
        mysql_query( 'drop table if exists ' . $constants[ 'DB_USERS' ] );
        mysql_query( 'create table ' . $constants[ 'DB_USERS' ] . ' ('
                . 'id int auto_increment primary key,'
                . 'name text,'
                . 'email text,'
                . 'password text,'
                . 'hash text,'
                . 'reminder text,'
                . 'data text'
        . ')' );
        mysql_query( 'insert into ' . $constants[ 'DB_USERS' ] . ' values('
                . '0,'
                . '"' . $_SESSION[ 'user' ][ 'name' ] . '",'
                . '"' . $_SESSION[ 'user' ][ 'email' ] . '",'
                . '"' . $_SESSION[ 'user' ][ 'pass' ] . '",'
                . '"' . $hash . '",'
                . '"",'
                . '""'
        . ')' );
        $user_id = mysql_insert_id( );

        /**
         * groups table, permissions for groups and
         * group information
         */
        mysql_query( 'drop table if exists ' . $constants[ 'DB_GROUPS' ] );
        mysql_query( 'create table ' . $constants[ 'DB_GROUPS' ] . ' ('
                . 'id int auto_increment primary key,'
                . 'name text,'
                . 'perm text,'
                . 'file_perm text'
        . ')' );
        mysql_query( 'insert into ' . $constants[ 'DB_GROUPS' ] . ' values ( '
                . '"",'
                . '"Users",'
                . '"a,e,c,d,t,o,s,u,f",'
                . '"|"'
        . ')' );

        /**
         * users groups, this table identifies which
         * groups a user is a member of
         */
        mysql_query( 'drop table if exists ' . $constants[ 'DB_USERS_GROUPS' ] );
        mysql_query( 'create table ' . $constants[ 'DB_USERS_GROUPS' ] . ' ('
                . 'user_id int,'
                . 'group_id text'
        . ')' );
        mysql_query( 'insert into ' . $constants[ 'DB_USERS_GROUPS' ] . ' values ( '
                . '1,'
                . '"_superuser"'
        . ')' );

        /**
         * trash table, keeps copies of deleted pages
         * note: not all page information is retained
         */
        mysql_query( 'drop table if exists ' . $constants[ 'DB_TRASH' ] );
        mysql_query( 'create table ' . $constants[ 'DB_TRASH' ] . ' ('
                . 'id int auto_increment primary key,'
                . 'name text,'
                . 'content text,'
                . 'slug text,'
                . 'template text,'
                . 'type text,'
                . 'edited date,'
                . 'user text,'
                . 'position int,'
                . 'parent int,'
                . 'perm text,'
                . 'home int,'
                . 'display int'
        . ')' );
        mysql_query( 'insert into ' . $constants[ 'DB_TRASH' ] . ' values('
                . '0,'
                . '"Example Page",'
                . '"Sample page content.",'
                . '"Example-Page",'
                . '"Default",'
                . '"Normal",'
                . '"' . date( 'Y-m-d' ) . '",'
                . '"Installer",'
                . '1,'
                . '"",'
                . '"|",'
                . '1,'
                . '1'
        . ')' );

        /**
         * options table hold key value pairs
         * seperated by category. used as general
         * purpose database storage
         */
        mysql_query( 'drop table if exists ' . $constants[ 'DB_OPTIONS' ] );
        mysql_query( 'create table ' . $constants[ 'DB_OPTIONS' ] . ' ( '
                . 'name text,'
                . 'value text,'
                . 'category text'
        . ')' );

        /**
         * file manager
         *
         * @todo this should be changed, i don't like how these values are hard coded
         */
        mysql_query( 'drop table if exists ' . $constants[ 'DB_FILES' ] );
        mysql_query( 'create table ' .  $constants[ 'DB_FILES' ]  . ' ( id int auto_increment primary key, name text, path text, owner int, perm text, type text, public int, hash text )' );
        // file manager add initial directory structure
        mysql_query( 'insert into ' .  $constants[ 'DB_FILES' ]  . ' values( "", "users", "users", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )') ; 
        $fm_users_id = mysql_insert_id( );
        mysql_query( 'insert into ' .  $constants[ 'DB_FILES' ]  . ' values( "", "1", "users/1", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
        $fm_users_su_id = mysql_insert_id( );
        mysql_query( 'insert into ' .  $constants[ 'DB_FILES' ]  . ' values( "", "groups", "groups", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
        $fm_groups_id = mysql_insert_id( );
        mysql_query( 'insert into ' .  $constants[ 'DB_FILES' ]  . ' values( "", "_superuser", "groups/_superuser", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
        $fm_groups_su_id = mysql_insert_id( );

}

/**
 * install_dirs
 *
 * creates directories required by the running
 * system including user files
 * 
 * @return void
 */
function install_dirs( ){

        global $constants;
        global $user_id;
        $user_files = $constants[ 'USERS_DIR' ];

        // dirs to create
        $dirs = array(
                $user_files,
                // system dirs
                $user_files . 'cache',
                $user_files . 'backup',
                $user_files . 'smarty',
                $user_files . 'smarty/templates',
                $user_files . 'smarty/templates_c',
                
                // file manager dirs
                $user_files . 'files',
                $user_files . 'files/users',
                $user_files . 'files/users/' . $user_id,
                $user_files . 'files/groups',
                $user_files . 'files/groups/_superuser'
        );

        // create dirs
        $match = true;
        foreach( $dirs as $dir ){
                if( !is_dir( $dir ) )
                        mkdir( $dir ) || $match = false;
        }
        if( $match == false )
                die( 'could not create some of the required directories in the user dir. make sure your user dir is writable' );

}

/**
 * install_email
 *
 * sends an email to the new user
 *
 * @return void
 */
function install_email( ){

        global $constants;
        global $hash;

        // email headers
        $headers  = 'From: Support - Furasta.Org <support@furasta.org>'."\r\n".'Reply-To: support@furasta.org'."\r\n";
        $headers .= 'X-Mailer: PHP/' .phpversion()."\r\n";
        $headers .= 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\r\n";

        $subject = 'User Activation - Furasta.Org';
        $message = 'Hi ' . $_SESSION[ 'user' ][ 'name' ] . ',<br/>'
                . '<br/>'
                . 'Please activate your new user by clicking on the link below:<br/>'
                . '<br/>'
                . '<a href="' . $constants[ 'SITE_URL' ] . '/admin/users/activate.php?hash='
                . $hash . '">' . $constants[ 'SITE_URL' ] . '/admin/users/activate.php?hash='
                . $hash . '</a><br/>'
                . '<br/>'
                . 'If you are not the person stated above please ignore this email.<br/>';

        // send mail
        mail( $_SESSION[ 'user' ][ 'email' ], $subject, $message, $headers );

}

?>
