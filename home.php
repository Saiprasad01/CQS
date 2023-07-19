<h1 class="text-center fw-bolder">Welcome to Clinic Queuing System</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<?php 
include_once("./Master.php");
?>
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">groups</span>
                </div>
                <div class="dash-box-title">Patients Today</div>
                <div class="dash-box-text"><?= $master->today_patients() ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">pending</span>
                </div>
                <div class="dash-box-title">Pending Patients</div>
                <div class="dash-box-text"><?= $master->today_patients_pending() ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">done_all</span>
                </div>
                <div class="dash-box-title">Done</div>
                <div class="dash-box-text"><?= $master->today_patients_done() ?></div>
            </div>
        </div>
    </div>
</div>