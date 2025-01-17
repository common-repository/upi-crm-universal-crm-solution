<?php

/**
 * Class UpiCRMMails
 */
if (!class_exists('UpiCRMMails')) {

    class UpiCRMMails extends WP_Widget {

        var $wpdb;

        public function __construct() {
            global $wpdb;
            $this->wpdb = &$wpdb;
        }

        function get() {
            //get mails
            $query = "SELECT * FROM " . upicrm_db() . "mails WHERE `mail_event` NOT LIKE 'custom_%'";
            $rows = $this->wpdb->get_results($query);
            return $rows;
        }

        function get_all() {
            //get mails
            $query = "SELECT * FROM " . upicrm_db() . "mails";
            $rows = $this->wpdb->get_results($query);
            return $rows;
        }

        function get_custom() {
            //get mails
            $query = "SELECT * FROM " . upicrm_db() . "mails WHERE `mail_event` LIKE 'custom_%'";
            $rows = $this->wpdb->get_results($query);
            return $rows;
        }

        function add($insertArr) {
            //add mails
            $this->wpdb->insert(upicrm_db() . "mails", $insertArr);
        }

        function update($updateArr, $mail_id) {
            //update lead mail
            $this->wpdb->update(upicrm_db() . "mails", $updateArr, array("mail_id" => $mail_id));
        }

        function remove($mail_id) {
            //delete mail template
            $this->wpdb->delete(upicrm_db() . "mails", array("mail_id" => $mail_id));
        }

        function get_new_id_to_insert() {
            //use this anytime you want to add custom email with: 'custom_' in the start of the string
            $query = "SELECT AUTO_INCREMENT as `county`
        FROM  INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA  = '" . DB_NAME . "'
        AND TABLE_NAME = '" . upicrm_db() . "mails'";
            $rows = $this->wpdb->get_results($query);
            return @$rows[0]->county ? $rows[0]->county : 0;
        }

        function update2($arr) {
            //update mail by arr - key = mail_event

            foreach ($arr as $key => $mail) {
                if ($key != "submit") {
                    foreach ($mail as $key2 => $value) {
                        $updateArr[$key2] = $mail[$key2];
                    }
                    $this->wpdb->update(upicrm_db() . "mails", $updateArr, array("mail_event" => $key));
                }
            }
        }
        
        
        function send($lead_id, $event, $to = "", $cancel_cc = false) {
           // error_log('inside upicrm_mails::send lead_id is : '.$lead_id);
            //send mail
            add_filter('wp_mail_from_name', array($this, 'filter_change_mail_from_name'));

            $UpiCRMUIBuilder = new UpiCRMUIBuilder();
            $UpiCRMFieldsMapping = new UpiCRMFieldsMapping();
            $UpiCRMLeads = new UpiCRMLeads();
            $UpiCRMUsers = new UpiCRMUsers();
            $UpiCRMLeadsStatus = new UpiCRMLeadsStatus();

            $lead = $UpiCRMLeads->get_by_id($lead_id);
            $getNamesMap = $UpiCRMFieldsMapping->get_all_by($lead->source_id, $lead->source_type);
            $list_option = $UpiCRMUIBuilder->get_list_option();
            $mail = $this->get_by_event($event);

            $message = nl2br($mail->mail_content);
            $subject = $mail->mail_subject;
            $default_email = get_option('upicrm_default_email');
            $extra_email = get_option('upicrm_extra_email');

            $LeadVarText = '<table width="100%" border="0" cellpadding="5" cellspacing="2">';
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    $getValue = $UpiCRMUIBuilder->lead_routing($lead, $key, $key2, $getNamesMap, true);
                    if ($getValue != "") {
                        $LeadVarText .= '<tr bgcolor="#E6E6FA"><td><strong>' . $value . '</strong></td></tr>';
                        $LeadVarText .= '<tr bgcolor="#ffffff"><td>&nbsp;&nbsp;&nbsp;' . $getValue;
                        $LeadVarText .= '</td></tr>';
                    }
                }
            }

            $LeadVarText .= '<tr bgcolor="#E6E6FA"><td><strong>Available Actions</strong></td></tr>';
            $LeadVarText .= '<tr><td>&nbsp;&nbsp;&nbsp; ' . __('You can assign this lead to the following UpiCRM users:', 'upicrm');

            $myID = $lead->user_id;
            if (get_the_author_meta('upicrm_user_permission', $lead->user_id) == 2) {
                $get_users = get_users(array('role' => ''));
                foreach ($get_users as $user) {
                    if (get_the_author_meta('upicrm_user_permission', $user->ID) > 0) {
                        $LeadVarText .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $LeadVarText .= '<a style="text-decoration: none;" href="' . get_admin_url() . 'admin.php?page=upicrm_api&action=change_lead_user_id&lead_id=' . $lead_id . '&user_id=' . $user->ID . '"><font color="blue">' . $user->display_name . ' (Link)</font></a>';
                    }
                }
            } else if (get_the_author_meta('upicrm_user_permission', $lead->user_id) == 1) {
                $user_arr = $UpiCRMUsers->get_as_array();
                $save_arr[$myID] = 1;

                $child_users = $UpiCRMUsers->get_childrens_by_parent_id($myID);
                if (is_array($child_users)) {
                    foreach ($child_users as $child_user) {
                        $save_arr[$child_user->user_id] = 1;
                    }
                }

                if (get_user_meta($myID, 'upicrm_user_reassign_manager', 1)) {
                    $parent = $UpiCRMUsers->get_inside_by_user_id($myID);
                    $save_arr[$parent->user_parent_id] = 1;
                }
                foreach (array_diff_key($user_arr, $save_arr) as $key => $value) {
                    unset($user_arr[$key]);
                }
                unset($user_arr[$lead->user_id]);
                foreach ($user_arr as $user_id => $user_name) {
                    $LeadVarText .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    $LeadVarText .= '<a style="text-decoration: none;" href="' . get_admin_url() . 'admin.php?page=upicrm_api&action=change_lead_user_id&lead_id=' . $lead_id . '&user_id=' . $user_id . '"><font color="blue">' . $user_name . ' (Link)</font></a>';
                }
            }

            $LeadVarText .= '</td></tr>';
            $LeadVarText .= '<tr><td><br /><br />&nbsp;&nbsp;&nbsp; ' . __('You can change the status of this lead to the one of the following:', 'upicrm');
            foreach ($UpiCRMLeadsStatus->get() as $LeadStatusObj) {
                $LeadVarText .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style="text-decoration: none;" href="' . get_admin_url() . 'admin.php?page=upicrm_api&action=change_lead_status&lead_id=' . $lead_id . '&lead_status_id=' . $LeadStatusObj->lead_status_id . '"><font color="blue">' . $LeadStatusObj->lead_status_name . ' (Link)</font></a>';
            }
            $LeadVarText .= '</td></tr>';
            $LeadVarText .= '</table>';
            $LeadVarText .= "<br /><br />";
            $LeadVarText .= 'Please manage this lead here: <a href="' . get_admin_url() . 'admin.php?page=upicrm_allitems">' . get_admin_url() . 'admin.php?page=upicrm_allitems</a><br />';
            $LeadVarText .= '<br /><br /><a href="http://www.upicrm.com?utm_source=upi_mail&utm_medium=web&utm_campaign=mail">This mail was sent by UpiCRM - Universal Wordpress CRM Plugin</a><br />';
            $LeadVarTextNoHTML = "";
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    $getValue = $UpiCRMUIBuilder->lead_routing($lead, $key, $key2, $getNamesMap, true);
                    if ($getValue != "") {
                        $fields[$value] = $getValue;
                        $LeadVarTextNoHTML .= "{$value}: {$getValue}" . "\r\n";
                    }
                }
            }
            $message = str_replace("[lead-plaintext]", $LeadVarTextNoHTML, $message);
            $message = str_replace("[lead]", $LeadVarText, $message);
            $message = str_replace("[url]", get_site_url(), $message);
            $message = str_replace("[assigned-to]", $UpiCRMUsers->get_by_id($lead->user_id), $message);
            $message = str_replace("[lead-status]", $UpiCRMLeadsStatus->get_status_name_by_id($lead->lead_status_id), $message);
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    if (isset($fields)) {
                        $message = str_replace("[field-$value]", $fields[$value], $message);
                    }
                }
            }
            $subject = str_replace("[url]", get_site_url(), $subject);
            $subject = str_replace("[assigned-to]", $UpiCRMUsers->get_by_id($lead->user_id), $subject);
            $subject = str_replace("[lead-status]", $UpiCRMLeadsStatus->get_status_name_by_id($lead->lead_status_id), $subject);
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    if (isset($fields)) {
                        $subject = str_replace("[field-$value]", $fields[$value], $subject);
                    }
                }
            }

            $headers = "";
            if (!get_option('upicrm_cancel_email_from')) {
                //$headers .= "From: UpiCRM" . "\r\n";
                $sitename = esc_html(strtolower($_SERVER['SERVER_NAME']));
                if (substr($sitename, 0, 4) == 'www.') {
                    $sitename = substr($sitename, 4);
                }
                $headers .= "From: wordpress@{$sitename}" . "\r\n";
                
             //   'wordpress@' . $sitename
            }

            $headers .= 'MIME-Version: 1.0' . "\r\n";
            if (get_option('upicrm_email_format') == 1)
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            if (get_option('upicrm_email_format') == 2)
                $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
            $cc = "";
            if ($extra_email != "")
                $cc .= $extra_email . " ";
            if ($to != "")
                $cc .= $default_email;
            else {
                $to = get_userdata($lead->user_id)->user_email;
            }
            if ($mail->mail_cc != "")
                $cc .= $mail->mail_cc;

            if ($event == 'change_lead_status' && get_the_author_meta('upicrm_user_manager_status_change_note', $lead->user_id) == 1) {
                $MyUser = $UpiCRMUsers->get_inside_by_user_id($lead->user_id);
                $user_info = get_userdata($MyUser->user_parent_id);
                $cc .= ' ' . $user_info->user_email;
            }
            $cc = str_replace(" ", ",", $cc);

            if (!$cancel_cc) {
                $headers .= "Cc: {$cc}" . "\r\n";
            }

            
           // error_log(print_r('inside upicrm_mails before wp_mail', true));
            
            //error_log('$to : ' . $to . ' , $subject: ' .$subject.' , $message : '.$message.' , $headers : '.print_r($headers, true) );
 
            
            $is_mail_sent = wp_mail($to, $subject, $message, $headers);
           
          //  error_log(print_r('inside upicrm_mails $is_mail_sent : ' . $is_mail_sent, true));
            if (!$is_mail_sent && !get_option('upicrm_cancel_email_failsafe')) {
                //error_log('WP_MAIL not sending, try send from MAIL....');
                $message = wordwrap($message, 70, "\r\n");
                @mail($to, $subject, $message, $headers);
            }
        }
        

        function get_by_event($mail_event) {
            $query = "SELECT * FROM " . upicrm_db() . "mails WHERE `mail_event`='{$mail_event}'";
            $rows = $this->wpdb->get_results($query);
            return $rows[0];
        }

        function get_by_id($mail_id) {
            $query = "SELECT * FROM " . upicrm_db() . "mails WHERE `mail_id`='{$mail_id}'";
            $rows = $this->wpdb->get_results($query);
            return $rows[0];
        }

        function filter_change_mail_from_name($old) {
            return get_option('upicrm_sender_email');
            //return 'UpiCRM';
        }
        
        function send_csv($lead_id) {
            $to = get_option('upicrm_send_csv_get_mail');
            $message = __('Attached CSV file', 'upicrm');
            $subject = __('New CSV lead ID: ', 'upicrm');
            $subject .= $lead_id;
            $headers = "";
            
            $path = upicrm_lead_to_csv_output($lead_id);
            $attachments = array($path);
            //error_log('$to : ' . $to . ' , $subject: ' .$subject.' , $message : '.$message.' , $headers : '.print_r($headers, true) );
            
            $is_mail_sent = @wp_mail($to, $subject, $message, $headers,$attachments);
            
            //error_log('inside upicrm_mails after @wp_mail $is_mail_sent is : ' . $is_mail_sent);
            if ($is_mail_sent) {
                upicrm_remove_lead_to_csv_file($path);
            }
        }
        

    }
}