<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/28 0028
 * Time: 8:57
 */
class UserCode_Model extends MY_Model
{
    /**
     * 表名
     */
    const TableName = "activity_Code";
    /*
        * 数据库字段
        */
    const codeId='codeId',token='token',source='source',code='code',status='status',gzhNo='gzhNo',createTime='createTime',lastUpdateTime='lastUpdateTime';

    function __construct()
    {
        parent::__construct();
    }

    static function getInstance()
    {
        $ci = get_instance();
        $var = __CLASS__;
        return $ci->$var;
    }

    public function getUserCodeList($token)
    {
        //return $this->db->get_where(self::Table,array(self::token => $toke));
        return $this->db->where(self::token,$token)->get(self::TableName)->result_array();
    }


    public function  getUserCodeInfoByGzhNo($token,$gzhNo)
    {
        $data = $this->db->where(self::token,$token)->where(self::gzhNo,$gzhNo)->get(self::TableName)->row_array();
        return $data;
    }

    public function  getUserCodeInfoByFanPai($token)
    {
        $data = $this->db->where(self::token,$token)->where(self::status,2)->where(self::source,4)->get(self::TableName)->row_array();
        return $data;
    }

    public function add($data)
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


    public  function isExistsFriendCode($token)
    {
        $data = $this->db->where(self::token,$token)->where(self::status,2)->get(self::TableName)->row_array();
        if(empty($data))
        {
            return false;
        }else
        {
            return true;
        }
    }

    public  function isExistsSignCode($token)
    {
        $data = $this->db->where(self::token,$token)->where(self::status,1)->get(self::TableName)->row_array();
        if(empty($data))
        {
            return false;
        }else
        {
            return true;
        }
    }

    /**
     * @param $code
     * 是否存在优惠code
     */
    private function isExistsByCode($code)
    {
        $data = $this->db->where(self::code,$code)->get(self::TableName)->row_array();
        if(!empty($data))
        {
            return true;
        }
        return false;
    }

    //生成可用的优惠码
    public function generateCode()
    {
        $result=0;

        while (true)
        {
            $result =rand(300000,330000);
            if(($this->isExistsByCode($result)) === false)
            {
                break;
            }
        }
        return $result;
    }
}