<?php

/**
 * Custom Consent Management
 *
 * @package Custom_Consent
 */

namespace Zippy_Core\Src\User;

defined('ABSPATH') or die();

class Zippy_Custom_Consent
{
    protected static $_instance = null;

    /**
     * @return Zippy_Custom_Consent
     */
  
    public static function get_instance()
    {
      if (is_null(self::$_instance)) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }
    public function __construct()
    {
        $this->set_hooks();
    }

    protected function set_hooks()
    {
        add_action('admin_init', [$this, 'custom_consent_settings_init']);
        add_shortcode('privacy_policy_link', [$this, 'privacy_policy_link_shortcode']);
        add_shortcode('custom_consent_checkbox', [$this, 'custom_consent_checkbox_shortcode']);
        add_action('woocommerce_register_form', [$this, 'add_consent_checkbox_to_registration_form']);
        add_action('wp_footer', [$this, 'disable_submit_if_consent_not_checked']);
        add_filter('woocommerce_registration_errors', [$this, 'validate_consent_checkbox'], 10, 3);
        add_action('woocommerce_created_customer', [$this, 'save_consent_checkbox_data']);
        add_action('show_user_profile', [$this, 'show_consent_in_user_profile']);
        add_action('edit_user_profile', [$this, 'show_consent_in_user_profile']);
        add_action('personal_options_update', [$this, 'save_consent_in_user_profile']);
        add_action('edit_user_profile_update', [$this, 'save_consent_in_user_profile']);
    }

    public function custom_consent_settings_init()
    {
        add_settings_section(
            'custom_consent_section',
            __('Custom Consent Settings', 'custom-consent'),
            [$this, 'custom_consent_section_callback'],
            'general'
        );

        add_settings_field(
            'custom_consent_description',
            __('Consent Description', 'custom-consent'),
            [$this, 'custom_consent_description_callback'],
            'general',
            'custom_consent_section'
        );

        add_settings_field(
            'custom_consent_checkbox_label',
            __('Checkbox Label', 'custom-consent'),
            [$this, 'custom_consent_checkbox_label_callback'],
            'general',
            'custom_consent_section'
        );

        register_setting('general', 'custom_consent_description', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'I consent to the terms and conditions.'
        ));
        register_setting('general', 'custom_consent_checkbox_label', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'I have read and agree with the terms and conditions.'
        ));
    }

    public function privacy_policy_link_shortcode()
    {
        $privacy_policy_page_id = get_option('wp_page_for_privacy_policy');
        
        if (!$privacy_policy_page_id) {
            return 'Privacy policy page is not set.';
        }
        $privacy_policy_url = get_permalink($privacy_policy_page_id);
        
        if (!$privacy_policy_url) {
            return 'Privacy policy page link is not available.';
        }
        return sprintf(
            '<a href="%s" target="_blank">Privacy Policy</a>',
            esc_url($privacy_policy_url)
        );
    }

    public function custom_consent_section_callback()
    {
        echo '<p>' . __('Customize the consent description text and checkbox label.', 'custom-consent') . '</p>';
    }

    public function custom_consent_checkbox_label_callback()
    {
        $label = get_option('custom_consent_checkbox_label', 'I have read and agree with the terms and conditions.');
        echo '<input type="text" name="custom_consent_checkbox_label" value="' . esc_attr($label) . '" class="regular-text" />';
    }

    public function custom_consent_description_callback()
    {
        $site_name = get_bloginfo('name');
        $description = get_option('custom_consent_description', "By using this site, you consent to the collection and use of your personal data by $site_name in accordance with our <a href='privacy-policy' target='_blank'>Privacy Policy</a> .");

        echo '<textarea name="custom_consent_description" rows="3" cols="50">' . esc_html($description) . '</textarea>';
    }

    public function custom_consent_checkbox_shortcode()
    {
        $consent_description = get_option('custom_consent_description', 'I consent to the terms and conditions.');
        $checkbox_label = get_option('custom_consent_checkbox_label', 'I have read and agree with the terms and conditions.');

        return '<p class="form-row terms">
             <div class="description"> ' . esc_html($consent_description) . '</div>
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="custom_consent" id="custom_consent" value="1" /> 
                <span>' . esc_html($checkbox_label) . '</span>
            </label>
        </p>';
    }

    public function add_consent_checkbox_to_registration_form()
    {
        echo do_shortcode('[custom_consent_checkbox]');
    }

    public function disable_submit_if_consent_not_checked()
    {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                var consentCheckbox = document.getElementById('custom_consent');
                var submitButton = document.querySelector('button[name="register"]');

                submitButton.disabled = true;

                consentCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        submitButton.disabled = false;
                    } else {
                        submitButton.disabled = true;
                    }
                });
            });
        </script>
        <?php
    }

    public function validate_consent_checkbox($errors, $username, $email)
    {
        if (!isset($_POST['custom_consent'])) {
            $errors->add('consent_error', __('You must consent to the terms and conditions.', 'woocommerce'));
        }
        return $errors;
    }

    public function save_consent_checkbox_data($customer_id)
    {
        if (isset($_POST['custom_consent'])) {
            update_user_meta($customer_id, 'custom_consent', 'yes');
        } else {
            update_user_meta($customer_id, 'custom_consent', 'no');
        }
    }

    public function show_consent_in_user_profile($user)
    {
        if (!current_user_can('administrator')) {
            return;
        }

        $consent = get_user_meta($user->ID, 'custom_consent', true);
        ?>
        <h3><?php _e("User Consent Information", "custom-consent"); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="custom_consent"><?php _e("Consent Status"); ?></label></th>
                <td>
                    <select name="custom_consent" id="custom_consent">
                        <option value="yes" <?php selected($consent, 'yes'); ?>><?php _e('Yes', 'custom-consent'); ?></option>
                        <option value="no" <?php selected($consent, 'no'); ?>><?php _e('No', 'custom-consent'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    public function save_consent_in_user_profile($user_id)
    {
        if (!current_user_can('administrator')) {
            return false;
        }

        if (isset($_POST['custom_consent'])) {
            update_user_meta($user_id, 'custom_consent', sanitize_text_field($_POST['custom_consent']));
        }
    }
}


?>