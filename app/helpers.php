<?php
/**
 * Created by PhpStorm.
 * User: hexu
 * Date: 2019/8/23
 * Time: 1:43 PM
 */

function utf8_array_asort(&$array) {
    if(!isset($array) || !is_array($array)) {
        return false;
    }
    foreach($array as $k=>$v) {
        $array[$k] = iconv('UTF-8', 'GBK//IGNORE',$v);
    }
    asort($array);
    foreach($array as $k=>$v) {
        $array[$k] = iconv('GBK', 'UTF-8//IGNORE', $v);
    }
    return true;
}