<?php
require_once('mas.php');
/* -------------------------------------------- */
/*                     URLS                     */
/* -------------------------------------------- */

$GLOBALS['sender_site_url'] = str_replace(array('https://', 'http://'), '', get_option('home'));

/* -------------------------------------------- */
/*          Wordpress options names map         */
/* -------------------------------------------- */

$GLOBALS['opt_plugin_is_enabled'] = 'marketing_automation_enable';
$GLOBALS['opt_base_url'] = 'marketing_automation_b_url';
$GLOBALS['opt_auth_token'] = 'marketing_automation_ausfkw_eNigek';
$GLOBALS['opt_logs'] = 'marketing_automation_logs';
$GLOBALS['opt_personal_token'] = 'marketing_automation_prsf_ebyokgn';
$GLOBALS['opt_cv_list'] = 'marketing_automation_custom_variables';

/* -------------------------------------------- */
/*                   Endpoints                  */
/* -------------------------------------------- */

$GLOBALS['endpoint_login'] = '/rest/connect/login';
$GLOBALS['endpoint_person_save'] = '/rest/person/save';
$GLOBALS['endpoint_cv_list'] = '/rest/CustomVariable/list';
$GLOBALS['endpoint_directory_form'] = '/rest/directory/form';
$GLOBALS['endpoint_event_save'] = '/rest/event/save';


/* -------------------------------------------- */
/*                   Api Constant               */
/* -------------------------------------------- */
$GLOBALS['form_name_placeholder'] = 'form_name';
$GLOBALS['form_input_name_placeholder'] = 'ma_input_label';
$GLOBALS['cv_placeholder'] = 'cv_';


/* -------------------------------------------- */
/*                 Misc Settings                */
/* -------------------------------------------- */

$GLOBALS['tags_num'] = 10;
$GLOBALS['max_logs'] = 50;
$GLOBALS['company_prefix_placeholder'] = 'company_';
$GLOBALS['company_suffix_placeholder'] = '_company';
$GLOBALS['company_field_hide'] = true;

$GLOBALS['log_to_terminal'] = true; // enable only for debug. Especially useful to get information regarding forms field types.

$GLOBALS['post_meta_ma_directory_form'] = '_ma_directory_form';

