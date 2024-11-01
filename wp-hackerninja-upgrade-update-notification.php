<?php
/*
  Plugin Name: WP Update & Upgrade Notification
  Plugin URI:
  Description: We (Hackerninja.Com) developed this plugin since we find many, many customers simply DO NOT UPDATE Wordpress regularly. We feel this plugin is a great little solution to notify everyone that upgrades are pending. Regular Wordpress upgrades are very important for your security.
  Version: 1.9
  Author: hackerninja
  Author URI: hackerninja.com

  We (Hackerninja.com) developed this plugin since we find many, many customers simply DO NOT UPDATE Wordpress regularly. We feel this plugin is a great little solution to notify everyone that upgrades are pending. Regular Wordpress upgrades are very important for your security.
 */

/* Begin Settings Page */
add_action('admin_menu', 'wp_update_notification_add_menu');

function wp_update_notification_add_menu() {
    $page = add_options_page(
            'Hackerninja WP Upgrade Notify', 'Hackerninja WP Upgrade Notify', 'manage_options', 'wp-hackerninja-upgrade-update-notification', 'wp_update_notification_settings_page');
}

add_action('admin_init', 'wp_update_notification_admin_init');

function my_test_validation($input) {
	return $input;
}

function wp_update_notification_admin_init() {

    register_setting('wp_update_notification_options', 'wp_update_notification_options', 'wp_update_notification_validate');
    $options = get_option('wp_update_notification_options');
    if (!$options) {
        $options = array(
            'frequency' => 'daily',
            'email' => get_bloginfo('admin_email'),
            'signup' => 0,
        );
        update_option('wp_update_notification_options', $options);
    }
    add_settings_section('wp_update_notification_main', '', 'wp_update_notification_section_text', 'wp-update-notification');

    $items = array(
        array('value' => 'daily', 'text' => 'Daily'),
        array('value' => 'weekly', 'text' => 'Weekly'),
        array('value' => 'monthly', 'text' => 'Monthly'),
    );
    add_settings_field('wp_update_notification_options_frequency', 'Notification Frequency', 'wp_update_notification_settings_field', 'wp-update-notification', 'wp_update_notification_main', array('type' => 'select', 'identifier' => 'frequency', 'option' => 'wp_update_notification_options', 'data' => $options, 'style' => 'width: 300px', 'items' => $items));
    add_settings_field('wp_update_notification_options_email', 'Notification Email(s)<BR>', 'wp_update_notification_settings_field', 'wp-update-notification', 'wp_update_notification_main', array('type' => 'text', 'identifier' => 'email', 'option' => 'wp_update_notification_options', 'data' => $options, 'style' => 'width: 300px', 'message' => 'Separate multiple email addresses by commas ","<BR>--These will receive emails from your blog<HR>'));
    add_settings_field('wp_update_notification_options_checkbox', 'Subscribe', 'wp_update_notification_settings_field', 'wp-update-notification', 'wp_update_notification_main', array('type' => 'checkbox', 'identifier' => 'signup', 'option' => 'wp_update_notification_options', 'data' => $options, 'style' => 'float: left;', 'message' => 'Sign me up for free <A HREF="http://hackerninja.com/?product=free-scan" target="_new">Malware & Security Vulnerability </A> monitoring.<BR> This is completely optional - you do not have to check this box to get Wordpress Update and Upgrade Notifications'));
}

function wp_update_notification_settings_page() {
    $options = get_option('wp_update_notification_options');
    ?>
    <h2>Hackerninja WP Upgrade & Update Notification Settings</h2>
    <div class="wrap" style="float: left;" />
    <div style="width: 550px;">
        <p>We (Hackerninja.com) developed this plugin since we find many, many customers simply DO NOT UPDATE Wordpress regularly. We feel this plugin is a great little solution to notify everyone that upgrades are pending. Regular Wordpress upgrades are very important for your security.</p>
</DIV>

    <div style="width: 400px; float: left;">
        <form method="post" action="options.php" name="wp_auto_commenter_form">
            <?php settings_fields('wp_update_notification_options'); ?>
<HR>
            <?php do_settings_sections('wp-update-notification'); ?>
<HR>
            <P>By the checking the above checkbox, you are agreeing to free Google Malware Monitoring and Notification services from <a href="http://www.hackerninja.com" target="_new">http://www.hackerninja.com</a>.</P>
            <p>You are free to remove yourself from this service at any time (by unchecking the above, or emailing us at: <a href="mailto:support@hackerninja.com?Subject=Remove" target="_top">support@hackerninja.com</a>).</p>
            <p class="submit">
                <input type="submit" name="Register Email Address and Website" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </form>
    </div>
    </div>
    <?php
}

function wp_update_notification_section_text() {
    return '';
}

function wp_update_notification_validate($input) {
    $options = get_option('wp_update_notification_options');
    if (($options['signup'] != '1') && ($input['signup'] == '1')) {
        $response = wp_remote_post('http://www.hackerninja.com/scan_signup.php', array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array('email' => $options['email'], 'url' => get_bloginfo('url')),
            'cookies' => array()
                )
        );

        if (is_wp_error($response)) {
            $input['signup'] = '0';
        } else {
            if (strpos($response['body'], 'SUCCESS') !== false) {
                $input['signup'] = '1';
            } else {
                $input['signup'] = '0';
            }
        }
    }
    else // Remove from list
    {
	if(empty($options['email']) == false) {
	        $response = wp_remote_post('http://www.hackerninja.com/scan_signup.php', array(
        	    'method' => 'POST',
            	    'timeout' => 45,
            	    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => array('email' => 'REMOVE ' . $options['email'], 'url' => get_bloginfo('url')),
                    'cookies' => array()
                    )
                );
	}
    }
    
    return $input;
}

function wp_update_notification_settings_field($args) {
    $type = $args['type'];
    $identifier = $args['identifier'];
    $value = $args['data'][$identifier];
    switch ($type) {
        case 'text':
        case 'password':
        case 'hidden':
            echo '<input type="' . $type . '" id="' . $identifier . '" name="' . $args['option'] . '[' . $identifier . ']" class="input" value="' . $value . '" style="' . $args['style'] . '"/>';
            break;
        case 'checkbox':
            echo '<input type="checkbox" id="' . $identifier . '" name="' . $args['option'] . '[' . $identifier . ']" class="input" value="1" ' . checked($value, 1, false) . ' style="' . $args['style'] . '"/>';
            break;
        case 'textarea':
            echo '<textarea id="' . $identifier . '" name="' . $args['option'] . '[' . $identifier . ']" class="input" style="' . $args['style'] . '">' . $value . '</textarea>';
            break;
        case 'select':
            echo '<select id="' . $identifier . '" name="' . $args['option'] . '[' . $identifier . ']" class="input" style="' . $args['style'] . '">';
            if ($args['items']) {
                foreach ($args['items'] as $item) {
                    echo '<option value=' . $item['value'] . ' ' . selected($item['value'], $value, false) . '>' . $item['text'] . '</option>';
                }
            }
            echo '</select>';
            break;
        case 'radio':
            if ($args['items']) {
                foreach ($args['items'] as $item) {
                    echo '<div class="radio-wrapper"><input type="radio" id="' . $identifier . '" name="' . $args['option'] . '[' . $identifier . ']" class="input" value="' . $item['value'] . '" ' . checked($item['value'], $value, false) . ' style="' . $args['style'] . '"/></div>';
                }
            }
            break;
    }
    if (isset($args['message'])) {
        echo '<br /><small>' . $args['message'] . '</small>';
    }
}

/* End Settings Page */

/* Begin Cron */
add_filter('cron_schedules', 'wp_update_notification_cron_schedules');

function wp_update_notification_cron_schedules($schedules) {
    $schedules['weekly'] = array('interval' => 604800, 'display' => 'Weekly');
    $schedules['monthly'] = array('interval' => 2635200, 'display' => 'Monthly');
    return $schedules;
}

if (!wp_next_scheduled('wp_update_notification_scheduler_hook')) {
    $options = get_option('wp_update_notification_options');
    wp_schedule_event(time(), $options['frequency'], 'wp_update_notification_scheduler_hook');
}

add_action('wp_update_notification_scheduler_hook', 'wp_update_notification_cron_function');

function wp_update_notification_cron_function() {
    $options = get_option('wp_update_notification_options');
    $email = '';

    if ($options) {
        error_reporting(0);
        /* Begin Wordpress Core Upgrade Check */
        do_action("wp_version_check");
        $update_core = get_site_transient("update_core");
        if ('upgrade' == $update_core->updates[0]->response) {
            $email .= 'WordPress Core Upgrade Available. Please update from version ' . get_bloginfo('version') . ' to ' . $update_core->updates[0]->current . '<br />';
        }
        /* End Wordpress Core Upgrade Check */

        /* Begin Wordpress Plugins Upgrade Check */
        do_action("wp_update_plugins");
        $update_plugins = get_site_transient('update_plugins');
        if (!empty($update_plugins->response) && (count($update_plugins->response) >= 1)) {
            foreach ($update_plugins->response as $key => $value) {
                $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $key);
                $email .= 'Upgrade Available for Plugin "' . $plugin_info['Name'] . '". Please update from version ' . $plugin_info['Version'] . ' to ' . $value->new_version ;
$email .= "\r\n";
            }
        }
        /* End Wordpress Plugins Upgrade Check */

        /* Begin Wordpress Themes Upgrade Check */
        do_action("wp_update_themes");
        $update_themes = get_site_transient('update_themes');
        if (!empty($update_themes->response) && (count($update_themes->response) >= 1)) {
            foreach ($update_themes->response as $key => $value) {
                $theme_info = get_theme_data(WP_CONTENT_DIR . '/themes/' . $key . '/style.css');
                $email .= 'Upgrade Available for Theme "' . $theme_info['Name'] . '". Please update from version ' . $theme_info['Version'] . ' to ' . $value['new_version'] ;
		$email .= "\r\n";
            }
        }
        /* End Wordpress Themes Upgrade Check */

        /* Begin Send Email Notifications */
        if ($email != '') {
		$notifycontents = $email;
            $email = 'Hello, today your Wordpress blog <' . get_bloginfo('url') . '> has the following updates for you:';
		$email .= "\r\n\r\n";
		$email .= $notifycontents;
		$email .= "\r\n\r\n";
		$email .= "\r\n\r\n";
            $email .=  get_bloginfo('url') . '/wp-admin/update-core.php <-- Click Here to Upgrade';
		$email .= "\r\n\r\n";
            $email .= 'We highly recommend that you perform these updates as soon as possible in order to protect your website from hackers and performance problems. If you need any help doing this - or need general upgrade, malware removal or Wordpress Hosting help, please see us at: http://www.hackerninja.com';
		$email .= "\r\n\r\n";
           $email .= 'Thank you - your Blog is a happier place for it :)';
		$email .= "\r\n";
$email .= 'http://www.hackerninja.com';
		$email .= "\r\n\r\n";
$email .='support@hackerninja.com ----  Phone: 855-Ya-Ninja';
		$email .= "\r\n";
            add_filter('wp_mail_content_type', 'wp_update_notification_set_html_content_type');
            wp_mail($options['email'], 'Update Notification ', $email);
            remove_filter('wp_mail_content_type', 'wp_update_notification_set_html_content_type');
        }
        /* End Send Email Notifications */
        error_reporting(1);
    }
}

function wp_update_notification_set_html_content_type() {
	return 'text/plain';
}

/* End Cron */
?>
