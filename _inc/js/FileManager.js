/**
 * File Manager Javascript, Furasta.Org
 *
 * Contains javascript functions which are used to access
 * file manager methods.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * FileManager
 * 
 * This object is used for accessing file manager
 * functions via AJAX
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files 
 * @todo update to cache results, instead of requesting via
 * ajax every time
 */
var FileManager = {

        /**
         * fmExec
         *
         * executes a method of the file manager class
         * with the given parameters and callback
         *
         * @param object postdata
         * @param function fn
         * @return void
         */
        fmExec : function( path, postdata, fn ){
                
                hash = Math.floor( Math.random( ) * 10000 );

                $.ajax({
                        type    : 'post',
                        url     : window.furasta.site.url + 'files' + path + '?hash=' + hash,
                        data    : postdata,
                        dataType: 'json',
                        success : fn,
                        error   : function( ){
                                fAlert( 'An unknown error occurred. Please try again.' );
                        }

                });

        },

        /**
         * readDir
         *
         * reads the given path and displays contents
         *
         * @param string path
         * @param function fn
         * @return void
         */
        readDir : function( path, fn ){
                
                this.fmExec(
                        path,
                        {
                                func : 'readDir'
                        },
                        fn
                );

        },


        /**
         * readFile
         *
         * gets the contents of a file
         *
         * @param string path
         * @param function fn
         * @return void
         */
        readFile : function( path, fn ){

                this.fmExec(
                        path,
                        {
                                func : 'readFile'
                        },
                        fn
                );

        },

        /**
         * addDir
         *
         * adds a directory at the given location
         *
         * @param string path
         * @param function fn
         * @return void
         */
        addDir : function( path, fn ){

                this.fmExec(
                        path,
                        {
                                func : 'addDir',
                        },
                        function( data ){

                                if( data != null && typeof( data ) == 'object' && typeof( data.desc ) == 'string' )
                                        fAlert( data.desc );
                                else
                                        fn( data );

                        }
                );

        },

        /**
         * uploadFile
         *
         * uploads a file using the file manager. uses
         * iframes to upload a file without a page
         * reload
         *
         * @param selector form
         * @return void
         */
        uploadFile : function( path, form, fn ){

                $( 'body' ).append( '<iframe name="postiframe" id="postiframe" style="display: none" />' );
 
                form.attr( 'action', window.furasta.site.url + 'files' + path );
                form.attr( 'method', 'post' );
                form.attr( 'enctype', 'multipart/form-data' );
                form.attr( 'encoding', 'multipart/form-data' );
                form.attr( 'target', 'postiframe' );
                file = $( 'input[type=file]', form ).attr( 'name' );
                form.append(
                                '<input type="hidden" name="upload-file" value="' + file + '"/>'
                                + '<input type="hidden" name="func" value="uploadFile"/>'
                );
                form.submit( );

                /**
                 * submit iframe
                 */
                $( '#postiframe' ).load( function( ){
                        result = $( '#postiframe' )[ 0 ].contentWindow.document.body.innerHTML;
                        if( result == '' )
                                return fn( );
                        $( "#dialog" ).prepend( '<p>' + iframeContents + '</p>' );
                });

        },

        /**
         * removeFile
         *
         * makes an ajax request to the file
         * manager to have a file removed
         *
         * @param string path
         * @param function fn
         * @return void
         */
        removeFile : function( path, fn ){

                this.fmExec(
                        path,
                        {
                                func : 'removeFile',
                        },
                        function( data ){

                                if( data != null && typeof( data ) == 'object' && typeof( data.desc ) == 'string' )
                                        fAlert( data.desc );
                                else
                                        fn( data );

                        }
                );

        },

        /**
         * removeDir
         *
         * makes an ajax request to the file
         * manager to have a directory removed
         *
         * @param string path
         * @param function fn
         * @return void
         */
        removeDir : function( path, fn ){

                this.fmExec(
                        path,
                        {
                                func : 'removeDir',
                        },
                        function( data ){

                                if( data != null && typeof( data ) == 'object' && typeof( data.desc ) == 'string' )
                                        fAlert( data.desc );
                                else
                                        fn( data );

                        }
                );

        },

}
