<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Eagle Eye</title>

    <!-- Bootstrap Core CSS -->

    <link href="<?php echo base_url('assets/css/custom_bootstrap.css') ?>" rel="stylesheet">

    <!-- Custom CSS -->
     <link href="<?php echo base_url('assets/css/styles.css') ?>" rel="stylesheet">
    <!-- Custom Fonts -->
     <link href="<?php echo base_url('assets/css/font-awesome.min.css')?>" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">
        <nav class="navbar navbar-default ">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo base_url('index.php/Dashboard') ?>">Engle Eye</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <li class="active"><a href="<?php echo base_url('index.php/Dashboard/contact_us') ?>" class="contact_us" > Contact Us<span class="sr-only">(current)</span></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">About<span class="caret"></span></a>
          <ul class="dropdown-menu">
              <li><a href="<?php echo base_url('index.php/Dashboard/about_us') ?>" class="about_us">About Us</a></li>
              <li><a href="<?php echo base_url('index.php/Dashboard/how_to_play') ?>" class="how_to_play" >How to play</a></li>
          </ul>
        </li>
        <li><a href="<?php echo base_url('index.php/MyWallet/') ?>" class="my-wallet">My Wallet</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
          <li><a href="#" class="btn btn-danger " data-toggle="modal" data-target=".premium_member">Become premium Member</a></li>
          <li><a href="#"><span class="fa fa-bell"></span> Messages</a></li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <span class=" fa fa-user"></span>Profile <span class="caret"></span></a>
          <ul class="dropdown-menu">
              <li><a href="<?php echo base_url('index.php/Dashboard/account') ?>"><span class="fa fa-user"></span> My Account</a></li>
              <li><a href="<?php echo base_url('index.php/Dashboard/settings') ?>"><span class="fa fa-gear"></span> Settings</a></li>
              <li><a href="<?php echo base_url('index.php/Auth/logout') ?>"><span class="fa fa-lock"> Logout</span></a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">

                        <div class="col-lg-12" style="background-color: #ffffff; height: 100px; margin-bottom: 20px;">

                        </div>


<!--                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-dashboard"></i>  <a href="index.html">Dashboard</a>
                            </li>
                            <li class="active">
                                <i class="fa fa-file"></i> Blank Page
                            </li>
                        </ol>-->
                    </div>
                    <div class="col-lg-12">
                      <?php echo $content ?>
                    </div>

                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <script>BASEPATH = "<?php echo $baseurl ?>";</script>

    <!-- jQuery -->
    <script src="<?php echo base_url('assets/js/jquery.js')?>" ></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo base_url('assets/js/bootstrap.min.js')?>"></script>
    <script src="<?php echo base_url('assets/js/application.js')?>"></script>
</body>

</html>
