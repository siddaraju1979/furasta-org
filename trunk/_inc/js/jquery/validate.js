/**
 * jQuery Form Validation Plugin
 *
 * A jquery plugin which may be used to validate forms.
 * This function accepts an array of fields to be processed.
 * It can be called using this syntax, where #form is any valid
 * jQuery selector for the form:
 *
 * $( "#form" ).validate( options, customErrorHandler );
 *
 * The second parameter, customErrorHandler, is optional.
 * The options array should look something like this:
 *
 * {
 *   'name-input' : {
 *      'name' : 'Name',
 *      'required' : true,
 *      'minlength' : 5,
 *      'pattern' : /^[A-Za-z0-9 ]{2,40}$/
 *   },
 *   'password-input' : {
 *      'name' : 'Password',
 *      'required' : true,
 *      'minlength' : 10,
 *      'match' : 'input[name="repeat-password-input"]'
 *   },
 *   'repeat-password-input' : {
 *      'name' : 'Repeat Password',
 *	'required' : true
 *   }
 *   'content-input' : {
 *   	'name' : 'Content',
 *      'pattern' : [ /^[A-Za-z0-9 ]{2,40}$/, 'The content field
 *      must be between 2 and 40 characters' ]
 *   }
 * }
 *
 * As seen above, the array key should be the name of the
 * input being validated, so the corresponding html for
 * the above array should be:
 *
 * <input name="name-input" type="text" />
 * <input name="password-input" type="password" />
 * <input name="repeat-password-input" type="password" />
 *
 * The key should contain an array value of conditions which
 * apply to that input. A list of all the possible conditions
 * follows:
 *
 * name		-	accepts string of name for input in errors, optional
 * required     -       accepts boolean true or false
 * email        -       accepts boolean true or false
 * minlength    -       accepts integer of minimum length
 *                      of string
 * maxlength	-	accepts integer of maximum length
 * 			of string
 * match        -       accepts name of an input which the
 *                      key input should match
 * pattern      -       accepts regex string or an array in
 * 			the format: [ regex, message ]
 * url          -       accepts boolean true or false
 *
 * Please note that in the case of boolean attributes they are
 * only really required when the value is true.
 *
 * The validate function also accepts a second paramater
 * which is a custom error handler. This error handler
 * specifies how notifications of errors should be displayed.
 * This paramater is optional and if null the default alert
 * function will be used.
 *
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com> http://blog.conormacaoidh.com
 * @license    The BSD License
 * @version    1.2
 */
( function( $ ){

	/**
	 * Validate
	 * 
	 * This object handles most of the processing for the plugin.
	 *
	 * @author Conor Mac Aoidh <conormacaoidh@gmail.com> http://blog.conormacaoidh.com
	 * @license The BSD License
	 */
	var Validate = {

	        /**
	         * errors 
	         * 
	         * This variable contains 0 if there are no errors,
	         * otherwise it contains an error.
	         *
	         * @todo change the errors from strings to integers
	         * and add language file support
	         */
	        errors : 0,

        	/**
         	 * validated
         	 *
         	 * indicates whether the form has been validated
         	 * or not. contains the value of 1 if has been
         	 * validated
         	 */
        	validated : 0,

		/**
		 * below are arrays which contain the fields to
		 * be processed by each validation function
		 */
		name_f : [ ],
	        required_f : [ ],
        	email_f : [ ],
	        pattern_f : [ ],
	        minlength_f : [ ],
		maxlength_f : [ ],
        	match_f : [ ],
	        url_f : [ ],
		file_f : [ ],

		/**
		 * addConds
		 *
		 * adds the given conditions to the Validate object
		 */
	        addConds : function( pieces ){
        	        for( var i in pieces ){
				selector = this.getSelector( i );
				if( selector == false )
					continue;
                	        for( var n in pieces[ i ] ){
	                                switch( n ){
        	                                case "name":
							this.name_f[ selector ] = pieces[ i ][ n ];
                                	        break;
        	                                case "required":
                	                                if( pieces[ i ][ n ] == true )
                        	                                this.required_f.push( selector );
                                	        break;
                                        	case "pattern":
							this.pattern_f[ selector ] = pieces[ i ][ n ];
                	                        break;
                        	                case "email":
                                	                if( pieces[ i ][ n ] == true )
                                        	                this.email_f.push( selector );
	                                        break;
        	                                case "minlength":
							this.minlength_f[ selector ] = pieces[ i ][ n ];
                                	        break;
						case 'maxlength':
							this.maxlength_f[ selector ] = pieces[ i ][ n ];
                                                break;
                                        	case "match":
							this.match_f[ selector ] = pieces[ i ][ n ];
                	                        break;
                        	                case 'url':
                                	                if( pieces[ i ][ n ] == true )
                                        	                this.url_f.push( selector );
	                                        break;
						case 'file':
							this.file_f[ i ] = pieces[ i ][ n ];
						break;
        	                        }
                	        }
	                }

        	},

		/**
		 * getSelector
		 * 
		 * returns a selector for the name
		 */
		getSelector : function( selector ){
			input = 'input[name="' + selector + '"]';
			if( $( input ).length != 0 )
				return input;

			select = 'select[name="' + selector + '"]';
			if( $( select ).length != 0 )
				return select;

			textarea = 'textarea[name="' + selector + '")';
			if( $( textarea ).length != 0 )
				return textarea;

			checkbox = 'checkbox[name="' + selector + '"]';
			if( $( checkbox ).length != 0 )
				return checkbox;

			return false;
		},

		/**
		 * customErrorHandler
		 *
		 * contains the custom error handler function,
		 * if present
		 */
	        customErrorHandler : null,

		/**
		 * errorHandler
		 *
		 * executes the custom error handler if present,
		 * else resorts to default
		 */
	        errorHandler : function( ){

        	        if( typeof( this.customErrorHandler ) == 'function' )
                	        return this.customErrorHandler( this.errors );

			/**
			 * this is the only Furasta.Org specific code, normally
			 * the alert function would be used here
			 */
	                fAlert( this.errors );

	        },

        	/**
         	 * required
         	 *
         	 * processes required fields and makes sure
         	 * they have some content in them
         	 */
        	required : function( ){

                	for( var i = 0; i < this.required_f.length; i++ ){
                        	var loc = $( this.required_f[ i ] );
	                        loc.removeClass( 'error' );

        	                if( loc.val( ) == '' ){
					this.errors = 'The ' + this.name_f[ this.required_f[ i ] ] + ' field is required';
                        	        loc.addClass( 'error' );

                                	this.errorHandler( );
	                                return false;
        	                }
                	}

                	return true;
	        },

		/**
		 * emailFormat
		 *
		 * processes email fields and makes sure they
		 * actually have emails in them
		 */
	        emailFormat : function( ){

        	        var filter=/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

	                for( var i = 0; i < this.email_f.length; i++ ){
        	                var loc = $( this.email_f[ i ] );
                	        loc.removeClass( 'error' );

                        	if( filter.test( loc.val( ) ) == false ){
	                                this.errors = 'Please enter a valid email address in the ' + this.name_f[ this.email_f[ i ] ] + ' field';
        	                        loc.addClass( 'error' );

                	                this.errorHandler( );
                        	        return false;
	                        }
        	        }

	                return true;
        	},

		/**
		 * minlength
		 *
		 * processes minlength fields and makes sure they
		 * have a correct length
		 */
	        minlength : function( ){

        	        for( var i in this.minlength_f ){

                	        var loc = $( i );
                        	loc.removeClass( 'error' );

	                        if( loc.val( ).length < this.minlength_f[ i ] ){
					this.errors = 'The ' + this.name_f[ i ] + ' field must be at least ' + this.minlength_f[ i ] + ' characters long';
                	                loc.addClass( 'error' );
                        	        this.errorHandler( );
                                	return false;
	                        }
        	        }

                	return true;
	        },

                /**
                 * maxlength
                 *
                 * processes maxlength fields and makes sure they
                 * have a correct length
                 */
                maxlength : function( ){

                        for( var i in this.maxlength_f ){

                                var loc = $( i );
                                loc.removeClass( 'error' );

                                if( loc.val( ).length > this.maxlength_f[ i ] ){
					this.errors = 'The ' + this.name_f[ i ] + ' field must be at most ' + this.minlength_f[ i ] + ' characters long';
                                        loc.addClass( 'error' );
                                        this.errorHandler( );
                                        return false;
                                }
                        }

                        return true;
                },

		/**
		 * match
		 *
		 * processes match fields and makes sure they match
		 */
	        match : function( ){
	
        	        for( var i in this.match_f ){
                	        var locone = $( i );
                        	var loctwo = $( this.getSelector( this.match_f[ i ] ) );
	                        locone.removeClass( 'error' );
        	                loctwo.removeClass( 'error' );
	
        	                if( locone.val( ) != loctwo.val( ) ){
					this.errors = 'The ' + this.name_f[ i ] + ' must match the ' + loctwo.attr( 'name' ) + ' field';
                        	        locone.addClass( 'error' );
                                	loctwo.addClass( 'error' );
	                                this.errorHandler( );
        	                        return false;
                	        }
	                }

	        	return true;

        	},

		/**
		 * url
		 *
		 * processes url fields and makes sure they have
		 * valid urls in them, supports http and https but
		 * not ftp or anything else
		 */
		url : function( ){

			var filter = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

                        for( var i = 0; i < this.url_f.length; i++ ){
		                var loc = $( this.url_f[ i ] );
		                loc.removeClass( 'error' );

	                        if( filter.test( loc.val( ) ) == false && loc.val( ) != "" ){
					this.errors = 'Please enter a valid URL in the ' + this.name_f[ this.url_f[ i ] ] + ' field';
                                	loc.addClass( 'error' );
					this.errorHandler( );
					return false;
                        	}
                	}

			return true;

	        },

		/**
		 * pattern
		 *
		 * processes pattern fields and makes sure
		 * the fields obey the given regex pattern
		 */
		pattern : function( ){

			for( var i in this.pattern_f ){

				/**
				 * check if array, or standard notaion is being used
				 */
				if( $.isArray( this.pattern_f[ i ] ) ){
					var regex = this.pattern_f[ i ][ 0 ];
					var message = this.pattern_f[ i ][ 1 ];
				}
				else{
					var regex = this.pattern_f[ i ];
					var message = 'The ' + this.name_f[ i ] + ' field is invalid';
				}

				/**
				 * if its not a regex string then convert it to one
				 */
			        if( typeof( regex ) != 'object' )
			                regex = new RegExp( regex );

				var loc = $( i );
				loc.removeClass( 'error' );

				if( regex.test( loc.val( ) ) == false ){
					this.errors = message;
			                loc.addClass( 'error' );
					this.errorHandler( );
					return false;
				}
		        }

		        return true;

		},


		/**
		 * file
		 * 
		 * checks file extension
		 */
		file : function( ){

			for( var i in this.file_f ){
				var loc = $( i );
				loc.removeClass( 'error' );

				if( typeof( this.file_f[ i ].extensions ) != 'undefined' ){
				
					extension = loc.val( ).split( '.' );
					extension = extension[ extension.length - 1 ];
					allowed_extensions = this.file_f[ i ].extensions.split( ',' );
					match = false;
					$( allowed_extensions ).each( function( e ){
						if( allowed_extensions[ e ] == extension )
							match = true;
					});
					if( !match ){
						this.errors = 'The ' + this.name_f[ i ] + ' field only accepts files of type: ' 
							+ this.file_f[ i ].extensions;
				                loc.addClass( 'error' );
						this.errorHandler( );
						return false;
					}

				}
				
			}

			return true;
		},

		/**
		 * execute
		 *
		 * Execute validation, when one field returns false the
		 * validation process stops. If form has been validated
		 * already then it returns true
		 */
	        execute : function( ){

       	 	        if( this.validated == 1 )
                	        return false;

	              	if( this.required_f.length != 0 && this.required( ) == false )
        	            	return false;

	              	if( this.email_f.length != 0 && this.emailFormat( ) == false )
        	              	return false;

                	if( this.pattern( ) == false )
				return false;

	              	if( this.minlength( ) == false )
        	              	return false;

                        if( this.maxlength( ) == false )
                                return false;

                	if( this.match( ) == false )
                        	return false;

                	if( this.url_f.length != 0 && this.url( ) == false )
                        	return false;

                	if( !this.file( ) )
                        	return false;

                	this.validated = 1;

                	return true;

		}

	};

	/**
	 * fn.validate
	 *
	 * initiates the validation process,
	 * binds a submit function to the form
	 */
	$.fn.validate = function( conds, errorHandler ) {
		switch( conds ){
			case 'execute':
				return Validate.execute( );
			break;
			case 'errorHandler':
				return Validate.customErrorHandler = errorHandler;
			break;
			case 'refresh':
				return Validate.validated = 0;
			break;
		}

	        if( typeof( errorHandler ) == 'function' )
        	        Validate.customErrorHandler = errorHandler;

	        Validate.addConds( conds );

		var id = this.attr( 'id' );
		this.unbind( 'submit.validate-' + id );
	        this.bind( 'submit.validate-' + id, function( ){
	  	        return Validate.execute( );
	        });

	        return this;

	};
})( jQuery );
