<?php 
session_start();
require_once('DBConnection.php');
$page = $_GET['page'] ?? 'home';
$title = ucwords(str_replace("_", " ", $page));
$_SESSION['formToken']['patients_queueu'] = password_hash(uniqid(), PASSWORD_DEFAULT);
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
                            <h3 class="text-center fw-bolder">Now Serving</h3>
                            <h3 class="text-center fw-bolder" id="patient_name"></h3>
                            <h1 class="text-center fw-bolder" id="patient_queue"></h1>
                            
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
        var speech = new SpeechSynthesisUtterance();
            speech.rate = 1;
            speech.pitch = 1;
            speech.volume = 1;
            speech.voice = speechSynthesis.getVoices()[0];
        const get_queue = () =>{
            var fetchData = $.ajax({
                url:"Master.php?a=get_current_queueu",
                method:'POST',
                data:{
                    token: '<?= $_SESSION['formToken']['patients_queueu'] ?>'
                },
                dataType: 'JSON',
                error: err=>{
                    console.error(err)
                },
                success: function(resp){
                    if(resp.status == 'success'){
                        $('#patient_name').text(resp.data.fullname)
                        $('#patient_queue').text(resp.data.queue_no)
                        var queue = resp.data.queue_no.split('').join(' ')
                        if(resp.data.notify == 1){
                            speech.text = `Now Serving Queue Number ${queue}. Calling Mr. or Ms. ${resp.data.fullname}, please proceed to the front desk.`
                            speechSynthesis.speak(speech)
                            var i = 1;
                           var interval = setInterval(() => {
                                if(i == 2){
                                    if(speechSynthesis.speaking == false){
                                        clearInterval(interval)
                                        setTimeout(() => {
                                            get_queue()
                                        }, 1500);
                                    }
                                    return false
                                }

                                if(speechSynthesis.speaking == false){
                                    speechSynthesis.speak(speech)
                                    i++;
                                }
                           }, 100);

                        }else{
                            setTimeout(() => {
                                get_queue()
                            }, 1500);
                        }
                    }else{
                        console.error(resp)
                    }
                }
            })
        }
        $(function(){
            get_queue()
        })
    </script>
</body>
</html>