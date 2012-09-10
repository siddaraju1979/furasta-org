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

function install_db( $settings, $constants ){

        // @todo update this segment
        $pagecontent='
        <h1>Welcome to Furasta.Org</h1>
        <p>Welcome to your new installation of Furasta.Org Content Management System.</p>
        <p>To log in to the administration area <a href=\'' . $constants[ 'SITE_URL' ] . '/admin\'>Click Here</a></p>
        ';

        // pages
        mysql_query('drop table if exists '.$constants[ 'PAGES' ]);
        mysql_query('create table '.$constants['PAGES'].' (id int auto_increment primary key,name text,content text,slug text,template text,type text,edited date,user text,position int,parent int,perm text, home int,display int)');
        mysql_query('insert into '.$constants['PAGES'].' values(0,"Home","'.$pagecontent.'","Home","Default","Normal","'.date('Y-m-d').'","Installer",1,0,"|",1,1)');

        // user
        mysql_query('drop table if exists '.$constants['USERS']);
        mysql_query('create table '.$constants['USERS'].' (id int auto_increment primary key,name text,email text,password text,hash text,reminder text, data text)');
        mysql_query('insert into '.$constants['USERS'].' values(0,"'.$_SESSION['user']['name'].'","'.$_SESSION['user']['email'].'","'.$_SESSION['user']['pass'].'","'.$hash.'","","")');
        $user_id = mysql_insert_id( );

        // groups
        mysql_query( 'drop table if exists ' . $groups );
        mysql_query( 'create table ' . $groups . ' ( id int auto_increment primary key, name text, perm text, file_perm text )' );
        mysql_query( 'insert into ' . $groups . ' values ( "", "Users", "a,e,c,d,t,o,s,u,f", "|" )' );

        // users_groups
        mysql_query( 'drop table if exists ' . $constants['USERS_GROUPS'] );
        mysql_query( 'create table ' . $constants['USERS_GROUPS'] . ' ( user_id int, group_id text )' );
        mysql_query( 'insert into ' . $constants['USERS_GROUPS'] . ' values ( 1, "_superuser" )' );

        // trash
        mysql_query('drop table if exists '.$constants['TRASH']);
        mysql_query('create table '.$constants['TRASH'].' (id int auto_increment primary key,name text,content text,slug text,template text,type text,edited date,user text,position int,parent int,perm text,home int,display int)');
        mysql_query('insert into '.$constants['TRASH'].' values(0,"Example Page","Sample page content.","Example-Page","Default","Normal","'.date('Y-m-d').'","Installer",1,"","|",1,1)');

        // options
        mysql_query( 'drop table if exists ' . $options );
        mysql_query( 'create table ' . $options . ' (name text,value text,category text)' );

        // file manager
        // @todo this should be changed, i don't like how these values are hard coded
        mysql_query( 'drop table if exists ' . $files );
        mysql_query( 'create table ' . $files . ' ( id int auto_increment primary key, name text, path text, owner int, perm text, type text, public int, hash text )' );
        // file manager add initial directory structure
        mysql_query( 'insert into ' . $files . ' values( "", "users", "users/", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )') ; 
        $fm_users_id = mysql_insert_id( );
        mysql_query( 'insert into ' . $files . ' values( "", "1", "users/1/", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
        $fm_users_su_id = mysql_insert_id( );
        mysql_query( 'insert into ' . $files . ' values( "", "groups", "groups/", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
        $fm_groups_id = mysql_insert_id( );
        mysql_query( 'insert into ' . $files . ' values( "", "_superuser", "groups/_superuser/", "0", "", "dir", 0, "' . md5( mt_rand( ) ) . '" )' );
        $fm_groups_su_id = mysql_insert_id( );

}
