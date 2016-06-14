<?php

/**
 * Created by PhpStorm.
 * User: cescwang
 * Date: 16/5/16
 * Time: 下午11:34
 */
class MY_Controller extends CI_Controller
{
    /**
     * 分页大小
     * @var int
     */
    protected $g_pageSize = 10;

    /**
     * session_key
     * @var string
     */
    protected $g_user_session_key = 'login_user_info';

    /**
     * 用户信息
     * @var array
     */
    protected $g_userInfo = array();

    function __construct()
    {
        parent::__construct();
        /*if(empty($_SESSION)){
           session_start(); 
        }*/
        $this->load->helper('class_autoload');
    }

    //检测是否登录
    protected function checkLogin($role = array())
    {
        if (empty($_SESSION)) {
            session_start();
        }
        //unset($_SESSION);
        if(empty($_SESSION[$this->g_user_session_key]['uid']) || empty($_SESSION[$this->g_user_session_key]['role'])){
            $loginUrl = base_url('index');
            header("Location:{$loginUrl}");
            exit();
            //show_error('跳转到登录页面', 500, 100001);
        }else {
            $loginUserInfo = $_SESSION[$this->g_user_session_key];
            if (!empty($role) && !in_array($loginUserInfo['role'], $role)) {//角色不匹配
                show_error('角色不匹配', 500, 100001);
            }
            return $_SESSION[$this->g_user_session_key];
        }
    }

    //获取登录用户信息
    protected function getLoginUserInfo()
    {
        if (empty($_SESSION)) {
            session_start();
        }
        return empty($_SESSION[$this->g_user_session_key]) ? array() : $_SESSION[$this->g_user_session_key];
    }

    /**
     * 配置分页
     */
    protected function _show_page($base_url, $total_num, $prepage = 10)
    {
        $this->load->library('paginatione');
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_num;
        $config['per_page'] = $prepage;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['ifShowNum'] = FALSE;
        $config['num_links'] = 2;
        $config['prev_link'] = '<span class="prev"></span>';
        $config['next_link'] = '<span class="next"></span>';
        $config['query_string_segment'] = 'page';
        $this->paginatione->initialize($config);
        return $this->paginatione->create_links();
    }

}