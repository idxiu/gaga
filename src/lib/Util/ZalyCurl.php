<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 10:34 AM
 */

class ZalyCurl
{
    protected $_curlObj = '';
    protected $_bodyContent = '';
    protected $timeOut = 3;///单位秒


    /**
     * @param $action
     * @param $requestBody
     * @param $url add &body_format=pb
     * @param $method
     * @return mixed
     * @throws Exception
     */
    public function requestWithActionByPb($action, $requestBody, $url, $method)
    {
        try {

            $anyBody = new \Google\Protobuf\Any();
            $anyBody->pack($requestBody);

            $transportData = new \Zaly\Proto\Core\TransportData();
            $transportData->setAction($action);
            $transportData->setBody($anyBody);
            $params = $transportData->serializeToString();

            $this->_curlObj = curl_init();
            $this->_getRequestParams($params);
//            $this->_setHeader($headers);
            $this->setRequestMethod($method);
            curl_setopt($this->_curlObj, CURLOPT_URL, $url);
            curl_setopt($this->_curlObj, CURLOPT_TIMEOUT, $this->timeOut);

            if (($resp = curl_exec($this->_curlObj)) === false) {
                error_log('when run Router, unexpected error :' . curl_error($this->_curlObj));
                throw new Exception(curl_error($this->_curlObj));
            }
            curl_close($this->_curlObj);
            return $resp;
        } catch (\Exception $e) {
            $message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            error_log('when run Router, unexpected error :' . $message);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 发送curl请求
     *
     * @author 尹少爷 2017.12.22
     *
     * @param string method
     * @param string url
     * @param array params
     * @param array headers
     *
     * @return bool|mix
     * @throws Exception
     */
    public function request($method, $url, $params = [], $headers = [])
    {
        try {
            $this->_curlObj = curl_init();
            $this->_getRequestParams($params);
            $this->_setHeader($headers);
            $this->setRequestMethod($method);
            curl_setopt($this->_curlObj, CURLOPT_URL, $url);

            if (($resp = curl_exec($this->_curlObj)) === false) {
                error_log('when run Router, unexpected error :' . curl_error($this->_curlObj));
                throw new Exception(curl_error($this->_curlObj));
            }
            curl_close($this->_curlObj);
            return $resp;
        } catch (\Exception $e) {
            $message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            error_log('when run Router, unexpected error :' . $message);
            throw new Exception($e->getMessage());
        }
    }

    protected function setRequestMethod($method)
    {
        curl_setopt($this->_curlObj, CURLOPT_TIMEOUT, $this->timeOut);
        switch (strtoupper($method)) {
            case 'HEAD':
                curl_setopt($this->_curlObj, CURLOPT_NOBODY, true);
                break;
            case 'GET':
                //	TRUE to include the header in the output.
                curl_setopt($this->_curlObj, CURLOPT_HEADER, false);
                //TRUE to reset the HTTP request method to GET. Since GET is the default, this is only necessary if the request method has been changed.
                curl_setopt($this->_curlObj, CURLOPT_HTTPGET, true);
                //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it directly.
                curl_setopt($this->_curlObj, CURLOPT_RETURNTRANSFER, true);
                break;
            case 'POST':
                curl_setopt($this->_curlObj, CURLOPT_HEADER, false);
                curl_setopt($this->_curlObj, CURLOPT_NOBODY, false);
                curl_setopt($this->_curlObj, CURLOPT_POST, true);
                curl_setopt($this->_curlObj, CURLOPT_POSTFIELDS, $this->_bodyContent);
                curl_setopt($this->_curlObj, CURLOPT_RETURNTRANSFER, true);
                break;
            default:
                curl_setopt($this->_curlObj, CURLOPT_HEADER, false);
                curl_setopt($this->_curlObj, CURLOPT_HTTPGET, true);
                curl_setopt($this->_curlObj, CURLOPT_RETURNTRANSFER, true);
        }
    }

    protected function _getRequestParams($params)
    {
        if (empty($params)) {
            return '';
        }
        $this->_bodyContent = $params;
        if (is_array($params)) {
            $this->_bodyContent = http_build_query($params, '', '&');
        }
    }

    protected function _setHeader($baseHeaders)
    {
        $headers = array();
        if (!$baseHeaders) {
            curl_setopt($this->_curlObj, CURLOPT_HEADER, 0);
            return false;
        }
        foreach ($baseHeaders as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($this->_curlObj, CURLOPT_HEADER, 1);
        curl_setopt($this->_curlObj, CURLOPT_HTTPHEADER, $headers);
    }
}
