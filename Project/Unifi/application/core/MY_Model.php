<?php

class MY_Model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加数据
     * @param array $data
     * @param string $table
     * @return int
     */
    function add($data, $table)
    {
        $res = $this->db->insert($table, $data);
        $nid = is_numeric($this->db->insert_id()) ? $this->db->insert_id() : 0;
        if ($res && $nid) return $nid;
        else return $res;
    }

    /**
     * 更改数据
     * @param $data
     * @param $where
     * @param $table
     * @return mixed
     */
    function update($data, $where, $table)
    {
        return $this->db->where($where)->update($table, $data);
    }
    
    /**
     * @param $where
     * @param $table
     * @return int
     */
    function count_num($where, $table)
    {
        $this->db->where($where);
        $num = $this->db->count_all_results($table);

        $num || $num = 0;
        return $num;
    }
}