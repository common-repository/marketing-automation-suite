<?php

require_once('api_functions.php');
require_once('settings_tables.php');
require_once('mas_Form_fields_mappings.php');

// import tabs
require_once('settings_tab_general.php');
require_once('settings_tab_help.php');
require_once('settings_tab_troubleshooting.php');
require_once('settings_tab_ma_directory_form.php');


// Add sidebar link with icon 
add_action('admin_menu', 'mas_marketingAutomationPage');
function mas_marketingAutomationPage()
{
    add_menu_page(
        'Marketing Automation',
        'Marketing Automation',
        'manage_options',
        'marketing-automation',
        'marketing_automation_page',
        'dashicons-email-alt2'
    );


    //wp_register_script('custom-script', "/js/whiterabbit.js");
}

// Main settings page content
function marketing_automation_page()
{
    ?>
    <div class="wrap">
        <h1>Marketing Automation</h1>
        <?php
        $active_tab = sanitize_text_field($_GET['tab'] ?? 'general_settings');
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=marketing-automation&tab=general_settings"
               class="nav-tab <?php echo esc_html_e($active_tab == 'general_settings' ? 'nav-tab-active' : ''); ?>"><?php echo esc_html_e(__('General Settings', 'marketing-automation-suite')) ?></a>
            <a href="?page=marketing-automation&tab=help"
               class="nav-tab <?php echo esc_html_e($active_tab == 'help' ? 'nav-tab-active' : ''); ?>"><?php echo esc_html_e(__('Mapping', 'marketing-automation-suite')) ?></a>
            <a href="?page=marketing-automation&tab=ma_directory_form"
               class="nav-tab <?php echo esc_html_e($active_tab == 'ma_directory_form' ? 'nav-tab-active' : ''); ?>"><?php echo esc_html_e(__('MA Form Mapping', 'marketing-automation-suite')) ?></a>
            <a href="?page=marketing-automation&tab=troubleshooting"
               class="nav-tab <?php echo esc_html_e($active_tab == 'troubleshooting' ? 'nav-tab-active' : ''); ?>"><?php echo esc_html_e(__('Troubleshooting', 'marketing-automation-suite')) ?></a>
        </h2>
        <form method="post" action="">
            <br/>
            <?php
            switch ($active_tab) {
                case 'general_settings':
                    mas_getGeneralTabContent();
                    break;
                case 'troubleshooting':
                    mas_getTroubleshootingTabContent();
                    break;
                case 'help':
                    mas_getHelpTabContent();
                    break;
                case 'ma_directory_form':
                    if (!empty($_GET['connect']) && !empty($_GET['post_id'])) {
                        mas_call_maDirectoryFormTabContent($_GET['post_id']);
                    }
                    mas_getMaDirectoryFormTabContent();

                    break;
                default:
                    mas_getGeneralTabContent();
            }
            ?>
        </form>
    </div>
    <?php
}


// localhost notice
add_action('admin_notices', 'marketing_automation_localhost_notice');

function marketing_automation_localhost_notice()
{
    $screen = get_current_screen();
    if ($screen->id === 'toplevel_page_marketing-automation') {
        $is_localhost = strpos(str_replace(array('https://', 'http://'), '', get_home_url()), 'localhost');

        if ($is_localhost === 0) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('This plugin doesn\'t work on localhost URLs.', 'marketing-automation-suite') . '</p></div>';
        }
    }
}
