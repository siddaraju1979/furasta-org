<?php

/**
 * System Functions, Furasta.Org
 *
 * Contains system functions which can be expected
 * available at all times during execution, frontend
 * and admin area, ie system wide.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * furasta_autoload
 * 
 * Autoloads classes stored in the _inc/class directory
 *
 * @param mixed $class_name 
 * @access protected
 * @return bool
 */
function furasta_autoload( $class_name ){

	$file = HOME . '_inc/class/' . $class_name . '.php';

	if( file_exists( $file ) )
		return require_once HOME . '_inc/class/' . $class_name . '.php';

	return false;

}
	
/**
 * error
 *
 * Displays errors by creating a new template object
 * and loading the admin/layout/error.php template. 
 * 
 * @param mixed $error to be displayed
 * @param mixed $title of the error, optional
 * @param bool $report to generate a report or not
 * @todo error language
 * @access public
 * @return void
 */
function error( $error, $title = 'Error', $report = true ){

	/**
	 * enable language file errors, this should be phased in
	 * and eventually implemented in all possible situations
	 */
	if( is_int( $error ) ){
		$Template = Template::getInstance( );
		$error_id = $error;
		$error = $Template->errorToString( $error ); 
	}

	/**
	 * if ajax loaded then error output
	 * will be dfferent
	 */
	if( defined( 'AJAX_LOADED' ) ){
		echo '<span style="font-weight:bold">' . $title . '</span><br />' . $error;
		if( $report ){
			generate_error_report( @$error_id, $error, $title );
			if( User::verify( ) )
				echo '<br /><a href="' . SITE_URL . 'files/error-report.txt">Download Error Report</a>';
		}
		exit;	
	}

	$Template = Template::getInstance( true );
	$Template->add( 'content', '<h1>' . $title . '</h1>' );
	$Template->add( 'content', '<p>' . $error . '</p>' );
	$Template->add( 'title', 'Fatal Error' );
	if( $report ){
		generate_error_report( @$error_id, $error, $title );
		if( User::verify( ) )
			$Template->add( 'content', '<br/><p><a href="' . SITE_URL . 'files/error-report.txt">Download Error Report</a></p>' );
	}
	require HOME . 'admin/layout/error.php';
	exit;

}

/**
 * email 
 * 
 * Syestem email function. Sends html emails formatted
 * with a Furasta.Org template.
 * 
 * @param mixed $to , who the email should be send to
 * @param mixed $subject of the email
 * @param mixed $message , content of the email
 * @access public
 * @return bool
 */
function email( $to, $subject, $message ){


        /**
         * set up mail headers 
         */
        $headers='From: Support - Furasta.Org <support@furasta.org>'."\r\n".'Reply-To: support@furasta.org'."\r\n";
        $headers.='X-Mailer: PHP/' .phpversion()."\r\n";
        $headers.='MIME-Version: 1.0'."\r\n";
        $headers.='Content-Type: text/html; charset=ISO-8859-1'."\r\n";

	/**
	 * message template 
	 */
        $message='
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

	/**
	 * return success of mail function 
	 */
        return mail( $to, $subject, $message, $headers );

}

/**
 * htaccess_rewrite 
 * 
 * Rewrite the htaccess file, with support for
 * plugin access.
 *
 * @access public
 * @return bool
 */
function htaccess_rewrite(){

        global $SETTINGS;

        require_once HOME . '_inc/function/defaults.php';

	$Plugins = Plugins::getInstance( );

	/**
	 * if the apache_get_modules function is
	 * available, make sure that the apache
	 * rewrite module is loaded
	 */
	if( function_exists( 'apache_get_modules' ) ){
		$modules=apache_get_modules();

		if(!in_array('mod_rewrite',$modules))
			error(
				'The apache module mod_rewrite must be installed. Please visit <a href="http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html">'
				. 'Apache Module mod_rewrite</a> for more details.'
				,'Apache Error'
			);
	}

	/**
	 * build up array of rewrite rules and filter
	 * through the plugin filter
	 */
        $rules = defaults_htaccess_rules( SITE_URL );
        
	$rules = $Plugins->filter( 'general', 'filter_htaccess', $rules );

        $htaccess = defaults_htaccess_content( $rules );

	/**
	 * write htaccess file or throw error
	 */
	file_put_contents( HOME . '.htaccess', $htaccess ) 
		or error(
			'You must grant <i>0777</i> write access to the <i>' . HOME . 
			'</i> directory for <a href="http://furasta.org">Furasta.Org</a> to function correctly.' .
			'Please do so then reload this page to save the settings.'
			,'Runtime Error'
		);

	if( $SETTINGS[ 'index' ] == 0 ){
                $robots = defaults_robots_content( 0, SITE_URL );

		$robots = $Plugins->filter( 'general', 'filter_robots', $robots );
	}
        else{
                $robots = defaults_robots_content( 1, SITE_URL );
                $file=HOME.'sitemap.xml';
                if(file_exists($file))
                        unlink($file);

	}

	/**
	 * write robots.txt or throw error
	 */
	file_put_contents( HOME. 'robots.txt', $robots );

	return true;
}

/**
 * settings_rewrite 
 * 
 * Used to change any of the variables written in the
 * settings.php file, executes htaccess_rewrite also as
 * it is possible within settings_rewrite that the active
 * plugins are changed.
 *
 * @param mixed $SETTINGS
 * @param mixed $DB
 * @param mixed $PLUGINS
 * @param mixed $constants optional
 * @access public
 * @return void
 */
function settings_rewrite( $SETTINGS, $DB, $PLUGINS, $constants = array( ) ){

        require_once HOME . '_inc/function/defaults.php';

        $default_constants = defaults_constants( $constants );

        $Plugins = Plugins::getInstance( );

	/**
	 * calculate plugin dependencies
	 */
	$plugins = array( );
	foreach( $PLUGINS as $p_name => $version ){

		$plugin = $Plugins->plugins( $p_name );
		if( !$plugin )
			require HOME . '_plugins/' . $p_name . '/plugin.php';	

		array_push(
			$plugins,
			array(
				'sys_name' => $p_name,
				'name' => $plugin[ 'name' ],
				'version' => $plugin[ 'version' ],
				'dependencies' => @$plugin[ 'dependencies' ]
			)
		);
	}
        $PLUGINS = resolve_dependencies( $plugins, $PLUGINS );

	/**
         * plugins - filter the settings, constants and plugins arrays 
         */
        $filter = $Plugins->filter( 'general', 'filter_settings', array( $SETTINGS, $constants, $PLUGINS ) );
	$SETTINGS = $filter[ 0 ];
	$constants = $filter[ 1 ];
	$PLUGINS = $filter[ 2 ];

        $filecontents = defaults_settings_content( $SETTINGS, $DB, $PLUGINS, $constants );

	/**
	 * write file or throw error
	 */
	file_put_contents( HOME . '.settings.php', $filecontents )
		or error(
			'You must grant <i>0777</i> write access to the <i>' . HOME . 
			'</i> directory for <a href="http://furasta.org">Furasta.Org</a> to function correctly.' .
			'Please do so then reload this page to save the settings.'
			,'Runtime Error'
		);

	return htaccess_rewrite( );

}

/**
 * remove_dir 
 *
 * Deletes all files and directories contained in a
 * dir, then deletes the dir itself.
 * 
 * @param mixed $dir to be deleted
 * @access public
 * @return bool
 */
function remove_dir($dir){
	if( !is_dir( $dir ) )
		return false;

	$objects=scandir($dir);
	foreach($objects as $object){
		if($object!='.'&&$object!='..'){
			if(filetype($dir.'/'.$object)=='dir')
				remove_dir($dir.'/'.$object);
			else
				unlink($dir.'/'.$object);
		}
	}
	reset($objects);
	return rmdir($dir);
} 

/**
 * scan_dir 
 * 
 * Scans a dir for subdirs. Returns an
 * array of dirs, files and hidden
 * directories are excluded.
 *
 * @param mixed $dir to be scanned
 * @access public
 * @return array
 */
function scan_dir($dir){
	if( !is_dir( $dir ) )
		return false;

	$files=scandir($dir);

	$dirs=array();
	foreach($files as $file){
		if($file=='.'||$file=='..'||substr($file,0,1)=='.')
			continue;
		if(is_dir($dir.'/'.$file))
			array_push($dirs,$file);
	}

	return $dirs;
}

/**
 * rss_fetch 
 * 
 * Retrieves rss feed of $url provided in an array. Can
 * choose number of array items to fetch, and to start at
 * a certain number.
 *
 * @param mixed $url to fetch rss feed from 
 * @param string $tagname , optional "item" is the default
 * @access public
 * @return array or bool false
 */
function rss_fetch( $url, $tagname = 'item' ){

	$dom = new DOMdocument( );

	$success = $dom->load( $url );
	if( !$success )
		return false;

	$elements = $dom->getElementsByTagName( $tagname );
	$items = array( );

	foreach( $elements as $element ){
		$item = array( );

		if( $element->childNodes->length ){
			foreach( $element->childNodes as $node ){
				$item[ $node->nodeName ] = $node->nodeValue;
			}
			$items[ ] = $item;
		}
	}

	return $items;
}

/**
 * validate 
 * 
 * Used to validate forms both with javascript and PHP.
 * Uses the Validate class for php validation and the
 * jQuery Form Validation Plugin for javascript validation.
 *
 * The function returns true if validation has succeeded or
 * the form wasn't submitted. Returns false if there has
 * been an error.
 *
 * The $conds parameter is an array of inputs to be validated,
 * and certain conditions which must be met for validation on
 * that input to succeed. For documentation of all these
 * parameters see the jQuery Form Validation Plugin located at
 * _inc/js/jquery/validate.js
 *
 * See below for an example usage of the function:
 *
 * $conds = array(
 * 	'input-name' => array(
 * 		'name'		=> 'Text Input',
 *		'required'	=> true,
 *		'minlength'	=> 2
 *	)
 * );
 *
 * The above adds one input named "Name" for validation. The
 * input is required (must be non-empty) and must have a minimum
 * length of 2 characters. The name is used for alerts or warnings
 * when validation fails.
 *
 * $valid = validate( $conds, "#form-id", 'submit-name' );
 *
 * The above adds form validation to the form with an id "form-id"
 * (prefixed with a hash, it's essentially a CSS selector), with
 * a submit input of the name "submit-name" and with the above
 * conditions.
 *
 * The form validation can then be checked using the following:
 *
 * 
 * if( $valid ){
 * 	// form validation successful
 * }
 *
 * The validation plugin uses the Template class for displaying
 * errors. To display errors with the template class simply
 * create an instance and call the display errors method:
 *
 * else{
 *	$Template::getInstance( );
 * 	$Template->displayErrors( );
 * }
 *
 * @param array $conds 
 * @param string $selector 
 * @param string $post 
 * @access public
 * @return bool
 */
function validate($conds,$selector,$post){

        if( !isset( $_POST[ $post ] ) )
                return true;

        $Validate = Validate::getInstance( );

	if( !$Validate->hasConds ){

		/**
		 * set up javascript validation 
		 */
	        $javascript = '
		$( document ).ready( function( ){
			$( "' . $selector . '" ).validate( ' . json_encode( $conds ) . ' );
		});';

		if( defined( 'FURASTA_FRONTEND' ) ){
			echo '<script type="text/javascript">' . $javascript . '</script>';
		}
		else{
			$Template = Template::getInstance( );
			$Template->add( 'javascript', $javascript );
		}
	}

	$Validate->addConds( $conds );

	return $Validate->execute( );
	
}

/**
 * stripslashes_array 
 * 
 * permforms the stripslashes function on an array
 *
 * @param array $value 
 * @access public
 * @return array
 */
function stripslashes_array( $value ){
	$value = is_array( $value ) ? array_map( 'stripslashes_array', $value ) : stripslashes( $value );

	return $value;
}

/**
 * addslashes_array
 *
 * performs the addslashes function on an array
 * 
 * @param array $value 
 * @return array
 */
function addslashes_array( $value ){
	$value = is_array( $value ) ? array_map( 'addslashes_array', $value ) : addslashes( $value );

	return $value;
}

/**
 * meta_keywords
 *
 * returns a string of imploded keywords for use
 * in a meta keywords tag  
 * 
 * @param mixed $string 
 * @access public
 * @return string
 */
function meta_keywords( $string ){
	$stop_words = array(
		'i', 'a', 'about', 'an', 'and', 'are',
		'as', 'at', 'be', 'by', 'com', 'de',
		'en', 'for', 'from', 'how', 'in', 'is',
		'it', 'la', 'of', 'on', 'or', 'that', 'the',
		'this', 'to', 'was', 'what', 'when', 'where',
		'who', 'will', 'with', 'und', 'the', 'www'
	);
 
	$string = preg_replace( '/ss+/i', '', $string );
	$string = trim( $string );

	/**
	 * only accept alphanumerical characters
	 * but keep the spaces and dashes
	 */
	$string = preg_replace( '/[^a-zA-Z0-9 -]/', '', $string );

	/**
	 * convert to lower case
	 */
	$string = strtolower( $string );
 
	preg_match_all( '/([a-z]*?)(?=s)/i', $string, $matches );

	$matches = $matches[0];

	foreach ( $matches as $key=>$item ) {
		if ( $item == '' || in_array( strtolower( $item ), $stop_words ) || strlen( $item ) <= 3 )
			unset( $matches[ $key ] );
	}


	$word_count = array();
	if ( is_array( $matches ) ) {
		foreach ( $matches as $key => $val ) {
			$val = strtolower( $val );
			if ( isset( $word_count[ $val ] ) )
				$word_count[ $val ]++;
			else
				$word_count[ $val ] = 1;
		}
    	}

	arsort( $word_count );
	$word_count = array_slice( $word_count, 0, 10 );
	$word_count = implode( ',', array_keys( $word_count ) );

	return $word_count;
}

function generate_error_report( $error_id, $error, $name ){
	$mysql = ( mysql_ping( ) == true ) ? 'active' : 'not active';	
	global $PLUGINS;

	$report = '
	# Furasta.Org Version ' . VERSION . ' Error Report
	error_id : ' . $error_id . '
	error : ' . strip_tags( $error ) . '
	error_title : ' . $name . '

	# system info
	plugins installed : ' . implode( ',', $PLUGINS ) . '

	# mysql status
	mysql : ' . $mysql . '
	';

	file_put_contents( USERS_FILES . 'files/error-report.txt', $report );	
}

/**
 * validate_file
 *
 * checks if file exists and also if file
 * name is safe
 *
 * @param string $file
 * @return bool
 */
function validate_file( $file ){
	if( strpos( $file, '..' ) !== false )
		return false;

	$file = HOME . $file;

	if( !file_exists( $file ) )
		return false;

	return true;
}

/**
 * maintenance_message
 *
 * displays the maintenance mode error
 *
 * @return void
 */
function maintenance_message( ){
	$message = single( 'select value from ' . DB_OPTIONS . ' where category="configuration_page_options" and name="maintenance_message"', 'value' );
	if( empty( $message ) ){
		global $SETTINGS;
		$message = $SETTINGS[ 'site_title' ] . ' is currently undergoing maintenance. Please try again later.';
	}
	error( $message, '' );
}

/**
 * split_perm
 *
 * this function splits permission strings in the
 * form: 1,2,3#4|1,2,3#4,5 to an array of form:
 *
 * array(
 * 	0 => array(
 *		'users' => array( 1, 2, 3 ),
 *		'groups' => array( 4 )	
 *	),
 *	1 => array(
 *		'users' => array( 1, 2, 3 ),
 *		'groups' => array( 4, 5 )
 *	),
 * )
 *
 * @param string $perms
 * @access public
 * @return array
 */
function split_perm( $perms ){
	// setup array format
	$result = array(
		0 => array(
			'users' => array( ),
			'groups' => array( )
		),
		1 => array(
			'users' => array( ),
			'groups' => array( )
		)
	);

	// if no "|" symbol, can't process
	if( strpos( $perms, '|' ) === false )
		return $result;

	$perms = explode( $perms, '|' );

	foreach( $perms as $i => $perm ){
		if( strpos( $perm, '#' ) === false ){
			$result[ $i ][ 'users' ] = explode( ',', $perm );
		}
		else{
			$permissions = explode( $perm, '#' );
			$result[ $i ][ 'users' ] = explode( ',', $perm[ 0 ] );
			$result[ $i ][ 'groups' ] = explode( ',', $perm[ 1 ] );
		}
	}

	return $result;
}

/**
 * merge_perm
 *
 * mergers an array of permissions in the following
 * format:
 *
 * array(
 * 	0 => array(
 *		'users' => array( 1, 2, 3 ),
 *		'groups' => array( 4 )	
 *	),
 *	1 => array(
 *		'users' => array( 1, 2, 3 ),
 *		'groups' => array( 4, 5 )
 *	),
 * )
 *
 * @param array $one
 * @param array $two
 * @access public
 * @return array
 */
function merge_perm( $one, $two ){
	if( empty( $one[ 0 ][ 'users' ] ) || empty( $two[ 0 ][ 'users' ] ) )
		return $one;

	$one[ 0 ][ 'users' ] = array_merge( $one[ 0 ][ 'users' ], $two[ 0 ][ 'users' ] );
	$one[ 0 ][ 'groups' ] = array_merge( $one[ 0 ][ 'groups' ], $two[ 0 ][ 'groups' ] );
	$one[ 1 ][ 'users' ] = array_merge( $one[ 1 ][ 'users' ], $two[ 1 ][ 'users' ] );
	$one[ 1 ][ 'groups' ] = array_merge( $one[ 1 ][ 'groups' ], $two[ 1 ][ 'groups' ] );
	return $one;
}

/**
 * resolve_dependencies
 *
 * Takes an array of plugin data and adds
 * plugins to the list which are dependencies
 * of other plugins
 *
 * This function is run when the settings.php file
 * is rewritten and automatically adds dependencies
 * to the list of installed plugins
 *
 * @param array $plugins
 * @param array $NEWPLUGINS
 * @return array
 */
function resolve_dependencies( $plugins, $NEWPLUGINS ){

	global $PLUGINS;

	/**
	 * build list of dependencies
	 */
	$dependencies = array( );
	$resolved = array( );
	$Plugins = Plugins::getInstance( );
	for( $i = 0; $i < count( $plugins ); ++$i ){

		if( is_array( $plugins[ $i ][ 'dependencies' ] ) ){

			array_merge( $dependencies, $plugins[ $i ][ 'dependencies' ] );

			foreach( $plugins[ $i ][ 'dependencies' ] as $dependency ){

				/**
				 * if dependency was already resolved, skip
				 */
				if( in_array( $dependency, $dependencies ) )
					 continue;

				/**
				 * if plugin is being removed and is still a dependency
				 * of another plugin, throw error
				 */
				if( isset( $PLUGINS[ $dependency ] ) && !isset( $NEWPLUGINS[ $dependency ] ) ){
					error( 	
						'The "' . $dependency . '" plugin cannot be uninstall as it is a dependency'
						. 'of the "' . $plugins[ $i ][ 'name' ] . '" plugin. To uninstall, remove'
						. 'the "' . $plugins[ $i ][ 'name' ] . '" plugin first.'
						,'Plugin Dependency Error'
					);
				}


				/**
				 * if plugin is unavailable then throw an error
				 */
				if( !plugin_exists( $dependency ) ){
					error(
						'The plugin "' . $plugins[ $i ][ 'name' ] . '" requires the "' . $dependency . '"'
						. ' plugin to be installed, however it is not available. In order to use'
						. ' this plugin you will have to download and install the dependency',
						'Plugin Dependency Unavailable'
					);
				}


				$plugin = $Plugins->plugins( $p_name );
				if( !$plugin )
					require HOME . '_plugins/' . $p_name . '/plugin.php';	

				/**
				 * add dependency to list of plugins
				 */
				$NEWPLUGINS[ $dependency ] = $plugin[ 'version' ];
			}

		}

		$NEWPLUGINS[ $plugins[ $i ][ 'sys_name' ] ] = $plugins[ $i ][ 'version' ];
	}

        return $NEWPLUGINS;

}

/**
 * tinymce
 *
 * creates an instance of the tinymce text editor
 * manages all javascript etc
 *
 * @param string $name
 * @param string $content
 * @param string $config optional
 * @return string
 */
function tinymce( $name, $content, $config = 'Normal' ){

	// add tinymce load to javascript
	$javascript = '
	$(function(){
		tinymce_changeConfig( \'textarea[name="' . $name . '"]\', "' . $config . '" );
	});
	';
	if( defined( 'FURASTA_FRONTEND' ) ){
		echo '<script type="text/javascript">' . $javascript . '</script>';
	}
	else{
		$Template = Template::getInstance( );
		$Template->add( 'javascript', $javascript );
	}

	// return textarea
	$content = '<textarea class="tinymce" name="' . $name . '" id="tinymce_' .$name . '">' . $content . '</textarea>';
	return $content;
}

/**
 * empty_array
 *
 * checks if there are empty elements in an array
 *
 * @param array $array
 * @return bool
 */
function empty_array( $array ){

	if( empty( $array ) )
		return false;

	foreach( $array as $element ){
		if( empty( $element ) )
			return true;
	}

	return false;
}
?>
