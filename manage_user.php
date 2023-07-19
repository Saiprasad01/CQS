<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT `user_id`, `fullname`, `username`, `status`, `type` FROM `user_list` where `user_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
}

// Generate Manage User Form Token
$_SESSION['formToken']['manage_user'] = password_hash(uniqid(),PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder"><?= isset($data['user_id']) ? "Update User Details" : "Add New User" ?></h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-6 col-md-8 col-sm-12 col-12 mx-auto">
    <div class="card rounded-0">
        <div class="card-body">
            <div class="container-fluid">
                <form action="" id="update-user">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['manage_user'] ?>">
                    <input type="hidden" name="user_id" value="<?= $_GET['id'] ?? '' ?>">
                    <div class="mb-3">
                        <label for="fullname" class="control-label">Fullname</label>
                        <input type="text" id="fullname" autofocus name="fullname" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="control-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="control-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">User Type</label>
                        <select name="type" id="type" class="form-select rounded-0" requried>
                            <option value="1" <?= isset($data['type']) && $data['type'] == 1 ? "selected" : "" ?>>Administrator</option>
                            <option value="2" <?= isset($data['type']) && $data['type'] == 2 ? "selected" : "" ?>>Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select rounded-0" requried>
                            <option value="0" <?= isset($data['status']) && $data['status'] == 0 ? "selected" : "" ?>>Inactive</option>
                            <option value="1" <?= isset($data['status']) && $data['status'] == 1 ? "selected" : "" ?>>Active</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <div class="row justify-content-evenly">
                <button class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-primary rounded-0" form="update-user"><?= isset($data['user_id']) ? "Update" : "Save" ?></button>
                <a class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-secondary rounded-0" href='./?page=users'>Cancel</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#update-user').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
                start_loader()
            $.ajax({
                url:'./LoginRegistration.php?a=save_user',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.replace("./?page=users");
                        return false;
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)
                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    end_loader()
                }
            })
        })
    })
</script>