/**
 * Installer JavaScript File, Furasta.Org
 *
 * Contains javascript used during the installation process
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license	http://furasta.org/licence.txt The BSD License
 * @version	1.0
 * @package	installer
 */
$(function(){
	rowColor( );
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
	$( ".row-color tr:not(:hidden):even" ).removeClass( "odd" ).addClass( "even" );
	$( ".row-color tr:not(:hidden):odd" ).removeClass( "even" ).addClass( "odd" );
}

function fAlert( message ){
	var dialogButtons = {};
	dialogButtons[ 'Close' ] = function( ){ $( this ).dialog( 'close' ) };
	$('#dialog')
		.html('<div id="dialog-content">'+message+'</div><div id="dialog-alert-logo">&nbsp;</div>')
		.attr('title', 'Alert')
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
	dialogButtons[ 'Close' ] = function( ){ $( this ).dialog( 'close' ); };
	$('#dialog')
		.html('<div id="dialog-content">'+message+'</div><div id="dialog-help-logo">&nbsp;</div>')
		.attr('title', 'Help' )
		.dialog({
			modal: true,
			buttons: dialogButtons,
			hide:'fade',
			show:'fade',
			resizeable:false
		})
		.dialog("open");
}

function checkConnection(details){
	var hostname=$("input[name="+details[0]+"]").val();
	var username=$("input[name="+details[1]+"]").val();
        var database=$("input[name="+details[2]+"]").val();
        var password=$("input[name="+details[3]+"]").val();

        var hash=Math.floor(Math.random()*1001);
	var dataString='hostname='+hostname+'&username='+username+'&database='+database+'&password='+password;

	$.ajax({
		type: "POST",
		data: dataString,
		url:'/install/connection.php?hash='+hash,
		async: false,
                success:function(html){
			window.html=html;
                }
	});			
	return window.html;
}
