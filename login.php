<?php
session_start();
if(isset($_SESSION['voter_id']) && $_SESSION['voter_id'] > 0){
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
    <title>LOGIN |E-Vote Nation</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        html, body{
            height:100%;
        }
    </style>
</head>
<body class="" style="background-image: url(./images/e-voting.jpg); background-size: cover; background-repeat: no-repeat;">
   <div class="h-100 d-flex jsutify-content-center ">
       <div class='w-100'>
                <h3 class=" display-4 fw-bold text-center text-light">E-Vote Nation</h3>

        <div class="card my-3 col-md-6 offset-md-3" style="background-color: rgba(255, 255, 255, 0.5);">
            <div class="card-body">
                <form action="" id="login-form">
                    <center><small>Please your credentials.</small></center>
                    <div class="form-group">
                        <label for="username" class="control-label">Username</label>
                        <input type="username" id="username" autofocus name="username" class="form-control form-control-sm rounded-5 bg-none" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password</label>
                        <input type="password" id="password" autofocus name="password" class="form-control form-control-sm rounded-5" required>
                    </div>
                    <div class="form-group d-flex w-100 justify-content-between align-items-center mt-3">
                        <a href="./registration.php">Register Voter's Account</a>
                        <button class="btn btn-sm btn-primary rounded-5 px-2 my-1">Login</button>
                    </div>
                </form>
            </div>
        </div>
       
       </div>
   </div>

 <center>
           <a href="./result.php" class="btn btn-light   ms-5 rounded-pill px-5" id="submit_btn" >View Result</a>
            <a href="./admin/login.php"  class="ms-2 btn btn-light   rounded-pill px-5">Admin Login</a>

       
        </center>
   
           
</body>
<script>
    $(function(){
        $('#login-form').submit(function(e){
            e.preventDefault();
            if('<?php echo !isset($_SESSION['election']['election_id']) ?>' == 1){
                alert('Management does not set the election yet.');
                return false;
            }
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Loging in...')
            $.ajax({
                url:'./Actions.php?a=e_login',
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
