<?php

require_once('utility_functions.php');

/**
 * Login Ma api call
 *
 * @return bool
 * @throws Exception
 */
function mas_loginAndGetAuthToken(): bool
{
    $sender_site_url = $GLOBALS['sender_site_url'];
    $personal_token = get_option($GLOBALS['opt_personal_token']);
    $settings_base_url = get_option($GLOBALS['opt_base_url']);
    $endpoint_login = $GLOBALS['endpoint_login'];
    $url = $settings_base_url . $endpoint_login;

    $headers = array(
        'MA-Directory' => $personal_token,
        'Content-Type' => 'application/json'
    );

    $body = json_encode(array('site' => $sender_site_url));

    $response = wp_remote_post(
        $url,
        array(
            'headers' => $headers,
            'body' => $body
        )
    );

    if (is_wp_error($response)) {
        $success = false;
        if ($GLOBALS['log_to_terminal']) {
            error_log(json_encode($response));
            mas_deleteSync();
            update_option($GLOBALS['opt_plugin_is_enabled'], false);
        }
    } else {
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);
        if (isset($json['connect']['token'])) {
            $decoded_auth_token = mas_decodeJwt($json['connect']['token']);
            update_option($GLOBALS['opt_auth_token'], json_decode($decoded_auth_token)->{'MA-Customer'});
            $success = true;
        } else {
            $success = false;
            if ($GLOBALS['log_to_terminal']) {
                error_log(json_encode($response));
            }
        }
    }
    if (!$success) {
        mas_logAction('ERROR', 'AUTH', "Authentication problem.");
        if (!is_wp_error($response)) {
            if ($response['response']['code'] == 404) {
               mas_deleteSync();
              update_option($GLOBALS['opt_plugin_is_enabled'], false);
        }
    }

    }

    return $success;
}

/**
 * Api call Person Save
 *
 * @return bool
 */

function mas_CvList(): bool
{
    $opt_auth_token = get_option($GLOBALS['opt_auth_token']);
    $settings_base_url = get_option($GLOBALS['opt_base_url']);
    $endpoint_cv_list = $GLOBALS['endpoint_cv_list'];
    $url = $settings_base_url . $endpoint_cv_list;
    $headers = array(
        'MA-Customer' => $opt_auth_token,
        'Content-Type' => 'application/json'
    );

    $response = wp_remote_get(
        $url,
        array(
            'headers' => $headers,
        )
    );
    $success = false;
    $cv_ma = [];
    if (is_wp_error($response)) {
        if ($GLOBALS['log_to_terminal']) {
            error_log(json_encode($response));
        }
    } else {
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);
        if (isset($json['custom_variables'])) {
            $success = true;

            foreach ($json['custom_variables'] as $cv) {
                if ($cv['group_type'] == 'standard') {
                    $cv_ma[] = ['name' => 'cv_' . $cv['name'], 'type' => $cv['type']];
                }
            }


        } else {
            $success = false;
            if ($GLOBALS['log_to_terminal']) {
                error_log(json_encode($body));
            }
        }
    }
    //mas_WriteLog($cv_ma);
    // mas_WriteLog($GLOBALS['opt_cv_list']);
    //mas_WriteLog($GLOBALS['log_to_terminal']);
    update_option($GLOBALS['opt_cv_list'], $cv_ma);
    return $success;
}

/**
 * @param array $user_info
 * @return bool
 * @throws Exception
 */
function mas_personSave(array $user_info): bool
{
    $opt_auth_token = get_option($GLOBALS['opt_auth_token']);
    $settings_base_url = get_option($GLOBALS['opt_base_url']);
    $endpoint_person_save = $GLOBALS['endpoint_person_save'];
    $url = $settings_base_url . $endpoint_person_save;
    $headers = array(
        'MA-Customer' => $opt_auth_token,
        'Content-Type' => 'application/json'
    );

    $data = array_filter($user_info, function ($v) {
        return !is_null($v);
    });


    $body = json_encode($data);

    $response = wp_remote_post(
        $url,
        array(
            'headers' => $headers,
            'body' => $body
        )
    );

    if (is_wp_error($response)) {
        $success = false;
        if ($GLOBALS['log_to_terminal']) {
            error_log(json_encode($response));
        }
    } else {
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);

        if (isset($json['result']) && $json['result'] == True) {
            $success = true;
        } else {
            $success = false;
            if (isset($json['code']) && $json['code'] == 401) {
                # if unauthorized, disable the enable settings flag
                update_option($GLOBALS['opt_plugin_is_enabled'], true);
                mas_logAction('ERROR', 'AUTH', "Your personal token is not valid. 'Enable Marketing Automation' setting automatically disable.");
            }
            if ($GLOBALS['log_to_terminal']) {
                error_log(json_encode($response));
            }
        }
    }

    // log results
    if ($success) {
        mas_logAction('OK', 'SAVE PERSON', "Person '" . $user_info['first_name'] . " " . $user_info['last_name'] . "' saved in Marketing Automation Suite.");
    } else {
        mas_logAction('ERROR', 'SAVE PERSON', "Error saving person '" . $user_info['first_name'] . " " . $user_info['last_name'] . "' in Marketing Automation Suite.");
    }

    return $success;
}


/**
 * @param array $event_save_data
 * @return bool
 * @throws Exception
 */
function mas_eventSave(array $event_save_data): bool
{
    $sender_site_url = $GLOBALS['sender_site_url'];
    $opt_auth_token = get_option($GLOBALS['opt_auth_token']);
    $settings_base_url = get_option($GLOBALS['opt_base_url']);
    $url = $settings_base_url . $GLOBALS['endpoint_event_save'];
    $contact = ['primaryKey' => 'email', 'value' => ''];

    if (!empty($event_save_data['contact_code'])) {

    } else {
        $contact['primaryKey'] = $event_save_data['primary_key'];
        $contact['value'] = $event_save_data['email_1'];
    }

    unset($event_save_data['primaryKey']);

    $event_save_structure = ['category' => 'form', 'action' => 'submit',
        'source' => $sender_site_url,
        'contact' => $contact,
        'payload' => ['type' => 'Prospect', 'form' => '']
    ];

    $event_save_structure['payload']['form'] = $event_save_data['form_name'];
    unset($event_save_data['form_name']);

    foreach ($event_save_data as $id => $value) {
        $event_save_structure['payload'][$id] = $value;
    }

    $headers = array(
        'MA-Customer' => $opt_auth_token,
        'Content-Type' => 'application/json'
    );

    $data = array_filter($event_save_structure, function ($v) {
        return !is_null($v);
    });

    $body = json_encode($data);

    if (!get_option($GLOBALS['opt_plugin_is_enabled'])) {
        return false;
    }

    if (!mas_validateJwtAndLogin(get_option($GLOBALS['opt_personal_token']))) {
        return false;
    }

    $response = wp_remote_post(
        $url,
        array(
            'headers' => $headers,
            'body' => $body
        )
    );

    if (is_wp_error($response)) {
        $success = false;
        if ($GLOBALS['log_to_terminal']) {
            error_log(json_encode($response));
        }
    } else {
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);

        if (isset($json['result']) && $json['result'] == True) {
            $success = true;
        } else {
            $success = false;
            if (isset($json['code']) && $json['code'] == 401) {
                # if unauthorized, disable the enable settings flag
                update_option($GLOBALS['opt_plugin_is_enabled'], true);
                mas_logAction('ERROR', 'AUTH', "Your personal token is not valid. 'Enable Marketing Automation' setting automatically disable.");
            }
            if ($GLOBALS['log_to_terminal']) {
                error_log(json_encode($response));
            }
        }
    }

    $view = $event_save_data['email_1'] ?? '';
    if (empty($view)) {
        $view = $event_save_data['telephone_1'] ?? $event_save_data['mobile_1'] ?? $event_save_data['first_name'] ?? '';
    }
    // log results
    if ($success) {
        mas_logAction('OK', 'SAVE EVENT/PERSON', "Person '" . $view . "' saved in Marketing Automation Suite.");
    } else {
        mas_logAction('ERROR', 'SAVE EVENT/PERSON', "Error saving person '" . $view . "' in Marketing Automation Suite.");
    }

    return $success;
}

/**
 * @param $string
 * @return string
 */
function mas_convertLabel($string): string
{
    $label = explode('_', $string);
    $label = array_map('ucwords', $label);
    return implode(' ', $label);
}

/**
 * @param array $payload
 * @return array
 * @throws Exception
 */

function mas_directoryForm(array $payload): array
{
    $res = [];
    $opt_auth_token = get_option($GLOBALS['opt_auth_token']);
    $settings_base_url = get_option($GLOBALS['opt_base_url']);
    $url = $settings_base_url . $GLOBALS['endpoint_directory_form'];
    $headers = array(
        'MA-Customer' => $opt_auth_token,
        'Content-Type' => 'application/json'
    );

    $data = array_filter($payload, function ($v) {
        return !is_null($v);
    });


    $body = json_encode($data);

    $response = wp_remote_post(
        $url,
        array(
            'headers' => $headers,
            'body' => $body
        )
    );

    if (is_wp_error($response)) {
        $success = false;
        $res['success'] = false;
        $res['error'][] = 'Generic Error';
        if ($GLOBALS['log_to_terminal']) {
            error_log(json_encode($response));
        }
    } else {
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);
        $res = $response['response'];
        if (isset($json['result']) && $json['result'] == True) {
            $success = true;
            $res['error'] = [];
            $res['success'] = $success;
        } else {
            $success = false;
            $res['success'] = false;
            $res['error'] = $json['errors'];
            if (isset($json['code']) && $json['code'] == 401) {
                mas_logAction('ERROR', 'AUTH', "Your personal token is not valid. 'Enable Marketing Automation' setting automatically disable.");
            }
            if ($GLOBALS['log_to_terminal']) {
                error_log(json_encode($response));
            }
        }
    }

    // log results
    if ($success) {
        mas_logAction('OK', 'SAVE FORM', "Person '" . $payload['form'] . "' saved in Marketing Automation Suite.");
    } else {
        mas_logAction('ERROR', 'SAVE FORM', "Error saving form '" . $payload['form'] . "' in Marketing Automation Suite.");
    }

    return $res;
}
