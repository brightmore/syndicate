<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 alert alert-info">
            <h1><?php echo lang('forgot_password_heading'); ?></h1>
            <div><?php echo sprintf(lang('forgot_password_subheading'), $identity_label); ?></div>

            <div id="infoMessage"><?php echo $message; ?></div>

            <?php echo form_open("/index.php/public/Member/forgot_password"); ?>

            <div class="row">
                
                <label for="email"><?php echo sprintf(lang('forgot_password_email_label'), $identity_label); ?></label> <br />
                <input type="email" id="email" name="email"  /><?php echo form_submit('submit', lang('forgot_password_submit_btn')); ?>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>