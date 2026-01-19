<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>FILI | Login</title>

    <link rel="icon" type="image/png" href="<?= base_url(); ?>/assets/images/fili.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="<?= base_url(); ?>/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url(); ?>/assets/libs/css/style.css">
    <link rel="stylesheet" href="<?= base_url(); ?>/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <style>
    html,
    body {
        height: 100%;
    }

    body {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
    }
    </style>
</head>

<body>
    <!-- ============================================================== -->
    <!-- login page  -->
    <!-- ============================================================== -->
    <div class="splash-container">
        <div class="card ">
            <div class="card-header text-center"><img class="logo-img" src="<?= base_url(); ?>/assets/images/logo.png" alt="logo"></a><span class="splash-description">ACCESO</span></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('auth') ?>">
                <?= csrf_field() ?>

    <div class="form-group">
        <input class="form-control form-control-lg"
               name="username"
               type="text"
               placeholder=""
               value="<?php  ?>"
               required autofocus>
    </div>

    <div class="form-group">
        <input class="form-control form-control-lg"
               name="password"
               type="password"
               placeholder=""
               value="<?php   ?>"
               required
               >
    </div>

    <button type="submit"
            class="btn btn-primary btn-lg btn-block">
        ACCEDER
    </button>

</form>
  <?php if (session()->getFlashdata('errors') !== null): ?>
               <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo session()->getFlashdata('errors'); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">×</span>
                  </button>
               </div>
            <?php endif; ?>

            </div>
            
        </div>
    </div>
  
    <!-- ============================================================== -->
    <!-- end login page  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <script src="<?= base_url(); ?>/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="<?= base_url(); ?>/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
</body>
 
</html>