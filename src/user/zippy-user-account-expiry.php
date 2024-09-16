<?php

namespace Zippy_Core\Src\User;

defined('ABSPATH') or die();

class Zippy_User_Account_Expiry
{
    protected static $_instance = null;

    /**
     * @return Zippy_User_Account_Expiry
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
        add_action('show_user_profile', [$this, 'add_expiry_date_field']);
        add_action('edit_user_profile', [$this, 'add_expiry_date_field']);

        add_action('personal_options_update', [$this, 'save_expiry_date_field']);
        add_action('edit_user_profile_update', [$this, 'save_expiry_date_field']);

        add_action('user_register', [$this, 'set_expiry_date_on_registration']);

        add_filter('manage_users_columns', [$this, 'add_creation_date_column']);
        add_filter('manage_users_columns', [$this, 'add_expiry_date_column']);

        add_action('manage_users_custom_column', [$this, 'show_creation_date_column_content'], 10, 3);
        add_action('manage_users_custom_column', [$this, 'show_expiry_date_column_content'], 10, 3);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_datepicker_script']);
        add_action('wp_login', [$this, 'check_user_expiry_date'], 10, 2);

        add_action('template_redirect', [$this, 'show_account_expired_message']);
        add_action('wp_head', [$this, 'add_inline_css_for_account_expired_message']);
        
        add_action('admin_footer', [$this, 'initialize_datepicker']);
    }

    public function add_expiry_date_field($user)
    {
        $current_user = wp_get_current_user();
        $is_admin = in_array('administrator', $current_user->roles);

        $expiry_date = get_the_author_meta('expiry_date', $user->ID);
        $formatted_expiry_date = $expiry_date ? date('d/m/Y', strtotime($expiry_date)) : '';

        $creation_date = get_the_author_meta('user_registered', $user->ID);
        $formatted_creation_date = date('d/m/Y', strtotime($creation_date));

        ?>
        <h3><?php _e('Account Details', 'textdomain'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="creation_date"><?php _e('Account creation date', 'textdomain'); ?></label></th>
                <td>
                    <p class="form-control-static"><?php echo esc_html($formatted_creation_date); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="expiry_date"><?php _e('Account expiry date', 'textdomain'); ?></label></th>
                <td>
                    <?php if ($is_admin) : ?>
                        <input type="text" name="expiry_date" id="expiry_date" value="<?php echo esc_attr($formatted_expiry_date); ?>" class="regular-text datepicker" />
                        <p class="description"><?php _e('Set the expiry date for this user\'s account. Format: dd/mm/yyyy', 'textdomain'); ?></p>
                    <?php else : ?>
                        <p class="form-control-static"><?php echo esc_html($formatted_expiry_date); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php
    }

    public function save_expiry_date_field($user_id)
    {
        if (!current_user_can('edit_user', $user_id) || !in_array('administrator', wp_get_current_user()->roles)) {
            return false;
        }

        $expiry_date = isset($_POST['expiry_date']) ? $_POST['expiry_date'] : '';
        $formatted_expiry_date = date('Y-m-d', strtotime(str_replace('/', '-', $expiry_date)));

        update_user_meta($user_id, 'expiry_date', sanitize_text_field($formatted_expiry_date));
    }

    public function set_expiry_date_on_registration($user_id)
    {
        $expiry_date = date('Y-m-d', strtotime('+5 years'));
        update_user_meta($user_id, 'expiry_date', $expiry_date);
    }

    public function add_creation_date_column($columns)
    {
        $columns['creation_date'] = __('Creation Date', 'textdomain');
        return $columns;
    }

    public function add_expiry_date_column($columns)
    {
        $columns['expiry_date'] = __('Expiry Date', 'textdomain');
        return $columns;
    }

    public function show_creation_date_column_content($value, $column_name, $user_id)
    {
        if ($column_name == 'creation_date') {
            $creation_date = get_the_author_meta('user_registered', $user_id);
            $formatted_creation_date = $creation_date ? date('d/m/Y', strtotime($creation_date)) : '';
            return esc_html($formatted_creation_date);
        }
        return $value;
    }

    public function show_expiry_date_column_content($value, $column_name, $user_id)
    {
        if ($column_name == 'expiry_date') {
            $expiry_date = get_user_meta($user_id, 'expiry_date', true);
            $formatted_expiry_date = $expiry_date ? date('d/m/Y', strtotime($expiry_date)) : '';
            if ($expiry_date && strtotime($expiry_date) < time()) {
                return __('Expired', 'textdomain');
            } else {
                return esc_html($formatted_expiry_date);
            }
        }
        return $value;
    }

    public function enqueue_datepicker_script()
    {
        wp_enqueue_script('jquery-ui-datepicker');
    }

    public function initialize_datepicker()
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.datepicker').datepicker({
                    dateFormat: 'dd/mm/yy'
                });
            });
        </script>
        <?php
    }

    public function check_user_expiry_date($user_login, $user)
    {
        $expiry_date = get_user_meta($user->ID, 'expiry_date', true);

        if ($expiry_date && strtotime($expiry_date) < time()) {
            update_user_meta($user->ID, 'account_disabled', true);
            wp_logout();

            wp_redirect(home_url('/?account_status=expired'));
            exit;
        }
    }

    public function show_account_expired_message()
    {
        if (isset($_GET['account_status']) && $_GET['account_status'] === 'expired') {
            $message = __('Your account has expired. Please contact the administrator for further details.', 'textdomain');
            echo '<div class="account-expired-message">' . esc_html($message) . '</div>';
        }
    }

    public function add_inline_css_for_account_expired_message()
    {
        if (isset($_GET['account_status']) && $_GET['account_status'] === 'expired') {
            $custom_css = "
                <style>
                    .account-expired-message {
                        background-color: #f8d7da;
                        color: #721c24;
                        padding: 15px;
                        border: 1px solid #f5c6cb;
                        border-radius: 5px;
                        text-align: center;
                        font-size: 16px;
                    }
                </style>
            ";
            echo $custom_css;
        }
    }
}

