# marketing_automation_suite

# Deploying

## Edits to be made to the plugin config before deploying
1. In `constants.php` file, set `$GLOBALS['log_to_terminal']` var to `false`, if `true`.
2. Keep in mind that the plugin doesn't work for local enviroments. See local testing for further info.

# Development

## Add or edit contact form fields
To add or edit supported contact form fields change the `$GLOBALS["FORM_FIELDS"]` var in the `form_fields_mappings.php` file.

## Support new contact forms plugins
To support new contact form plugin:
1. add the corresponding mapping class to the `form_fields_mappings.php`, using as a reference the ones of WPFORMS and Contact Form 7.
2. Update the `$GLOBALS["FORM_FIELDS"]` var in the `form_fields_mappings.php` file, adding at every field the new class in the mappings.
3. Add the corresponding email sent hooks and logic in the `form_actions.php` file, adding also a parser for the posted data if not already compatible with the `process_posted_content` function.

# Local Testing

## Debug
In `constants.php` file, set `$GLOBALS['log_to_terminal']` var to `true`, to check the intercepted forms data and server call errors.

## Run a Wordpress installation locally
This plugin by default doesn't work for localhost installations out-of-the-box. If needed, temporary change the `$GLOBALS['sender_site_url']` in `constants.php` to hardcode a valid website url, or forward localhost wordpress installation to some url via `hosts` file.

1. Enter the `docker-compose` folder
2. Run che command: `docker-compose up`
3. Access the console at: `http://localhost:8111`
4. Fill the init config details
5. If you are having troubles adding or deleting plugin, at the following row in the `/docker-compose/html/wp-config.php` file, after the comment that says where to add custom values:
```php
define( 'FS_METHOD', 'direct' );
```
1. Everything is ready now, and you can edit now the code of the plugin directly: changes will affect immediatly the running Wordpress installation.
2. `docker-compose down` to turn off Wordpress and mariadb after testing.

## Configure fake email for testing
1. Install and activate `Disable Email` Wordpress plugin
2. Now you can send email via Contact form etc., they won't be actually send but will avoid throwing errors and will trigger our plugin actions.

# WPFORMS: support notes

## Note on field mappings
At the moment we are mapping the WPFORMS field by field name, not id like in contact form. This is not ideal; even if we provide different translations and alternatives we can't cover all the field names that a user may want to use for each field.

Some possible approaches to address this issue could be:
- Port the old connector from White Rabbit plugin, to map each field.
- Develop (if feasible) a new, simpler connector to only add a "marketing-automation-id" custom field to WPFORM fields properties, and use that just like in Contact Form 7.

## Enabling GDPR consent fields
In order to enable GDPR consent draggable fields in WPFORMS, the user needs to enable the option `GDPR Enhancements` in the plugin general settings page.

**Known issue**: At the moment all gdpr consent fields in WPFORMS are mandatory. How to make them optional?

## Special fields require the pro version
**Date**, **address** and **phone** fields requires the pro license, so at the moment we are using plain text fields, with all the limitations of the case.


# Contact Form 7: support notes

## Country field
Contact fort 7 doesn't seems to support natively **country** and **state** field. We are using at the moment a plain text field with max and min lengths = 2.



