<?php
function class_autoload($class_name)
{
    $ci = get_instance();
    $isModel = strpos($class_name, 'Model');
    if ($isModel) {
        $ci->load->model($class_name, $class_name);
    } else {
        $ci->load->library($class_name, null, $class_name);
    }
}

spl_autoload_register('class_autoload');