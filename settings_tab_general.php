<?php

$submitted = false;

require_once('utility_functions.php');

function mas_getGeneralTabContent()
{
    settings_fields('mas_generalSettings');
    do_settings_sections('mas_generalSettings');
    submit_button('Save');
    if (mas_validateJwtAndLogin(get_option($GLOBALS['opt_personal_token']))) {
        submit_button(__('Disable Sync', 'marketing-automation-suite'), 'ma-button-danger', 'DisableSync');
    }

}

// Register settings  
add_action('admin_enqueue_scripts', 'mas_generalSettings');
// Register notices  
add_action('admin_notices', 'mas_generalNotices');

/* -------------------------------------------- */
/*                   Settings                   */
/* -------------------------------------------- */

function mas_generalSettings()
{
    $screen = get_current_screen();
    if ($screen->id === 'toplevel_page_marketing-automation') {
        if ($_POST && sanitize_text_field($_POST['opt_personal_token'])) {
            update_option($GLOBALS['opt_personal_token'], sanitize_text_field($_POST['opt_personal_token']));
        }
        register_setting('mas_generalSettings', 'opt_personal_token');
        add_settings_section('general_settings_section', __('General Settings', 'marketing-automation-suite'), 'mas_generalSettingsSectionCallback', 'mas_generalSettings');
        add_settings_field('marketing_automation_enable', __('Enable API Marketing Automation', 'marketing-automation-suite'), 'mas_enableCallback', 'mas_generalSettings', 'general_settings_section');
        add_settings_field('opt_personal_token', __('Personal Token', 'marketing-automation-suite'), 'mas_optPersonalTokenCallback', 'mas_generalSettings', 'general_settings_section');
    }
    //insert CSS
    wp_register_style('marketing_automation_css', plugin_dir_url(__FILE__) . 'css/admin_dashboard_ma.css');
    wp_enqueue_style('marketing_automation_css');


    /*
    wp_enqueue_scripts('prefix_bootstrap');
    wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js');
    wp_enqueue_style('prefix_bootstrap');
    wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
     */

}

function mas_generalSettingsSectionCallback()
{
    echo '<p>' . __('Please fill in the general details provided from Marketing Automation settings panel', 'marketing-automation-suite') . '</p>';
}

function mas_optPersonalTokenCallback()
{
    $opt_personal_token = get_option($GLOBALS['opt_personal_token']);
    echo '<textarea rows="4" style="width:80%;" type="text" id="opt_personal_token" name="opt_personal_token" >' . esc_attr($opt_personal_token) . '</textarea>';
}

function mas_enableCallback()
{
    $marketing_automation_enable = get_option($GLOBALS['opt_plugin_is_enabled']);
    echo '<input type="checkbox" id="marketing_automation_enable" name="marketing_automation_enable" value="1" ' . checked(1, $marketing_automation_enable, false) . '>';
}

/**
 * @param $opt_personal_token
 * @return bool
 * @throws Exception
 */

function mas_validateJwtAndLogin($opt_personal_token): bool
{
    // check JWT validity
    $pattern = '/^([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_\-\+\/=]*)/';
    $matches = preg_match($pattern, $opt_personal_token);

    if ($matches) {
        // set opt_base_url option
        update_option($GLOBALS['opt_base_url'], json_decode(mas_decodeJwt($opt_personal_token))->{'ma_suite_base_url'});

        // validate against the server and get the auth token
        return mas_loginAndGetAuthToken();
    } else {
        return false;
    }
}

function mas_deleteSync(): bool
{
    if (post_type_exists('wpforms')) {
        $args_wpforms = ['post_type' => 'wpforms', 'posts_per_page' => -1];
        $data_wpforms = get_posts($args_wpforms);
    }
    if (post_type_exists('wpcf7_contact_form')) {
        $args_wpcf7 = ['post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1];
        $data_wpcf7 = get_posts($args_wpcf7);
    }

    $posts = array_merge($data_wpcf7 ?? [], $data_wpforms ?? []);
    foreach ($posts as $post) {
        update_post_meta($post->ID, $GLOBALS['post_meta_ma_directory_form'], null);
    }

    return true;
}


/**
 * @return void
 * @throws Exception
 */
function mas_generalNotices()
{
    $screen = get_current_screen();

    if ($screen->id === 'toplevel_page_marketing-automation') {
        if (mas_validateJwtAndLogin(get_option($GLOBALS['opt_personal_token']))) {
            if ($_POST && isset($_POST['submit']) || isset($_POST['DisableSync'])) {
                if (isset($_POST['DisableSync'])) {
                    mas_deleteSync();
                    update_option($GLOBALS['opt_plugin_is_enabled'], false);
                    update_option($GLOBALS['opt_personal_token'], '');
                    mas_logAction('OK', 'SETTINGS UPDATED', 'Disabled Sync.');
                } else {
                    # if jwt is valid, submit the marketing_automation_enable checkbox also
                    if (!empty($_POST['marketing_automation_enable'])) {
                        update_option($GLOBALS['opt_plugin_is_enabled'], true);
                    } else {
                        update_option($GLOBALS['opt_plugin_is_enabled'], false);
                    }
                    // mas_WriteLog(mas_CvList());
                    mas_CvList();
                    mas_logAction('OK', 'SETTINGS UPDATED', 'Configuration saved. Successfully logged in Marketing Automation Suite.');
                    echo '<div class="notice notice-success connected is-dismissible"><p>' . __('Connection with Marketing Automation Suite was successful.', 'marketing-automation-suite') . '</p></div>';
                }
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuration saved.', 'marketing-automation-suite') . '.</p></div>';
            } elseif (sanitize_text_field($_GET['tab'] ?? '') == 'general_settings' || sanitize_text_field($_GET['tab'] ?? '') == '') {
                echo '<div class="notice notice-success connected is-dismissible"><p>' . __('Connection verified.', 'marketing-automation-suite') . '</p></div>';
            }
        } else {
            if ($_POST && sanitize_text_field($_POST['submit'])) {
                # if wjt is invalid, disable plugin
                update_option($GLOBALS['opt_plugin_is_enabled'], false);
            }
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Disconnected. Please check your configuration.', 'marketing-automation-suite') . '</p></div>';
        }
    }
}
