<?php

/**
 * Template Class, Furasta.Org
 *
 * This file contains a class which temporarily holds information
 * to be displayed in the admin area templates located in
 * admin/layout/
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_temnplate
 */

/**
 * Template
 *
 * The Template class manages all output in the
 * admin area. Instead of outputing in a procedural
 * way, this class stores all output as it is 
 * registered and then outputs it all at the end of
 * execution. The class also compresses the outputed
 * content.
 *
 * USING THE CLASS
 *
 * There are different areas of the template that
 * you can output code to:
 *
 * title - the title of the page
 * menu - the menu of the page
 * content - the main content area of the page
 *
 * You can add output to these areas like so:
 *
 * $Template->add( 'title', 'Admin Page Name' );
 *
 * This class is used so that at any stage of
 * execution you can add output to any part of
 * the template, rather than doing everything
 * strictly proceduarily.
 *
 * As well as handling html this class handles CSS
 * and javascript. It packs javascript, compresses
 * CSS and caches it.
 *
 * CSS VARIABLES
 *
 * This class currently only makes one variable
 * available in CSS but this may be expanded on
 * in the future.
 *
 * %SITE_URL%
 * to load images properly the %SITE_URL% var
 * should be used. The CSS will be parsed by
 * this class and that var will be replaced
 * with the value of the site url if present.
 * 
 * 
 * @package admin_template
 * @author Conor Mac Aoidh <conormacaoidh@gmail.com> 
 * @license http://furasta.org/licence.txt The BSD License
 */
class Template {
	/**
	 * instance
	 * 
	 * holds the Template instance
	 *
	 * @var object
	 * @static
	 * @access public
	 */
	private static $instance;

	/**
	 * diagnosticMode
	 * 
	 * re-creates the javascript cache without compression
	 * for diagnostic reasons
	 *
	 * @var mixed
	 * @access public
	 */
	public $diagnosticMode = DIAGNOSTIC_MODE;
	
        /**
         * title 
         * 
         * @var string
         * @access public
         */
        public $title = '';


	/**
	 * head 
	 * 
	 * @var string
	 * @access public
	 */
	public $head = '';

	/**
	 *  runtineError
	 *
	 * @var array
	 * @access public
	 */
	public $runtimeError = array( );

        /**
         * menu 
         * 
         * @var string
         * @access public
         */
        public $menu='';

        /**
         * content 
         * 
         * @var string
         * @access public
         */
        public $content='';

	/**
	 * javascript 
	 * 
	 * used to store page specific javascript
	 *
	 * @var string
	 * @access public
	 */
	public $javascript='';

        /**
         * javascriptFiles
         * 
         * @var array
         * @access public
         */
        public $javascriptFiles=array();

        /**
         * javascriptSources
         * 
         * @var array
         * @access public
         */
        public $javascriptSources=array();

        /**
         * cssFiles
         * 
         * @var array
         * @access public
         */
        public $cssFiles=array();

	/**
	 * cssSources
	 *
	 * @var array
	 * @access public
	 */
	public $cssSources=array();

	/**
	 * lang_errors
	 *
	 * @var array
	 * @access public
	 */
	public $lang_errors = array( );

	/**
	 * lang
	 *
	 * @var array
	 * @access public
	 */
	public $lang = array( );

	/**
	 * __construct
	 * 
	 * loads up the language files, filters them through
	 * plugin language files
	 * 
	 * @access public
	 */
	public function __construct( ){
		// include language file
		require_once HOME . 'admin/lang/' . LANG . '.php';

		// filter through plugins
		$Plugins = Plugins::getInstance( );
		$lang_errors = $Plugins->filter( 'admin', 'filter_lang_errors', $lang_errors );
		$lang = $Plugins->filter( 'admin', 'filter_lang', $lang );

		$this->lang = $lang;
		$this->lang_errors = $lang_errors;

	}

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

			self::$instance = new Template();
			return self::$instance;
		}

		/**
		 *  checks if an instance exists
		 */
		if(!self::$instance)
			self::$instance = new Template();

		return self::$instance;
	}

	/**
	 * add 
	 * 
	 * @param string $type 
	 * @param string $content 
	 * @access public
	 * @return bool
	 */
	public function add( $type, $content ){

		/**
		 * if var is not set return false
 		 */
		if( !isset( $this->$type ) )
			return false;		

		return ( $this->$type .= $content );
	}

	/**
	 * display 
	 * 
	 * @param string $type 
	 * @access public
	 * @return string or bool false
	 */
	public function display( $type ){

                /**
                 * if var is not set return false
                 */
                if( !isset( $this->$type ) )
                        return false;

                return $this->$type;
	}

	/**
	 * e
	 *
	 * translates a word from english to the currently
	 * selected language. words must be pre-defined in the
	 * language file or the language file must be added to
	 * via a plugin filter
	 *
	 * if no match is found returns original string
	 *
	 * @access public
	 * @params string $word
	 * @return string
	 */
	public function e( $word ){
		if( isset( $this->lang{ $word } ) )
			return $this->lang{ $word };
		return $word;
	}

	/**
	 * runtimeError 
	 * 
	 * register a runtime error by passing an id of the error
	 * in the language file
	 *
	 * @param int $id of error in language file or string if error is not in language file
	 * @param array $params optional, can be string or array with multiple params
	 * @access public
	 * @return bool
	 */
	public function runtimeError( $id, $params = false ){

		/**
		 * if not numeric its most likely a plugin error
		 * else its an error being thrown without use of lang files
		 */
		if( !is_numeric( $id ) ){
			if( isset( $this->lang_errors{ $id } ) )
				$this->runtimeError{ $id } = $this->lang_errors{ $id };
			else
				$this->runtimeError{ -1 } = $id;
			return true;
		}

		/**
		 * if error not in lang file print unknown error
		 */
		if( !isset( $this->lang_errors[ $id ] ) ){
			$this->runtimeError{ $id } = 'An unknown error occured.';
			return false;
		}

		$error = $this->lang_errors[ $id ];


		/**
		 * if params var is present replace occurances
		 * of %i with var. can take an array of vars 
		 */
		if( $params != false ){

			if( is_array( $params ) ){
				/**
				 * reindex array to start with 1 instead of 0
				 */
				$params = array_combine( range( 1, count( $params ) ), array_values( $params ) );

				for( $i = 1; $i <= count( $params ); $i++ )
					$error = str_replace( '%' . $i, $params[ $i ], $error );

			}
			else
				$error = str_replace( '%1', $params, $error );
				
		}

		$this->runtimeError{ $id } = $error;

		return true;	

	}

	/**
	 * errorToString 
	 * 
	 * Converts an error id to the corresponding
	 * lang string without adding it to the runtime
	 * error array
	 *
	 * @param integer $id 
	 * @param string or array $params 
	 * @access public
	 * @return string
	 */
	function errorToString( $id, $params = false ){

		$this->runtimeError( $id, $params );

		$error = $this->runtimeError{ $id };

		unset( $this->runtimeError{ $id } );

		return $error;

	}

	/**
	 * displayErrors
	 *
	 * displays both runtime and system errors 
	 * 
	 * @access public
	 * @return string
	 * @todo change to display different class for runtime and system errors
	 */
	public function displayErrors( ){

		if( count( $this->runtimeError ) == 0 )
			return '';

		$errors = '<div id="system-error">
				<span class="right link x-img" id="errors-close">&nbsp;</span>
				<span id="dialog-alert-logo" style="float:left">&nbsp;</span>';

		foreach( $this->runtimeError as $key => $error )
			$errors .= '<span class="runtime-errors" id="error-' . $key . '">' . $error . '</span><br/>';

		return ( $errors .= '<br style="clear:both"/></div>' );
	}
	
	

        /**
         * loadJavascript
	 *
	 * This function is used to load javascript files
	 * or a string of javascript without the script tags.
	 *
	 * To add a file:
	 * $Template->loadJavascript( 'path/to/file' );
	 *
	 * To add a string of javascript:
	 * $Template->loadJavascript( 'UNIQUE_NAME_TO_IDENTIFY_CODE', $code );
	 *
	 * The unique name above will be used to create the
	 * unique URL later. The names should follow the
	 * scripts purpose, such as FURASTA_ADMIN_DB_PAGES_LIST
         * 
         * @param string $name 
         * @param string $content optional
         * @access public
         * @return bool
         */
        public function loadJavascript( $name, $content = 'file' ){
	
		/**
		 * determine wether a file or source code are being loaded
		 */
		if( $content == 'file' || $content == false ){
			if( !file_exists( HOME . $name ) || strpos( $name, '..' ) !== false )
				return false;

	                return $this->javascriptFiles{ $name } = ( $content ) ? 'true' : 'false';
		}
		else
			return ( $this->javascriptSources{ $name } = $content );
        }

        /**
         * javascriptUrls
	 *
	 * Returns a unique URL to load which will
	 * contain all of the javascript loaded
	 * during runtime.
         * 
         * @access public
         * @return string
         */
        public function javascriptUrls(){

		$urls = array( );
		foreach( $this->javascriptFiles as $file => $cache ){
			if( $cache == 'true' )
				array_push( $urls, cache_js( $file, $file ) );
			else
				array_push( $urls, SITE_URL . $file );
		}

		foreach( $this->javascriptSources as $file => $name )
			array_push( $urls, cache_js( $file, $name ) );

		return $urls;

	}

	/*
         * loadCSS
	 *
	 * Works with the same syntax and principal as
	 * the loadJavascript function above, except
	 * this function uses compression rather than minification.
	 *
	 * note: the compression is actually done in the
	 * cssUrls function
         * 
         * @param string $name 
         * @param string $content optional
         * @access public
         * @return bool
         */
        public function loadCSS( $name, $content = false ){

                /**
                 * determine wether a file or source code are being loaded
                 */
                if( $content == false )
                        return ( array_push( $this->cssFiles, $name ) );
                else
                        return ( $this->cssSources{ $name } = $content );
        }

        /**
         * cssUrls 
	 *
	 * Returns a unique URL to load which
	 * will contain all of the CSS added
	 * during runtime.
         * 
         * @access public
         * @return string
         */
        public function cssUrls(){

                $files = $this->cssFiles;
                $sources = $this->cssSources;
                $scripts = $files + $sources;
                $urls = array( );

		foreach( $scripts as $name => $file )
			array_push( $urls, cache_css( $name, $file ) );		

		return $urls;
	
        }

}

?>
