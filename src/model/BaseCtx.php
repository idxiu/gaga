<?php


/**
 *
 * Help Ide Code AutoComplement
 *
 * @property File_Manager File_Manager
 * @property Site_Login Site_Login
 * @property SiteConfig SiteConfig
 *
 * @property Site_Config Site_Config
 *
 * @property SiteConfigTable SiteConfigTable
 * @property SiteSessionTable SiteSessionTable
 * @property SiteUserTable SiteUserTable
 * @property PassportPasswordTable PassportPasswordTable
 * @property PassportPasswordTokenTable PassportPasswordTokenTable
 * @property  PassportPasswordPreSessionTable PassportPasswordPreSessionTable
 * @property SiteUserFriendTable SiteUserFriendTable
 * @property SiteFriendApplyTable SiteFriendApplyTable
 * @property SiteU2MessageTable SiteU2MessageTable
 * @property SiteGroupTable SiteGroupTable
 * @property SiteGroupUserTable SiteGroupUserTable
 * @property SiteGroupMessageTable SiteGroupMessageTable
 *
 * @property SitePluginTable SitePluginTable
 *
 * @property Message_Client Message_Client
 * @property Message_News Message_News
 * @property Push_Client Push_Client
 * @property Gateway_Client Gateway_Client
 * @property ZalyBcmath ZalyBcmath
 * @property Wpf_Logger Wpf_Logger
 * @property ZalyCurl ZalyCurl
 * @property ZalyRsa ZalyRsa
 * @property ZalyHelper ZalyHelper
 *
 */
class BaseCtx extends Wpf_Ctx
{
    public $db;
    private $_dbName = "openzalySiteDB.sqlite3";
    private $_dbPath = ".";
    private $_dbSqlFile = "./database-sql/site.sql";
    public $dbVersion = 1;
    private $dbName = "openzalySiteDB";
    private $dbUser = "root";
    private $dbPwd = "root";
    private $dbHost = "127.0.0.1";
    private $dbType = "sqlite";
    private $suffix = ".sqlite3";

    public function __construct()
    {
        $this->checkDBExists();
    }

    private function checkDBExists()
    {
        $sqliteConfig = ZalyConfig::getConfig("sqlite");
        $this->_dbName = $sqliteConfig['sqliteDBName'];
        if(!empty($this->_dbName)   && file_exists(dirname(__FILE__)."/../".$this->_dbName)) {
            switch ($this->dbType) {
                case "sqlite":
                    $dbInfo = $this->_dbPath . "/" . $this->_dbName;
                    $this->db = new \PDO("sqlite:{$dbInfo}");
                    break;
                case "mysql":
                    $dsn = "mysql:dbname=$this->dbName;host=$this->dbHost";
                    $this->db = new \PDO($dsn, $this->dbUser, $this->dbPwd);
                    break;
            }
        }
    }

    public function initSite( $sqliteName,  $siteName, $siteHost, $Port, $defaultApiProtocol, $defaultImProtocol)
    {
        $this->_dbName = $sqliteName;
        $this->checkDBExists();
        $this->_checkDBAndTable($siteName, $siteHost, $Port, $defaultApiProtocol, $defaultImProtocol);
    }

    private function _checkDBAndTable($siteName, $siteHost, $Port, $defaultApiProtocol, $defaultImProtocol)
    {
        $dbVersion = $this->getDBVersion();
        if ($dbVersion >= 1) {
            return;
        }
        $this->_checkDBTables();
        $this->_checkDefaultTableVal($siteName, $siteHost, $Port, $defaultApiProtocol, $defaultImProtocol);
    }


    private function _checkDBTables()
    {
        ////TODO 检测数据表是否存在
//        $sql = 'sqlite3 $this->_dbName  ".read {$this->_dbSqlFile}"';
//        $this->db->exec($sql);
        $this->_checkSiteConfigTable();
        $this->_checkSiteGroupTable();
        $this->_checkSiteGroupUserTable();
        $this->_checkSitePluginTable();
        $this->_checkSiteSessionTable();
        $this->_checkSiteUserTable();

        $this->_checkSiteU2MessageTable();// check table + index
        $this->_checkSiteU2MessagePointerTable();// check table + index

        $this->_checkSiteGroupMessageTable();// check table + index
        $this->_checkSiteGroupMessagePointerTable();// check table + index

        $this->_checkSiteUserFriendTable();
        $this->_checkSiteFriendApplyTable();

        $this->_checkPassportPasswordTable();
        $this->_checkPassportPasswordPreSessionTable();
        $this->_checkPassportPasswordTokenTable();

        $this->createDBLockFile();
    }

    private function _checkDefaultTableVal($siteName, $siteHost, $Port, $defaultApiProtocol, $defaultImProtocol)
    {
//        $loginPluginId = 100;
        $loginPluginId = ZalyConfig::getConfig("loginPluginId");
        $this->_insertSiteLoginPlugin($loginPluginId);
        $this->_insertSiteConfig($loginPluginId, $siteName, $defaultApiProtocol, $defaultImProtocol);

        //admin web
        $this->_insertSiteManagerPlugin($siteHost, $Port);
        //user square
        $this->_insertSiteUserSquarePlugin($siteHost, $Port);

        return;
    }

    private function _checkSiteFriendApplyTable()
    {
        $sql = "CREATE TABLE if not EXISTS siteFriendApply(
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              userId VARCHAR(100) NOT NULL,
              friendId VARCHAR(100) NOT NULL,
              greetings VARCHAR(100),
              applyTime BIGINT,
              UNIQUE(userId, friendId)
        );";
        $this->db->exec($sql);

    }

    private function _checkSiteConfigTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteConfig(
                  id INTEGER PRIMARY KEY AUTOINCREMENT,
                  configKey VARCHAR(100) NOT NULL,
                  configValue TEXT ,
                  UNIQUE (configKey)
                );";
        $this->db->exec($sql);
    }

    private function _checkSitePluginTable()
    {
        $sql = "
            CREATE TABLE sitePlugin(
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              pluginId INTEGER NOT NULL,
              name VARCHAR(100) NOT NULL, /*名字*/
              `logo` TEXT NOT NULL,/*logo*/
              `order` INTEGER,/*排序 数值越小，排位靠前*/
              `landingPageUrl` TEXT,/*落地页*/
              `landingPageWithProxy` LONG, /*是否使用resp加载落地页*/
              usageType INTEGER,          /*功能类型*/
              loadingType INTEGER,/*展现方式*/
               permissionType INTEGER ,    /*使用权限*/
               authKey VARCHAR(32) NOT NULL,
               addTime BIGINT,
               UNIQUE(pluginId,usageType)
              );";
        $this->db->exec($sql);

        $sql = "CREATE INDEX IF NOT EXISTS indexSitePluginOrder ON sitePlugin(\"order\");";
        $this->db->exec($sql);
    }

    private function _checkSiteUserTable()
    {
        $sql = "CREATE TABLE  IF NOT EXISTS siteUser (
                   id INTEGER PRIMARY KEY AUTOINCREMENT,
                   userId VARCHAR(100) UNIQUE NOT NULL,
                   loginName VARCHAR(100) UNIQUE NOT NULL,
                   loginNameLowercase VARCHAR(100) NOT NULL,
                   nickname VARCHAR(100) NOT NULL,
                   nicknameInLatin VARCHAR(100),
                   avatar VARCHAR(256),
                   availableType INTEGER,
                   countryCode VARCHAR(10),
                   phoneId VARCHAR(11),
                   friendVersion INTEGER,
                   timeReg BIGINT
            );";
        $this->db->exec($sql);
    }

    private function _checkSiteUserFriendTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteUserFriend(
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    userId VARCHAR(100) NOT NULL,
                    friendId VARCHAR(100) NOT NULL,
                    aliasName VARCHAR(100),
                    aliasNameInLatin VARCHAR(100),
                    relation INTEGER,
                    mute BOOLEAN,/*1互为好友 2我删除了对方 3临时会话 */
                    version INTEGER,
                    addTime BIGINT,/*是否静音 1表示静音，0表示没有静音*/
                    UNIQUE(userId, friendId)
                );";

        $this->db->exec($sql);
    }

    private function _checkSiteSessionTable()
    {
        $sql = "create table IF NOT EXISTS siteSession(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sessionId VARCHAR(100) UNIQUE NOT NULL,
                userId VARCHAR(100) NOT NULL,
                deviceId VARCHAR(100) NOT NULL,
                devicePubkPem TEXT, -- DEVICE PUBK PEM
                clientSideType INTEGER,     -- 0:手机客户端  1:web客户端
                timeWhenCreated BIGINT,/*创建时间*/
                ipWhenCreated VARCHAR(100),/*创建时候的ip*/
                timeActive BIGINT,/*最后活跃时间*/
                ipActive VARCHAR(100),/*活跃时间的IP*/
                userAgent VARCHAR(100),
                userAgentType INTEGER,
                gatewayURL VARCHAR(100),
                gatewaySocketId VARCHAR(100),
                UNIQUE(sessionId,userId)
            );";
        $this->db->exec($sql);

        $indexSql = "CREATE INDEX IF NOT EXISTS indexSiteSessionUserId ON siteSession('userId');";
        $this->db->exec($indexSql);
    }

    private function _checkSiteGroupTable()
    {
        $sql = "
                CREATE TABLE  IF NOT EXISTS siteGroup (
               id INTEGER PRIMARY KEY AUTOINCREMENT,
               groupId VARCHAR(100) NOT NULL,/*6到16位*/
               `name` VARCHAR(100) NOT NULL,/*群名*/
               nameInLatin VARCHAR(100) NOT NULL,
               owner VARCHAR(100) NOT NULL,
               avatar VARCHAR(256),/*群头像*/
               description TEXT,/*群描述*/
               descriptionType INTEGER default 0,/*descrption type， 0 text, 1 md*/
               permissionJoin INTEGER,/*加入方式*/
               canGuestReadMessage BOOLEAN,/*游客是否允许读群消息*/
               maxMembers INTEGER,/*群最大成员数*/
               speakers TEXT, /*发言人*/
                status INTEGER default 1,/*表示群的状态， 1表示正常*/
                isWidget INTEGER default 0, /*表示1是挂件，0不是挂件*/
               timeCreate BIGINT,
               UNIQUE(groupId)
        );";
        $this->db->exec($sql);
    }

    private function _checkSiteGroupUserTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteGroupUser(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
               groupId VARCHAR(100) NOT NULL,
               userId VARCHAR(100) NOT NULL,
               memberType INTEGER,
               isMute BOOLEAN default 0 ,/*是否静音 1表示静音，0表示没有静音*/
               timeJoin BIGINT,
               UNIQUE(groupId, userId)
        );";
        $this->db->exec($sql);
    }

    private function _checkSiteU2MessageTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteU2Message(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            msgId VARCHAR(100) UNIQUE NOT NULL, 
            userId VARCHAR(100) NOT NULL, 
            fromUserId VARCHAR(100),
            toUserId VARCHAR(100) NOT NULL,
            roomType INTEGER,
            msgType INTEGER, 
            content TEXT,   -- 可能是一个json，可能是一个proto toString
            msgTime BIGINT
            );";
        $this->db->exec($sql);

        $indexSql = "CREATE INDEX IF NOT EXISTS indexSiteU2MessageUserId ON siteU2Message(userId);";
        $this->db->exec($indexSql);
    }

    private function _checkSiteU2MessagePointerTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteU2MessagePointer(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            userId VARCHAR(100) NOT NULL,
            deviceId VARCHAR(100),
            clientSideType INTEGER,     -- 0:无效，1:手机客户端  2:web客户端
            pointer INTEGER      
            );";
        $this->db->exec($sql);

        $indexSql = "CREATE UNIQUE INDEX IF NOT EXISTS indexSiteU2MessagePointerUd ON siteU2MessagePointer(userId,deviceId);";
        $this->db->exec($indexSql);
    }

    private function _checkSiteGroupMessageTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteGroupMessage(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            msgId VARCHAR(100) UNIQUE NOT NULL,
            groupId VARCHAR(100) NOT NULL,
            fromUserId VARCHAR(100),
            msgType INTEGER,
            content TEXT,
            msgTime BIGINT
            );";
        $this->db->exec($sql);

        $indexSql = "CREATE INDEX IF NOT EXISTS indexSiteGroupMessageGroupId ON siteGroupMessage(groupId);";
        $this->db->exec($indexSql);
    }

    private function _checkPassportPasswordTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS passportPassword(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                userId VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password VARCHAR(100) NOT NULL,
                nickname VARCHAR(100) NOT NULL,
                loginName VARCHAR(100) NOT NULL,
                timeReg BIGINT,
                unique(userId),
                unique(email),
                unique(loginName)
            );";
        $this->db->exec($sql);
    }

    private function _checkPassportPasswordPreSessionTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS passportPasswordPreSession(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                userId VARCHAR(100) NOT NULL,
                preSessionId VARCHAR(100) NOT NULL,
                sitePubkPem TEXT,
                unique(userId)
            );";
        $this->db->exec($sql);
    }

    private  function _checkPassportPasswordTokenTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS passportPasswordToken(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                loginName VARCHAR(100) NOT NULL,
                token VARCHAR(100) NOT NULL,
                timeReg BIGINT,
                UNIQUE(loginName)
            );";
        $this->db->exec($sql);
    }



    private function _checkSiteGroupMessagePointerTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS siteGroupMessagePointer(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            groupId VARCHAR(100) NOT NULL,
            userId VARCHAR(100) NOT NULL,
            deviceId VARCHAR(100),
            clientSideType INTEGER, -- 0:无效，1:手机客户端  2:web客户端
            pointer INTEGER
            );";
        $this->db->exec($sql);

        $indexSql = "CREATE INDEX IF NOT EXISTS indexSiteGroupMessagePointerGud ON siteGroupMessagePointer(groupId,userId,deviceId);";
        $this->db->exec($indexSql);
    }

    private function _insertSiteConfig($loginPluginId, $siteName, $apiProtocol, $imProtocol)
    {
        $siteConfig = SiteConfig::$siteConfig;

        $siteConfig[SiteConfig::SITE_NAME] = $siteName;

        $siteConfig[SiteConfig::SITE_ADDRESS_FOR_API] = $apiProtocol;

        $siteConfig[SiteConfig::SITE_ADDRESS_FOR_IM] = $imProtocol;

        $siteConfig[SiteConfig::SITE_LOGIN_PLUGIN_ID] = $loginPluginId;

        $pubkAndPrikBase64 = SiteConfig::getPubkAndPrikPem();
        $siteConfig = array_merge($siteConfig, $pubkAndPrikBase64);

        $this->Wpf_Logger->writeSqlLog(__CLASS__ . "-" . __FUNCTION__, json_encode($siteConfig));

        $sqlStr = "";
        foreach ($siteConfig as $configKey => $configVal) {
            $sqlStr .= "('$configKey','$configVal'),";
        }
        $sqlStr = trim($sqlStr, ",");
        $sql = "insert into 
                        siteConfig(configKey, configValue) 
                    values 
                        $sqlStr;";
        $prepare = $this->db->prepare($sql);
        $prepare->execute();
    }

    private function _insertSiteLoginPlugin($pluginId)
    {
        $this->Wpf_Logger->info("------ add login plugin -----", "pluginId=" . $pluginId);

        $sql = 'insert into
                    sitePlugin(pluginId, name, logo, `order`, landingPageUrl, usageType,loadingType,permissionType,authKey)
                values
                    (100,
                    "登录注册页面",
                    "", 
                    100,
                    "http://open.akaxin.com:5208/index.php?action=page.login",
                     ' . Zaly\Proto\Core\PluginUsageType::PluginUsageLogin . ', 
                     ' . Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage . ', 
                     ' . Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll . ',
                     "");
                ';
        $this->db->exec($sql);

        $sql = 'insert into
                    sitePlugin(pluginId, name, logo, `order`, landingPageUrl, usageType,loadingType,permissionType,authKey)
                values
                    (' . $pluginId . ',
                    "密码账号注册页面",
                    "", 
                    105,
                    "./index.php?action=page.loginSite",
                     ' . Zaly\Proto\Core\PluginUsageType::PluginUsageLogin . ', 
                     ' . Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage . ', 
                     ' . Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll . ',
                     "");
                ';
        $this->db->exec($sql);
    }

    private function _insertSiteManagerPlugin($host, $port)
    {
        $pluginAddress = "http://" . $host . ":" . $port . "/index.php?action=manage.index";
        $sql = 'insert into
                    sitePlugin(pluginId, name, logo, `order`, landingPageUrl, usageType,loadingType,permissionType,authKey)
                values
                    (101,
                    "manager",
                    "", 
                    1,
                    "' . $pluginAddress . '",
                     ' . Zaly\Proto\Core\PluginUsageType::PluginUsageIndex . ', 
                     ' . Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage . ', 
                     ' . Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll . ',
                     "");
                ';
        $this->Wpf_Logger->info("add site manager plugin", "sql=" . $sql);
        $prepare = $this->db->prepare($sql);

        $this->Wpf_Logger->info("add site manager plugin", "errCode=" . json_encode($prepare->errorCode()));
        $this->Wpf_Logger->info("add site manager plugin", "errInfo=" . json_encode($prepare->errorInfo()));

        $prepare->execute();
    }

    private function _insertSiteUserSquarePlugin($host, $port)
    {
        $pluginAddress = "http://" . $host . ":" . $port . "/index.php?action=manage.index";
        $sql = 'insert into
                    sitePlugin(pluginId, name, logo, `order`, landingPageUrl, usageType,loadingType,permissionType,authKey)
                values
                    (102,
                    "square",
                    "", 
                    2,
                    "' . $pluginAddress . '",
                    ' . Zaly\Proto\Core\PluginUsageType::PluginUsageIndex . ', 
                    ' . Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage . ', 
                    ' . Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll . ',
                     "");
                ';

        $this->Wpf_Logger->info("add user square plugin", "sql=" . $sql);

        $prepare = $this->db->prepare($sql);
        $prepare->execute();
    }

    private function createDBLockFile()
    {
        $file = fopen("./db.lock", "r+");
        fwrite($file, "dbVersion = $this->dbVersion");
        fclose($file);
    }

    private function getDBVersion()
    {
        $file = fopen("./db.lock", "a+");
        $fileStr = fread($file, 16);
        $dbVersion = 0;
        if ($fileStr) {
            $fileArr = explode("=", $fileStr);
            $dbVersion = array_pop($fileArr);
        }
        fclose($file);
        return $dbVersion;
    }

}