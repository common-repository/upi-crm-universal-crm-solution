<?php 

$users_permission_arr = get_option('upicrm_affiliate_edit_permission_users');
$table_name = 'upicrm_affiliate_table_'.upicrm_clean_data($atts['id']);

if (isset($users_permission_arr[get_current_user_id()])) {
    $edit_link = @get_option('upicrm_affiliate_edit_link');
}
?>
<table id="<?php echo  $table_name; ?>" class="display" style="width:100%;">
    <thead>
        <tr>
            <?php
            $i = 0;
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    if (isset($field_on[$key][$key2])) {
                        echo "<th>".upicrm_clean_data($value)."</th>";
                        
                        if (isset($atts['field']) && $atts['field'] == $key2) {
                            $orderby = $i;
                        }
                        
                        $i++;
                    }
                }
            }
            if ($edit_link) {
                echo '<td>'.__('Edit', 'upicrm').'</td>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getLeads as $leadObj) {
            echo "<tr>";
            foreach ($list_option as $key => $arr) {
                foreach ($arr as $key2 => $value) {
                    if (isset($field_on[$key][$key2])) {
                        echo "<td>";
                        echo $UpiCRMUIBuilder->lead_routing($leadObj, $key, $key2, $getNamesMap, true, true);
                        echo "</td>";
                    }
                }
            }
            if ($edit_link) {
                echo '<td><a href="'.esc_html($edit_link).'?lead_id='.esc_html($leadObj->lead_id).'">'. __('Edit', 'upicrm').'</a></td>';
            }
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<script>
    jQuery(document).ready(function ($) {

        var id_<?php echo $table_name;?> = $('#<?php echo  $table_name; ?>').DataTable({
            responsive: true,
            <?php if (isset($lang['name'])) { ?>
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo upicrm_clean_data($lang['name']); ?>.json"
                },
            <?php } ?>
            <?php if (isset($atts['field'])) { ?>
                order: [ <?php echo  upicrm_clean_data($orderby); ?>, "desc" ],
            <?php } ?>

        });
   
        $("body").on("click", ".affiliate_time", function () {
            $(this).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
            $(this).focus();
        });
        
        $("body").on("click", ".aff_click", function () {
            elem = $(this).closest("div");
            elem.find(".loader").show();
            elem.find(".aff_save_icon").hide();
            
            
            var data = {
                'action': 'affiliate_time_save',
                'lead_id': elem.data("lead_id"),
                'affiliate_time': elem.find(".affiliate_time").val(),
            };
            $.post('<?php echo  admin_url('admin-ajax.php') ?>', data, function (response) {
                //console.log(response);
                if (response == 1) {
                    //alert("<?php _e('saved successfully!', 'upicrm'); ?>");
                    elem.find(".loader").hide();
                    elem.find(".aff_save_icon").show();
                }
                else
                    alert("<?php _e('Oh no! Error!', 'upicrm'); ?>");
            });
        });
        
       setTimeout(function(){ 
        $("#count_<?php echo $table_name;?>").html(<?php echo isset($getLeads) ? count($getLeads) : 0;?>);
    }, 1);

        
    });
</script>