<?php 
session_start();
require_once('DBConnection.php');
$_SESSION['formToken']['patient-form'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$page = $_GET['page'] ?? 'home';
$title = ucwords(str_replace("_", " ", $page));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucwords($title) ?> | CQS</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/custom.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient fixed-top mb-5" id="topNavBar">
        <div class="container">
            <a class="navbar-brand" href="./patient_side.php">
            Clinic Queuing System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <div class="container-md pt-5 pb-3" id="page-container">
        <div class="my-4">
            <?php if(isset($_SESSION['message']['success'])): ?>
                <div class="alert alert-success py-3 rounded-0">
                    <?= $_SESSION['message']['success'] ?>
                </div>
                <?php unset($_SESSION['message']['success']) ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['message']['error'])): ?>
                <div class="alert alert-danger py-3 rounded-0">
                    <?= $_SESSION['message']['error'] ?>
                </div>
                <?php unset($_SESSION['message']['error']) ?>
            <?php endif; ?>
            <div class="col-lg-6 col-md-8 col-sm-12 col-12 mx-auto">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="container-fluid">
                            <p>Please Fill all the fields below</p>
                            <form action="" id="patient-form">
                                <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['patient-form'] ?>">
                                <input type="hidden" name="patient_id" value="<?= $data['patient_id'] ?? '' ?>">
                                <input type="hidden" name="encoded_by" value="patient">
                                <?php if(isset($data['patient_id'])): ?>
                                <div class="mb-3">
                                    <label for="queue_no" class="text-body-tertiary">Queue Number</label>
                                    <input type="text" class="form-control rounded-0" id="queue_no" name="queue_no" required="required" value="<?= $data['queue_no'] ?? "" ?>" readonly>
                                </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="fullname" class="text-body-tertiary">Fullname</label>
                                    <input type="text" class="form-control rounded-0" id="fullname" name="fullname" required="required" autofocus value="<?= $data['fullname'] ?? "" ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="contact" class="text-body-tertiary">Contact</label>
                                    <input type="text" class="form-control rounded-0" id="contact" name="contact" required="required"  value="<?= $data['contact'] ?? "" ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="text-body-tertiary">Address</label>
                                    <textarea rows="5" class="form-control rounded-0" id="address" name="address" required="required" ><?= $data['address'] ?? "" ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="age" class="text-body-tertiary">Age</label>
                                    <input type="number" class="form-control rounded-0" id="age" name="age" value="<?= $data['age'] ?? "" ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <button class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-primary rounded-0" form="patient-form">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="position-fixed bottom-0 w-100 bg-gradient bg-light">
        <div class="lh-1 container py-4">
            <div class="text-center">All rights reserved &copy; <?= date("Y") ?> - CQS(php)</div>
            <div class="text-center">Developed by:<a href="mailto:oretnom23@gmail.com" class='text-body-tertiary'>oretnom23</a></div>
        </div>
    </footer>

    <script>
        $(function(){
            $('#patient-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            $.ajax({
                url:'./Master.php?a=save_patient',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                }
            })
        })
        })
    </script>
</body>
</html>