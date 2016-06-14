<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/27 0027
 * Time: 23:57
 */
class UserInfo_Model extends MY_Model
{
    /**
     * 表名
     */
    const TableName = "activity_UserInfo";


    /*
     * 数据库字段
     */
    const token='token',activityId='activityId',mobile='mobile',userName='userName',icon='icon',activityName='activityName',gzhNo='gzhNo',recommendToken='recommendToken',createTime='createTime',lastUpdateTime='lastUpdateTime';



    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
    }

    /**
     * @return UserInfo_Model
     */
    static function getInstance()
    {
        $ci = get_instance();
        $var = __CLASS__;
        return $ci->$var;
    }

    public function getUserInfo($toke)
    {
        //return $this->db->get_where(self::Table,array(self::token => $toke));
        return $this->db->where(self::token,$toke)->get(self::TableName)->row_array();
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