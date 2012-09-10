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
		'PREFIX'                => defined( 'PREFIX' )          ? PREFIX                : '',
		'PAGES'                 => defined( 'PAGES' )           ? PAGES                 : '',
		'USERS'                 => defined( 'USERS' )           ? USERS                 : '',
		'TRASH'                 => defined( 'TRASH' )           ? TRASH                 : '',
		'GROUPS'                => defined( 'GROUPS' )          ? GROUPS                : '',
		'USERS_GROUPS'          => defined( 'USERS_GROUPS' )    ? USERS_GROUPS          : '',
		'FILES'                 => defined( 'FILES' )           ? FILES                 : '',
		'OPTIONS'               => defined( 'OPTIONS' )         ? OPTIONS               : '',
		'SITEURL'               => defined( 'SITEURL' )         ? SITEURL               : '',
		'USER_FILES'            => defined( 'USER_FILES' )      ? USER_FILES            : '',
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

	$filecontents = '<?php' . "\n" .
			'# Furasta.Org - .settings.php' . "\n\n";

	foreach( $constants as $constant => $value )
		$filecontents .= 'define( \'' . $constant . '\', \'' . $value . '\' );' . "\n"; 

	$filecontents .= "\n" .
			'$PLUGINS = array(' . "\n";


	foreach( $PLUGINS as $plugin => $version )
		$filecontents .= "\t" . '\'' . $plugin . '\' => \'' . $version . '\',' . "\n";

	$filecontents .= ');' . "\n" . "\n" .
			'$SETTINGS = array(' . "\n";

	/**
	 * print settings array, two layer deep array
	 */
	foreach( $SETTINGS as $setting => $value ){
		if( is_array( $value ) ){
			$filecontents .= "\t" . '\'' . $setting . '\' => array(' . "\n";
			foreach( $value as $s => $v )
				$filecontents .= '		\'' . $s . '\' => \'' . $v . '\',' . "\n";
			$filecontents .= "\t" . '),' . "\n";
			continue;
		}
		$filecontents .= "\t" . '\'' . $setting . '\' => \'' . addslashes( $value ) . '\',' . "\n";
	}

	$filecontents .= "\n" 
			. ');' . "\n"
			. '$DB = array(' . "\n"
			. "\t" . '\'name\' => \'' . $DB[ 'name' ] . '\',' . "\n" 
			. "\t" . '\'host\' => \'' . $DB[ 'host' ] . '\',' . "\n" 
			. "\t" . '\'user\' => \'' . $DB[ 'user' ] . '\',' . "\n" 
			. "\t" . '\'pass\' => \'' . $DB[ 'pass' ] . '\'' . "\n" 
			. ');' . "\n";

        return $filecontents;
}
