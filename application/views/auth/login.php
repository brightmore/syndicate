<header>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo">Eagle Eye</div>
            </div>
            <div class="col-sm-6 col-xs-6">
                <div class="row">
                    <div id="login_error" style="padding-left:30px;color:#FFF;margin:7px; "></div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <input type="text" class="form-control" id="login_username" placeholder="username">
                            <div class="login-bottom-text checkbox hidden-sm">
                                <label>
                                    <input type="checkbox" id="remember_me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                    </div>	
                    <div class="col-sm-5">
                        <div class="form-group">
                            <input type="password" id="login_password" class="form-control" placeholder="Password">
                            <div class="login-bottom-text hidden-sm"> <a href="<?php echo base_url('index.php/Auth/forget_password') ?>">Forgot your password? </a> </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="button" value="login" id="login" class="btn btn-primary btn-header-login">
                        </div>
                    </div>
                </div>	
            </div>
        </div>
    </div>
</header>
<article class="container">
    <div class="row">
        <div class="col-sm-8 col-xs-12">
            <div class="login-main">
                <h4><i class="fa fa-dashboard"></i> Gorgeous color and design</h4>
                <span>Some sample description text about the template goes here</span>

                <h4> <i class="fa fa-money"></i> 100%  fully responsive </h4>
                <span>Another description text about the template goes here</span>

                <h4><i class="fa fa-mobile-phone"></i> Competible with all browers and mobile devices</h4>
                <span>Yet another sample description text can be placed in one line</span>

                <h4> <i class="fa fa-trophy"></i> Easy to use and custmize with mobile friendly and responsive</h4>
                <span>Your last description text about your startup or business</span>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div class="">

                <h3><i class="fa fa-shield"></i> Register now</h3>
                <hr>
                <div id="form_errors"></div>
                
                    <div class="form-group">
                        <label for="username" class="label">Username:</label>
                        <input type="text" name="username" id="username" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="email" class="label">Email</label>
                        <input type="text" name="email" id="email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="phone" class="label">Phone</label>
                        <input type="tel" id="phone" class="form-control" name="phone" />
                    </div>
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

                    <div class="row mobile_money" >
                        <small>Note: For now we only accept M.T.N Mobile Money, Others are on their way.</small>
                        <div>
                            <label class="label"><input type="checkbox" name="same_phone_as_mobile_money" id="same_phone_as_mobile_money" value="1"> Using same Phone Number as Mobile Money number</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="label">Mobile Money Number</label>
                        <input type="text" id="mobile_money" name="mobile_money" class="form-control" />
                    </div>
                <div class="form-group">
                        <small>
                    By clicking Sign Up, you agree to our Terms and that you have read our
                    Data Use Policy, including our Cookie Use.
                </small>
                        <button id="submit_account" class="btn btn-danger">Create Profile</button>
                    </div>
                </div>
        </div>
    </div>
</div>
</article>
<footer class="container">
    <hr>
    <div class="footer-options col-sm-6 col-xs-6">
        <ul >
            <li><a href="#">About Us</li>
            <li><a href="#">How to play</li>
            <li><a href="#">Contact us</li>
            <li><a href="#">People</li>
            <li><a href="#">Places</a></li>
            <li><a href="#">Games</a></li>
   
        </ul>
    </div>
    <div class="col-sm-6 col-xs-6 text-right">
        <small class="copyrights"> © Copyrights reserved  2016</small>
    </div>
</footer>
</body>
<script>
    var BASEPATH = "<?php echo $baseurl ?>";
</script>
<script src="<?php echo base_url('assets/js/jquery.js')?>"></script>
<script src="<?php echo base_url('assets/js/validation.js')?>"></script>
<script src="<?php echo base_url('assets/js/auth.js')?>"></script>
</html>
