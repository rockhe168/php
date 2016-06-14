<?php

/**
 * Created by PhpStorm.
 * User: xhhe
 * Date: 2016/6/1
 * Time: 16:55
 */
class Coupon_Model extends MY_Model
{
    /**
     * 表名
     */
    const TableName = "activiy_signincoupon";

    /*
      * 数据库字段
      */
    const id='id',mobile='mobile',createTime='createTime',lastUpdateTime='lastUpdateTime';

    static function getInstance()
    {
        $ci = get_instance();
        $var = __CLASS__;
        return $ci->$var;
    }

    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
    }

    public function  add($data)
    {
        $res = $this->db->insert(self::TableName, $data);
        $nid = is_numeric($this->db->insert_id()) ? $this->db->insert_id() : 0;
        if ($res && $nid) return $nid;
        else return $res;
    }

    function update($data, $where)
    {
        return $this->db->where($where)->update(self::TableName,$data);
    }

    function isExistsMobile($mobile)
    {
        $data = $this->db->where(self::mobile,$mobile)->get(self::TableName)->row_array();
        if(empty($data))
        {
            return false;
        }else {
            return true;
        }
    }
}