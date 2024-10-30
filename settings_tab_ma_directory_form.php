<?php
require_once('settings_tables.php');

/**
 * @throws Exception
 */
function mas_getMaDirectoryFormTabContent()
{
    ?>
    <div>
        <em><?php echo esc_html_e(__('Check if all your modules are correctly mapped to the MA suite', 'marketing-automation-suite')) ?>
        </em>
        <em>
            <p><?php echo esc_html_e(__('If the Form is not synchronized with the MA suite, only the contact will be saved but it will not be possible to activate an automation on that compilation', 'marketing-automation-suite')) ?></p>
        </em>
    </div>
    <br/>
    <?php
    if (mas_validateJwtAndLogin(get_option($GLOBALS['opt_personal_token']))) {
        mas_getMaDirectoryFormMappingsTable();
    }


}

/**
 * @param int $post_id
 * @return void
 * @throws Exception
 */
function mas_call_maDirectoryFormTabContent(int $post_id)
{
    $data = [];
    $post = get_post($post_id);
    if ($post->post_type == 'wpcf7_contact_form') {
        //$myId = substr(get_post_meta($post_id, '_hash', true), 0, 7);

        $ContactForm = WPCF7_ContactForm::get_instance($post_id);
        $tags = $ContactForm->scan_form_tags();
        foreach ($tags as $tag) {
            if (!empty($tag['options'])) {
                foreach ($tag['options'] as $option) {
                    if (explode(':', $option)[0] == $GLOBALS['form_input_name_placeholder']) {
                        $data['sync_data']['payload'][$tag['name']]['description'] = explode(':', $option)[1];
                    }
                }
            }

            if (!empty($tag['name'])) {
                /*if ($tag['name'] == $GLOBALS['form_name_placeholder']) {
                    $form_name = $tag['values'][0];
                }*/
                $data['sync_data']['payload'][$tag['name']]['name'] = $tag['name'];
                $data['sync_data']['payload'][$tag['name']]['type'] = $tag['basetype'];
            }
        }

    }

    if ($post->post_type == 'wpforms') {
        $masWpFormsMAMappingField = new MasWpFormsMAMappingField();
        //$myId = $post_id;
        $post_content = json_decode($post->post_content);
        $fields = [];
        $post_fields = json_decode(json_encode($post_content->fields), true);
        foreach ($masWpFormsMAMappingField->mapping as $ma) {
            if (isset($post_content->settings->{$ma})) {
                if ($post_content->settings->{$ma} !== '') {
                    $fields[$post_content->settings->{$ma}] = $ma;
                }
            }
        }

        foreach ($fields as $id => $field) {
            $data['sync_data']['payload'][$field]['name'] = $field;
            $data['sync_data']['payload'][$field]['type'] = $post_fields[$id]['type'];
            $data['sync_data']['payload'][$field]['description'] = $post_fields[$id]['label'];
        }
    }

    $data['sync_data']['form'][] = mas_getFormName($post->post_title, $post->ID);
    $data = mas_preparePayloadDirectoryForm($data['sync_data'], $post_id);

    if ($data['sync_response']['code'] == 200) {
        ?>
        <div class="notice notice-success"><?php echo esc_html_e(__('Success Save Form on MA suite for form id ', 'marketing-automation-suite')) . $post_id ?></div>
        <?php
    } else {
        ?>
        <div class="notice notice-error"><?php echo esc_html_e(__('Error Save Form on MA suite for form id ', 'marketing-automation-suite')) . $post_id ?></div>
        <?php
        foreach ($data['sync_response']['error'] as $error) {
            ?>
            <div class="notice notice-error"><?php echo $error; ?></div>
            <?php
        }
        ?>
        <?php
    }


}