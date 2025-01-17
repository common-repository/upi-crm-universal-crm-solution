<?php
if (!class_exists('UpiCRMAdminImportExport')):

    class UpiCRMAdminImportExport {

        public function Render($param) {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'import_all':
                        $this->importAll();
                        break;
                    case 'excel_output':
                        $this->upicrm_excel_output();
                        break;
                    case 'excel_fromat_output':
                        $this->excel_fromat_output();
                        break;
                    case 'excel_fromat_upload':
                        $this->excel_fromat_upload();
                        break;
                    case 'export_csv':
                        upicrm_export_csv();
                        break;
                    case 'export_last_csv':
                        upicrm_export_csv(true);
                        break;
                    case 'save_custom':
                        $this->save_custom();
                        break;
                    case 'csv_upload_import':
                        $this->csv_upload_import();
                        break;
                }
            }

            require_once get_upicrm_template_path($param);
            //  require_once get_upicrm_template_path('import_export');
        }

        function upicrm_excel_output() {

            set_time_limit(0);
            upicrm_load('excel');
            $UpiCRMLeads = new UpiCRMLeads();
            $UpiCRMUIBuilder = new UpiCRMUIBuilder();
            $UpiCRMUsers = new UpiCRMUsers();
            $UpiCRMFieldsMapping = new UpiCRMFieldsMapping();
            $objPHPExcel = new PHPExcel();
            $list_option = $UpiCRMUIBuilder->get_list_option();

            $start = 0;
            $check_date = 0;
            $end = 0;
            if ($_POST['date_start2']) {
                //'2019-01-25'
                $start = date("Y-m-d", strtotime($_POST['date_start2']));
                $check_date = "custom";
            }

            if ($_POST['date_end2']) {
                //'2019-01-25'
                $end = date("Y-m-d", strtotime($_POST['date_end2']));
                $check_date = "custom";
            }

            if ($UpiCRMUsers->get_permission() == 1) {
                $userID = get_current_user_id();
                $getLeads = $UpiCRMLeads->get($userID, 0, 0, "DESC", $check_date, $start, $end);
            }
            if ($UpiCRMUsers->get_permission() == 2) {
                $getLeads = $UpiCRMLeads->get(0, 0, 0, "DESC", $check_date, $start, $end);
            }
            $getNamesMap = $UpiCRMFieldsMapping->get();
            $fileName = '/leads-' . upicrm_random_hash() . '.xlsx';
            $dirName = WP_CONTENT_DIR . "/uploads/upicrm";
            if (!file_exists($dirName)) {
                mkdir($dirName, 0777, true);
            }
            $t = "A";
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    $objPHPExcel->getActiveSheet()->getStyle($t . '1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($t . '1', $value);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($t)->setWidth(25);
                    $t++;
                }
            }
            $i = 2;
            foreach ($getLeads as $leadObj) {
                $t = "A";
                foreach ($list_option as $key => $arr) {
                    foreach ($arr as $key2 => $value) {

                        if ($key2 == 'source_id' && $leadObj->source_id != 0) {//Form Name local
                            $getValue = $UpiCRMLeads->get_source_form_name($leadObj->source_id, $leadObj->source_type);
                        } else if ($key2 == 'source_id' && $leadObj->source_id == 0) {//Form Name remote
                            $lead_content = $leadObj->lead_content;
                            $form_name = json_decode($lead_content, true);
                            $getValue = @$form_name['Form Name'];
                        } else {
                            $getValue = $UpiCRMUIBuilder->lead_routing($leadObj, $key, $key2, $getNamesMap, true);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($t . $i, $getValue);
                        $t++;
                    }
                }
                $i++;
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($dirName . $fileName);
            echo '<script>window.onload = function (event) { window.location="' . home_url() . '/wp-content/uploads/upicrm/' . $fileName . '"; };</script>';
        }

        function excel_fromat_output() {
            upicrm_load('excel');
            $UpiCRMLeads = new UpiCRMLeads();
            $UpiCRMUIBuilder = new UpiCRMUIBuilder();
            $UpiCRMFieldsMapping = new UpiCRMFieldsMapping();
            $objPHPExcel = new PHPExcel();

            $list_option = $UpiCRMUIBuilder->get_list_option();
            $getLeads = $UpiCRMLeads->get();
            $getNamesMap = $UpiCRMFieldsMapping->get();
            $fileName = '/upicrm_format-' . upicrm_random_hash() . '.xlsx';
            $dirName = WP_CONTENT_DIR . "/uploads/upicrm";
            if (!file_exists($dirName)) {
                mkdir($dirName, 0777, true);
            }
            $t = "A";
            foreach ($list_option as $key => $arr) {
                if ($key == "content") {
                    foreach ($arr as $key2 => $value) {
                        $objPHPExcel->getActiveSheet()->getStyle($t . '1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue($t . '1', $value);
                        $objPHPExcel->getActiveSheet()->getColumnDimension($t)->setWidth(25);
                        $t++;
                    }
                }
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($dirName . $fileName);
            echo '<script>window.onload = function (event) { window.location="' . home_url() . '/wp-content/uploads/upicrm/' . $fileName . '"; };</script>';
        }

        function importAll() {
            $UpiCRMgform = new UpiCRMgform();
            $UpiCRMwpcf7 = new UpiCRMwpcf7();
            $UpiCRMninja = new UpiCRMninja();
            if ($UpiCRMgform->is_active()) {
                $UpiCRMgform->import_all();
            }
            if ($UpiCRMwpcf7->is_db_active()) {
                $UpiCRMwpcf7->import_all();
            }
            if ($UpiCRMninja->is_active()) {
                $UpiCRMninja->import_all();
            }
        }

        function excel_fromat_upload() {
            upicrm_load('excel');
            $UpiCRMLeads = new UpiCRMLeads();
            $fileName = '/import.xlsx';
            $dirName = WP_CONTENT_DIR . "/uploads/upicrm";
            $file_name = key($_FILES);
//        var_dump($_FILES);
            if ($_FILES[$file_name]['name']) {
                if (!$_FILES[$file_name]['error']) {
                    move_uploaded_file($_FILES[$file_name]['tmp_name'], $dirName . $fileName);
                    upicrm_load('excel');
                    $objPHPExcel = PHPExcel_IOFactory::load($dirName . $fileName);
                    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    $i = 0;
                    $new_records = 1;
                    $field = [];
                    foreach ($sheetData as $sheet) {
                        if ($i == 0) {
                            $field = $sheet;
                        } else {
                            $content = array();
                            $is_empty_sheet = true;
                            foreach ($sheet as $key => $value) {
                                if ($value) {
                                    $content[$field[$key]] = $value;
                                    $is_empty_sheet = false;
                                }
                            }

                            if (!$is_empty_sheet) {
                                $UpiCRMLeads->add($content, 4, 0, false);
                                $new_records++;
                            }
                        }
                        $i++;
                    }
                    ?><div class="updated"><p>
                    <?php _e('Success!', 'upicrm'); ?>
                    <?php echo $new_records - 1; ?>
                    <?php _e('new records imported into UpiCRM.', 'upicrm'); ?>
                        </p></div><br/><br/><?php
                }
            } else {
                ?>
                <div class="error">
                    <p><?php _e('Error occurred, could not import data', 'upicrm'); ?></p>
                </div><br/><br/><?php
            }
        }

        function csv_upload_import() {
            $UpiCRMLeads = new UpiCRMLeads();
            $fileName = '/import.csv';
            $dirName = WP_CONTENT_DIR . "/uploads/upicrm";
            $file_name = key($_FILES);
            if (isset($_POST['submit'])) {
                if ($_FILES[$file_name]['name']) {
                    if (!$_FILES[$file_name]['error']) {
                        //Print file details
                        echo "<div class=\"updated\"><p>";
                        echo "Upload file: " . $_FILES[$file_name]['name'] . "<br />";
//                    echo "Type: " . $_FILES[$file_name]['type'] . "<br />";
//                    echo "Size: " . ($_FILES[$file_name]['size'] / 1024) . " Kb<br />";
//                    echo "Temp file: " . $_FILES[$file_name]['tmp_name'] . "<br />";
                        //if file already exists
                        if (file_exists("upload/" . $_FILES[$file_name]['name'])) {
                            echo $_FILES[$file_name]['name'] . " already exists. ";
                        } else {
                            move_uploaded_file($_FILES[$file_name]['tmp_name'], $dirName . $fileName);
//                        echo "Stored in: " . "upload/" . $_FILES[$file_name]['name'] . "<br />";
                        }
                        $row = 1;
                        $csv_file = fopen($dirName . $fileName, r);
                        $firstline = fgets($csv_file, 4096);
                        //Gets the number of fields, in CSV-files the names of the fields are mostly given in the first line
                        $num = strlen($firstline) - strlen(str_replace(';', '', $firstline));
                        echo $num . "  fields in lead.<br />";
                        //save the different fields of the firstline in an array called fields
                        $fields_from_head = explode(';', str_replace('"', '', $firstline), ($num + 1));
                        $line = [];
                        $i = 0;
                        //CSV: one line is one record and the cells/fields are seperated by ";"
                        //so $loaded is an two dimensional array saving the records like this: $loaded[number of record][number of cell]
                        $loaded = [];
                        while ($line[$i] = fgets($csv_file, 4096)) {
                            $line[$i] = str_replace(array('\ufeff', '"'), '', $line[$i]);
                            $loaded[$i] = explode(';', $line[$i], ($num + 1));
//
                            $associated_arr = array_combine($fields_from_head, $loaded[$i]);
                            $UpiCRMLeads->add($associated_arr, 4, 0, false);
                            $i++;
                        }
                        fclose($csv_file);
                        echo $i . "  leads exported from file.<br />";
                        echo "</p></div><br/><br/>";
                    }
                } else {
                    echo '<div class="error"><p>';
                    _e('Error occurred, could not import data', 'upicrm');
                    echo '</p></div><br/><br/>';
                }
            }
        }
        
        
        function save_custom() {
            $UpiCRMFields = new UpiCRMFields();
            $show = [];
            if (isset($_POST['show']) && count($_POST['show']) > 0) {
                foreach ($_POST['show'] as $key => $value) {
                    $show[$value] = $value;
                }
            }
            update_option('upicrm_export_show_fields', $show);
            
            if (isset($_POST['order']) && is_array($_POST['order'])) {
                foreach ($_POST['order'] as $key => $value) {
                    $value = (int)$value;
                    $key = (int)$key;
                    $UpiCRMFields = new UpiCRMFields();
                    $UpiCRMFields->update(['field_order2' => $key], $value);
                }
            } 
            //var_dump($_POST);
            echo '<script>location ="admin.php?page=upicrm_export"</script>';
        }
        

    }

    
endif;
