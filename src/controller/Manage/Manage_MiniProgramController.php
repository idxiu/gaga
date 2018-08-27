<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:49 PM
 */

class Manage_MiniProgramController extends ManageController
{

    public function doRequest()
    {
        $this->ctx->Wpf_Logger->info("1111111", 'mange index');
        echo $this->display("manage_miniProgramIndex");
        return;
    }

}