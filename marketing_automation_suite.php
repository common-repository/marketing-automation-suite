<?php
/*
Plugin Name: Marketing Automation Suite
Description: Manage Marketing Automation integration and settings.
Version: 2.0.4
Author: Marketing Automation
Domain Path: /languages
*/

require_once('constants.php');
require_once('form_actions.php');
require_once('settings_page_main.php');


//WPForm integration
add_filter('wpforms_builder_settings_sections', [
    new MasWpFormsMAMappingField(), 'mas_wpformsSettingsSection'
], 20, 2);
add_filter('wpforms_form_settings_panel_content', [
    new MasWpFormsMAMappingField(), 'mas_wpformsSettingsSectionContent'
], 20, 2);


// Connect the "after_generic_email" hook to the "email sent" event of WPCF7
add_action('wpcf7_mail_sent', function ($contact_form) {
    mas_afterGenericEmail('WPCF7', $contact_form);
}, 10, 1);



add_action('wpforms_process_complete', [
    new MasWpFormsMAMappingField(), 'mas_prepareWpformsDataApiCall'
], 10, 4);

load_plugin_textdomain('marketing-automation-suite', false, basename( dirname( __FILE__ ) ) . '/languages/');

/*add_action('wpforms_process_complete', function ($fields, $entry, $form_data, $entry_id) {
    after_generic_email('WPFORMS', $fields);
}, 10, 4);*/