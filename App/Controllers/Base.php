<?php
/**
 * @author      Wployalty (Alagesan)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlwd\App\Controllers;


use Wlr\App\Helpers\Template;
use Wlr\App\Helpers\Woocommerce;

defined('ABSPATH') or die;

class Base
{
    function addMenu()
    {
        if (Woocommerce::hasAdminPrivilege()) {
            add_menu_page(__('WPLoyalty - Woo Discount Rule Compatibility', 'wp-loyalty-woo-discount-rule'), __('WPLoyalty - Woo Discount Rule Compatibility', 'wp-loyalty-woo-discount-rule'), 'manage_woocommerce', WLWD_PLUGIN_SLUG, array($this, 'manageLoyaltyPages'), 'dashicons-megaphone', 57);
        }
    }

    function manageLoyaltyPages(){
        if (!Woocommerce::hasAdminPrivilege()) {
            wp_die(esc_html(__("Don't have access permission", 'wp-loyalty-woo-discount-rule')));
        }
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == WLWD_PLUGIN_SLUG) {
            $path = WLWD_PLUGIN_PATH . 'App/Views/Admin/main.php';
            $template = new Template();
            $main_page_params = array();
            $template->setData($path, $main_page_params)->display();
        } else {
            wp_die(esc_html(__('Page query params missing...', 'wp-loyalty-woo-discount-rule')));
        }
    }

    function menuHideProperties()
    {
        ?>
        <style>
            #toplevel_page_wp-loyalty-woo-discount-rule{
                display: none !important;
            }
        </style>
        <?php
    }

    function addConditions($available_conditions){
        if (file_exists(WLWD_PLUGIN_PATH . 'App/Conditions/')) {
            $conditions_list = array_slice(scandir(WLWD_PLUGIN_PATH . 'App/Conditions/'), 2);
            if (!empty($conditions_list)) {
                foreach ($conditions_list as $condition) {
                    $class_name = basename($condition, '.php');
                    if (!in_array($class_name, array('Base'))) {
                        $condition_class_name = 'Wlwd\App\Conditions\\' . $class_name;
                        if (class_exists($condition_class_name)) {
                            $condition_object = new $condition_class_name();
                            if ($condition_object instanceof \Wdr\App\Conditions\Base) {
                                $rule_name = $condition_object->name();
                                if (!empty($rule_name)) {
                                    $available_conditions[$rule_name] = array(
                                        'object' => $condition_object,
                                        'label' => $condition_object->label,
                                        'group' => $condition_object->group,
                                        'template' => $condition_object->template,
                                        'extra_params' => $condition_object->extra_params,
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        return $available_conditions;
    }
}