<?php

require_once('utility_functions.php');
require_once('mas_Form_fields_mappings.php');
require_once('mas_Wpforms_MA_mapping_fields.php');


// process posted content using the form fields mappings definition
// returns $user_info if the form is complaint, null if not
function mas_processPostedContent($posted_data, $form_type)
{
    // log $posted data to terminal if the corresponding option is enabled
    if ($GLOBALS['log_to_terminal']) {
        mas_WriteLog('======== FORM DATA ========');
        mas_WriteLog($posted_data);
    }

    // default user info, same for all requests
    $user_info = array();

    // if > 0 at the end, trigger error
    $missing_required_fields = array();

    switch ($form_type) {
        case 'WPCF7':
            $mapping_name = 'MasWpcf7Mapping';
            break;
        default:
            break;
    }

    // cycle over all the fields from the fields mapping
    foreach ($GLOBALS['FORM_FIELDS'] as $field) {
        $current_form_type_mapping = $field->mas_getMapping($mapping_name);
        //$subfield = $current_form_type_mapping->subfield ?? null;
        $allowed_aliases = $current_form_type_mapping->allowed_aliases;
        $data_found = false;

        // for each field, match the data inside testing the allowed aliases (if any, data_found = false if none)
        foreach ($allowed_aliases as $allowed_alias) {
            $allowed_alias = strtolower($allowed_alias);
            if (array_key_exists($allowed_alias, $posted_data)) {// at least one of the alias must match, only the first will be considered
                //$form_value = $subfield ? $posted_data[$allowed_alias][$subfield] : $posted_data[$allowed_alias];
                $form_value = $posted_data[$allowed_alias];
                $user_info[$field->id] = $current_form_type_mapping->mas_parseData($form_value); // parse the data depending on corresponding data type defined in the mapping
                $data_found = true;
                unset($posted_data[$allowed_alias]);
                break;
            }
        }

        if (!$data_found) {
            if ($field->required) {
                // add the field to $missing_required_fields, in order to trigger an error
                $missing_required_fields[] = $field->name;
            } else {
                // set default value
                $user_info[$field->id] = $field->default_value;
            }
        }
    }

    /*add input NOT mapping into MA*/
    foreach ($posted_data as $id => $post) {
        if (!array_key_exists($id, $user_info)) {
            $user_info[$id] = $post;
        }
    }

    if (count($missing_required_fields) > 0) {
        mas_logAction('ERROR', $form_type, 'Form submission detected. Missing required fields: ' . implode(", ", $missing_required_fields));
        return null;
    } else {
        mas_logAction('INFO', $form_type, 'Form submission detected.');
        $user_info = mas_finalizeUserInfo($user_info);
        return $user_info;
    }
}

/* -------------------------------------------- */
/*                    PARSERS                   */
/* -------------------------------------------- */

function mas_finalizeUserInfo($user_info)
{

    $mas = new Mas();

    /* ------------ REMOVE EMPTY FIELDS ----------- */
    $user_info = array_filter($user_info, function ($value) {
        return $value !== '';
    });
    /* -------------- STANDARD FIELDS ------------- */
    $user_info["primary_key"] = "email";
    $user_info["type"] = "Prospect";

    /* ---------------- GDPR FIELDS --------------- */
    // add _date field related to corresponding gdpr fields, if present
    $keys_with_associated_date = [
        "gdpr_marketing",
        "gdpr_profiling",
        "gdpr_thirdparties",
        "gdpr_outsideeu",
        "gdpr_outcollection",
        "gdpr_other1",
        "gdpr_other2",
        "gdpr_other3",
    ];
    foreach ($keys_with_associated_date as $key) {
        if (array_key_exists($key, $user_info)) {
            $user_info[$key . "_date"] = date('Y-m-d');
        }
    }
    $keys_with_associated_date = [
        $mas->placeholderCompany("gdpr_marketing"),
        $mas->placeholderCompany("gdpr_profiling"),
        $mas->placeholderCompany("gdpr_thirdparties"),
        $mas->placeholderCompany("gdpr_outsideeu"),
        $mas->placeholderCompany("gdpr_outcollection"),
        $mas->placeholderCompany("gdpr_other1"),
        $mas->placeholderCompany("gdpr_other2"),
        $mas->placeholderCompany("gdpr_other3"),
    ];

    foreach ($keys_with_associated_date as $key) {
        if (array_key_exists($key, $user_info)) {
            //$gdpr = str_replace($GLOBALS['company_prefix_placeholder'], '', $key);
            //$user_info[$gdpr . "_date" . $GLOBALS['company_prefix_placeholder']] = date('Y-m-d');
            $user_info[$key . "_date"] = date('Y-m-d');
        }
    }


    /* --------------- GENDER FIELD --------------- */
    // parse gender to map with known ones
    if (array_key_exists('gender', $user_info)) {
        $gender = strtolower($user_info['gender']);

        $male_array = array('male', 'man', 'boy', 'maschio', 'uomo', 'ragazzo',);
        $female_array = array('female', 'woman', 'girl', 'femmina', 'donna', 'ragazza',);

        if (in_array($gender, $male_array)) {
            $user_info['gender'] = 'Male';
        } else if (in_array($gender, $female_array)) {
            $user_info['gender'] = 'Female';
        } else {
            unset($user_info['gender']);
        }
    }
    /* --------------- TAG FIELDS --------------- */
    for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
        if (array_key_exists('tag' . $i, $user_info)) {
            $user_info['tags'][]['name'] = $user_info['tag' . $i];
            unset($user_info['tag' . $i]);
        }
    }
    for ($i = 1; $i <= $GLOBALS['tags_num']; $i++) {
        /*if (array_key_exists('tag' . $i . $GLOBALS['company_prefix_placeholder'], $user_info)) {
            $user_info['tags' . $GLOBALS['company_prefix_placeholder']][]['name'] = $user_info['tag' . $i . $GLOBALS['company_prefix_placeholder']];
            unset($user_info['tag' . $i . $GLOBALS['company_prefix_placeholder']]);
        }*/
        if (array_key_exists($mas->placeholderCompany('tag' . $i), $user_info)) {
            $user_info[$mas->placeholderCompany('tags')][]['name'] = $user_info[$mas->placeholderCompany('tag' . $i)];
            unset($user_info[$mas->placeholderCompany('tag' . $i)]);
        }

    }

    /* --------------- CUSTOM VARIABLE FIELDS --------------- */

    foreach ($user_info as $name => $value) {
        if (substr($name, 0, strlen($GLOBALS['cv_placeholder'])) == $GLOBALS['cv_placeholder']) {
            $nameCv = substr($name, strlen($GLOBALS['cv_placeholder']));
            if (substr($nameCv, strlen($nameCv) - strlen($GLOBALS['company_prefix_placeholder'])) !== $GLOBALS['company_suffix_placeholder']) {
                $user_info['custom_variables'][] = ['name' => $nameCv, 'value' => $value];
                unset($user_info[$name]);
            } else {
                $nameCv = str_replace($GLOBALS['company_suffix_placeholder'], '', $nameCv);
                $user_info['custom_variables' . $GLOBALS['company_suffix_placeholder']][] = ['name' => $nameCv, 'value' => $value];
                unset($user_info[$name]);
            }
        }
    }


    //mas_WriteLog($user_info);
    return $user_info;
}

// define the function to call after all the "email sent" hooks
/**
 * @throws Exception
 */
function mas_afterGenericEmail($form_type, $form_data)
{
    // Parse form data based on form type
    switch ($form_type) {
        case 'WPCF7':
            $submission = WPCF7_Submission::get_instance();
            if ($submission) {
                $posted_data = $submission->get_posted_data();
                $user_info = mas_processPostedContent($posted_data, $form_type);
            }
            break;
        default:
            $user_info = null;
            break;
    }

    if ($user_info && get_option($GLOBALS['opt_plugin_is_enabled'])) {
        //mas_personSave($user_info);
        $wpcf7 = WPCF7_ContactForm::get_current();
        $post_meta_ma_directory_form = get_post_meta($wpcf7->id(), $GLOBALS['post_meta_ma_directory_form'], true);
        $db = json_decode($post_meta_ma_directory_form, true);

        if (empty($db)) {
            $user_info['form_name'] = mas_getFormName($wpcf7->title(), $wpcf7->id());
        } else {
            $user_info['form_name'] = $db['sync_status'] ? $db['sync_data']['form'] ?? '' : '';
        }
        //$user_info['form_name'] = $db['sync_data']['form'] ?? '';
        mas_eventSave($user_info);
    }
}


/**
 * @param array $data
 * @param int $post_id
 * @return array
 * @throws Exception
 */
function mas_preparePayloadDirectoryForm(array $data, int $post_id): array
{
    $dir_info = [];
    $mas = new Mas();
    $decoded_auth_token = mas_decodeJwt(get_option($GLOBALS['opt_personal_token']));
    $dir_info['directory'] = json_decode($decoded_auth_token)->{'site'};
    $dir_info['form'] = $data['form'][0];
    $dateTimeFormat = get_option('date_format') . ' ' . get_option('time_format');
    $timezone = new DateTimeZone(get_option('timezone_string'));
    $now = new DateTime("now", $timezone);
    $dir_info['form_date_synchronized'] = $now->format($dateTimeFormat);
    $result = [];
    $index = 0;
    foreach ($data['payload'] as $id => $input) {
        $result['sync_payload'][] = $id;
        $dir_info['elements'][$index]['name'] = $mas->getMaFieldName($input['name']) ?? $input['name'];
        $dir_info['elements'][$index]['field_name'] = $input['name'];
        $dir_info['elements'][$index]['description'] = mas_convertLabel($input['description'] ?? $input['name']);
        $dir_info['elements'][$index]['type'] = $input['type'];
        $index++;
    }

    if (!empty($dir_info) && get_option($GLOBALS['opt_plugin_is_enabled'])) {
        $response = mas_directoryForm($dir_info);
        $result['sync_status'] = $response['code'] == 200;
        $result['sync_response'] = $response;
        if ($response['success']) {
            $result['sync_data'] = $dir_info;
        } else {
            $post_meta_post_meta_ma_directory_form = get_post_meta($post_id, $GLOBALS['post_meta_ma_directory_form'], true);
            $db = json_decode($post_meta_post_meta_ma_directory_form, true);
            $result['sync_payload'] = $db['sync_payload'];
            $result['sync_data'] = $db['sync_data'];
        }
        update_post_meta($post_id, $GLOBALS['post_meta_ma_directory_form'], json_encode($result));
    } else {
        $result['sync_status'] = false;
        $result['sync_response']['code'] = 500;
        $result['sync_response']['error'][] =  __('No Enabled API Plugin', 'marketing-automation-suite');

    }

    return $result;

}

