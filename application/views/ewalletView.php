<div class="col-lg-4 col-md-4 col-xs-12 col-sm-6">
    <div id="place_bet" class="content-wrapper">
        <div class="header"> EWallet Balance</div>
        <div class="content">
            <?php if (!$balance) { ?>
                <h2 class="text-success text-right"><?php echo "GHc 0.00" ?></h2>
                <small>Your Wallet shows that you haven't topup before. Please if you have any doubt contact our support center on <span class="today_numbers">+233-(0)24-441-9580</span></small>
            <?php } else { ?>
                <h2 class="text-success text-right">GHc<?php echo $balance ?></h2>
            <?php } ?>
        </div>
    </div>

    <div id="place_bet" class="content-wrapper">
        <div class="header"> Top up</div>
        <div class="content">
            <?php form_open('') ?>
        <div class="form-group">
            <label>Mobile Money Transaction ID</label>
            <input type="text" id="transaction_id" name="transaction_id" class="form-control">
        </div>
            <div class="form-group">
                <label>Mobile Money Amount</label>
                <input type="text" id="topupwallet" name="topupwallet" class="form-control" />
            </div>
            
            <div class="form-group">
                <!--<label for="captcha"><?php echo $captcha['image']; ?></label><br>-->
                <input type="hidden" 
                       autocomplete="off" 
                       name="userCaptcha" 
                       placeholder="Enter above text"
                       class="form-control"
                       value="" />
                <span class="required-server">
                    <?php echo form_error('userCaptcha', '<p style="color:#F83A18">', '</p>'); ?>
                </span> 
            </div>
            <button name="submit_wallet" type="submit" id="submit_wallet" class="btn btn-info">Top up</button>
            <?php form_close() ?>
        </div>
    </div>
</div>

<div class="col-lg-4 col-md-4 col-xs-12 col-sm-6 ">
    <div id="place_bet" class="content-wrapper">
        <div class="header"> E-Transfer </div>
        <div class="content">
            
            <table>
                <tr>
                    <td>
                        <div class="form-group">
                            <label>Transfer Amount</label>
                            <input type="number" id="transfer_amount" class="" name="transfer_amount" />
                        </div>
                        <div class="note">
                            Note: Only premium members can transfer money
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label>Transfer To</label>
                            <input type="text" id="username" name="username" class="" />
                            <br />
                        </div>
                    </td>
                </tr>
                
            </table>
            <div class="text-center">
                <button name="submit_transfer_wallet" type="submit" id="submit_transfer_wallet" class="btn btn-danger">Transfer</button>
            </div>
        </div>
    </div>
    
    <div id="place_bet" class="content-wrapper">
        <div class="header">E-Voucher </div>
        <div class="content">
            <div class="form-group">
                <label>Voucher</label>
            <input id="voucher_number" placeholder="Enter your 16 digits voucher ....." type="number" class="form-control" />
          </div>
            <button name="submit_voucher_wallet" type="submit" id="submit_voucher_wallet" class="btn btn-info">Top up</button>
        </div>
    </div>
</div>

<div class="col-lg-4 col-md-4 col-xs-12 col-sm-6 ">
    <div id="place_bet" class="content-wrapper">
        <div class="header"> Transaction Log for your last 10 activities</div>
        <div class="content">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Candy</td>
                            <td>Ghc 20.00</td>
                            <td>12:20 PM</td>
                        </tr>
                        <tr>
                            <td>Bright</td>
                            <td>Ghc 200.00</td>
                            <td>10:20 PM</td>
                        </tr>
                        <tr>
                            <td>Sam</td>
                            <td>Ghc 12.00</td>
                            <td>01:20 PM</td>
                        </tr>
                        <tr>
                            <td>Candy</td>
                            <td>Ghc 20.00</td>
                            <td>12:20 PM</td>
                        </tr>
                        <tr>
                            <td>Bright</td>
                            <td>Ghc 200.00</td>
                            <td>10:20 PM</td>
                        </tr>
                        <tr>
                            <td>Sam</td>
                            <td>Ghc 12.00</td>
                            <td>01:20 PM</td>
                        </tr>
                        <tr>
                            <td>Candy</td>
                            <td>Ghc 20.00</td>
                            <td>12:20 PM</td>
                        </tr>
                        <tr>
                            <td>Bright</td>
                            <td>Ghc 200.00</td>
                            <td>10:20 PM</td>
                        </tr>
                        <tr>
                            <td>Sam</td>
                            <td>Ghc 12.00</td>
                            <td>01:20 PM</td>
                        </tr>
                        <tr>
                            <td>Candy</td>
                            <td>Ghc 20.00</td>
                            <td>12:20 PM</td>
                        </tr>
                        <tr>
                            <td>Bright</td>
                            <td>Ghc 200.00</td>
                            <td>10:20 PM</td>
                        </tr>
                        <tr>
                            <td>Sam</td>
                            <td>Ghc 12.00</td>
                            <td>01:20 PM</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>