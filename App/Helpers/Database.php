<?php

namespace Wlwd\App\Helpers;

class Database extends Base
{
    public static function getCurrentLevelId( $points){

        if ( $points < 0 ) return '';
        global $wpdb;

        $select = ["id"];

        // table name
        $table_name = "{$wpdb->prefix}wlr_levels";
        // where condition
        $where = [];
        $where[]  = $wpdb->prepare( "from_points <= %d AND (to_points >= %d OR to_points = 0) AND active = 1", [(int) $points,(int) $points]);

        $query = self::buildQuery($select, $table_name, '', $where, '', '');
        return self::fetchData($query,'value');
    }

    public static function getAvailableLevels(){
        global $wpdb;
        $select = ["*"];
        // table name
        $table_name = "{$wpdb->prefix}wlr_levels";

        // where condition
        $where = [];
        $where[]  = $wpdb->prepare( " active = 1", []);

        $query = self::buildQuery($select, $table_name, '', $where, '', '');
        return self::fetchData($query,'multiple');

    }
}