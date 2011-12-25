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
                'required'      =>      true
        ),
	'SubTitle' => array(
		'required'	=>	true
	),
	'URL' => array(
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
	 * stripslashes from the settings array
	 */
	$SETTINGS = stripslashes_array( $SETTINGS );
	$Template->runtimeError( '13' );
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

});
';

$Template->add( 'javascript', $javascript );

$maintenance = ( $SETTINGS[ 'maintenance' ] == 1 ) ? 'checked="checked"' : '';
$index = ( $SETTINGS[ 'index' ] == 1 ) ? 'checked="checked"' : '';
$diagnostic = ( $Template->diagnosticMode == 1 ) ? 'checked="checked"' : '';
$url = ( isset( $url ) ) ? $url : SITEURL;
$lang = ( isset( $lang ) ) ? $lang : LANG;

$content='
<span class="header-img" id="header-Configuration">&nbsp;</span><h1 class="image-left">Configuration</h1></span>
<br/>

<form method="post" id="config-settings">
<table id="config-table" class="row-color">
	<col width="50%"/>
        <col width="50%"/>
	<tr>
		<th colspan="2">Website Options</th>
	</tr>
	<tr>
		<td>Title:</td>
		<td><input type="text" name="Title" value="' . $SETTINGS[ 'site_title' ] . '" class="input" /></td>
	</tr>
	<tr>
		<td>Sub Title:</td>
		<td><input type="text" name="SubTitle" value="' . $SETTINGS[ 'site_subtitle' ] . '" class="input" /></td>
	</tr>
	<tr>
		<td>Website URL: <a class="help link" id="help-url">&nbsp;</a></td>
		<td><input type="text" name="URL" value="' . SITEURL . '" class="input" /></td>
	</tr>
	<tr>
		<td>Maintenance Mode: <a class="help link" id="help-maintenance">&nbsp;</a></td>
		<td><input type="checkbox" name="Maintenance" class="checkbox" value="1" '.$maintenance.'/></td>
	</tr>
	<tr>
		<td>Don\'t Index Website: <a class="help link" id="help-index">&nbsp;</a></td>
		<td><input type="checkbox" name="Index" value="1" class="checkbox" '.$index.'/></td>
	</tr>
        <tr>
                <td>Diagnostic Mode: <a class="help link" id="help-diagnostic">&nbsp;</a></td>
                <td><input type="checkbox" name="DiagnosticMode" value="1" class="checkbox" '. $diagnostic .'/></td>
        </tr>
        <tr>
                <td>Language: </td>
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
        </tr>
</table>
<input type="submit" id="config-save" name="settings_general" class="submit right" style="margin-right:10%" value="Save"/>
</form></br style="clear:both"/>
';

$Template->add('content',$content);

?>
