<a id="custom"></a>
<?php /* <h2><?php _e('Custom email template', 'upicrm'); ?></h2>
<form method="post" action="admin.php?page=upicrm_email_notifications#custom">
    <input type="hidden" name="action" value="edit_custom_show" />

    <select name="mail_id">
        <?php
        $getMails = $UpiCRMMails->get_custom();
        foreach ($getMails as $mail) {
            ?>
            <option value="<?php echo  $mail->mail_id; ?>"><?php echo  $mail->mail_event_name; ?></option>
        <?php } ?>
    </select>
    <input type="submit" value="<?php _e('Edit', 'upicrm'); ?>" class="button button-primary" />
</form> */ ?>
<br />
<h2><?php _e($edit_mode ? 'Edit' : 'Create', 'upicrm'); ?> <?php _e('Custom email template', 'upicrm'); ?></h2>

<br />
<section id="widget-grid" class="">
    <!-- row -->
    <div id="" class="row">
        <article class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueLight" id="wid-id-1" data-widget-editbutton="false">
                <header>
                    <span class="widget-icon">
                        <i class="fa fa-table">
                        </i>
                    </span>
                    <h2>  
                        <?php _e('List of custom email template', 'upicrm'); ?>
                    </h2>
                </header>
                <!-- widget div-->
                <div>
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                    </div>
                    <!-- end widget edit box -->
                    <!-- widget content -->
                    <div class="widget-body no-padding">
                        <table id="datatable_fixed_column" class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th data-class="expand">
                                        <?php _e('ID', 'upicrm'); ?>
                                    </th>
                                    <th data-class="expand">
                                        <?php _e('Template Name', 'upicrm'); ?>
                                    </th>
                                    <th data-class="expand">
                                        <?php _e('Actions', 'upicrm'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>        
                                <?php
                                $getMails = $UpiCRMMails->get_custom();
                                if (count($getMails) > 0 ) {
                                foreach ($getMails as $mail) {
                                    ?>
                                <tr>
                                    <td><?php echo  $mail->mail_id; ?></td>
                                    <td><?php echo  upicrm_clean_data($mail->mail_event_name); ?></td>
                                    <td class="upicrm_lead_actions">
                                        <span class="glyphicon glyphicon-edit" data-callback="edit" data-mail_id="<?php echo  $mail->mail_id; ?>" title="<?php _e('Edit', 'upicrm'); ?>"></span>
                                        <span class="glyphicon glyphicon-remove" data-callback="remove" data-mail_id="<?php echo  $mail->mail_id; ?>" title="<?php _e('Remove', 'upicrm'); ?>"></span>
                                    </td>
                                </tr>
                                <?php }
                                }
                                else {
                                    ?>
                                <tr>
                                    <td><?php _e('No email template yet!', 'upicrm'); ?></td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- end widget content -->
                </div>
                <!-- end widget div -->
            </div>
            <!-- end widget -->
        </article>
    </div>
    <!-- end row -->
    <!-- end row -->
</section>
<script type="text/javascript">
    $j(document).ready(function($) {
        $j("*[data-callback='remove']").click(function() {
                if (confirm("<?php _e('Remove this email template?','upicrm'); ?>")) {
                    GetSelect = $j(this);
                    var data = {
                        'action': 'remove_mail_template',
                        'mail_id': $j(this).attr("data-mail_id"),
                    };
                    $j.post(ajaxurl, data , function(response) {
                        GetSelect.closest("tr").fadeOut();
                        console.log(response);
                    });
                }
            });
        
        $j("*[data-callback='edit']").click(function() {
            var mail_id = $j(this).attr("data-mail_id");
            window.location = "admin.php?page=upicrm_email_notifications&action=edit_custom_show&id="+mail_id+"#custom";
        });
        
    });
</script>

<?php if ($edit_mode) {
    $getEditMail = $UpiCRMMails->get_by_id($_GET['id']);
}
?>
<form method="post" action="admin.php?page=upicrm_email_notifications">
    <?php _e('Here you can define an email message to be sent either to UpiCrm users / admin or your website visitors, upon filling a lead form. The email template will be used according to the “Auto Lead Management” configuration.','upicrm'); ?><br /><br />

    <input type="hidden" name="action" value="<?php echo  !$edit_mode ? 'new_custom' : 'edit_custom'; ?>" />
    <?php if ($edit_mode) {
       ?><input type="hidden" name="mail_id" value="<?php echo  $getEditMail->mail_id; ?>" /><?php 
    }
    ?>
    <div class="row">
        <div class="col-xs-12 col-sm-5 col-md-5 col-lg-6">
            <label><?php _e('Content:', 'upicrm'); ?> </label><br />
            <textarea name="mail_content" rows="12" cols="50"><?php echo  !$edit_mode ? '[lead]' : upicrm_clean_data($getEditMail->mail_content);?></textarea>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-5 col-lg-6">
            <label><?php _e('Subject:', 'upicrm'); ?> </label><br />
            <input type="text" name="mail_subject" value="<?php echo  !$edit_mode ? '' : upicrm_clean_data($getEditMail->mail_subject);?>" />
            <br /><br />
            <label><?php _e('Unique name to use on auto lead management:', 'upicrm'); ?> </label><br />
            <input type="text" name="mail_event_name" value="<?php echo  !$edit_mode ? '' : upicrm_clean_data($getEditMail->mail_event_name) ;?>" />
            <br /><br />
            <strong><?php _e('Variables:', 'upicrm'); ?></strong> <br />
<?php echo  upicrm_clean_data($the_var); ?>
        </div>
    </div>
<?php submit_button(); ?>
</form>