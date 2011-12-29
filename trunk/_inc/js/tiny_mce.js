/**
 * TinyMCE JS, Furasta.Org
 *
 * Javascript stuff to do with tiny mce
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * tinymceConfigs
 *
 * tinymce configs, can be extended and then the
 * tinymce_changeConfig function can be used to load
 * and keep track of tinymce instances
 */
window.tinymceConfigs = {
	"Normal" : {	
		// General options
		theme : "advanced",
		plugins : "safari,pagebreak,style,table,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js"
	}
};

/**
 * adds a jquery event to element removals
 * so that tinymce configurations can be removed
 * before the elements are removed so that if the
 * elements are loaded again, tinymce can also
 * be loaded again
 */
(function() {
    var ev = new $.Event('remove'),
        orig = $.fn.remove;
    $.fn.remove = function() {
        $(this).trigger(ev);
        return orig.apply(this, arguments);
    }
})();

/**
 * load up tinymce without creating any
 * instances
 */
$(function(){
	tinyMCE.init({
		mode : "none",
		theme : "advanced"
	});
});

/**
 * tinymce_changeConfig
 *
 */
function tinymce_changeConfig( element, key ){
	var id = $( element ).attr( 'id' );
	tinyMCE.settings = window.tinymceConfigs[ key ];
	tinyMCE.execCommand( 'mceAddControl', false, id );
	tinyMCE.triggerSave();
	$( element ).bind( 'remove', function( ev ){
		if( ev.target !== this )
			return;

		if( tinyMCE.getInstanceById( id ) ){
			tinyMCE.execCommand( 'mceFocus', false, id );
			tinyMCE.execCommand( 'mceRemoveControl', false, id );
		}

		return true;
	});
}