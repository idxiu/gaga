<?php

/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 13/07/2018
 * Time: 11:48 AM
 */
class Wpf_Logger
{
    private $_level = [
        "info",
        "warn",
        "error",
        "sql"
    ];

    private $fileName = 'openzaly-site-log';
    private $filePath = '/akaxin';////TODO 需要修改
    private $handler = '';
    private $logType = "";

    public function __construct()
    {
        $this->fileName = $this->fileName . "-" . date("Ymd") . ".log";
        $this->filePath = $this->filePath . "/" . $this->fileName;
//        $this->handler = fopen($this->filePath, "a+");
    }

    /**
     * info log
     * @param $tag
     * @param $infoMsg
     */
    public function info($tag, $infoMsg)
    {
        $this->logType = "info";
        $this->writeLog($tag, $infoMsg);
    }

    /**
     * warn log
     * @param $tag
     * @param $infoMsg
     */
    public function warn($tag, $infoMsg)
    {
        $this->logType = "warn";
        $this->writeLog($tag, $infoMsg);
    }

    /**
     * error log
     * @param $tag
     * @param $infoMsg
     */
    public function error($tag, $infoMsg)
    {
        $this->logType = "error";
        $this->writeLog($tag, $infoMsg);
    }

    /**
     * write log
     * @param $tag
     * @param $msg
     */
    private function writeLog($tag, $msg)
    {
        if (!in_array($this->logType, $this->_level)) {
            return;
        }

        if (is_array($msg)) {
            $msg = json_encode($msg);
        }

        $content = "[$this->logType] " . date("Y-m-d H:i:s") . " $tag $msg \n";
//        fwrite($this->handler, $content);
        error_log($content);

    }

    public function writeSqlLog($tag, $sql, $params = [], $startTime = 0)
    {
        if (is_array($params)) {
            $params = json_encode($params);
        }
        $this->logType = "sql";
        $expendTime = microtime(true) - $startTime;
        $content = "[$this->logType] " . date("Y-m-d H:i:s") . " $tag  sql=$sql  params=$params  expend_time=$expendTime\n";
//        fwrite($this->handler, $content);
        error_log($content);
    }

    public function dbLog($tag, $sql, $params = [], $startTime = 0, $result)
    {
        if (is_array($params)) {
            $params = json_encode($params);
        }

        if (is_array($result)) {
            $result = json_encode($result);
        }


        $this->logType = "sql";
        $expendTime = microtime(true) - $startTime;
        $content = "[$this->logType] "
            . date("Y-m-d H:i:s")
            . " $tag  sql=$sql  params=$params  expend_time=$expendTime "
            . "result=$result \n";

        error_log($content);
    }
}
