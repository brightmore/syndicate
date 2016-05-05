<div class="row">
    <div class="col-lg-4 col-md-4">

    </div>
    <div class="col-lg-6 col-md-6">
        <div class="setting-content-wrapper">
            <div class="text-right">
                <h2>Profile Settings</h2>
            </div>
            <div class="setting-content" id="sms-notification">
                <p>SMS Notification </p>
                <input id="sms-switch-state" type="checkbox" name="sms-switch-state"> <span  id="sms_loading"><img src="<?php echo base_url('assets/img/468.GIF') ?>" /></span> 

                <div>
                    By enabling SMS notification, you will be notify of all your transactions and your betting activities.
                </div>
                <p style="color:yellow; font-style: italic; font-size: 12px;">
                    Note: SMS charges are apply 
                </p>
            </div>

            <div class="setting-content" id="bet-head">
                <p>Bet Ahead </p>
                <input id="bet-head-switch-state" type="checkbox" name="bet-head-switch-state">

                <div class="content">
                    <div class="input-group">
                        <span class="input-group-addon">Amount To Bet GHc</span> 
                        <input type="text" class="form-control" id="bet-head-amount" aria-label="Amount (to the nearest Ghana cedis)">
                        <span class="input-group-addon">.00</span>
                    </div>
                    <p class="text-right">
                        
                        <button class="btn btn-primary" id="submit_bet_head"><span id="bet-head-preloader" ><img src="<?php echo base_url('assets/img/468.GIF') ?>" /></span> <span>Start Now</span></button>
                    </p>
                </div>
            </div>
            
            <div class="setting-content" id="premium-member">
                <p>Become Premium Member</p>
                
                <div>
                    <input id="premium-member-switch-state" type="checkbox" name="premium-member-switch-state"><span id="premium-member-loading"><img src="<?php echo base_url('assets/img/468.GIF') ?>" /></span> 
                </div>
                
                <div>
                    <p>Becoming premium member comes with benefits</p>
                    <ul>
                        <li>You get some percentage off on all the bets place </li>
                        <li>You will get notification on some key activities and security alert </li>
                        <li>You can transfer money from your e-wallet to other member on the platform.</li>
                    </ul>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal -->
<div class = "modal fade" id="premiumModal" tabindex = "-1" role = "dialog" 
   aria-labelledby = "myModalLabel" aria-hidden = "true">
   
   <div class = "modal-dialog">
      <div class = "modal-content">
         
         <div class = "modal-header">
            <button type = "button" class ="close" data-dismiss = "modal" aria-hidden = "true">
                  &times;
            </button>
            
            <h4 class = "modal-title" id = "myModalLabel">
               Premium Membership Activation
            </h4>
         </div>
         
         <div class = "modal-body">
             <p>Activation of the premium membership will cost you GHc 50 only and it is a one time payment.</p>
             <p>Do you still want to continue?</p>
         </div>
         
         <div class = "modal-footer">
            <button type = "button" class = "btn btn-default" data-dismiss = "modal">
               Close
            </button>
            
             <button type = "button" class = "btn btn-primary" id="activatePremium">
              Activate
            </button>
         </div>
         
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
  
</div>

