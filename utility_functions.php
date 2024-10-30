<?php
require_once('constants.php');

// log action in options, can be viewed from settings troubleshooting tab
/**
 * @throws Exception
 */
function mas_logAction($log_type, $event, $details): void
{
    $timezone = new DateTimeZone(get_option('timezone_string'));
    $now = new DateTime("now", $timezone);

    switch ($log_type) {
        case 'OK':
            $color = "ma-success";
            break;
        case 'ERROR':
            $color = "ma-error";
            break;
        case 'INFO':
            $color = "ma-info";
            break;
        default:
            $color = 'inherit';
            break;
    }

    $log = get_option($GLOBALS['opt_logs'], array());
    $entry = ['time' => $now->format("Y-m-d H:i:s"), 'event' => $event, 'details' => $details, 'log_type' => $log_type, 'color' => $color];
    array_unshift($log, $entry);
    while (count($log) > $GLOBALS['max_logs']) {
        array_pop($log);
    }
    update_option($GLOBALS['opt_logs'], $log);
}


if (!function_exists('mas_WriteLog')) {
    function mas_WriteLog($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}


/**
 * @param array $form_list
 * @param string $post_meta_ma_directory_form
 * @param int $post_id
 * @return bool
 */
function mas_controlFormStatus(array $form_list, string $post_meta_ma_directory_form, int $post_id): bool
{
    $data = json_decode($post_meta_ma_directory_form, true);
    $same1 = array_diff(array_values($data['sync_payload'] ?? []), array_values($form_list['payload'] ?? []));
    $same2 = array_diff(array_values($form_list['payload'] ?? []), array_values($data['sync_payload'] ?? []));
    $response = (empty($same1) && empty($same2)) && $data['sync_status'];
    if (!$response && $data['sync_status']) {
        $data['sync_status'] = false;
        update_post_meta($post_id, $GLOBALS['post_meta_ma_directory_form'], json_encode($data));
    }

    return $response;

}


/**
 * @param array $data
 * @return string
 */

function mas_decodeJson(array $data): string
{
    $result = '';
    foreach ($data['sync_payload'] as $input) {
        $result .= "[" . $input . "]";
    }

    return $result;


}

/**
 * @param string $type
 * @param string $txt
 * @return string
 */
function mas_printResponseYesNO(string $type, string $txt): string
{
    return '<h6><span class="badge badge-' . $type . '">' . __($txt, "marketing-automation-suite") . '</span></h6>';

}


/**
 * @param string $title
 * @param int $post_id
 * @return string
 */

function mas_getFormName(string $title, int $post_id): string
{
    return trim($title) . " (" . $post_id . ")";

}

/**
 * @param $jwt_string
 * @return false|string
 */
function mas_decodeJwt($jwt_string)
{
    $parts = explode(".", $jwt_string);
    return base64_decode($parts[1]);
}

