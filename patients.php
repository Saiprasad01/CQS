<?php 
$_SESSION['formToken']['patients'] = password_hash(uniqid(),PASSWORD_DEFAULT);
$from = (isset($_GET['from']) ? date("Y-m-d", strtotime($_GET['from'])) : date("Y-m-d", strtotime(date("Y-m-d")))) . " 00:00:00";
$to = (isset($_GET['to']) ? date("Y-m-d", strtotime($_GET['to'])) : date("Y-m-d", strtotime(date("Y-m-d")))) . " 23:59:59";
?>
<style>
    #patientTBL .btn-group .btn-sm{
        line-height: .9rem !important;
        padding: 5px;
    }
    #patientTBL .btn-group .material-symbols-outlined{
        line-height: .9rem !important;
        font-size: .85rem !important;
    }
    #input-search-field input{
        border-top-left-radius:3em;
        border-bottom-left-radius:3em;
    }
    #input-search-field span.input-group-text{
        border-top-right-radius:50%;
        border-bottom-right-radius:50%;
    }
</style>
<h1 class="text-center fw-bolder">List of Patients</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-10 col-md-11 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="mb-3">
                    <div class="row align-items-end justify-content-center">
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <label for="date_from">Date From</label>
                            <input type="date" value="<?= date("Y-m-d", strtotime($from)) ?>" class="form-control form-control-sm rounded-0" id="date_from" name="date_from" required="required">
                        </div>
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <label for="date_to">Date To</label>
                            <input type="date" value="<?= date("Y-m-d", strtotime($to)) ?>" class="form-control form-control-sm rounded-0" id="date_to" name="date_to" required="required">
                        </div>
                        <div class="col-lg-auto">
                            <button class="btn btn-primary btn-sm rounded-0 d-flex align-items-center" id="filter"><span class="material-symbols-outlined">filter_alt</span> Filter</button>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="mx-auto col-lg-6 col-md-8 col-sm-10 col-12">
                        <div class="input-group" id="input-search-field">
                            <input type="search" class="form-control" id="search" placeholder="Enter keyword to search here">
                            <span class="input-group-text"><span class="material-symbols-outlined">search</span></span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover table-striped" id="patientTBL">
                        <colgroup>
                            <col width="5%">
                            <col width="20%">
                            <col width="20%">
                            <col width="25%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">DateTime</th>
                                <th class="text-center">Fullname</th>
                                <th class="text-center">Contact</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
                            $from->setTimezone(new DateTimeZone('UTC'));
                            $from = $from->format("Y-m-d");
                            $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
                            $to->setTimezone(new DateTimeZone('UTC'));
                            $to = $to->format("Y-m-d");
                            $i = 1;
                            $patients_sql = "SELECT * FROM `patient_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}' ORDER BY strftime('%s', `date_created`) desc";
                            
                            $patients_qry = $conn->query($patients_sql);
                            while($row = $patients_qry->fetchArray()):
                                $date_created = new DateTime($row['date_created'], new DateTimeZone('UTC'));$date_created->setTimezone(new DateTimeZone('Asia/Manila'));
                            ?>
                            <tr>
                                <td class="text-center"><?= $i++; ?></td>
                                <td class="text-center"><?= $date_created->format('M d, Y g:i A') ?></td>
                                <td class="">
                                    <div class="lh-1">
                                        <div class="text-secondary">Queue #: <?= $row['queue_no'] ?></div>
                                        <div><?= $row['fullname'] ?></div>
                                    </div>
                                </td>
                                <td><?= $row['contact'] ?></td>
                                <td class="text-center">
                                    <?php 
                                        switch($row['status']){
                                            case 0:
                                                echo "<span class='badge bg-light border rounded-pill px-3 text-dark'>Pending</span>";
                                                break;
                                            default:
                                                echo "<span class='badge bg-success border rounded-pill px-3'>Done</span>";
                                                break;
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-sm btn-outline-dark rounded-0 view_data" href="./?page=view_patient&id=<?= $row['patient_id'] ?>" data-id='<?= $row['patient_id'] ?>' title="View patient"><span class="material-symbols-outlined">subject</span></a>
                                        <?php if($row['status'] == 0): ?>
                                        <a class="btn btn-sm btn-outline-info rounded-0 notify_data" href="#" data-id='<?= $row['patient_id'] ?>' title="Notify Again"><span class="material-symbols-outlined">campaign</span></a>
                                        <?php endif; ?>
                                        <a class="btn btn-sm btn-outline-primary rounded-0 edit_data" href="./?page=manage_patient&id=<?= $row['patient_id'] ?>" data-id='<?= $row['patient_id'] ?>' title="Edit patient"><span class="material-symbols-outlined">edit</span></a>
                                        <button class="btn btn-sm btn-outline-danger rounded-0 delete_data" type="button" data-id='<?= $row['patient_id'] ?>' title="Delete patient"><span class="material-symbols-outlined">delete</span></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(!$patients_qry->fetchArray()): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.delete_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this patient data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_patient',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['patients'] ?>',
                        id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('.notify_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to notify again the queued patient?`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=notify_queue',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['patients'] ?>',
                        patient_id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('#filter').click(function(e){
            e.preventDefault()
            var from = $('#date_from').val()
            var to = $('#date_to').val()
            location.replace(`./?page=patients&from=${from}&to=${to}`)
        })

        $('#search').on('input change', function(e){
            var _search = $(this).val().toLowerCase();
            $('#patientTBL tbody tr').each(function(){
                var _text = $(this).text().toLowerCase();
                if(_search == ""){
                    $(this).toggle(true)
                }else{
                    if(_text.includes(_search) === false){
                        $(this).toggle(false)
                    }else{
                        $(this).toggle(true)
                    }
                }
            })
        })
    })
</script>
