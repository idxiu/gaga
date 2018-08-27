<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 16/07/2018
 * Time: 3:33 PM
 */

class Api_Plugin_ProxyController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPluginProxyRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPluginProxyResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {

            $pluginId = $request->getPluginId();
            $reqUrl = $request->getUrl();
            $requestUrl = $this->getPluginRequestUrl($reqUrl, $pluginId);
            $method = $request->getMethod();

            switch ($method) {
                case \Zaly\Proto\Core\HttpQueryType::HttpQueryGet:
                    $method = "get";
                    break;
                case \Zaly\Proto\Core\HttpQueryType::HttpQueryPost:
                    $method = "post";
                    break;
                default:
                    throw new Exception("http query invalid");
            }
            $cookie = $request->getCookie();
            $body = $request->getBody();

            $respBody = $this->ctx->ZalyCurl->request($method, $requestUrl, $body, $headers = []);
            $response = $this->getApiPluginProxyResponse($respBody, $cookie);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }


    /**
     * @param $reqUrl
     * @param $pluginId
     * @return string
     */
    private function getPluginRequestUrl($reqUrl, $pluginId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $proxyRequestUrl = $reqUrl;
        try {
            //special host in url,if no return empty
            $reqUrlParams = parse_url($reqUrl, PHP_URL_HOST);

            //get plugin profile
            $pluginProfile = $this->getPluginFromDB($pluginId);

            $timestamp = $this->ctx->ZalyHelper->getMsectime();
            $signKey = $this->ctx->ZalyAes->encrypt($pluginProfile['auth_key'], $timestamp);
            $authKeySignBase64 = base64_encode($signKey);

            $this->ctx->Wpf_Logger->info($tag, "requestUrl==" . $reqUrl);
            $this->ctx->Wpf_Logger->info($tag, "urlParams ==" . $reqUrlParams);
            $this->ctx->Wpf_Logger->info($tag, "authKeySignBase64=" . $authKeySignBase64);

            if (isset($reqUrlParams)) {
                $proxyRequestUrl .= "&signBase64=" . $authKeySignBase64;
                $this->ctx->Wpf_Logger->info("api.plugin.proxy", "proxyRequestUrl=" . $proxyRequestUrl);
                return $reqUrl . "&signBase64=" . $authKeySignBase64;
            }

            $pluginUrl = parse_url($pluginProfile['landingPageUrl']);

            $pluginUrlSchemeAndHost = (isset($pluginUrl['scheme']) ? $pluginUrl['scheme'] : "http") . "://" . $pluginUrl['host']
                . (isset($pluginUrl['port']) ? ":" . $pluginUrl['port'] : "");

            $proxyRequestUrl = $pluginUrlSchemeAndHost . "/" . $proxyRequestUrl . "&signBase64=" . $authKeySignBase64;

            $this->ctx->Wpf_Logger->info("api.plugin.proxy", "proxyRequestUrl=" . $proxyRequestUrl);

            return $proxyRequestUrl;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info($tag, " error_msg=" . $e->getMessage());
            return "";
        }
    }

    /**
     * @param $pluginId
     * @return mixed
     */
    private function getPluginFromDB($pluginId)
    {
        $pluginProfile = $this->ctx->SitePluginTable->getPluginById($pluginId);
        return $pluginProfile;
    }

    /**
     * @param $body
     * @return \Zaly\Proto\Site\ApiPluginProxyResponse
     */
    private function getApiPluginProxyResponse($body, $cookie)
    {
        $response = new \Zaly\Proto\Site\ApiPluginProxyResponse();
        $response->setBody($body);
        $response->setCookie($cookie . "-" . date("Y-m-d H:i:s", time()));
        return $response;
    }
}