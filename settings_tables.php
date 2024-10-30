<?php
require_once('constants.php');

/* -------------------------------------------- */
/*              Help mappings table             */
/* -------------------------------------------- */

function mas_getHelpMappingsTable()
{
    /*Hide field*/
    $form_fields = array_filter($GLOBALS["FORM_FIELDS"], function ($item) {
        return !$item->hide;
    });


    ?>
    <table class="wp-list-table widefat striped">
        <thead>
        <tr>
            <th><?php echo esc_html_e(__('Name', 'marketing-automation-suite')) ?></th>
            <th><?php echo esc_html_e(__('Description', 'marketing-automation-suite')) ?></th>
            <th><?php echo esc_html_e(__('Required', 'marketing-automation-suite')) ?></th>
            <th><?php echo esc_html_e(__('Default Value', 'marketing-automation-suite')) ?></th>
            <th><?php echo esc_html_e(__('Mappings', 'marketing-automation-suite')) ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($form_fields as $field) : ?>
            <!-- Hide tag1..tag10 and cv Company-->
            <?php /*if ($field->name == 'Tag' || $field->name == 'Custom Variable Company'): */ ?><!--
                <?php /*continue; */ ?>
            --><?php /*endif; */ ?>
            <tr>
                <td><strong><?php echo esc_html_e($field->name); ?></strong></td>
                <td><?php echo esc_html_e($field->description); ?></td>
                <td><?php echo esc_html_e($field->required ? 'Yes' : 'No'); ?></td>
                <td><?php echo esc_html_e($field->default_value ? $field->default_value : '-'); ?></td>
                <td>
                    <?php foreach ($field->mappings as $mapping) : ?>
                        <p><strong style="color:#4169e1"><?php echo esc_html_e($mapping->plugin_name); ?>:</strong><br/>
                            <?php echo esc_html_e(__('Allowed field names:', 'marketing-automation-suite')) ?>
                            <strong><?php echo esc_html_e(implode(', ', $mapping->allowed_aliases)); ?></strong><br/>
                            <?php echo esc_html_e(__('Field type:', 'marketing-automation-suite')) ?>
                            <strong><?php echo esc_html_e($mapping->type); ?></strong></p>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/* -------------------------------------------- */
/*          Troubleshooting Logs Table          */
/* -------------------------------------------- */

function mas_getTroubleshootingLogstable()
{
    // Define the logs array with action and details objects
    $logs = get_option($GLOBALS['opt_logs'], array());
    // Create the table markup
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th class="time"><?php echo esc_html_e(__('Time', 'marketing-automation-suite')) ?></th>
            <th class="event_status"><?php echo esc_html_e(__('Event Status', 'marketing-automation-suite')) ?></th>
            <th class="event"><?php echo esc_html_e(__('Event', 'marketing-automation-suite')) ?></th>
            <th><?php echo esc_html_e(__('Details', 'marketing-automation-suite')) ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Loop through the logs array and output each log as a table row
        foreach ($logs as $log) {
            ?>
            <tr>
                <td class="time"><?php echo esc_html_e($log["time"]); ?></td>
                <td class="event_status <?php echo esc_html_e($log["color"]); ?>"><?php echo esc_html_e($log["log_type"]); ?></td>
                <td class="event"><?php echo esc_html_e($log["event"]); ?></td>
                <td><?php echo esc_html_e($log["details"]); ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}


/* -------------------------------------------- */
/*              MA Events mappings table  */
/* -------------------------------------------- */

/**
 * @throws Exception
 */
function mas_getMaDirectoryFormMappingsTable()
{
    $form_list_wpcf = [];
    $form_list_cf = [];
    $dateTimeFormat = get_option('date_format') . ' ' . get_option('time_format');
    $timezone = new DateTimeZone(get_option('timezone_string'));
    /*wpcf7_contact_form*/
    if (post_type_exists('wpcf7_contact_form')) {
        $args_wpcf7 = ['post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1];
        $args_wpforms = ['post_type' => 'wpforms', 'posts_per_page' => -1];
        if ($data = get_posts($args_wpcf7)) {
            foreach ($data as $id => $key) {
                //$form_list_wpcf[$id]['synchronizable'] = false;
                //$hash = substr(get_post_meta($key->ID, '_hash', true), 0, 7);
                //$form_list_wpcf[$id]['id'] = $key->ID ."(".$hash.")";
                $form_list_wpcf[$id]['type'] = 'wpcf7_contact_form';
                $form_list_wpcf[$id]['id'] = $key->ID;
                $form_list_wpcf[$id]['post_id'] = $key->ID;
                $form_list_wpcf[$id]['post_title'] = $key->post_title;
                $form_list_wpcf[$id]['post_modified'] = wp_date($dateTimeFormat, strtotime($key->post_modified_gmt), $timezone);
                $ContactForm = WPCF7_ContactForm::get_instance($key->ID);
                $tags = $ContactForm->scan_form_tags();
                $form_fields = [];
                $form_list_wpcf[$id]['form_name'] = mas_getFormName($key->post_title, $key->ID);
                //$form_list_wpcf[$id]['synchronizable'] = true;
                foreach ($tags as $tag) {
                    if (!empty($tag['name'])) {
                        /*if ($tag['name'] == $GLOBALS['form_name_placeholder']) {
                            $form_list_wpcf[$id]['form_name'] = $tag['values'][0];
                            $form_list_wpcf[$id]['synchronizable'] = true;
                        }*/
                        $form_fields['fields_format'][] = "[" . $tag['name'] . "]";
                        $form_fields['payload'][] = $tag['name'];
                        $form_fields['fields'][$tag['name']] = ['format_name' => "[" . $tag['name'] . "]",
                            'basetype' => $tag['basetype'],
                            'name' => $tag['name'],
                            'ma_events_form_name' => $tag['name'] == $GLOBALS['form_name_placeholder'],
                            'ma_events_form_value' => $tag['name'] == $GLOBALS['form_name_placeholder'] ? $tag['values'][0] : ''
                        ];
                    }
                }
                $form_list_wpcf[$id]['form_fields'] = $form_fields;

            }
        }

        /*wpforms*/
        if (post_type_exists('wpforms')) {
            if ($data = get_posts($args_wpforms)) {
                $masWpFormsMAMappingField = new MasWpFormsMAMappingField();
                foreach ($data as $id => $key) {
                    //$form_list_cf[$id]['synchronizable'] = false;
                    $form_list_cf[$id]['type'] = 'wpforms';
                    $form_list_cf[$id]['id'] = $key->ID;
                    $form_list_cf[$id]['post_id'] = $key->ID;
                    $form_list_cf[$id]['post_title'] = $key->post_title;
                    $form_list_cf[$id]['post_modified'] = wp_date($dateTimeFormat, strtotime($key->post_modified_gmt), $timezone);
                    $form_list_cf[$id]['form_name'] = '';
                    $post_content = json_decode($key->post_content);
                    $fields = [];
                    $form_fields = [];
                    foreach ($masWpFormsMAMappingField->mapping as $ma) {
                        if (isset($post_content->settings->{$ma})) {
                            if ($post_content->settings->{$ma} !== '') {
                                $fields[] = $ma;
                            }
                        }
                    }
                    $form_list_cf[$id]['form_name'] = mas_getFormName($key->post_title, $key->ID);
                    //$form_list_cf[$id]['synchronizable'] = true;
                    foreach ($fields as $fied) {
                        /*if ($fied == $GLOBALS['form_name_placeholder']) {
                            $form_list[$id]['form_name'] = $tag['values'][0];
                            $form_list[$id]['synchronizable'] = true;
                        }*/
                        $form_list_cf[$id]['form_name'] = mas_getFormName($key->post_title, $key->ID);
                        //$form_list_cf[$id]['synchronizable'] = true;
                        $form_fields['fields_format'][] = "[" . $fied . "]";
                        $form_fields['payload'][] = $fied;
                        $form_fields['fields'][$fied] = ['format_name' => "[" . $fied . "]",
                            'basetype' => $tag['basetype'],
                            'name' => $tag['name'],
                            'ma_events_form_name' => $fied == $GLOBALS['form_name_placeholder'],
                        ];
                        $form_list_cf[$id]['form_fields'] = $form_fields;
                    }
                }
            }
        }
    }
    $form_list = array_merge($form_list_wpcf, $form_list_cf);
    if (empty($form_list)) {
        echo '<h6><span class="badge badge-warning">' . __('No Contact Form found', "marketing-automation-suite") . '</span></h6>';
    } else {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th class="form_title" colspan="1"><?= esc_html_e(__('Form ID', 'marketing-automation-suite')) ?></th>
                <th class="form_title"><?= esc_html_e(__('Form Type', 'marketing-automation-suite')) ?></th>
                <th class="form_title"><?= esc_html_e(__('Form Title', 'marketing-automation-suite')) ?></th>
                <th class="form_title"><?= esc_html_e(__('Form Map Name', 'marketing-automation-suite')) ?></th>
                <th class="form_date"><?= esc_html_e(__('Form Modified', 'marketing-automation-suite')) ?></th>
                <th class="form_title"><?= esc_html_e(__('Form Structure', 'marketing-automation-suite')) ?></th>
                <th class="form_title"><?= esc_html_e(__('Form Structure Synchronized', 'marketing-automation-suite')) ?></th>
                <th class="form_date"><?= esc_html_e(__('Last Form Synchronization Date', 'marketing-automation-suite')) ?></th>
            <th class="form_title" colspan="2"><?= esc_html_e(__('Synchronized', 'marketing-automation-suite')) ?></th>
            </tr>
            </thead>
            <tbody>

            <?php
            $form_already_present = [];
            $form_block = [];
            $form_change_block = [];
            foreach ($form_list as $id => $form) {
                $post_meta_ma_directory_form = get_post_meta($form['id'], $GLOBALS['post_meta_ma_directory_form'], true);
                if (empty($post_meta_ma_directory_form)) {
                    $form_list[$id]['synchronized'] = false;
                    $form_list[$id]['form_date_synchronized'] = false;
                    $form_list[$id]['form_structure_synchronized'] = '';
                } else {
                    $db = json_decode($post_meta_ma_directory_form, true);
                    $form_list[$id]['synchronized'] = mas_controlFormStatus($form['form_fields'], $post_meta_ma_directory_form,$form['id']);
                    $form_list[$id]['form_structure_synchronized'] = mas_decodeJson($db);
                    $form_list[$id]['form_date_synchronized'] = $db['sync_data']['form_date_synchronized'];
                    $form_list[$id]['form_name_synchronized'] = $db['sync_data']['form'];
                }

            }
            foreach ($form_list as $form) {
                if (in_array($form['form_name'], $form_already_present)) {
                    $form_block[] = $form['form_name'];
                    ?>
                    <div class="notice notice-error">
                        <?= esc_html_e(__('There are multiple forms with the same mapping name', 'marketing-automation-suite')) ?>
                    </div>
                    <?php
                }
                $form_already_present[] = $form['form_name'];
                if ($form['synchronized']) {
                    if ($form['form_name_synchronized'] != $form['form_name']) {
                        $form_change_block[] = $form['form_name'];
                        ?>
                        <div class="notice notice-error">
                            <?= $form['id'] ?>
                            <?= esc_html_e(__('You have changed the mapping of a form already synchronized with ', 'marketing-automation-suite')) . '<b>' . $form['form_name_synchronized'] . '</b>' ?>
                        </div>
                        <?php
                    }
                }
            }
            foreach ($form_list as $form) {
                ?>
                <tr>
                    <td class="form_data"><?= esc_html_e($form['id']); ?></td>
                    <td class="form_data"><?= esc_html_e($form['type']); ?></td>
                    <td class="form_data"><?= esc_html_e($form['post_title']); ?></td>
                    <td class="form_data"><?= esc_html_e($form['form_name']); ?></td>
                    <td class="form_data"><?= $form['post_modified']; ?></td>
                    <td class="form_data"><?= esc_html_e(implode('  ', $form['form_fields']['fields_format'] ?? [])); ?></td>
                    <td>
                        <?php
                        echo $form['form_structure_synchronized'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $form['form_date_synchronized'];
                        ?>
                    </td>
                    <td colspan="2">
                        <?php
                        if (in_array($form['form_name'], $form_block)) {
                            echo '<h6><span class="badge badge-danger">' . __('Form Name already use', "marketing-automation-suite") . '</span></h6>';
                        } else {
                            if (in_array($form['form_name'], $form_change_block)) {
                                echo '<h6><span class="badge badge-danger">' . __('Change Mapping Name', "marketing-automation-suite") . '</span></h6>';
                            } else {
                                if ($form['synchronized']) {
                                    echo mas_printResponseYesNO('success', 'yes');
                                } else {
                                    if (!empty($form['form_name']) && !empty($form['form_fields']['fields_format'])) {
                                        echo "<a class=\"button\" href=\"?page=marketing-automation&tab=ma_directory_form&connect=1&post_id=" . $form['post_id'] . "\">" . __('Sync now', "marketing-automation-suite") . "</a>";
                                    } else {
                                        echo '<h6><span class="badge badge-warning">' . __('Empty Map Name', "marketing-automation-suite") . '</span></h6>';
                                    }
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>

        <?php
    }
}
