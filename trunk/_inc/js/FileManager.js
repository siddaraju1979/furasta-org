/**
 * File Manager Javascript, Furasta.Org
 *
 * Contains javascript functions which are used to access
 * file manager methods.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
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

}
