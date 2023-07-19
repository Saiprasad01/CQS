<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT * FROM `patient_list` where `patient_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();

}

// Generate Manage patient Form Token
$_SESSION['formToken']['patient-form'] = password_hash(uniqid(),PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder"><?= isset($data['patient_id']) ? "Update Patient Details" : "Add New Patient" ?></h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-6 col-md-8 col-sm-12 col-12 mx-auto">
    <div class="card rounded-0">
        <div class="card-body">
            <div class="container-fluid">
                <form action="" id="patient-form">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['patient-form'] ?>">
                    <input type="hidden" name="patient_id" value="<?= $data['patient_id'] ?? '' ?>">
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
                    <div class="mb-3">
                        <label for="weight" class="text-body-tertiary">Weight <em>(kg)</em></label>
                        <input type="number" step="any" class="form-control rounded-0" id="weight" name="weight" value="<?= $data['weight'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="bp_rate" class="text-body-tertiary">BP Rate <em>(systolic/diastolic)</em></label>
                        <input type="text" class="form-control rounded-0" id="bp_rate" name="bp_rate" value="<?= $data['bp_rate'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="text-body-tertiary">Status</label>
                        <select class="form-select rounded-0" id="status" name="status">
                            <option value="0" <?= isset($data['status']) && $data['status'] == 0 ? "selected" : "" ?>>Pending</option>
                            <option value="1" <?= isset($data['status']) && $data['status'] == 1 ? "selected" : "" ?>>Done</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <div class="row justify-content-evenly">
                <button class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-primary rounded-0" form="patient-form">Save</button>
                <a class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-secondary rounded-0" href='./?page=patients'>Cancel</a>
            </div>
        </div>
    </div>
</div>
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
                        if('<?= $_GET['toview'] ?? "" ?>' == ""){
                            location.replace("./?page=patients");
                        }else{
                            location.replace("./?page=view_patient&id=<?= $data['patient_id'] ?? "" ?>");
                        }
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