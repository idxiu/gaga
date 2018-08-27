<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/07/2018
 * Time: 2:39 PM
 */
class ZalyErrorEn extends  ZalyErrorBase
{
    private $defaultError = "operation failed";

    public $errorInfo = [
        "error.user.needRegister" => "用户需要注册",
        "error.db.writable"   => "db is not hava write permission",
        "error.session.id"    =>  "login timeout",
        "error.userProfile"   => "parse data is error",
        "error.siteLogin"     => "login fail",
        "error.plugin.list"   => "get list is fail",
        "error.group.name.length"       => "group name's length is error",
        "error.group.create.permission" => "current site is not permission to create table",
        "error.group.create"  => "create group fail",
        "error.group.info"    => "获取群信息失败",
        "error.group.exist"   => "群已经被解散",
        "error.group.owner"   => "只有群主可以操作",
        "error.group.admin"   => "没有权限操作",
        "error.group.delete"  => "删除群组失败",
        "error.group.maxMemberCount" => "群满员",
        "error.group.member"  => "不是群成员，不能拉人",
        "error.group.idExists" => "群id无效",
        "error.user.idExists"  => "用户id无效",
        "error.group.profile"  => "not found group profile",
        "error.group.invite"   => "邀请失败",

    ];

    public function getErrorInfo($errorCode)
    {
        return isset($this->errorInfo[$errorCode]) ? $this->errorInfo[$errorCode] : $this->defaultError;
    }
}