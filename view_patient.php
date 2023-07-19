<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT * FROM `patient_list` where `patient_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
    $date_created = new DateTime($data['date_created'], new DateTimeZone('UTC'));
    $date_created->setTimezone(new DateTimeZone('Asia/Manila'));
    $date_created = $date_created->format("M d, Y g:i A");

}else{
    throw new ErrorException("This page requires a valid ID.");
}
$_SESSION['formToken']['patients'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['patientDetails'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['comment-form'] = password_hash(uniqid(), PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder">Patient Details</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-8 col-md-10 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                
                <table class="table table-sm table-bordered">
                    <colgroup>
                        <col width="50%">
                        <col width="50%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><b>Patient Queue #</b></td>
                            <td><?= $data['queue_no'] ?? "" ?></td>
                        </tr>
                        <tr>
                            <td><b>Name</b></td>
                            <td><?= $data['fullname'] ?? "" ?></td>
                        </tr>
                        <tr>
                            <td><b>Contact #</b></td>
                            <td><?= $data['contact'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Address</b></td>
                            <td><?= $data['address'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Age</b></td>
                            <td><?= $data['age'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Weight</b> <small><em>(em)</em></small></td>
                            <td><?= $data['weight'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>BP Rate</b> <small><em>(mmHg)</em></small></td>
                            <td><?= $data['bp_rate'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Status</b></td>
                            <td>
                            <?php 
                                if(isset($data['status'])){
                                    switch($data['status']){
                                        case 0:
                                            echo "<span class='badge bg-light border rounded-pill px-3 text-dark'>Pending</span>";
                                            break;
                                        default:
                                            echo "<span class='badge bg-success border rounded-pill px-3'>Done</span>";
                                            break;
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <hr>
                <div class="text-center">
                    <a href="./?page=patients" class="btn btn-sm btn-secondary rounded-0">Back to List</a>
                    <a href="./?page=manage_patient&id=<?= $data['patient_id'] ?>&toview=true" class="btn btn-sm btn-primary rounded-0">Edit</a>
                    <button type="button" data-id="<?= $data['patient_id'] ?>" class="btn btn-sm btn-danger rounded-0 delete_data">Delete</button>
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
                            location.replace("./?page=patient")
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
    })
</script>
