=== Marketing Automation Suite===
Plugin Name: Marketing Automation Suite
Contributors: marketingautomationsuite
Tags: Marketing Automation Suite
Requires at least: 5.1
Tested up to: 6.4.3
Requires PHP: 7.4.0
Stable tag: 2.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Marketing Automation Integration Plugin

== Description ==
The Marketing Automation Integration Plugin is the perfect solution to streamline your digital marketing strategy directly from your WordPress site. This versatile tool allows you to seamlessly sync form submissions from popular plugins like Contact Form 7 and WPForms with your Marketing Automation (MA) platform.

With this seamless integration, you can efficiently collect compiled forms and, through MA, send targeted newsletters, and track customer activity, all without the hassle of complex data transfer processes.

Key features include:

Automatic Form Submission Sync: Automatically receive form submissions from your visitors through Contact Form 7 and WPForms directly into your Marketing Automation platform.

Maximum Customization: Easily configure which data fields you want to send to MA, ensuring a seamless and personalized sync tailored to your marketing needs.

Operational Efficiency: Save time and effort by eliminating the need to manually manage form submission data. The plugin handles everything automatically and seamlessly.

Enhanced Customer Engagement: Utilize the collected data to create targeted and personalized customer engagement strategies, improving overall customer experience.

With the Marketing Automation Integration Plugin, empower your digital marketing strategy and take your marketing to the next level.


Read more on [https://marketingautomation.it](https://marketingautomation.it)

== Installation ==
Currently, the sites created with the Marketing Automation Suite are not automatically linked to the suite. In the future this link will be done automatically. To link he newly created Wordpress site and be able to interact with its multiple tools (from direct publication via the Suite of the articles to automatic classification on the CRM of the received contacts) you will have to download the package plugin to install on Wordpress.
1- Click on the Download plugin icon featured in the Actions
2- Download the linking package suitable for your site type (e.g. Wordpress) by clicking on the Download button
3- Within the Wordpress Administration Panel, select from the menu on the left the Plugin option, then Add new
4- Click on the Upload plugin button
5- Select the .zip file downloaded from the Suite of Marketing Automation (do not unzip the .zip file, but select the .zip file)
6- Click Wordpress Install Now button to proceed with the installation of the Plugin
7- Following the installation procedure, you will be prompted a page with the message "Plugin installed correctly", click on the link "Activate plugin"
8- You should now find within the Dashboard of Wordpress plugins, the activated Plugin of Marketing Automation (in the event of failed activation you will still find the "Enable plugin" option of the Marketing Automation plugin)
9- Click on "Marketing Automation" option from the menu on the left of the Wordpress Administration area
10- Enter the Token Site Login Suite given from the suite on the site settings (Attention! Not the Wordpress login data, but the Suite Login data)
11- In case you already have WooCommerce installed, you must enter the API REST keys. You can create them by following the official WooCommerce documentation.
12- Click on Save changes, the system should prompt a successful connection message

The Actions menu of a non linked site (top) and a linked site (bottom)
Now go back to the Suite, the site in question should be correctly linked to the Suite, you will find the link symbol that turned into a green chain instead of a red disconnected chain).

== Frequently Asked Questions ==
= Why do I get an activation error of the plugin? =
You are very likely to have an old version of the plugin. To solve the problem, uninstall the old plugin (version lower than or equal to 2.3.4) and reinstall it from here.
= Why doesn't the Suite fetch all the fields when I fill out a form? =
To make sure the Suite fetches all the fields in a form, you need to use the [Contact Form 7](https://it.wordpress.org/plugins/contact-form-7/) plugin or [WPForms](https://it.wordpress.org/plugins/wpforms-lite) plugin. You also need to edit the contact form and use the exact field names that the Suite accepts. In case the procedure doesn't work, even if followed correctly, delete the website from the Suite and the plugin from your website and start over by re-connecting the website.
= What are the field names that the Suite accepts in the contact form? =
The complete field names list can be found at the following [link](https://s3-eu-west-1.amazonaws.com/whiterabbitsuite.com/plugins/wordpress/Campi_WPCF7.xlsx). Some minor details about the fields:
- Only one tag can be passed
- The "province/nation" field must be written in the ISO format (PE, MI, NA...) to make sure that the CRM will interpret it in the correct manner. If the province is italian, the nation will be automatically set to IT.
- Use the exact same nomenclature that the fields in the left column of the spreadsheet above inside the Contact Form 7 forms' HTML have.
- The Suite does not perform any type of validation of the fields, so make sure to validate them inside the form before the sending, using the form controls that Contact Form 7 provides.
= What am I supposed to do in case of errors or malfunctioning of the plugin? =
In case any problem arises, you can contact the support at this [email](mailto:assistenza@whiterabbit.cloud) and make sure to attach your server error log. In some cases you will be asked to give us access to your website to solve the problem.
= Why doesn't the plugin track WooCommerce data in real time? =
You probably have an outdated version of WooCommerce that our plugin doesn't support for technical reasons. The plugin supports [WooCommerce](https://it.wordpress.org/plugins/woocommerce/) versions from 3 onwards.
= How do I create the API REST keys to import WooCommerce's data in the Suite? =
To create WooCommerce's API REST keys, follow the [official guide](https://woocommerce.github.io/woocommerce-rest-api-docs/#rest-api-keys) taken from their documentation. The plugin asks for these keys only when WooCommerce has been installed before our plugin.

== Screenshots ==
1. Marketing Automation Suite plugin setting

== Changelog ==
Version 1.0.0   First release
Version 1.1.1   Implemented data passing from forms created with wpcf7 and wpforms
Version 2.0.0   New api event/save to trigger automation in the MA suite
Version 2.0.1   API call optimization
Version 2.0.2   De Sync plugin
Version 2.0.3   Fix Various
Version 2.0.4   Fix Various

== Upgrade Notice ==
