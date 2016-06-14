<?php
/**
 * Created by PhpStorm.
 * User: cescwang
 * Date: 16/5/16
 * Time: 下午9:11
 */

class CreateModel extends CI_Controller {

    function __construct() {
        parent::__construct ();

    }

    function index() {
        $this->load->database();
        $db = $this->input->get ( 'db' );
        $table = $this->input->get ( 'table' );
        if (! $db) {
            $rs = $this->db->query ( "show databases" )->result_array ();
            foreach ( $rs as $v ) {
                echo "<a href='?db=" . urlencode ( $v ['Database'] ) . "'>{$v['Database']}</a><br>";
            }
            exit ();
        } else if (! $table) {
            $this->db->query ( "use $db" );
            $rs = $this->db->query ( "show tables" )->result_array ();
            foreach ( $rs as $v ) {
                echo "<a href='?db=" . urlencode ( $db ) . "&table=" . urlencode ( $v ['Tables_in_' . $db] ) . "'>{$v['Tables_in_'.$db]}</a><br>";
            }
            exit ();
        } else {
            $this->db->query ( "use $db" );
            // rs = $this->db->field_data ( $table );
            $rs = $this->db->query ( "show FIELDS from $table" )->result_array ();
            $fields = "\r\n	const ";
            foreach ( $rs as $k => $v ) {
                if ($k == 0)
                    $sp = '';
                else
                    $sp = $k % 6 ? "," : ";\r\n	const ";
                $this->concat ( $fields, " F_{$v['Field']} = '{$v['Field']}'", $sp );
            }
            // echo fields("a","b","c","d");exit;
            $model = $table [strlen ( $table ) - 1] == '_' ? "model" : "_model";
            header ( "Content-Type:application/force-download" );
            header ( "Content-Type:application/octet-stream" );
            header ( "Content-Type:application/download" );
            header ( "Content-Disposition:inline;filename=\"{$table}{$model}.php\"" );
            header ( "Content-Transfer-Encoding:binary" );
            header ( "Cache-Control: must-revalidate,post-check=0,pre-check=0" );
            header ( "Pragma: no-cache" );
            echo "<?php\r\nclass {$table}{$model} extends MY_Model {\r\n	/**\r\n	 * 表名\r\n	 */\r\n	const Table = \"{$table}\";\r\n	\r\n	/*\r\n	 * 数据库字段\r\n	 */" . $fields . ";\r\n	\r\n	\r\n	\r\n	function __construct() {\r\n		parent::__construct ();\r\n	}\r\n	/**\r\n	 *\r\n	 * @return {$table}{$model}\r\n	 */\r\n	static function getInstance() {\r\n		\$ci = get_instance ();\r\n		\$var = __CLASS__;\r\n		return \$ci->\$var;\r\n	}\r\n}\r\n\r\n?>";
        }
    }

    function concat(&$str1, $str2, $sp = ',') {
        $str1 .= ($str1 || $str1 === '0') ? $sp . $str2 : $str2;
    }
}