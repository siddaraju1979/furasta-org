<?php

/**
 * System Defaults, Furasta.Org
 *
 * These functions return system default values. They are used
 * by both the installer and the running system so they are
 * completley self-contained
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * defaults_htaccess_rules
 *
 * returns the default htaccess rules
 *
 * @param string $siteurl
 * @return array
 */
function defaults_htaccess_rules( $site_url ){
        return array(
		'admin'         => 'RewriteRule ^admin[/]*$ ' . $site_url . 'admin/index.php [L]',
		'sitemap'       => 'RewriteRule ^sitemap.xml ' . $site_url . '_inc/sitemap.php [L]',
		'files'         => 'RewriteRule ^files/(.*)$ ' . $site_url . '_inc/files.php?name=$1 [QSA,L]',
		'pages'         => 'RewriteRule ^([^./]{3}[^.]*)$ ' . $site_url . 'index.php?page=$1 [QSA,L]',
	);
}

/**
 * defaults_htaccess_contents
 *
 * returns the default htaccess file content
 *
 * @param array $rules
 * @return string
 */
function defaults_htaccess_content( $rules ){

	$htaccess=
		"# .htaccess - Furasta.Org\n".
		"<IfModule mod_deflate.c>\n".
        	"	SetOutputFilter DEFLATE\n".
		"	Header append Vary User-Agent env=!dont-vary\n".
        	"</IfModule>\n\n".

        	"php_flag magic_quotes_gpc off\n\n".

		"RewriteEngine on\n".
		"RewriteCond %{SCRIPT_NAME} !\.php\n";

	foreach( $rules as $rule ){
		$htaccess .= $rule . "\n";
	}

	$htaccess .=
		"\nAddCharset utf-8 .js\n".
		"AddCharset utf-8 .xml\n".
		"AddCharset utf-8 .css\n".
                "AddCharset utf-8 .php";
        return $htaccess;
}

/**
 * defaults_robots_content
 *
 * returns the default content for the
 * robots.txt file, depending on the index
 * setting being enabled
 *
 * @param int $index
 * @param string $site_url
 * @return string
 */
function defaults_robots_content( $index, $site_url ){

        if( $index ){
                return
                "# robots.txt - Furasta.Org\n".
                "User-agent: *\n".
                "Disallow: " . $site_url . "\n";
        }
        else{
                return
		"# robots.txt - Furasta.Org\n".
		"User-agent: *\n".
		"Disallow: " . $site_url . "admin\n".
		"Disallow: " . $site_url . "install\n".
		"Disallow: " . $site_url . "_user\n".
		"Sitemap: " . $site_url . "sitemap.xml";
        }

}

/**
 * defaults_constants
 *
 * returns an array of the default constants,
 * sets them to their currently defined values
 * if defined and merges with the constants
 * parameter
 *
 * @param array $constants
 * @return array
 */
function defaults_constants( $constants ){

        /**
         * note: no constants should be added here without
         * also adding to the installer in install/complete.php
         */
	$defaults = array(
		'TEMPLATE_DIR'          => defined( 'TEMPLATE_DIR' )    ? TEMPLATE_DIR          : '',
		'VERSION'               => defined( 'VERSION' )         ? VERSION               : '',
		'DB_PREFIX'                => defined( 'DB_PREFIX' )          ? DB_PREFIX                : '',
		'DB_PAGES'                 => defined( 'DB_PAGES' )           ? DB_PAGES                 : '',
		'DB_USERS'                 => defined( 'DB_USERS' )           ? DB_USERS                 : '',
		'DB_TRASH'                 => defined( 'DB_TRASH' )           ? DB_TRASH                 : '',
		'DB_GROUPS'                => defined( 'DB_GROUPS' )          ? DB_GROUPS                : '',
		'DB_USERS_GROUPS'          => defined( 'DB_USERS_GROUPS' )    ? DB_USERS_GROUPS          : '',
		'DB_FILES'                 => defined( 'DB_FILES' )           ? DB_FILES                 : '',
		'DB_OPTIONS'               => defined( 'DB_OPTIONS' )         ? DB_OPTIONS               : '',
		'SITE_URL'               => defined( 'SITE_URL' )         ? SITE_URL               : '',
                'USERS_DIR'            => defined( 'USERS_DIR' )      ? USERS_DIR            : '',
		'DIAGNOSTIC_MODE'       => defined( 'DIAGNOSTIC_MODE' ) ? DIAGNOSTIC_MODE       : '',
		'LANG'                  => defined( 'LANG' )            ? LANG                  : '',
		'SET_REVISION'          => defined( 'REVISION' )        ? REVISION              : '',
        );

	return array_merge( $defaults, $constants );

}

/**
 * defaults_settings_content
 *
 * returns the default layout of the settings
 * file, with the various settings passed as
 * arguments
 *
 * @param array $SETTINGS
 * @param array $DB
 * @param array $PLUGINS
 * @param array $constants
 * @return string
 */
function defaults_settings_content( $SETTINGS, $DB, $PLUGINS, $constants ){

       $filecontents = "<?php \n\n"
                . "/**\n"
                . " * Settings File, Furasta.Org\n"
                . " * \n"
                . " * Contains settings created by the installer.\n"
                . " * This file is also altered during the running\n"
                . " * system.\n"
                . " * \n"
                . " * @author\tInstaller\n"
                . " * @version\t1.0\n"
                . " */\n\n";

	foreach( $constants as $constant => $value )
		$filecontents .= "define( '" . $constant . "', '" . $value . "' );\n"; 

	$filecontents .= "\n" .
			'$PLUGINS' . " = array( \n";


	foreach( $PLUGINS as $plugin => $version )
		$filecontents .= "\t'" . $plugin . "' => '" . $version . "',\n";

	$filecontents .= ");\n\n" .
			'$SETTINGS' . " = array( \n";

	/**
	 * print settings array, two layer deep array
	 */
	foreach( $SETTINGS as $setting => $value ){
		if( is_array( $value ) ){
			$filecontents .= "\t'" . $setting . "' => array(\n";
			foreach( $value as $s => $v )
				$filecontents .= "\t\t" . $s . "' => '" . $v . "',\n";
			$filecontents .= "\t),\n";
			continue;
		}
		$filecontents .= "\t'" . $setting . "' => '" . addslashes( $value ) . "',\n";
	}

	$filecontents .= "\n\n" 
                        . ');' . "\n"
			. '$DB = array(' . "\n"
			. "\t'name'\t=>\t'" . $DB[ 'name' ] . "',\n" 
			. "\t'host'\t=>\t'" . $DB[ 'host' ] . "',\n" 
			. "\t'user'\t=>\t'" . $DB[ 'user' ] . "',\n" 
			. "\t'pass'\t=>\t'" . $DB[ 'pass' ] . "'\n" 
			. ');' . "\n";

        return $filecontents;
}

/**
 * defaults_email_template
 *
 * returns the default furasta email template, given the
 * message as a parameter
 *
 * @param string $message
 * @return string
 */
function defaults_email_template( $message ){

        return '
        <div style="margin:0;text-align:center;background:#eee;font:1em Myriad Pro,Arial,Sans Serif;color:#333;border:1px solid transparent">
        <div id="container" style="width:92%;background:#fff;border:1px solid #999;margin:20px auto 0 auto">                <div id="container-top" style="border-top:1px solid transparent">
                        <div id="header" style="margin:25px auto 0 auto;height:100px;width:92%;background:#2a2a2a">                                <img style="float:left;max-width:100%" src="http://files.furasta.org/images/email/email-header-logo.png"/>                        </div>
                </div>
                <div id="container-right" style="border-top:1px solid transparent;border-bottom:1px solid transparent">
                        <div id="main" style="text-align:left;margin:25px auto;width:92%;border-top:1px solid transparent">
                               <div id="right">

                                        ' . $message . '
                                        <br/>
                                        Thanks <br/>
                                        --- <br/>                                        Furasta.Org <br/>                                        <a style="text-decoration:none" href="http://furasta.org">http://furasta.org</a> <br/>
                                        <a style="text-decoration:none" href="mailto:support@furasta.org">support@furasta.org</a> <br/>
                                        <br/>
                                        <table>
                                                <tr>                                                        <th colspan="2">Need help working the CMS?</th>
                                                        <th colspan="2">Want to report a bug?</th>                                                </tr>                                                <tr>
                                                        <td><img src="http://files.furasta.org/images/email/email-footer-forum.jpg"></td>                                                        <td><a href="http://forum.furasta.org">http://forum.furasta.org</a><p>Visit the forum where you can ask questions about any aspect of the system. You can even add feature requests!</p></td>
                                                        <td><img src="http://files.furasta.org/images/email/email-footer-bugs.jpg"></td>
                                                        <td><a href="http://bugs.furasta.org">http://bugs.furasta.org</a><p>Using the Bug Tracker you can log a bug and it will be 
completed depending on its priority.</p></td>
                                                <tr>
                                        </table>                                </div>                        </div>
                </div>
        </div>
        <div id="bottom" style="width:92%;margin:0 auto;text-align:left">
                <p style="float:left;color:#050;font-size:13px">&copy; <a href="http://furasta.org" style="text-decoration:none;color:#050;font-size:13px">Furasta.Org</a> | <a href="http://forum.furasta.org" style="text-decoration:none;color:#050;font-size:13px">Forum</a> | <a href="http://bugs.furasta.org" style="text-decoration:none;color:#050;font-size:13px">Bug Tracker</a></p>
                <br style="clear:both"/>
        </div>
        </div>
        ';
}
