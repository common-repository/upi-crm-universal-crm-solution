<?php

if (!class_exists('UpiCRMAJAX')) {

    class UpiCRMAJAX {

        function wp_ajax_upicrm_on_load_callback() {
            ignore_user_abort(true);
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            $UpiCRMIntegrationsLib = new UpiCRMIntegrationsLib();
            $UpiCRMIntegrationsLib->send_waiting_slave();
            //echo 'UpiCRM';
            die();
        }

    }

    add_action('wp_ajax_upicrm_on_load', array(new UpiCRMAJAX, 'wp_ajax_upicrm_on_load_callback'));
    add_action('wp_ajax_nopriv_upicrm_on_load', array(new UpiCRMAJAX, 'wp_ajax_upicrm_on_load_callback'));
}
?>