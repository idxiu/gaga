<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 2:46 PM
 */

class Api_Passport_PasswordRegController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordRegRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordRegResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordRegRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $loginName = $request->getLoginName();
            $email     = $request->getEmail();
            $password  = $request->getPassword();
            $nickname  = $request->getNickname();
            $sitePubkPem = $request->getSitePubkPem();

            $this->checkLoginName($loginName);
            $this->checkEmail($email);
            $preSessionId = $this->registerUserForPassport($loginName, $email, $password, $nickname,$sitePubkPem);
            $response = new \Zaly\Proto\Site\ApiPassportPasswordRegResponse();
            $response->setPreSessionId($preSessionId);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private  function checkEmail($email)
    {
        $isEmail = ZalyHelper::isEmail($email);
        if(!$isEmail) {
            $errorCode = $this->zalyError->errorInvalidEmail;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("email is useless");
        }
        $user = $this->ctx->PassportPasswordTable->getUserByEmail($email);
        if($user){
            $errorCode = $this->zalyError->errorExistEmail;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("email is exists");
        }
    }

    private function checkLoginName($loginName)
    {
        $user = $this->ctx->PassportPasswordTable->getUserByLoginName($loginName);
        if($user){
            $errorCode = $this->zalyError->errorExistLoginName;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("loginName is exists");
        }
    }

    private function registerUserForPassport($loginName, $email, $password, $nickname,$sitePubkPem)
    {
       try{
           $this->ctx->BaseTable->db->beginTransaction();
           $userId   = ZalyHelper::generateStrId();
           $userInfo = [
               "userId"    => $userId,
               "loginName" => $loginName,
               "email"     => $email,
               "password"  => password_hash($password, PASSWORD_BCRYPT),
               "nickname"  => $nickname,
           ];
           $this->ctx->PassportPasswordTable->insertUserInfo($userInfo);
           $preSessionId = ZalyHelper::generateStrId();

           $preSessionInfo = [
               "userId" => $userId,
               "preSessionId" => $preSessionId,
               "sitePubkPem" => base64_encode($sitePubkPem)
           ];
           $this->ctx->PassportPasswordPreSessionTable->insertPreSessionData($preSessionInfo);

           $this->ctx->BaseTable->db->commit();
           return $preSessionId;
       }catch (Exception $ex) {
           $this->ctx->BaseTable->db->rollback();
       }
    }



}