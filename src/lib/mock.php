<?php
/**
 * Created by PhpStorm.
 * User: sssl
 * Date: 2018/8/28
 * Time: 11:42 AM
 */

// mock bcmath
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


if(extension_loaded("openssl")) {
    // fix OpenSSL
    //
    // 所有PHP手册中含有下述信息的，都需要wrapper一下再用:
    //  // Note: You need to have a valid openssl.cnf installed for this function to operate correctly. See the notes under the installation section for more information.
    //
    //
    define("MOCK_OPENSSL_CNF", __DIR__ . "/mock-openssl.cnf");
}


