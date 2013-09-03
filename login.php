<!DOCTYPE html>
<html lang="en"><head>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <title>Sign in </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="<?php echo base_url();?>public/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
        <script src="<?php echo base_url();?>public/assets/js/jquery-1.9.1.min.js"></script>
        <script src="<?php echo base_url();?>public/assets/js/account.js"></script>

        <style type="text/css">
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }

            .form-signin {
                max-width: 300px;
                padding: 19px 29px 29px;
                margin: 0 auto 20px;
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin input[type="text"],
            .form-signin input[type="password"] {
                font-size: 16px;
                height: auto;
                margin-bottom: 15px;
                padding: 7px 9px;
            }

        </style>

    </head>

    <body>

        <div class="container">

            <div class="container"> 
                <form class="form-horizontal" name="signupform" id="signupform" onsubmit="return sigupValidation();" method="post">


                    <div id="signup" class="modal hide fade in" style="display: none; ">  

                        <div class="modal-header">  
                            <a class="close" data-dismiss="modal">×</a>  
                            <h3>Sign-Up</h3>  
                        </div>  

                        <div class="modal-body">

                            <div class="control-group">
                                <label class="control-label">First Name</label>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="icon-user"></i></span>
                                        <input class="input-xlarge" placeholder="First Name" type="text" id="fname" name="fname">
                                    </div>
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label">Last Name</label>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="icon-user"></i></span>
                                        <input class="input-xlarge" placeholder="Last Name" type="text" id="lname" name="lname">
                                    </div>
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label">Email</label>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on">
                                            <i class="icon-envelope"></i>
                                        </span>
                                        <input class="input-xlarge" placeholder="Email Address" type="text" id="email" name="email">
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Password</label>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on">
                                            <i class="icon-lock"></i>
                                        </span>
                                        <input class="input-xlarge" placeholder="Password" type="password" id="password" name="password">
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Confirm Password</label>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on">
                                            <i class="icon-lock"></i>
                                        </span>
                                        <input class="input-xlarge" placeholder="Re_Enter Password" type="password" id="repeatpass" name="repeatpass">
                                    </div>
                                </div>
                            </div>

                        </div>  

                        <div class="modal-footer"> 
                            <input type="hidden" name="action" value="signup_users">
                            <button type="submit" class="btn primary" name="sign" id="sign" >Register</button>
                            <a href="#" class="btn" data-dismiss="modal">Cancel</a> 

                        </div>  

                    </div>   
                    </form>
            </div> 
        
    </div>


    <form class="form-signin" id="loginform" name="loginform" onsubmit="return loginValidation();" method="post" >

        <h2 class="form-signin-heading">Please sign in</h2>
        <input class="input-block-level" placeholder="Email address" type="text" id="email" name="email">
        <input class="input-block-level" placeholder="Password" type="password" id="password" name="password">
        <label class="checkbox">
            <input value="remember-me" type="checkbox"> Remember me
        </label>
        <input type="hidden" name="action" value="login_users">
        <button class="btn btn-large btn-primary" style="background:red;" id="login" name="login">Sign in</button>
        <a href="#signup" role="button" class="btn btn-large btn-primary" data-toggle="modal" style="background:green;">Sign Up</a>
    </form>

</div> <!-- /container -->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="assets/bootstrap/js/bootstrap.js"></script>

</body>
</html>





