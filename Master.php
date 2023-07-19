<?php 
if(session_id() ==="")
session_start();
require_once('DBConnection.php');
/**
 * Login Registration Class
 */
Class Master extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function save_patient(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowed_field = ['fullname', 'contact', 'address', 'age', 'weight', 'bp_rate', 'status'];
        $from = date("Y-m-d"). " 00:00:00";
        $to = date("Y-m-d"). " 23:59:59";
        $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d h:i:s");
        $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d h:i:s");
        $allowedToken = $_SESSION['formToken']['patient-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $cols=[];
            $vals = [];
            foreach( $_POST as $k => $v ){
                if(in_array($k, $allowed_field)){
                    $cols[] = $k;
                    $vals[] = $v;
                }
            }
            if(empty($patient_id)){
                $queue_no = 0;
                $get_queue = $this->querySingle("SELECT `queue_no` FROM `patient_list` where `date_created` BETWEEN '{$from}' and '{$to}' order by strftime('%s', `date_created`) desc limit 1 ");
                if($get_queue){
                    $queue_no = $get_queue;
                }
                $queue_no = intval($queue_no) + 1;
                $queue_no = sprintf("%'.04d", $queue_no);
                
                $cols[] = 'queue_no';
                $vals[] = $queue_no;
                $cols = "`". implode("`, `", $cols) . "`";
                $vals = "'". implode("', '", $vals) . "'";
                $sql = "INSERT INTO `patient_list` ({$cols}) VALUES ({$vals})";
            }else{
                $data = '';
                foreach($cols as $k => $v){
                    $data = " `{$v}` = '{$vals[$k]}' ";
                }
                $sql = "UPDATE `patient_list` set {$data} where `patient_id` = '{$patient_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($patient_id)){
                    if(isset($encoded_by) && $encoded_by == 'patient'){
                        $id = $this->lastInsertRowID();
                        $queue_no = $this->querySingle("SELECT `queue_no` FROM `patient_list` where `patient_id` = '{$id}'");
                        $resp['queue'] = $queue_no;
                        $resp['msg'] = "You are now in queue. Your Queue number is <b>{$queue_no}</b>.";
                    }else{
                        $resp['msg'] = 'New Patient has been addedd successfully';
                    }
                }else{
                    $resp['msg'] = 'Patient Details has been updated successfully';
                }
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function delete_patient(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['patients'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `patient_list` where `patient_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The patient data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function get_current_queueu(){
        extract($_POST);
        $from = date("Y-m-d"). " 00:00:00";
        $to = date("Y-m-d"). " 23:59:59";
        $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d h:i:s");
        $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d h:i:s");
        $allowedToken = $_SESSION['formToken']['patients_queueu'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $get = $this->query("SELECT * FROM `patient_list` where `date_created` BETWEEN '{$from}' and '{$to}' and `status` = 0 order by strftime('%s', `date_created`) asc limit 1 ");
            $data = $get->fetchArray(SQLITE3_ASSOC);
            if($data){
                $resp['status'] = 'success';
                $resp['data'] = $data;
                $this->query("UPDATE `patient_list` set `notify` = 0 where `patient_id` = '{$data['patient_id']}'");
            }
        }
        return json_encode($resp);
        
    }
    function notify_queue(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['patients'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $update =  $this->query("UPDATE `patient_list` set `notify` = 1 where `patient_id` = '{$patient_id}'");
            if($update){
                $resp['status'] = 'success';
                $resp['msg'] = "Patient will be notify again at the queueing monitor.";
                $_SESSION['message']['success'] = $resp['msg'];
            }
        }
        return json_encode($resp);
    }
    function today_patients(){
        $from = date("Y-m-d", strtotime(date("Y-m-d"))) . " 00:00:00";
        $to =  date("Y-m-d", strtotime(date("Y-m-d"))) . " 23:59:59";
        $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d");
        $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d");

        $total = $this->querySingle("SELECT COUNT(`patient_id`) FROM `patient_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}'");
        return number_format($total);
    }
    function today_patients_pending(){
        $from = date("Y-m-d", strtotime(date("Y-m-d"))) . " 00:00:00";
        $to =  date("Y-m-d", strtotime(date("Y-m-d"))) . " 23:59:59";
        $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d");
        $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d");

        $total = $this->querySingle("SELECT COUNT(`patient_id`) FROM `patient_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}' and `status` = 0");
        return number_format($total);
    }
    function today_patients_done(){
        $from = date("Y-m-d", strtotime(date("Y-m-d"))) . " 00:00:00";
        $to =  date("Y-m-d", strtotime(date("Y-m-d"))) . " 23:59:59";
        $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
        $from->setTimezone(new DateTimeZone('UTC'));
        $from = $from->format("Y-m-d");
        $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
        $to->setTimezone(new DateTimeZone('UTC'));
        $to = $to->format("Y-m-d");

        $total = $this->querySingle("SELECT COUNT(`patient_id`) FROM `patient_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}' and  `status` = 1");
        return number_format($total);
    }

}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$master = new Master();
switch($a){
    case 'save_settings':
        echo $master->save_settings();
    break;
    case 'save_patient':
        echo $master->save_patient();
    break;
    case 'delete_patient':
        echo $master->delete_patient();
    break;
    case 'get_current_queueu':
        echo $master->get_current_queueu();
    break;
    case 'notify_queue':
        echo $master->notify_queue();
    break;
    default:
    // default action here
    break;
}