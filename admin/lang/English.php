<?php

/**
 * Language Page - English, Furasta.Org
 *
 * This file contains an array of errors and content
 * in the language stated above.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_lang
 */

$lang_errors = array( 
	'1' => 'Page Updated',
	'2' => 'Page Deleted',
	'3' => 'Page Created',
	'4' => 'A page named "%1" already exists or is reserved for the system.',
	'5' => 'The %1 is a required field, please do not leave it blank.', 
	'6' => 'Please enter a valid email in the %1 field.', 
	'7' => 'The %1 field is not valid.',
	'8' => 'The %1 field must be at least %2 characters long.',
	'9' => 'The %1 must match the %2 field.', 
	'10'=> 'Please enter a valid URL in the %1 field.',
	'11'=> 'User not activated.',
	'12'=> 'Login details are incorrect',
	'13'=> 'Details Updated',
	'14'=> 'A new version of Furasta.Org is available. <a href="%2">Click here to download %1</a>',
	'15'=> 'Pages Trashed',
	'16'=> 'Pages Restored',
	'17'=> 'Pages Deleted',
	'18'=> 'The %1 field must be at most %2 characters long'
);

$lang = array( 

	// general terms
	'yes' => 'Yes',
	'no' => 'No',
	'name' => 'Name',
	'author' => 'Author',
	'type' => 'Type',
	'edited' => 'Edited',
	'new' => 'New',
	'pages' => 'Pages',
	'trash' => 'Trash',
	'groups' => 'Groups',
	'users' => 'Users',
	'template' => 'Template',
	'version' => 'Version',
	'forum' => 'Forum',
	'bug_tracker' => 'Bug Tracker',
	'group' => 'Group',
	'parent' => 'Parent',
	'permissions' => 'Permissions',
	'loading' => 'Loading',
	'title' => 'Title',
	'save' => 'Save',
	'delete' => 'Delete',
	'restore' => 'Restore',
	'edit' => 'Edit',
	'date' => 'Date',
	'user' => 'User',
	'email' => 'Email',
	'options' => 'Options',
	'normal' => 'Normal',
	'default' => 'Default',

	// menu items
	'menu_overview' => 'Overview',
	'menu_pages' => 'Pages',
	'menu_edit_pages' => 'Edit Pages',
	'menu_new_page' => 'New Page',
	'menu_trash' => 'Trash',
	'menu_users_groups' => 'Users & Groups',
	'menu_edit_users' => 'Edit Users',
	'menu_edit_groups' => 'Edit Groups',
	'menu_settings' => 'Settings',
	'menu_configuration' => 'Configuration',
	'menu_template' => 'Template',
	'menu_plugins' => 'Plugins',
	'menu_update' => 'Update',
	
	// Overview Page
	'overview_recently_trashed' => 'Recently Trashed',
	'overview_recently_edited' => 'Recently Edited',
	'overview_furasta_development_blog' => 'Furasta.Org Development Blog',
	'overview_website_overview' => 'Website Overview',

	// Pages
	'pages_new_subpage' => 'New Subpage',
	'pages_edit_page' => 'Edit Page',
	'pages_list_go_button' => 'Go',
	'pages_list_expand_collapse_all' => 'Expand / Collapse all',
	'pages_show_options' => 'Show Options',
	'pages_hide_options' => 'Hide Options',
	'pages_is_home' => 'Is Home Page',
	'pages_hide_in_navigation' => 'Hide in Navigation',
	'pages_name_pattern' => 'The name field must be between 2 and 40 characters in length. It must only contain alphabetical characters, numbers and spaces.',
	'pages_permissions_error' => 'You have insufficient privelages to edit this page. Please contact one of the administrators',
	'pages_permissions_admin_area' => 'Admin Area',
	'pages_permissions_frontend' => 'Frontend',
	'pages_permissions_who_can_edit' => 'Who can edit this page',
	'pages_permissions_who_can_see' => 'Who can see this page',
	'pages_permissions_everyone' => 'Everyone',
	'pages_permissions_selected' => 'Selected',
	'pages_permissions_select_by_user_group' => 'Select by User or by Group',
	'pages_help_reorder' => 'To reorder pages simply drag and drop them where you want. You can also make pages into subpages by dropping them on top of a page when it is highlighted.',
	

	// Trash
	'trash_empty' => 'No Trash!',
	'trash_go_button' => 'Go',
	'trash_confirm_restore' => 'Are you sure you want to restore this page? Certain features have not been preserved, such as page parent, type, and template settings.',

	// Users
	'users_password' => 'Password',
	'users_repeat_pass' => 'Repeat Password',
	'users_invalid_details' => 'The details you provided are not valid. Please try creating a user again. The cause may be that email adresses may only be used by one user.',
	'users_user_added' => 'An email has been sent to your address for validation. This user will not be able to login until the email is validated.',

	// Groups
	'groups_delete_prompt' => 'Are you sure you want to delete this group? <br/><b>ALL USERS IN THE GROUP WILL BE DELETED</b>',
	'groups_invalid_details' => 'The details you provided are not valid. Please try creating a group again. The group name may already be taken.',
	
	// Configuration
	'configuration_website_options' => 'Website Options',
	'configuration_website_title' => 'Title',
	'configuration_website_subtitle' => 'Subtitle',
	'configuration_website_url' => 'URL',
	'configuration_maintenance_mode' => 'Maintenance Mode',
	'configuration_maintenance_mode_message' => 'Maintenance Mode Message',
	'configuration_dont_index' => 'Don\'t Index Website',
	'configuration_diagnostic_mode' => 'Diagnostic Mode',
	'configuration_language' => 'Language',
	

	// Prompts
	'prompt_yes' => 'Yes',
	'prompt_no' => 'No',
	'prompt_save' => 'Save',
	'prompt_close' => 'Close',
	'prompt_cancel' => 'Cancel',
	'prompt_confirm' => 'Are you sure?',
	'prompt_alert' => 'Warning!',
	'prompt_help' => 'Information',
	'prompt_confirm_delete' => 'Are you sure you wish to delete this item?',
	'prompt_confirm_multiple' => 'Are you sure you wish to perform a multiple action?',
	'prompt_insufficient_privellages' => 'Sorry, you have insufficient privellages to perform this action',
	'prompt_error' => 'There has been an error processing your request. Please try again.',
	

	// Help prompts
	'help_configuration_index' => 'Ticking this box will force search engines such as Google, Yahoo etc not to index this website. This option should be enabled to make the website more private, though people will still be able to access it through direct URLs.',
	'help_configuration_maintenance' => 'Ticking this box will enable Maintenance Mode; a feature which should be enabled while making sitewide changes. For example, if you are performing a re-structure of your website and you enable Maintenance Mode people who view the website will see a maintenance notice and will not be able to access any content.',
	'help_configuration_diagnostic' => '<b>Note: For developers only</b><br/>The CMS carrys out all kinds of caching to make page loads quicker, including that of CSS and JavaScript. If you want to temporarily disable this feature for development reasons then you can enable Diagnostic Mode. During this time there will be no caching of CSS or JavaScript, so it is recommended to enable it again when finished testing',
	'help_configuration_url' => 'The default URL for the website. All links will use this URL. Some may want to add remove the \"www.\"',

	// error
	'error_unknown' => 'An unknown error has occured. Please refresh the page and try again',
 	'error_permissions_title' => 'Permissions Error',
	'error_javascript_title' => 'Javascriot Error',
	'error_javascript_body' => 'You must have a javascript enabled browser to contine. The jQuery library is also required. If you do not have internet access then you may not have access to jQuery.',

);

?>
