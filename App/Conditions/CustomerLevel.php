<?php

namespace Wlwd\App\Conditions;
use Wdr\App\Conditions\Base;
use Wlr\App\Models\Levels;
use Wlr\App\Models\Users;

defined('ABSPATH') or die;
class CustomerLevel extends Base
{
    function __construct()
    {
        parent::__construct();
        $this->name = 'customer_level';
        $this->label = __('Level', 'woo-discount-rules-pro');
        $this->group = __('Customer', 'woo-discount-rules-pro');
        $this->template = WLWD_PLUGIN_PATH . 'App/Views/Admin/Conditions/Customer/level.php';
    }

    public function check($cart, $options)
    {
        if (isset($options->value) && isset($options->operator)) {
            $post_data = $this->input->post('post_data');
            $post = array();
            if (!empty($post_data)) {
                parse_str($post_data, $post);
            }
            $customer_level = (int)$this->input->post('customer_level', 0);

            if (empty($customer_level)) {
                if(!isset($post['billing_email'])){
                    $post['billing_email'] = $this->input->post('billing_email');
                }
                $user_email = NULL;
                if (isset($post['billing_email']) && !empty($post['billing_email'])) {
                    $user_email = $post['billing_email'];
                } elseif (get_current_user_id()) {
                    $user_email = get_user_meta(get_current_user_id(), 'billing_email', true);
                    if (empty($user_email) || $user_email == '') {
                        $user = (is_user_logged_in()) ? get_user_by('ID', get_current_user_id()) : NULL;
                        if (!empty($user)) {
                            $user_email = isset($user->data->user_email) ? $user->data->user_email : NULL;
                        }
                    }
                }
                $level_model = new Levels();
                if(!empty($user_email)){
                    $base_helper = new \Wlr\App\Helpers\Base();
                    $wlr_user = $base_helper->getPointUserByEmail($user_email);
                    $level_id = isset($wlr_user->level_id) && !empty($wlr_user->level_id) ? $wlr_user->level_id : 0;
                    if ($level_id <= 0) {
                        $points = (int)(isset($wlr_user->points) && $wlr_user->points > 0 ? $wlr_user->points : 0);
                        $customer_level = $level_model->getCurrentLevelId($points);
                    }
                }elseif (isset($options->value) && empty($user_email)) {
                    $customer_level = $level_model->getCurrentLevelId(0);
                }
            }
            return $this->doCompareInListOperation($options->operator, $customer_level, $options->value);
        }
        return false;
    }
}