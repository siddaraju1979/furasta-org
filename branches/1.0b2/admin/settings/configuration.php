<?php

/**
 * Configuration, Furasta.Org
 *
 * Allows for changes to the $SETTINGS variable.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_settings
 */

/**
 * set up form validation
 */
$conds = array(
        'Title' => array(
		'name' => $Template->e( 'configuration_website_title' ),
                'required'      =>      true
        ),
	'SubTitle' => array(
		'name' => $Template->e( 'configuration_website_subtitle' ),
		'required'	=>	true
	),
	'URL' => array(
		'name' => $Template->e( 'configuration_website_url' ),
		'required'	=> true,
		'url'		=> true
	)
);

$valid = validate( $conds, "#config-settings", 'settings_general' );

/**
 * executed if form is submitted 
 */
if( isset( $_POST[ 'settings_general' ] ) && $valid == true ){

        $SETTINGS['site_title']=addslashes($_POST['Title']);
        $SETTINGS['site_subtitle']=addslashes($_POST['SubTitle']);
        $SETTINGS['maintenance']=(int) @$_POST['Maintenance'];
        $SETTINGS['index']=(int) @$_POST['Index'];
	$url = @$_POST[ 'URL' ];
	$constants = array( );

	update_options( @$_POST[ 'options' ], 'configuration_page_options' );

	/**
	 * set the diagnostic mode setting if
	 * diagnostic mode is to be enabled
	 */
	if( @$_POST[ 'DiagnosticMode' ] == 1 ){
		$constants[ 'DIAGNOSTIC_MODE' ] = 1;
		$Template->diagnosticMode = 1;
	}
	else{
		$constants[ 'DIAGNOSTIC_MODE' ] = 0;
		$Template->diagnosticMode = 0;
	}

	/**
	 * if the siteurl has changes, then add the new
	 * value to the constants array
	 */
	if( $url != SITEURL )
		$constants[ 'SITEURL' ] = $url;

	$lang = @$_POST[ 'lang' ];
	if( $lang != LANG && file_exists( HOME . 'admin/lang/' . $lang . '.php' ) ){
		$constants[ 'LANG' ] = $lang;
	}

        /**
         * rewrite the settings file 
         */
        settings_rewrite( $SETTINGS, $DB, $PLUGINS, $constants );
	cache_clear( );

	/**
	 * reload the page to make sure all changes have
	 * taken effect
	 */
	header( 'location: settings.php?page=configuration&error=13' );
}


$javascript = '
$(document).ready(function(){

        $("#help-index").click(function(){

                fHelp( "' . $Template->e( 'help_configuration_index' ) . '" );

        });

        $("#help-maintenance").click(function(){

                fHelp( "' . $Template->e( 'help_configuration_maintenance' ) . '" );

        });

	$( "#help-diagnostic" ).click( function( ){

		fHelp( "' . $Template->e( 'help_configuration_diagnostic' ) . '" );

	});

	$( "#help-url" ).click( function( ){

		fHelp( "' . $Template->e( 'help_configuration_url' ) . '" );

	});

	$( "input[name=Maintenance]" ).change( function( ){
		var message = $( "#maintenance-message" );
		if( message.hasClass( "hidden" ) ){
			message.removeClass( "hidden" ).slideDown( "slow" );
		}
		else
			message.addClass( "hidden" ).slideUp( "slow" );
	});

});
';

$Template->add( 'javascript', $javascript );

$options = options( 'configuration_page_options' );

if( $SETTINGS[ 'maintenance' ] == 1 ){
	$maintenance = 'checked="checked"';
	$hidden = '';
}
else{
	$maintenance = '';
	$hidden = ' class="hidden"';
}

$maintenance = ( $SETTINGS[ 'maintenance' ] == 1 ) ? 'checked="checked"' : '';
$index = ( $SETTINGS[ 'index' ] == 1 ) ? 'checked="checked"' : '';
$diagnostic = ( $Template->diagnosticMode == 1 ) ? 'checked="checked"' : '';
$url = ( isset( $url ) ) ? $url : SITEURL;
$lang = ( isset( $lang ) ) ? $lang : LANG;
$maintenance_message = ( isset( $options[ 'maintenance_message' ] ) ) ?
	$options[ 'maintenance_message' ] :
	$SETTINGS[ 'site_title' ] . ' is undergoing maintenance. It should be back to normal shortly.'; 

$content='
<span class="header-img" id="header-Configuration">&nbsp;</span><h1 class="image-left">Configuration</h1></span>
<br/>

<form method="post" id="config-settings">
<table id="config-table" class="row-color">
	<col width="50%"/>
        <col width="50%"/>
	<tr>
		<th colspan="2">' . $Template->e( 'configuration_website_options' ) . '</th>
	</tr>
	<tr>
		<td>' . $Template->e( 'configuration_website_title' ) . ':</td>
		<td><input type="text" name="Title" value="' . $SETTINGS[ 'site_title' ] . '" class="input" /></td>
	</tr>
	<tr>
		<td>' . $Template->e( 'configuration_website_subtitle' ) . ':</td>
		<td><input type="text" name="SubTitle" value="' . $SETTINGS[ 'site_subtitle' ] . '" class="input" /></td>
	</tr>
	<tr>
		<td>' . $Template->e( 'configuration_website_url' ) . ': <a class="help link" id="help-url">&nbsp;</a></td>
		<td><input type="text" name="URL" value="' . SITEURL . '" class="input" /></td>
	</tr>
	<tr>
		<td>' . $Template->e( 'configuration_maintenance_mode' ) . ': <a class="help link" id="help-maintenance">&nbsp;</a></td>
		<td><input type="checkbox" name="Maintenance" class="checkbox" value="1" '.$maintenance.'/></td>
	</tr>
	<tr id="maintenance-message"' . $hidden . '>
		<td>' . $Template->e( 'configuration_maintenance_mode_message' ) . ':</td>
		<td>' . tinymce( 'options[maintenance_message]', $maintenance_message ) . '</td>
	</tr>
	<tr>
		<td>' . $Template->e( 'configuration_dont_index' ) . ': <a class="help link" id="help-index">&nbsp;</a></td>
		<td><input type="checkbox" name="Index" value="1" class="checkbox" '.$index.'/></td>
	</tr>
        <tr>
                <td>' . $Template->e( 'configuration_diagnostic_mode' ) . ': <a class="help link" id="help-diagnostic">&nbsp;</a></td>
                <td><input type="checkbox" name="DiagnosticMode" value="1" class="checkbox" '. $diagnostic .'/></td>
        </tr>';/*
        <tr>
                <td>' . $Template->e( 'configuration_language' ) . ': </td>
                <td><select name="lang">';

		$files = new DirectoryIterator( HOME . 'admin/lang' );
		foreach( $files as $file ){
			if( $file->isFile( ) && $file->getExtension( ) == 'php' ){
				$l = reset( explode( '.', $file->getFilename( ) ) );
				$content .= '<option';
				if( $l == $lang )
					$content .= ' selected="selected"';
				$content .= '>' . $l . '</option>';
			}
		}

$content .= '
		</select></td>
        </tr>*/
$content .= '
</table>
<input type="submit" id="config-save" name="settings_general" class="submit right" style="margin-right:10%" value="' . $Template->e( 'save' ) . '"/>
</form></br style="clear:both"/>
';

$Template->add('content',$content);

?>
