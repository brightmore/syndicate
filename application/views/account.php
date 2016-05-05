


<div class="row">
    <div class="col-lg-3 col-md-3">
        <div class="setting-content-wrapper">
            <h2>Change Password</h2>
             <div class="form-group">
                        <label for="username" class="label">Old Password</label>
                        <input type="password" name="oldPasswd" id="oldPassword" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="newPassword" class="label">Current Password</label>
                        <input type="password" name="newPassword" id="newPassword" class="form-control" />
                    </div>
            <div class="form-group">
                        <label for="email" class="label">Confirm Password</label>
                        <input type="password" name="confirmNewPassword" id="confirmNewPassword" class="form-control" />
                    </div>
            <div class="form-group">
                        
                        <button id="submit_change_password" class="btn btn-danger">Change Password</button>
                    </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-5">
        <div class="setting-content-wrapper">
            <div class="text-right">
                <h2>Profile Update</h2>
            </div>
           <div class="">

                <h3><i class="fa fa-shield"></i> Update</h3>
                <hr>
                <div id="form_errors"></div>
                
                    <div class="form-group">
                        <label for="username" class="label">Username:</label>
                        <input type="text" name="username" id="username" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="email" class="label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="phone" class="label">Phone</label>
                        <input type="tel" id="phone" class="form-control" name="phone" />
                    </div>
                    
                    <input type="hidden" name="id" value="<?php echo $id ?>" id="record_id" />
                
                    <div class="form-group">

                        <label for="region" class="label">Region</label>
                        <select name="region" id="region" class="form-control">
                            <option value="error">Select</option>
                            <option value="Ashanti">Ashanti</option>
                            <option value="Central">Central</option>
                            <option value="Greater Accra">Greater Accra</option>
                            <option value="Western">Western</option>
                            <option value="Eastern">Eastern</option>
                            <option value="Volta">Volta</option>
                            <option value="Northern">Northern</option>
                            <option value="Upper East">Upper East</option>
                            <option value="Upper West">Upper West</option>
                        </select>
                        <div class="error"></div>
                    </div>
                    <div class="form-group">
                        <label class="label">Town</label>
                        <input type="text" id="town" name="town" class="form-control" />
                    </div>     
                <div class="form-group">
                        
                        <button id="submit_account" class="btn btn-danger">Create Profile</button>
                    </div>
                </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        
        <?php if($isSuspended) {?>
        <div class="setting-content-wrapper">
            <h4>Your account is suspended</h4>
            <div>
                <a href="<?php echo base_url('index.php/Auth/unsuspend_account')?>" class="btn btn-danger"><span class="fa fa-lock"></span> Unlock</a>
            </div>
            
            <?php if(! $hasBalance) {?>
            <p>To Unsuspended your account you need to TOPUP your E-wallet first. You don't have enough money in your account</p>
            <p class="note">Note: Some charges are apply..</p>
            <?php } ?>
        </div>
        <?php } ?>
        
        <div class="setting-content-wrapper">
            <div id="form_errors"></div>
            
            <h2>Change Mobile Money </h2>
            <div class="form-group">
                <input type="text" id="mobile_money" name="mobile_money" class="form-control" placeholder="Enter here..." />  
            </div>
            <div>
                <button type="button" id="change_mobile_money" class="btn btn-default">Change</button>
            </div>
            <small>Note: For now we only accept M.T.N Mobile Money, Others are on their way.</small>
        </div>
        
    </div>
</div>