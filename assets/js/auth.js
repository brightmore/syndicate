$(function(){
   
   $('#login_username').focus(function(event){
       $('#login_error').hide();
   });
   
    $('#login').on('click',function(event){
        
        var name = $('#login_username').val(),
        password = $('#login_password').val();
        
        if(! name || password){
            $('#login_error').html('Provide your username or password');
        }
        
        if(name && password){
          var xhr =  $.ajax({
                url:BASEPATH+'index.php/Auth/process_login',
                type:'post',
                data:{username:name,password:password},
                beforeSend:function(xhr){},
                success:function(serverData){
                    
                    var result = JSON.parse(serverData); 
                    
                    switch(result.message){
                        case "success":
                            var url = BASEPATH+'index.php/Dashboard';
                            window.location = url;    
                            break;
                        case "security_issues":
                            //
                            break;
                        case "form_error":
                            $('#login_error').html(result.error);
                            break;
                        case "account_suspended":
                            window.location = BASEPATH+'index.php/Auth/suspended';
                            break;
                        case "account_suspended_now": 
                            window.location = BASEPATH+'index.php/Auth/suspended';
                            break;
                        
                    }
                    
                }
            });
             
        }else{
            console.log("error...");
            $('#login_error').html('Provide your username and password');
        }
        return false;
    });
    
    $('#submit_account').on('click',function(event){
        var username = $('#username'),
            password = $('#password'),
            email = $('#email'),
            region = $('#region'),
            town = $('#town'),
            phone = $('#phone'),
            same_phone_as_mobile_money = $('#same_phone_as_mobile_money'),
            mobile_money = $('#mobile_money');
            
        var txtUsername = username.val(),
            txtpassword = password.val(),
            txtemail = email.val(),
            txtregion = region.val(),
            txttown = town.val(),
            txtPhone = phone.val(),
            is_same_number = same_phone_as_mobile_money.is(':checked')? true : false;
            
            var txtmobile_money = mobile_money.val();
        
            var data = {
                'username':txtUsername,
                'phone':txtPhone,
                'email':txtemail,
                'password':txtpassword,
                'region':txtregion,
                'town':txttown,
                'mobile_money': is_same_number?txtPhone:txtmobile_money
            };
            
                        
              $.ajax({
                url:BASEPATH+'/index.php/Auth/process_profile',
                type:'post',
                data:data,
                beforeSend:function(xhr){},
                success:function(serverData){
                   var result = JSON.parse(serverData);
                   var message = result.message;
                  
                  if(message === 'form_error'){
                      console.log(result.error);
                        $('#form_errors').html(result.error);
                  }else if(message === "verify_token_email"){
                       var url = BASEPATH+'index.php/Dashboard';
                            window.location = url;
                  }else if(message === "create_failed"){
                       $('#form_errors').html('Creating of the account failed, please try again later.');
                  }else if(message === "security_issues"){
                      
                  }else if(message === "error_hashing_password"){
                       $('#form_errors').html('Error hashing your password, try again later.');
                  }else if(message === "server_error"){
                      
                  }else if(message === "user_exist"){
                        $('#form_errors').html('Username already taken.')
                  }else if(message === "phone_exist"){
                       $('#form_errors').html('Phone number already exist');
                  }else if(message === "email_exist"){
                       $('#form_errors').html('Email already exist');
                  }else if(message === "verify_token_email"){
                      $('#form_errors').html("Please verify your account, check your email and follow instructions");
                  }else if(message === 'verify_token_sms'){
                      $('#form_errors').html("Please verify your account, check your messages on your phone and follow instructions");
                  }else if(message === 'verify_token_email'){
                      var urll = BASEPATH + 'index.php/Auth/account_activation';
                      window.location = url;
                  }else if(message === 'verify_token_sms'){
                      var urll = BASEPATH + 'index.php/Auth/account_activation';
                      window.location = url;
                  }
                 
                }
            });
    });
    
});

//change password
//$(function(){
//    $('#submit_change_password').on('click',function(event){
//        alert('alert');
//        return false;
//    });
//});