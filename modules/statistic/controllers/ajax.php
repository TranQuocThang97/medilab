<?php
if (!defined('IN_ims')) {
    die('Access denied');
}
$_SESSION["statistic_detail"] = (isset($_SESSION["statistic_detail"])) ? $_SESSION["statistic_detail"] : array();
$_SESSION["statistic_session"] = (isset($_SESSION["statistic_session"])) ? $_SESSION["statistic_session"] : md5(time());
$nts = new sMain();
class sMain {
    var $modules = "statistic";
    var $action = "ajax";
    var $time_use_session = 4;
    var $islive = true;
    var $livetime = 2000;
    var $use_sonline = true;
    var $use_syesterday = false;
    var $use_sday = true;
    var $use_sweeklast = false;
    var $use_sweek = true;
    var $use_smonthlast = false;
    var $use_smonth = true;
    var $use_syearlast = false;
    var $use_syear = false;
    var $use_stotal = true;

    function __construct() {
        global $ims;
        $ims->func->load_language($this->modules);
        //include ($this->modules."_func.php");
        $fun = (isset($ims->input['f'])) ? $ims->input['f'] : '';
        switch ($fun) {
            case "statistic":
                echo $this->do_statistic();
                exit;
                break;
            default:
                echo '';
                exit;
                break;
        }
        flush();
        exit;
    }
    //----- get_pic_statistic
    function get_pic_statistic($domain) {
        global $ims;
        $time = time();
        $time_use_session = $this->time_use_session;
        $go_sql = 0;
        $session_code = "statistic_out_" . md5($domain);
        $_SESSION[$session_code] = (isset($_SESSION[$session_code]) && is_array($_SESSION[$session_code])) ? $_SESSION[$session_code] : array();

        if (is_array($_SESSION[$session_code]) && isset($_SESSION[$session_code]["date_update"])) {
            if (($time - $_SESSION[$session_code]["date_update"]) >= $time_use_session) {
                $go_sql = 1;
            }
        } else {
            $go_sql = 1;
        }
        $output = '';
        //$go_sql = 1;

        if ($this->use_stotal == true) {
            if ($go_sql == 1) {
                $sql_total = "select sum(1) as s_number from statistic ";
                $result_total = $ims->db->query($sql_total);
                if ($row_total = $ims->db->fetch_row($result_total)) {
                    $_SESSION[$session_code]['total'] = $row_total['s_number'] + $ims->conf['visitors_start'];
                }
            }
            $_SESSION[$session_code]['total'] = ($_SESSION[$session_code]['total']) ? $_SESSION[$session_code]['total'] : 1;
            $output.= "imsStatistic.aVal.stotal = " . $_SESSION[$session_code]["total"] . ";";
        }
        if ($this->use_sonline == true) {
            if ($go_sql == 1) {
                $sql_online = "select sum(1) as s_number from statistic where date_update>" . ($time - $time_use_session - floor($time_use_session / 2)) . " ";
                //echo $sql_online;
                $result_online = $ims->db->query($sql_online);
                if ($row_online = $ims->db->fetch_row($result_online)) {
                    $_SESSION[$session_code]["online"] = $row_online["s_number"];
                }
            }
            $_SESSION[$session_code]['online'] = ($_SESSION[$session_code]['online']) ? $_SESSION[$session_code]['online'] : 1;
            $output.= "imsStatistic.aVal.sonline = " . $_SESSION[$session_code]["online"] . ";";
        }
        if ($this->use_syesterday == true) {
            if ($go_sql == 1) {
                $tmpdate_log = date('d/m/Y', time() - 24 * 3600);
                $sql_total_syesterday = "select sum(1) as s_number from statistic where date_log='" . $tmpdate_log . "' ";
                $result_total_syesterday = $ims->db->query($sql_total_syesterday);
                if ($row_total_syesterday = $ims->db->fetch_row($result_total_syesterday)) {
                    $_SESSION[$session_code]["total_syesterday"] = $row_total_syesterday["s_number"];
                }
            }
            $_SESSION[$session_code]["total_syesterday"] = ($_SESSION[$session_code]["total_syesterday"]) ? $_SESSION[$session_code]["total_syesterday"] : 0;
            $output.= "imsStatistic.aVal.syesterday = " . $_SESSION[$session_code]["total_syesterday"] . ";";
        }
        if ($this->use_sday == true) {
            if ($go_sql == 1) {
                $sql_total_day = "select sum(1) as s_number from statistic where date_log='" . date('d/m/Y') . "' ";
                $result_total_day = $ims->db->query($sql_total_day);
                if ($row_total_day = $ims->db->fetch_row($result_total_day)) {
                    $_SESSION[$session_code]["total_day"] = $row_total_day["s_number"];
                }
            }
            $_SESSION[$session_code]["total_day"] = ($_SESSION[$session_code]["total_day"]) ? $_SESSION[$session_code]["total_day"] : 1;
            $output.= "imsStatistic.aVal.sday = " . $_SESSION[$session_code]["total_day"] . ";";
        }
        if ($this->use_sweeklast == true) {
            if ($go_sql == 1) {
                $tmpe = time() - (date('w') + 1) * 24 * 3600;
                $tmpe = mktime(23, 59, 59, date('m', $tmpe), date('d', $tmpe), date('Y', $tmpe));
                $tmps = $tmpe - 7 * 24 * 3600 + 1;
                $sql_total_sweeklast = "select sum(1) as s_number from statistic where date_time>='" . $tmps . "' and date_time<='" . $tmpe . "' ";
                $result_total_sweeklast = $ims->db->query($sql_total_sweeklast);
                if ($row_total_sweeklast = $ims->db->fetch_row($result_total_sweeklast)) {
                    $_SESSION[$session_code]["total_sweeklast"] = $row_total_sweeklast["s_number"];
                }
            }
            $_SESSION[$session_code]["total_sweeklast"] = ($_SESSION[$session_code]["total_sweeklast"]) ? $_SESSION[$session_code]["total_sweeklast"] : 0;
            $output.= "imsStatistic.aVal.sweeklast = " . $_SESSION[$session_code]["total_sweeklast"] . ";";
        }
        if ($this->use_sweek == true) {
            if ($go_sql == 1) {
                $tmpe = time() + (7 - 1 - date('w')) * 24 * 3600;
                $tmpe = mktime(23, 59, 59, date('m', $tmpe), date('d', $tmpe), date('Y', $tmpe));
                $tmps = $tmpe - 7 * 24 * 3600 + 1;
                $sql_total_sweek = "select sum(1) as s_number from statistic where date_time>='" . $tmps . "' and date_time<='" . $tmpe . "' ";
                $result_total_sweek = $ims->db->query($sql_total_sweek);
                if ($row_total_sweek = $ims->db->fetch_row($result_total_sweek)) {
                    $_SESSION[$session_code]["total_sweek"] = $row_total_sweek["s_number"];
                }
            }
            $_SESSION[$session_code]["total_sweek"] = ($_SESSION[$session_code]["total_sweek"]) ? $_SESSION[$session_code]["total_sweek"] : 0;
            $output.= "imsStatistic.aVal.sweek = " . $_SESSION[$session_code]["total_sweek"] . ";";
        }
        if ($this->use_smonthlast == true) {
            if ($go_sql == 1) {
                $tmpe = mktime(0, 0, 0, date('m'), 1, date('Y')) - 1;
                $tmps = $tmpe - (date('t') + 1) * 24 * 3600 + 1;
                $sql_total_smonthlast = "select sum(1) as s_number from statistic where date_time>='" . $tmps . "' and date_time<='" . $tmpe . "' ";
                $result_total_smonthlast = $ims->db->query($sql_total_smonthlast);
                if ($row_total_smonthlast = $ims->db->fetch_row($result_total_smonthlast)) {
                    $_SESSION[$session_code]["total_smonthlast"] = $row_total_smonthlast["s_number"];
                }
            }
            $_SESSION[$session_code]["total_smonthlast"] = ($_SESSION[$session_code]["total_smonthlast"]) ? $_SESSION[$session_code]["total_smonthlast"] : 0;
            $output.= "imsStatistic.aVal.smonthlast = " . $_SESSION[$session_code]["total_smonthlast"] . ";";
        }
        if ($this->use_smonth == true) {
            if ($go_sql == 1) {
                $tmps = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $tmpe = $tmps + (date('t') + 1) * 24 * 3600 - 1;
                $sql_total_smonth = "select sum(1) as s_number from statistic where date_time>='" . $tmps . "' and date_time<='" . $tmpe . "' ";
                $result_total_smonth = $ims->db->query($sql_total_smonth);
                if ($row_total_smonth = $ims->db->fetch_row($result_total_smonth)) {
                    $_SESSION[$session_code]["total_smonth"] = $row_total_smonth["s_number"];
                }
            }
            $_SESSION[$session_code]["total_smonth"] = ($_SESSION[$session_code]["total_smonth"]) ? $_SESSION[$session_code]["total_smonth"] : 0;
            $output.= "imsStatistic.aVal.smonth = " . $_SESSION[$session_code]["total_smonth"] . ";";
        }
        if ($this->use_syearlast == true) {
            if ($go_sql == 1) {
                $tmps = mktime(0, 0, 0, 1, 1, (date('Y') - 1));
                $tmpe = mktime(0, 0, 0, 1, 1, date('Y')) - 1;
                $sql_total_syearlast = "select sum(1) as s_number from statistic where date_time>='" . $tmps . "' and date_time<='" . $tmpe . "' ";
                $result_total_syearlast = $ims->db->query($sql_total_syearlast);
                if ($row_total_syearlast = $ims->db->fetch_row($result_total_syearlast)) {
                    $_SESSION[$session_code]["total_syearlast"] = $row_total_syearlast["s_number"];
                }
            }
            $_SESSION[$session_code]["total_syearlast"] = ($_SESSION[$session_code]["total_syearlast"]) ? $_SESSION[$session_code]["total_syearlast"] : 0;
            $output.= "imsStatistic.aVal.syearlast = " . $_SESSION[$session_code]["total_syearlast"] . ";";
        }
        if ($this->use_syear == true) {
            if ($go_sql == 1) {
                $tmps = mktime(0, 0, 0, 1, 1, date('Y'));
                $tmpe = mktime(0, 0, 0, 1, 1, (date('Y') + 1)) - 1;
                $sql_total_syear = "select sum(1) as s_number from statistic where date_time>='" . $tmps . "' and date_time<='" . $tmpe . "' ";
                $result_total_syear = $ims->db->query($sql_total_syear);
                if ($row_total_syear = $ims->db->fetch_row($result_total_syear)) {
                    $_SESSION[$session_code]["total_syear"] = $row_total_syear["s_number"];
                }
            }
            $_SESSION[$session_code]["total_syear"] = ($_SESSION[$session_code]["total_syear"]) ? $_SESSION[$session_code]["total_syear"] : 0;
            $output.= "imsStatistic.aVal.syear = " . $_SESSION[$session_code]["total_syear"] . ";";
        }
        if ($go_sql == 1) {
            $_SESSION[$session_code]["date_update"] = $time;
        }
        if ($this->islive === true) {
            $output.= "setTimeout(function(){ imsStatistic.do_statistic(); }," . $this->livetime . ");";
        }
        return $output;
    }
    //----- do_statistic
    function do_statistic() {
        global $ims;
        $time = time();
        $time_use_session = $this->time_use_session;
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = $ims->func->getBrowser($agent);
        $os = $ims->func->getOs($agent);
        $web_link = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
        //$domain = $_SERVER['SERVER_NAME'];
        $domain = $ims->func->getHost_URL($web_link);
        $date = date("d/m/Y", $time);
        if (!$web_link) {
            die("none");
        }
        $screen_width = isset($ims->get["screen_width"]) ? $ims->get["screen_width"] : '';
        $screen_height = isset($ims->get["screen_height"]) ? $ims->get["screen_height"] : '';
//        $referrer_link = isset($ims->get["referrer_link"]) ? $ims->get["referrer_link"] : '';
        $referrer_link = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
        $referrer_domain = $ims->func->getHost_URL($referrer_link);
//        $md5_web_link = md5($web_link);
        $md5_web_link = md5($referrer_link);
        $go_insert = 0;

        if (isset($_SESSION["statistic_detail"][$md5_web_link]) && is_array($_SESSION["statistic_detail"][$md5_web_link])) {
            if (($time - $_SESSION["statistic_detail"][$md5_web_link]["date_update"]) > ($time_use_session + floor($time_use_session / 2))) {
                $go_insert = 1;
            }
        } else {
            $go_insert = 1;
        }
        if ($go_insert == 1) {
            //Clear Old statistic
            $session_cleared = md5($ims->conf['rooturl']) . '_statistic_cleared';
            if (!isset($_SESSION[$session_cleared])) {
                $_SESSION[$session_cleared] = 1; //Only one
                $sql_clear = "select date_time from statistic order by date_time asc limit 0,1 ";
                //echo $sql_online;
                $result_clear = $ims->db->query($sql_clear);
                if ($row_clear = $ims->db->fetch_row($result_clear)) {
                    $time_clear = time() - 30 * 24 * 3600;
                    if ($row_clear['date_time'] < $time_clear) {
                        $ims->db->query("DELETE FROM statistic WHERE date_time<'" . $time_clear . "' ");
                        $num_clear = $ims->db->affected();
                        $ims->conf['visitors_start'] += $num_clear;
                        $ims->db->query("UPDATE sysoptions SET option_value='" . $ims->conf['visitors_start'] . "' WHERE option_key ='visitors_start' ");
                    }
                }
            }
            //End clear
            $cot_d = array();
//            $cot_d['session'] = $_SESSION["statistic_session"];
            $cot_d['session'] = $md5_web_link;
            $cot_d['date_log'] = $date;
            $cot_d['domain'] = $domain;
            $cot_d['web_link'] = $web_link;
            $cot_d['referrer_domain'] = $referrer_domain;
            $cot_d['referrer_link'] = $referrer_link;
            $cot_d['agent'] = $agent;
            $cot_d['browser'] = $browser;
            $cot_d['ip'] = $ip;
            $cot_d['os'] = $os;
            $cot_d['screen_width'] = $screen_width;
            $cot_d['screen_height'] = $screen_height;
            $cot_d['date_time'] = $time;
            $cot_d['date_update'] = $time;
            $cot_d['time_stay'] = 0;
            $ok = $ims->db->do_insert("statistic", $cot_d);
            if ($ok) {
                $insertid = $ims->db->insertid();
                $_SESSION["statistic_detail"][$md5_web_link] = array('id' => $insertid, 'domain' => $domain, 'web_link' => $web_link, 'date_time' => $time, 'date_update' => $time);
            }
        } else {
            $cot_d = array();
            $cot_d['time_stay'] = $time - (int)$_SESSION["statistic_detail"][$md5_web_link]["date_time"];
            $cot_d['date_update'] = $time;
            $ok = $ims->db->do_update("statistic", $cot_d, "id=" . $_SESSION["statistic_detail"][$md5_web_link]["id"]);
            if ($ok) {
                $_SESSION["statistic_detail"][$md5_web_link]["date_update"] = $time;
            }
        }

        header("Content-Type: text/javascript");
        return $this->get_pic_statistic($domain);
    }
    // end class

}
?>