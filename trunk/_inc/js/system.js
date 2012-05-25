/**
 * System JS Functions, Furasta.Org
 *
 * Contains javascript functions which can be expected
 * available on all admin and frontend pages.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

function fAlert(message){
	var dialogButtons = {};
	dialogButtons[ trans( 'prompt_close' ) ] = function( ){ $( this ).dialog( 'close' ) };
        $('#dialog')
		.html('<div id="dialog-content">'+message+'</div><div id="dialog-alert-logo">&nbsp;</div>')
		.attr('title',trans( 'prompt_alert' ))
		.dialog({
			modal: true,
			buttons: dialogButtons,
			hide:'fade',
			show:'fade',
			resizeable:false
		})
		.dialog("open");
}


function fConfirm(message,callback,param){
	var dialogButtons = {};
	dialogButtons[ trans( 'prompt_cancel' ) ] = function( ){ $( this ).dialog( 'close' ) };
	dialogButtons[ trans( 'prompt_yes' ) ] = function( ){
		callback( param );
		$( this ).dialog( 'close' );
	};
        $('#dialog')
		.html('<div id="dialog-content">'+message+'</div><div id="dialog-confirm-logo">&nbsp;</div>')
		.attr('title',trans( 'prompt_confirm' ) )
		.dialog({
			modal: true,
			buttons: dialogButtons,
			hide:'fade',
			show:'fade',
			resizeable:false
		})
		.dialog("open");
}

function fHelp(message){
	var dialogButtons = {};
	dialogButtons[ trans( 'prompt_close' ) ] = function( ){ $( this ).dialog( 'close' ); };
        $('#dialog')
		.html('<div id="dialog-content">'+message+'</div><div id="dialog-help-logo">&nbsp;</div>')
		.attr('title',trans( 'prompt_help' ))
		.dialog({
			modal: true,
			buttons: dialogButtons,
			hide:'fade',
			show:'fade',
			resizeable:false
		})
		.dialog("open");
}

/**
 * fetch 
 * 
 * @param string url to be fetched
 * @param selector put - optional place to put loaded content
 * @access public
 * @return void
 */
function fetch( url, callback, param ){
	$.ajax({
		url	:	url,
		timeout	:	10000,
		success	:	function( html ){
					if( html == '1' ){
						fAlert( trans( 'error_unknown' ) );
					}
					else if( callback != null ){
						callback( param, html );
					}

				},
		error	:	function( ){
					fAlert( trans( 'error_unknown' ) );
					if( callback != null ){
						callback( param, "content not loaded" );
					}

				}
	});
}

/**
 * queryString
 *
 * returns the value of the querystring requested
 * with the name var
 * 
 * @param string name of querystring section to return
 * @access public
 * @return string
 */
function queryString( name ){
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if( results == null ){
		return "";
	}
  	else{
    		return decodeURIComponent(results[1].replace(/\+/g, " "));
	}
}

function file_upload( ){

}
