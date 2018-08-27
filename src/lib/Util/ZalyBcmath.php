<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/08/2018
 * Time: 4:39 PM
 */

if(!extension_loaded("bcmath")) {
    function bcadd($left_operand,  $right_operand,  $scale = 0 )
    {
        if($scale === 0 ) {
            return floor($left_operand + $right_operand);
        }
        return (string) floatval($left_operand+$right_operand);
    }

    function bccomp($left_operand, $right_operand, $scale = 0)
    {
        $left_operand = floor($left_operand);
        $right_operand = floor($right_operand);

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
        if($scale == 0 ) {
            return (string) ($left_operand - $right_operand);
        } else {
            $num = 10;
            $num = pow($num, $scale);
            $left_operand = $left_operand * $num;
            $right_operand = $right_operand *$num;
            return (string)(($left_operand-$right_operand)/$num);
        }
    }
}