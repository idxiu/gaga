<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 10:34 AM
 */

class SiteConfig
{
    const SITE_NAME = "name";
    const SITE_LOGO = "logo";

    //masters = administrator + managers
    const SITE_ADMIN = "administrator";
    const SITE_MANAGERS = "managers";

    const SITE_LOGIN_PLUGIN_ID = "loginPluginId";

    const SITE_ADDRESS_FOR_API = "serverAddressForApi";
    const SITE_ADDRESS_FOR_IM = "serverAddressForIM";

    const SITE_ENABLE_CREATE_GROUP = "enableCreateGroup";
    const SITE_ENABLE_ADD_FRIEND = "enableAddFriend";
    const SITE_ENABLE_TMP_CHAT = "enableTmpChat";
    const SITE_ENABLE_INVITATION_CODE = "enableInvitationCode";//邀请码
    const SITE_ENABLE_REAL_NAME = "enableRealName";
    const SITE_ENABLE_WIDGET_WEB = "enableWidgetWeb";

    const SITE_SUPPORT_PUSH_TYPE = "pushType";
    const SITE_GROUP_INVITATION_URL_EXPIRATION = "groupInvitationUrlExpiration";
    const SITE_MAX_CLIENTS_NUM = "maxClientsNum";
    const SITE_MAX_GROUP_MEMBERS = "maxGroupMembers";

    const SITE_ID = "siteId";
    const SITE_ID_PRIK_PEM = "siteIdPrikPem";
    const SITE_ID_PUBK_PEM = "siteIdPubkPem";


    //site default value
    const SITE_NAME_VAL = "duck-site";
    const SITE_LOGO_VAL = "site-logo";

    const SITE_LOGIN_PLUGIN_ID_VAL = 1;

    const SITE_ADDRESS_FOR_API_VAL = 1;
    const SITE_ADDRESS_FOR_IM_VAL = 1;

    const SITE_ENABLE_CREATE_GROUP_VAL = 1;
    const SITE_ENABLE_ADD_FRIEND_VAL = 1;
    const SITE_ENABLE_TMP_CHAT_VAL = 0;
    const SITE_ENABLE_REAL_NAME_VAL = 0;    //是不是不需要？
    const SITE_ENABLE_WIDGET_WEB_VAL = 0;
    const SITE_ENABLE_INVITATION_CODE_VAL = 0;

    const SITE_SUPPORT_PUSH_TYPE_VAL = Zaly\Proto\Core\PushType::PushNotificationOnly;
    const SITE_MAX_GROUP_MEMBERS_VAL = 100;
    const SITE_MAX_CLIENTS_NUM_VAL = 200;
    const SITE_GROUP_INVITATION_URL_EXPIRATION_VAL = "20";//群邀请链接


    public static function getPubkAndPrikPem()
    {
        $pair = ZalyRsa::newRsaKeyPair(2048);

        return [
            self::SITE_ID_PUBK_PEM => $pair[ZalyRsa::$KeyPublicKey],
            self::SITE_ID_PRIK_PEM => $pair[ZalyRsa::$KeyPrivateKey]
        ];
    }


    public static $siteConfig = [
        self::SITE_NAME => self::SITE_NAME_VAL,
        self::SITE_LOGO => self::SITE_LOGO_VAL,

        self::SITE_ADDRESS_FOR_API => self::SITE_ADDRESS_FOR_API_VAL,
        self::SITE_ADDRESS_FOR_IM => self::SITE_ADDRESS_FOR_IM_VAL,

        self::SITE_LOGIN_PLUGIN_ID => self::SITE_LOGIN_PLUGIN_ID_VAL,

        self::SITE_ENABLE_CREATE_GROUP => self::SITE_ENABLE_CREATE_GROUP_VAL,
        self::SITE_ENABLE_ADD_FRIEND => self::SITE_ENABLE_ADD_FRIEND_VAL,
        self::SITE_ENABLE_TMP_CHAT => self::SITE_ENABLE_TMP_CHAT_VAL,
        self::SITE_ENABLE_INVITATION_CODE => self::SITE_ENABLE_INVITATION_CODE_VAL,
        self::SITE_ENABLE_REAL_NAME => self::SITE_ENABLE_REAL_NAME_VAL,
        self::SITE_ENABLE_WIDGET_WEB => self::SITE_ENABLE_WIDGET_WEB_VAL,

        self::SITE_ID_PUBK_PEM => "",
        self::SITE_ID_PRIK_PEM => "",

        self::SITE_SUPPORT_PUSH_TYPE => self::SITE_SUPPORT_PUSH_TYPE_VAL,
        self::SITE_MAX_CLIENTS_NUM => self::SITE_MAX_CLIENTS_NUM_VAL,
        self::SITE_GROUP_INVITATION_URL_EXPIRATION => self::SITE_GROUP_INVITATION_URL_EXPIRATION_VAL,
        self::SITE_MAX_GROUP_MEMBERS => self::SITE_MAX_GROUP_MEMBERS_VAL,
    ];

}