<?php
/**
 * Created by PhpStorm.
 * User: sssl
 * Date: 2018/8/28
 * Time: 11:42 AM
 */

if(!extension_loaded("bcmath")) {
    function bcadd($left_operand,  $right_operand, $scale = 0 )
    {
        return (string)(intval($left_operand) + intval($right_operand));
    }

    function bccomp($left_operand, $right_operand, $scale = 0)
    {
        $left_operand = intval($left_operand);
        $right_operand = intval($right_operand);

        if($left_operand > $right_operand) {
            return 1;
        } else if($left_operand == $right_operand) {
            return 0;
        } else {
            return -1;
        }
    }

    function bcsub($left_operand, $right_operand, $scale = 0)
    {
        return (string)(intval($left_operand) - intval($right_operand));
    }
}