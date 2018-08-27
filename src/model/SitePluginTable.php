<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 11:24 AM
 */

class SitePluginTable extends BaseTable
{
    private $tableName = "sitePlugin";
    private $columns = [
        "id",
        "pluginId",
        "name",
        "logo",
        "`order`",
        "landingPageUrl",
        "landingPageWithProxy",
        "usageType",
        "loadingType",
        "permissionType",
        "authKey",
        "addTime"
    ];

    public function init()
    {
        $this->columns = implode(",", $this->columns);
    }

    /**
     * get plugin by pluginId(not pk id)
     *
     * @param $pluginId
     * @return array|mixed
     */
    public function getPluginById($pluginId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {
            $startTime = microtime(true);
            $sql = "select $this->columns from $this->tableName where pluginId=:pluginId;";
            $prepare = $this->db->prepare($sql);
            $prepare->bindValue(":pluginId", $pluginId);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $results = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $pluginId, $startTime);
            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex->getMessage());
            return [];
        }
    }

    /**
     * get plugin list by usageType
     *
     * @param $usageType
     * @return array
     */
    public function getPluginList($usageType)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        try {

            if ($usageType === Zaly\Proto\Core\PluginUsageType::PluginUsageNone) {
                $sql = "select $this->columns from $this->tableName  where 1!=:usageType order by `order` ASC, id DESC";
            } else {
                $sql = "select $this->columns from $this->tableName where usageType=:usageType order by  `order` ASC, id DESC";
            }

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":usageType", $usageType);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $usageType, $startTime);

            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex->getMessage());
            return [];
        }

    }

}