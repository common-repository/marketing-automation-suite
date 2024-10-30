<?php
require_once('settings_tables.php');

function mas_getHelpTabContent()
{
    ?>
    <div>
        <em><?php echo esc_html_e(__('Check the field mappings to create forms that will send user details to Marketing Automation.
         All required fields must be present in the form that you want to link with Marketing Automation.', 'marketing-automation-suite')) ?>
        </em>
    </div>
    <br/>
    <?php
    mas_getHelpMappingsTable();
}
