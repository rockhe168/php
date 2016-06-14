<?php

/**
 * Created by PhpStorm.
 * User: xhhe
 * Date: 2016/5/31
 * Time: 18:19
 */
class Config_Model extends MY_Model
{
    /**
     * 表名
     */
    const TableName = "activity_config";

    /*
      * 数据库字段
      */
    const configkey='configkey',access_token_expires_in='access_token_expires_in',access_token_cache='access_token_cache',jsapi_ticket_expires_in='jsapi_ticket_expires_in',jsapi_ticket_cache='jsapi_ticket_cache',createTime='createTime',lastUpdateTime='lastUpdateTime';

    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
    }

    static function getInstance()
    {
        $ci = get_instance();
        $var = __CLASS__;
        return $ci->$var;
    }

    public function getWeConfig()
    {
        return $this->db->where(self::configkey,'wxconfig')->get(self::TableName)->row_array();
    }

    function update($data, $where)
    {
        return $this->db->where($where)->update(self::TableName,$data);
    }
}