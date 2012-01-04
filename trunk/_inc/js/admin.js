/**
 * Admin JS, Furasta.Org
 *
 * Contains admin js functions and some
 * javascript to be executed on admin
 * pages.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

if( typeof jQuery == 'undefined' ){
	window.location = window.furasta.site.url + '_inc/noscript.php';
}

$(document).ready(function(){
        rowColor();
        $("#menu ul").dropDownMenu({timer:1500,parentMO:"parent-hover"});
        var link=window.location.href.split( "?" );
	var path = link[ 0 ];
        if(path==window.furasta.site.url + "admin"||path==window.furasta.site.url + "admin/"){
                $("#Overview").addClass("current");
        }
	else if( path == window.furasta.site.url + "admin/plugin.php" ){
		link = link[ 1 ].split(/[=&]+/);
		link = 'menu_' + link[ 1 ].replace( '-', '_' ).toLowerCase( );
		$( "#" + link ).addClass( "current" );
	}
        else{
                $("#menu li a[href=\'"+path+"\']").addClass("current");
        }

	$( "#errors-close" ).live( "click", function( ){
		fetch( window.furasta.site.url + "_inc/ajax.php?file=admin/settings/update/system-alert.php" );
		$( "#system-error" ).fadeOut( "fast", function( ){ $( this ).remove( ); } );
	});
});

/**
 * rowColor 
 * 
 * adds odd and even classes to trs in all tables
 * with the row-color class
 *
 * @access public
 * @return void
 */
function rowColor( ){
	$( ".row-color tr:even" ).not( ":hidden" ).removeClass( "odd" ).addClass( "even" );
	$( ".row-color tr:odd" ).not( ":hidden" ).removeClass( "even" ).addClass( "odd" );
}

/**
 * trans
 *
 * translates the string using language files
 *
 * @param string string
 * @return string or bool
 */
function trans( string ){
	if( window.furasta.lang[ string ] ){
		return window.furasta.lang[ string ];
	}
	return string;
}

/**
 * trans_error
 *
 * returns the error string associated with the
 * given id and parses for params
 *
 * @params int id
 * @params array params
 * @return string
 */
function trans_error( id, params ){
	if( !window.furasta.lang_errors[ id ] ){
		return false;
	}
	error = window.furasta.lang_errors[ id ];
	if( params ){
		if( $.isArray( params ) ){
			for( var i = 1; i <= params.length; i++ )
				error = error.replace( '%' + i, params[ i ] );
		}
		else
			error = error.replace( '%1', params ); 
	}
	return error;
}

function loadPageType(type,id){
        var hash=Math.floor(Math.random()*1001);
        $.ajax({
                url: window.furasta.site.url + '_inc/ajax.php?file=admin/pages/type.php&type='+type+'&id='+id+'&hash='+hash,
                success: function(html){
                        $("#pages-type-content").html(html);

	                if( $( "#ajax-errors" ).length != 0 ){
        	                $( "#main" ).prepend( $( "#ajax-errors" ).html( ) );
			}
                }
        });
        return;
}

function displayOptions(){
        var options=$("#options");
        if(options.is(":hidden")){
                $("#options-link").html(trans( 'pages_hide_options' ));
                $(options).slideDown('slow');
        }
        else{
                $("#options-link").html(trans( 'pages_show_options' ));
                $(options).slideUp('slow');
        }

        return;
}

/*
function passwordReminder(){
        var html='<script type="text/javascript">$(document).ready(function(){ $("#jqi_state0_buttonSend").click(function(){ $("#loading-icon").html("<img src=\'/_inc/img/loading.gif\'/>");var email=$("#reminder-email").val();$.ajax({url:"/_inc/ajax.php?file=admin/settings/users/verify_email.php&email="+email,success: function(html){ if(html==1) $("#loading-icon").html("error"); else{ $("#loading-icon").html("success"); setTimeout(function(){ $("#jqibox").fadeOut("slow"); },300); } }}); return false; }); });</script><p style="margin-left:0;font-style:italic">Enter your email address below and a new password will be sent to you.</p><table><td class="small">Email:</td><td><input type="text" id="reminder-email" name="Email"/></td><td class="small" id="loading-icon"></td></tr></table><br/>';
        $('#dialog').html(html);
        $('#dialog').attr('title','Password Reminder');
        $('#dialog').dialog({ modal: true,buttons:{ Cancel: function(){ $(this).dialog('close'); }, Send:function(){ $(this).dialog('close'); }},hide:'fade',show:'fade',resizeable:false });
        $('#dialog').dialog("open");
}*/
function slugCheck(url){
        if(!/^[A-Za-z0-9 ]{2,40}$/.test(url)){
                return false;
	}
        if(url==1){
                return false;
	}
        url=url.replace(/\s/g,'-');
        return url;
}

/**
 * getParents
 *
 * returns the parents of a particular page,
 * or of the selected page in the admin pages area. 
 * 
 * @param int page  
 * @access public
 * @return string
 */
function getParents( page ){

	if( page == null ){
		page = $( "#select-parent :selected" ).attr( 'class' );
	}

	if( page == 0 ){
		return '';
	}

	var parent = $( '#select-parent option[ value = ' + page + ' ]' );

	var url = '';

	if( parent.attr( 'class' ) != 0 ){
		url += getUrl( parent.attr( 'class' ) ) + '/';
	}

	url += parent.text( ) + '/';

	return url;

}

/**
 * getUrl 
 *
 * used in admin pages section, uses the getParents
 * function
 * 
 * @param string new_pagename, bit to add to end of url. '' can be used
 * @access public
 * @return void
 */
function getUrl( new_pagename ){
	
	var parent = $( "#select-parent :selected" ).text( );

	var host = "http://" + window.location.hostname + "/";

	$( "#page-name" ).removeClass( "error" );

	var fullUrl = ( parent == "---" ) ? host + new_pagename.replace(/\s/g,"-") : host + getParents( ) + ( parent + "/" + new_pagename ).replace(/\s/g,"-");

	$( "#slug-url" ).html( fullUrl );

	$( "#slug-url" ).attr( "href", fullUrl );

	$( "#slug-put" ).attr( "value", new_pagename );
}

/**
 * toggleDisabled
 *
 * toggles checkboxes disabled attr using the selector 
 * 
 * @param string selector 
 * @access public
 * @return void
 */
function toggleDisabled( selector ){

	$( selector ).each( function( ){

		if( $( this ).attr( 'disabled' ) == '' ){
			$( this ).attr( 'disabled', 'disabled' );
		}
		else{
			$( this ).removeAttr( 'disabled' );
		}

	});

}
