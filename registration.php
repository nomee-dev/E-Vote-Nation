<?php
session_start();
if(!isset($_SESSION['election']['election_id'])){
    echo "<script>alert('Management does not set the election yet.'); location.replace('./');</script>";
}
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0){
    header("Location:./");
    exit;
}
require_once('./DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | E-Vote Nation</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./select2/css/select2.min.css">
    <link rel="stylesheet" href="./css/custom.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./select2/js/select2.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        html, body{
            height:100%;
        }
        #logo-img{
            width:75px;
            height:75px;
            object-fit:scale-down;
            background : var(--bs-light);
            object-position:center center;
            border:1px solid var(--bs-dark);
            border-radius:50% 50%;
        }
    </style>
</head>
<?php 
$scope_arr = array();
$region_arr = array();
$region_qry = $conn->query("SELECT *,region_id as id FROM `region_list` order by `name` asc");
while($row = $region_qry->fetchArray()):
    $region_arr[$row['region_id']] = $row['name'];
    $scope_arr[2][$row['region_id']]=$row;
endwhile;

$province_arr = array();
$province_qry = $conn->query("SELECT *,province_id as id FROM `province_list` order by `name` asc");
while($row = $province_qry->fetchArray()):
    $province_arr[$row['province_id']] = $row['name'] .(isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
    $row['name'] = $row['name']. (isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
    $scope_arr[3][$row['province_id']]=$row;
endwhile;

$district_arr = array();
$district_qry = $conn->query("SELECT *,district_id as id FROM `district_list` order by `name` asc");
while($row = $district_qry->fetchArray()):
    $district_arr[$row['district_id']] = $row['name'] . (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
    $row['name'] = $row['name']. (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
    $scope_arr[4][$row['district_id']]=$row;
endwhile;

$city_arr = array();
$city_qry = $conn->query("SELECT *,city_id as id FROM `city_list` order by `name` asc");
while($row = $city_qry->fetchArray()):
    $city_arr[$row['city_id']] = $row['name']. (isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '');
    $row['name'] = $row['name']. (isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '');
    $scope_arr[5][$row['city_id']]=$row;
endwhile;
?>
<body class="bg-light bg-gradient">
   <div class="h-100 d-flex jsutify-content-center align-items-center">
       <div class='w-100'>
        <h3 class="py-5 text-center">E-Vote Nation - User Registration</h3>
        <div class="card my-3 col-md-8 offset-md-2">
            <div class="card-body">
                <form action="" id="register-form">
                    <input type="hidden" name="id" value="0">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="lastname" class="control-label">First Name</label>
                            <input type="text" id="lastname" autofocus name="lastname" class="form-control form-control-sm rounded-0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="firstname" class="control-label">Last Name</label>
                            <input type="text" id="firstname" name="firstname" class="form-control form-control-sm rounded-0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="middlename" class="control-label">Middle Name</label>
                            <input type="text" id="middlename" name="middlename" class="form-control form-control-sm rounded-0" placeholder = "(optional)">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender" class="control-label">Gender</label>
                                <select type="text" id="gender" name="gender" class="form-control form-control-sm rounded-0" required>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contact" class="control-label">Contact #</label>
                                <input type="text" id="contact" pattern="[0-9+/s//]+" name="contact" class="form-control form-control-sm rounded-0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dob" class="control-label">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control form-control-sm rounded-0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username" class="control-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control form-control-sm rounded-0" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control form-control-sm rounded-0" required>
                    </div>
                    <div class="form-group">
                        <label for="cpassword" class="control-label">Confirm Password</label>
                        <input type="password" id="cpassword" name="cpassword" class="form-control form-control-sm rounded-0" required>
                    </div>
                    <div class="form-group">
                        <label for="city_id" class="control-label">Address</label>
                        <select name="city_id" id="city_id" class="form-select form-select-sm select2 rounded-0">
                            <option <?php echo (!isset($city_id)) ? 'selected' : '' ?> disabled>Please Select Position</option>
                            <?php 
                            if(isset($scope_arr[5])): 
                                asort($scope_arr[5]);
                                
                            ?>
                            <?php foreach($scope_arr[5] as $k => $v): ?>
                            <option value="<?php echo $k ?>"><?php echo $v['name'] ?></option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="form-group d-flex w-100 justify-content-between">
                        <a href="./">Already has an Account?</a>
                        <button class="btn btn-sm btn-primary rounded-0 my-1">Save</button>
                    </div>
                </form>
            </div>
        </div>
       </div>
   </div>
</body>
<script>
    function display_img(input){
        if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#logo-img').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
    }
    $(function(){
        $('#register-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            $('#password, #cpassword').removeClass('border-danger border-success')
            $('.err_msg').remove()
            if($('#password').val() != $('#cpassword').val()){
                $('#password, #cpassword').addClass('border-danger')
                $('#cpassword').after('<small class="text-danger err_msg">Password doesn\'t match</small>')
                return false;
            }
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Saving data...')
            $.ajax({
                url:'././Actions.php?a=save_user',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.replace('./');
                        }, 2000);
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
</html>