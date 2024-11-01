<?php
if (!class_exists('UpiCRMLeadsChangesLog')) {
    class UpiCRMLeadsChangesLog extends WP_Widget {

        var $wpdb;

        public function __construct() {
            global $wpdb;
            $this->wpdb = &$wpdb;
        }

        function add($lead) {
            if (get_option('upicrm_enable_audit_log')) {
                $lead = (object)$lead;
                //print_r($lead);
                $add = [
                    'lead_status_id' => isset($lead->lead_status_id) ? $lead->lead_status_id : 0,
                    'user_id' => isset($lead->user_id) ? $lead->user_id : 0,
                    'lead_id' => $lead->lead_id,
                    'maker_user_id' => get_current_user_id(),
                    'maker_lead_route_id' => '',
                    'lead_management_comment' => $lead->lead_management_comment,
                    'lead_change_log_edit_text' => isset($lead->lead_change_log_edit_text) ? json_encode($lead->lead_change_log_edit_text) : ''
                ]; 

                $this->wpdb->insert(upicrm_db()."leads_changes_log", $add);

                $this->wpdb->update(upicrm_db()."leads", ['lead_log_text' => $this->get_text($add)] , ["lead_id" => $add['lead_id']]);
            }
        }
        
        function get_text($lead,$full=false) {
            $UpiCRMUsers = new UpiCRMUsers(); 
            $UpiCRMLeadsStatus = new UpiCRMLeadsStatus();
             mysqli_set_charset($dblink, "utf8");    

            
            $lead = (object)$lead;
            if ($lead->lead_change_log_time) {
                $text = $lead->lead_change_log_time;
            } else {
                 $text = date("Y-m-d H:i:s");
            }
            $text.= " - ";
            if ($lead->maker_user_id > 0) {
                $user_name = 'User '.$UpiCRMUsers->get_by_id($lead->maker_user_id);
            }
            if ($lead->lead_change_log_edit_text) {
                return $text.$user_name.' edit lead';
            }
            if ($lead->lead_management_comment) {
                return $text.$user_name.' add management comment: '.$lead->lead_management_comment;
            }
            if ($lead->user_id > 0) {
                return $text.$user_name.' assigned lead to: '.$UpiCRMUsers->get_by_id($lead->user_id);
            }
            if ($lead->lead_status_id > 0) {
                return $text.$user_name.' change lead status to: '.$UpiCRMLeadsStatus->get_status_name_by_id($lead->lead_status_id);
            }
            
            return '';
        }
        
        function get_by_lead_id($lead_id) {
            $rows = $this->wpdb->get_results("SELECT * FROM ".upicrm_db()."leads_changes_log where lead_id = {$lead_id} order by lead_change_log_id DESC");
            return $rows;
        }

    }

}
?>