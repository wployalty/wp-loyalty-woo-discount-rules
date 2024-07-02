<?php

namespace Wlwd\App\Helpers;

class Heplers
{
    public static function hasAdminPrivilege() {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            return true;
        } else {
            return false;
        }
    }
}