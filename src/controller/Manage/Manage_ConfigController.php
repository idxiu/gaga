<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

class Manage_ConfigController extends ManageController
{
    /**
     * 站点管理
     */
    public function doRequest()
    {
        $this->ctx->Wpf_Logger->info("1111111", 'mange index');
        echo $this->display("manage_siteConfigIndex");
        return;
    }

}