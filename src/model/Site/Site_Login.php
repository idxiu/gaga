<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 06/08/2018
 * Time: 11:45 PM
 */

class Site_Login
{
    private $ctx;
    private $zalyError;
    private $sessionVerifyAction = "api.session.verify";
    private $pinyin;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
        $this->zalyError = $this->ctx->ZalyErrorZh;
        $this->pinyin = new \Overtrue\Pinyin\Pinyin();
    }

    public function checkPreSessionIdFromPlatform($preSessionId, $devicePubkPem = "")
    {

        try {
            //get site config::publicKey
            $sitePriKeyPem = $this->getSiteConfigPriKeyFromDB();
            $this->ctx->Wpf_Logger->info("api.login", "check preSession prikPem=" . $sitePriKeyPem);
            //get userProfile from platform
            $loginUserProfile = $this->getUserProfileFromPlatform($preSessionId, $sitePriKeyPem);

            $this->ctx->Wpf_Logger->info("api.login", "get profile from platform =" . $loginUserProfile->getUserId());
            $this->ctx->Wpf_Logger->info("api.login", "get profile from platform =" . $loginUserProfile->getLoginName());
            $this->ctx->Wpf_Logger->info("api.login", "get profile from platform =" . $loginUserProfile->getNickname());

            $userProfile = $this->handleUserProfile($loginUserProfile, $devicePubkPem);
            return $userProfile;
        } catch (Exception $ex) {
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " errorMsg = " . $ex->getMessage());
            throw new Exception($ex->getMessage());
        }
    }

    private function handleUserProfile($loginUserProfile, $devicePubkPem)
    {


        if (!$loginUserProfile) {
            $errorCode = $this->zalyError->errorSession;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            throw new Exception($errorInfo);
        }
        $nameInLatin = $this->pinyin->permalink($loginUserProfile->getNickName(), "");

        $userProfile = [
            "userId" => $loginUserProfile->getUserId(),
            "loginName" => $loginUserProfile->getLoginName(),
            "nickname" => $loginUserProfile->getNickname(),
            "countryCode" => "+86",
            "loginNameLowercase" => strtolower($loginUserProfile->getLoginName()),
            "nicknameInLatin" => $nameInLatin,
            "phoneId" => $loginUserProfile->getPhoneNumber(),
            "timeReg" => $this->ctx->ZalyHelper->getMsectime()
        ];
        $user = $this->checkUserExists($userProfile);
        if (!$user) {
            $userProfile['availableType'] = \Zaly\Proto\Core\UserAvailableType::UserAvailableNormal;
            $this->insertUser($userProfile);
        }

        //这里
        $sessionId = $this->insertOrUpdateUserSession($userProfile, $devicePubkPem);
        $userProfile['sessionId'] = $sessionId;
        return $userProfile;
    }

    private function getSiteConfigPriKeyFromDB()
    {
        try {
            $results = $this->ctx->SiteConfigTable->selectSiteConfig(SiteConfig::SITE_ID_PRIK_PEM);
            return $results[SiteConfig::SITE_ID_PRIK_PEM];
        } catch (Exception $ex) {
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, "errorMsg = " . $ex->getMessage());
            return '';
        }
    }

    private function getUserProfileFromPlatform($preSessionId, $sitePrikPem)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $sessionVerifyRequest = new \Zaly\Proto\Platform\ApiSessionVerifyRequest();
            $sessionVerifyRequest->setPreSessionId($preSessionId);

            $anyBody = new \Google\Protobuf\Any();
            $anyBody->pack($sessionVerifyRequest);

            $transportData = new \Zaly\Proto\Core\TransportData();
            $transportData->setBody($anyBody);
            $transportData->setAction($this->sessionVerifyAction);
            $data = $transportData->serializeToString();


            $pluginIds = $this->ctx->SiteConfigTable->selectSiteConfig(SiteConfig::SITE_LOGIN_PLUGIN_ID);
            $pluginId = $pluginIds[SiteConfig::SITE_LOGIN_PLUGIN_ID];

            $sessionVerifyUrl = ZalyConfig::getSessionVerifyUrl($pluginId);

            $this->ctx->Wpf_Logger->info("api.login", "get profile from platform url=" . $sessionVerifyUrl);

            $result = $this->ctx->ZalyCurl->request("post", $sessionVerifyUrl, $data);
            $this->ctx->Wpf_Logger->info("api.login", "get profile from platform result=" . $result);


            //解析数据
            $transportData = new \Zaly\Proto\Core\TransportData();
            $transportData->mergeFromString($result);
            $response = $transportData->getBody()->unpack();

            $header = $transportData->getHeader();

            foreach ($header as $key => $val) {
                if ($key == "_1" && $val != "success") {
                    throw new Exception("get user info failed");
                }
            }

            if (isset($header[\Zaly\Proto\Core\TransportDataHeaderKey::HeaderErrorCode]) && $header[\Zaly\Proto\Core\TransportDataHeaderKey::HeaderErrorCode] != $this->defaultErrorCode) {
                throw new Exception($header[\Zaly\Proto\Core\TransportDataHeaderKey::HeaderErrorInfo]);
            }

            ///获取数据
            $key = $response->getKey();
            $aesData = $response->getEncryptedProfile();
            $randomKey = $this->ctx->ZalyRsa->decrypt($key, $sitePrikPem);
            $serialize = $this->ctx->ZalyAes->decrypt($aesData, $randomKey);
            //获取LoginUserProfile
            $loginUserProfile = unserialize($serialize);

            return $loginUserProfile;
        } catch (Exception $ex) {
            $errorCode = $this->zalyError->errorSession;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->ctx->Wpf_Logger->error($tag, "api.site.login error=" . $ex->getMessage());
            throw new Exception($errorInfo);
        }
    }

    private function checkUserExists($userProfile)
    {
        try {
            $user = $this->ctx->SiteUserTable->getUserByUserId($userProfile["userId"]);
            return $user;
        } catch (Exception $ex) {
            throw new Exception("check user is fail");
        }
    }

    private function insertUser($userProfile)
    {
        //getInvitationCode()
        try {
            $this->ctx->SiteUserTable->insertUserInfo($userProfile);
        } catch (Exception $e) {
            throw new Exception("insert user is fail");
        }
    }

    private function insertOrUpdateUserSession($userProfile, $devicePubkPem)
    {
        $sessionId = $this->ctx->ZalyHelper->generateStrId();
        $deviceId = sha1($devicePubkPem);

        try {
            ///TODO 需要替换
            $userId = $userProfile["userId"];
            $sessionInfo = [
                "sessionId" => $sessionId,
                "userId" => $userId,
                "deviceId" => $deviceId,
                "devicePubkPem" => $devicePubkPem,
                "timeWhenCreated" => $this->ctx->ZalyHelper->getMsectime(),
                "ipWhenCreated" => "",
                "timeActive" => $this->ctx->ZalyHelper->getMsectime(),
                "ipActive" => "",
                "userAgent" => "",
                "userAgentType" => "",
            ];
            $this->ctx->SiteSessionTable->insertSessionInfo($sessionInfo);
        } catch (Exception $ex) {
            $userId = $userProfile["userId"];
            $sessionInfo = [
                "sessionId" => $sessionId,
                "timeActive" => $this->ctx->ZalyHelper->getMsectime(),
                "ipActive" => "",
                "userAgent" => "",
                "userAgentType" => "",
            ];
            $where = [
                "userId" => $userId,
                "deviceId" => $deviceId,
            ];
            $this->ctx->SiteSessionTable->updateSessionInfo($where, $sessionInfo);
        }
        return $sessionId;
    }
}