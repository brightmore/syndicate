<div class="row">
    <div class="col-lg-4">
        <div class=" content-wrapper" id="wallet">
            <div class="header">
                <span>Top up wallet</span>
            </div>
            <div class="content">
                <?php form_open('') ?>
                <input type="text" id="topupwallet" name="topupwallet" class="form-control" />
                <div class="form-group">
                    <label for="captcha"><img src="<?php echo $captcha['image']; ?>" /></label><br>
                    <input type="text" 
                           autocomplete="off" 
                           name="userCaptcha" 
                           placeholder="Enter above text"
                           class="form-control"
                           value="<?php
                           if (!empty($userCaptcha)) {
                               echo $userCaptcha;
                           }
                           ?>" />
                    <span class="required-server">
                        <?php echo form_error('userCaptcha', '<p style="color:#F83A18">', '</p>'); ?>
                    </span> 
                </div>
                <button name="submit_wallet" type="submit" id="submit_wallet" class="btn btn-google">Top up</button>
                <?php form_close() ?>
            </div>
        </div>


        <div id="place_bet">
            <div class="header"> Place a Bet</div>
            <div class="content">
                <?php form_open('') ?>
                <input type="text" id="topupwallet" name="topupwallet" class="form-control" />
                <div class="form-group">
                    <label for="captcha"><?php echo $captcha['image']; ?></label><br>
                    <input type="text" 
                           autocomplete="off" 
                           name="userCaptcha" 
                           placeholder="Enter above text"
                           class="form-control"
                           value="<?php
                           if (!empty($userCaptcha)) {
                               echo $userCaptcha;
                           }
                           ?>" />
                    <span class="required-server">
                        <?php echo form_error('userCaptcha', '<p style="color:#F83A18">', '</p>'); ?>
                    </span> 
                </div>
                <button name="submit_wallet" type="submit" id="submit_wallet" class="btn">Top up</button>
                <?php form_close() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-6">
                <div class="content-inner-wrapper">

                    <div class="today_numbers">
                        <div class="header">Today Numbers</div>
                        <div class="content">
                            <div class="numbers">89-2-17-39-70-12-67-85-90-22</div>
                        </div>
                    </div>

                    <div class="header">Total Bet Place</div>
                    <div class="content">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Bet Place</th>
                                        <th>Time</th>
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
                                </tbody>
                            </table>
                            <div class="total_bet">Ghc 232.00</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content-wrapper">
                    <div class="header"> Winnings Numbers</div>
                    <div class="content">
                        Last Week Winnings Numbers
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>