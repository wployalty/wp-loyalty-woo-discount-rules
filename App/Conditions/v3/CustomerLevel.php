<?php
namespace Wlwd\App\Conditions\v3;

defined('ABSPATH') or die;

use WDR\Core\Modules\Conditions\BaseCondition;
use WDR\Core\Models\Custom\RuleModel;
use Wlwd\App\Helpers\Database;

class CustomerLevel extends BaseCondition
{
    public static function getFields(array $data = []):array
    {
        return [
            'type' => [
                'required' => true,
                'default' => 'customer_level'
            ],
            'options' => [
                'operator' => [
                    'type' => 'select',
                    'required' => true,
                    'description' => __('Customer level should be', 'wp-loyalty-woo-discount-rule'),
                    'options' => [
                        ['value' => 'in_list' , 'label' => __('In List', 'wp-loyalty-woo-discount-rule')],
                        ['value' => 'not_in_list' , 'label' => __('Not In List', 'wp-loyalty-woo-discount-rule')],
                    ],
                    'default' => 'in_list',
                ],
                'value' => [
                    'type' => 'select',
                    'required' => true,
                    //'is_not_empty' => true,
                    'is_multiple' => true,
                    //'is_searchable' => true,
                    'description' => __('Select customer level', 'wp-loyalty-woo-discount-rule'),
                    /*'sub_validation' => array(
                        'required' => true,
                    ),*/
                    "extra_class" => "wdr-rbbt-col-span-6",
                    'default' => [],
                    'options' => self::getLevelOptions()
                ],
            ]
        ];
    }

    public function check(array $data , RuleModel $rule):bool
    {
        if (empty($data['options']) || empty($data['options']['value']) || empty($data['options']['operator'])) {
            return false;
        }
        
        $user_email = $this->getUserEmail();
        if(empty($user_email)){
            return false;
        }

        $loyalty_user = Database::getPointUserByEmail($user_email);
        $customer_level = isset($loyalty_user->level_id) && !empty($loyalty_user->level_id) ? $loyalty_user->level_id : 0;
        if ($customer_level <= 0) {
            $points = (int)(isset($wlr_user->points) && $wlr_user->points > 0 ? $wlr_user->points : 0);
            $customer_level = Database::getCurrentLevelId($points);
        }
        if($customer_level <= 0){
            return false;
        }
        return self::checkLists([$customer_level], $data['options']['value'], $data['options']['operator']);
        
    }

    private function getUserEmail(){
        $user = get_user_by('id', get_current_user_id());
        return $user->user_email ?? null;
    }

    protected static function getLevelOptions(){
        $levels = Database::getAvailableLevels();
        return array_map(function($level){
            return ['value' => $level->id, 'label' => $level->name];
        }, $levels);
    }

    public function fetch(array $args = []):array
    {
        return (array)apply_filters('wdr_customer_level_condition_fetch', [],$args);
    }

    public function validate(array $data, string $key): bool
    {
        return (bool)apply_filters('wdr_customer_level_condition_validate', $this->validateFields($data, self::getFields(), $key), $data);
    }

     /**
     * Add label to value.
     *
     * @param array $data Condition data.
     * @return array
     */
    public function addLabel(array $data): array
    {
        return $data;
    }

    /**
     * Remove label.
     *
     * @param array $data Condition data.
     * @return array
     */
    public function removeLabel(array $data): array
    {
        return $data;
    }
}