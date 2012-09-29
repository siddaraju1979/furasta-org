/**
 * FileManagerGUI Javascript, Furasta.Org
 *
 * Contains tools for creating a GUI for the
 * file manager
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */

/**
 * FileManagerGUI
 * 
 * This object uses the file manager class and
 * extends it to implement a basic gui
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */
var FileManagerGUI = {

        // selectors
        filecrumbs      : null,
        filecrumb       : null,
        newfolder       : null,
        dir_list        : null,
        dir             : null,
        file            : null,
        item            : null,
        filemenu        : null,
        upload_file     : null,

        // vars
        path            : null,

        /**
         * init
         *
         * initiates the file manager gui on the
         * given selector
         *
         * @param selector element
         * @return void
         */
        init : function( element ){

                // initial html
                element.html( 
                          '<div id="files">'
                        + '     <div id="directory-container">'
                        + '             <div class="th"><h3 style="color:#fff;margin-top:5px;margin-left:5px">Directory Listing</h3></div>'
                        + '             <ul id="panel-top">'
                        + '                     <li><a class="link" id="file-upload"><img src="' + window.furasta.site.url + '_inc/img/file.png"/><span>Upload File</span></a></li>'
                        + '                     <li><a class="link" id="new-folder"><img src="' + window.furasta.site.url + '_inc/img/folder.png"/><span>New Folder</span></a></li>'
                        + '                     <li><a class="link" id="dir-settings"><span id="menu-configuration-img"></span><span>Folder Settings</span></a></li>'
                        + '             </ul>'
                        + '             <div id="filecrumbs"></div>'
                        + '             <ul id="directory-list"></ul> '     
                        + '             <br style="clear:both"/>'
                        + '     </div>'
                        + '</div>'
                );

                // initiate selectors
                this.filecrumbs  = $( '#filecrumbs' );
                this.filecrumb   = $( '.crumb' );
                this.newfolder   = $( '#new-folder' );
                this.dir_list    = $( '#directory-list' );
                this.dir         = $( '.dir' );
                this.file        = $( '.file' );
                this.item        = $( '.item' );
                this.filemenu    = $( '.menu-left' );
                this.upload_file = $( '#file-upload' );

                /**
                 * list the root of the files dir by default
                 */
                this.showDir( '/users/' + window.furasta.user.id + '/' );

                /**
                 * new folder event
                 */
                this.newfolder.live( 'click', function( ){
                        FileManagerGUI.newFolder( );
                        return false;
                });

                /**
                 * filecrumb event
                 */
                this.filecrumb.live( 'click', function( ){
                        FileManagerGUI.showDir( $( this ).attr( 'href' ) );
                        return false;
                });

                /**
                 * change directory when clicked
                 */
                this.dir.live( 'click', function( ){
                        FileManagerGUI.showDir( $( this ).attr( 'href' ) );
                        return false;
                });

                /**
                 * show file contents
                 */
                this.file.live( 'click', function( ){
                        FileManagerGUI.showFile( $( this ).attr( 'href' ) );
                        return false;
                });

                /**
                 * upload file event
                 */
                this.upload_file.live( 'click', function( ){
                        FileManagerGUI.uploadFile( );
                        return false;
                });

                /**
                 * item hover event
                 */
                $( 'li', this.dir_list ).live({
                        mouseenter : function( ){
                                $( '.delete-img', $( this ) ).addClass( 'delete-small-img' );
                        },
                        mouseleave : function( ){
                                $( '.delete-img', $( this ) ).removeClass( 'delete-small-img' );
                        }
                });

        },

        /**
         * showDir
         *
         * reads the given path and displays contents
         *
         * @param string path
         * @return void
         */
        showDir : function( path ){

                this.dir_list.html( '<img src="' + window.furasta.site.url + '_inc/img/loading.gif"/>' );

                FileManager.readDir( path,
                        function( data ){

                                FileManagerGUI.path = path;
                                FileManagerGUI.showFilecrumbs( path );

                                if( data.length == 0 )
                                        FileManagerGUI.dir_list.html( '<p><b>This folder is empty.</b></p>' );
                                else{
                                        FileManagerGUI.dir_list.html( '' ); 
                                        for( var i in data ){
                                                FileManagerGUI.dir_list.append( '<li>' + FileManagerGUI.showItem( data[ i ] ) + '</li>' );
                                        }
                                }
                        }
                );

        },

        /**
         * showFile
         *
         * displays the content of the file in the appropriate format
         *
         * @param string path
         * @return void
         */
        showFile : function( path ){
                 
                // fetch file contents with ajax
                FileManager.readFile( path, function( content ){

                        $( '#dialog' ).html( content );

                });

                // create dialog for resulting request
                $( '#dialog' )
                        .html( '<p>Loading..</p><img src="' + window.furasta.site.url + '_inc/img/loading.gif"/></p>' )
                        .attr( 'title', path )
                        .dialog( {
                                modal   : true,
                                buttons : {
                                        'Cancel' : function( ){
                                                $( this ).dialog( 'close' );
                                        },
                                        'Save' : function( ){
                                                $( this ).dialog( 'close' );
                                        }   
                                },
                                hide    : 'fade',
                                width   : 400,
                                height  : 400,
                                show    : 'fade',
                                resizeable : false
                        })
                        .dialog( 'open' );

        },

        /**
         * showItem
         *
         * shows a given file, switching between different file
         * types - dir, file, image etc
         *
         * @param object data
         * @return string
         */
        showItem : function( data ){
          
                content = '<a class="delete link" href="' + data.path + '"><span class="delete-img">&nbsp;</span></a>';
                content += '<a class="link ' + data.type + ' item" href="' + data.path + '">';
                content += '<img width="48px" height="48px" src="' + window.furasta.site.url;

                switch( data.type ){
                        case 'dir':
                                content += '_inc/img/folder.png';
                        break;
                        case 'file':
                                content += '_inc/img/file.png';
                        break;
                        // image
                        case 'image/jpeg':
                        case 'image/png':
                        case 'image/gif':
                        case 'image/jpg':
                                content += 'files' + data.path;
                        break;
                }

                content += '"/><br/>' + data.name + '</a>';
                
                return content;

        },

        /**
         * showFilecrumbs
         *
         * prints the breadcrumbs for the directory
         * that is being viewed, supplied by path
         *
         * @param string path
         * @return void
         */
        showFilecrumbs : function( path ){

                this.filecrumbs.attr( 'current', path );

                content = '<a class="link crumb" href="/">files</a>';
                path    = path.split( '/' );
                cur     = '';
                for( i = 0; i < path.length - 1; ++i ){
                        cur += path[ i ] + '/';
                        content += '<a class="link crumb" href="' + cur + '">' + path[ i ] + '</a> / ';

                }

                this.filecrumbs.html( content );

        },

        /**
         * newFolder
         *
         * prompts user for new folder name and
         * sends request for folder to be created
         *
         * @return void
         */
        newFolder : function( ){

                fPrompt( 'Please enter the name of the folder you would like to create.', function( param, val ){
                       dir = this.filecrumbs.attr( 'current' );
                       name = $( 'input[name="prompt-input"]' ).val( );
                       FileManager.addDir( dir + name, function(  ){
                                FileManagerGUI.showDir( dir );              
                       });
                }, null );

        },

        /**
         * uploadFile
         *
         * an interface for uploading files
         *
         * @return void
         */
        uploadFile : function( ){

                /**
                 * upload submit function
                 */ 
                uploadSubmit = function( ){

                        form = $( '#upload-form' );

                        FileManager.uploadFile( FileManagerGUI.path, form, function( data ){
                                FileManagerGUI.showDir( FileManagerGUI.path );
                                $( '#dialog' ).dialog( 'close' );
                        });

                        return false;

                };

                /**
                 * dialog for file upload
                 */
                $( '#dialog' )
                        .html(
                                  '<form id="upload-form">'
                                +       '<p>Upload File to <i>' + this.path + '</i></p>'
                                +       '<input type="file" name="file-upload"/>'
                                + '</form>'   
                        )
                        .attr( 'title', 'Upload File' )
                        .dialog({
                                modal   : true,
                                buttons : {
                                        'Cancel' : function( ){
                                                $( this ).dialog( 'close' );
                                        },
                                        'Upload' : uploadSubmit, 
                                },
                                hide    : 'fade',
                                width   : 500,
                                height  : 200,
                                show    : 'fade',
                                resizeable : false
                        })
                        .dialog( 'open' );

        },

};

/**
 * fmGUI
 *
 * A jquery plugin which uses the FileManagerGUI
 * object
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */
( function( $ ){

	/**
	 * fn.fmGUI
	 *
	 * initiates the file manager gui on the
         * given selector
	 */
	$.fn.fmGUI = function( conds ) {

                /**
                 * switch between possible arguements
                 */        
                switch( conds ){
                        default:
				FileManagerGUI.init( this );
		}

	        return this;

	};

})( jQuery );
