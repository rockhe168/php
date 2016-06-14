<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/28 0028
 * Time: 8:58
 */
class GzhConfig_Model extends MY_Model
{
    /**
     * 表名
     */
    const TableName = "activity_gzhConfig";

    static function getInstance()
    {
        $ci = get_instance();
        $var = __CLASS__;
        return $ci->$var;
    }

    /*
       * 数据库字段
       */
    const configId='configId',gzhNo='gzhNo',twoDimensionCode='twoDimensionCode',title='title',descinfo='descinfo',recommendGzhNo1='recommendGzhNo1',recommendGzhNo2='recommendGzhNo2',recommendGzhNo3='recommendGzhNo3',createTime='createTime',lastUpdateTime='lastUpdateTime';

    public function __construct()
    {
        parent::__construct();
    }

    public function getGzhConfigByGzhNo($gzhNo)
    {
        return $this->db->where(self::gzhNo,$gzhNo)->get(self::TableName)->row_array();
    }

    public function getAllGzhConfig()
    {
        return $this->db->get(self::TableName)->result_array();
    }


}