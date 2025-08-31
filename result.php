<?php
require_once('./DBConnection.php');
if(!isset($_SESSION['election']['election_id'])){
    echo "<script>alert('Management does not set the election yet.'); location.replace('./Actions.php?a=e_logout');</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result | E-Vote Nation</title>
    <link rel="stylesheet" href="./fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./select2/css/select2.min.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./summernote/summernote-lite.min.css">
    <link rel="stylesheet" href="./css/custom.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./select2/js/select2.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./summernote/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./fontawesome/js/all.min.js"></script>
    <script src=""></script>
    <style>
            .candidate-img-holder {
                width: 28% !important;
                height: 15vh;
                overflow: hidden;
            }
            .candidate-item {
                cursor: unset;
            }
    </style>
</head>
<body class="bg-light">
    <main>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient" id="topNavBar">
        <div class="container">
            <a class="navbar-brand" href="./">
           E-Vote Nation - Result
            </a>
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button> -->
            <!-- <div class="collapse navbar-collapse" id="navbarNav">
                
            </div> -->
            <div>
            </div>
        </div>
    </nav>
    <div class="container py-3" id="page-container">
        
    <?php 
    $pid =  isset($_GET['position_id']) ? $_GET['position_id'] : 'all';
    $city_id =  isset($_GET['city_id']) ? $_GET['city_id'] : 0;
    $position_type = array("All","National",'Regional','Provincial','District','City');
    $scope = array();
    $region_arr = array();
    $region_qry = $conn->query("SELECT *,region_id as id FROM `region_list` order by `name` asc");
    while($row = $region_qry->fetchArray()):
        $region_arr[$row['region_id']] = $row['name'];
        $scope[2][$row['region_id']]=$row;
    endwhile;

    $province_arr = array();
    $province_qry = $conn->query("SELECT *,province_id as id FROM `province_list` order by `name` asc");
    while($row = $province_qry->fetchArray()):
        $province_arr[$row['province_id']] = $row['name'] .(isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
        $row['name']= $row['name'] .(isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
        $scope[3][$row['province_id']]=$row;
    endwhile;
    $district_arr = array();
    $district_qry = $conn->query("SELECT *,district_id as id FROM `district_list` order by `name` asc");
    while($row = $district_qry->fetchArray()):
        $district_arr[$row['district_id']] = $row['name'] . (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
        $row['name'] = $row['name'] . (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
        $scope[4][$row['district_id']]=$row;
    endwhile;

    $city_arr = array();
    $city_qry = $conn->query("SELECT *,city_id as id FROM `city_list` order by `name` asc");
    while($row = $city_qry->fetchArray()):
        $city_arr[$row['city_id']] = $row['name'];
        $row['name'] = $row['name']. (isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '');
        $scope[5][$row['city_id']]=$row;
    endwhile;
    ?>

        <div class="col-12">
        <form action="" id="filter">
            <div class="row align-items-end mb-3">
                <div class="form-group col-md-3">
                    <label for="position_id" class="control-label">Position</label>
                    <select class="form-select form-select-sm rounded-0 select2" name="position_id" required>
                        <option value="all" <?php echo $pid == 'all' ? 'selected' : '' ?>>All</option>
                        <?php
                        $position_arr=array();
                        $position = $conn->query("SELECT * FROM position_list order by `order_by` asc ");
                        while($row = $position->fetchArray()): 
                            $position_arr[$row['position_id']] = $row['name'];
                        ?>
                        <option value="<?php echo $row['position_id'] ?>" <?php echo $pid == $row['position_id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="city_id" class="control-label">Candidates Per City</label>
                    <select class="form-select form-select-sm rounded-0 select2" name="city_id" id="city_id">
                        <option value="" disabled <?php echo $city_id > 0 ? '' : 'selected' ?>></option>
                    <?php
                        if(isset($scope[5])):
                        foreach($scope[5] as $row): 
                        ?>
                        <option value="<?php echo $row['city_id'] ?>" <?php echo $city_id == $row['city_id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <button class="btn btn-sm rounded-0 btn-primary"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
            
                    <?php 
                    $candidate_arr = array();
                    $pwhere = "";
                    if(is_numeric($pid) && $pid > 0){
                        $pwhere .= " and position_id = '{$pid}' ";
                    }
                    
                    
                    $sql = "SELECT * FROM `position_list` where status = 1 {$pwhere} order by order_by asc";
                    $qry = $conn->query($sql);
                    $i = 1;
                        while($row = $qry->fetchArray()):
                            $cwhere = "" ;
                            if($city_id > 0){
                                if($row['type'] == 5){
                                    $cwhere = " and scope_id = '{$city_id}' ";
                                }
                                if($row['type'] == 4){
                                    $cwhere = " and scope_id in (SELECT district_id FROM city_list where city_id = '{$city_id}') ";
                                }
                                if($row['type'] == 3){
                                    $cwhere = " and scope_id in (SELECT province_id FROM district_list where district_id in (SELECT district_id FROM city_list where city_id = '{$city_id}')) ";
                                }
                                if($row['type'] == 2){
                                    $cwhere = " and scope_id in (SELECT region_id FROM province_list where province_id in (SELECT province_id FROM district_list where district_id in (SELECT district_id FROM city_list where city_id = '{$city_id}'))) ";
                                }
                            }
                            $candidate = $conn->query("SELECT *,(firstname || ' ' || middlename || ' '|| lastname|| ' '|| suffix) as fullname FROM `candidate_list` where position_id = '{$row['position_id']}' and election_id = '{$_SESSION['election']['election_id']}' {$cwhere} order by fullname asc");
                            $count = $conn->query("SELECT count(candidate_id) as `count` FROM `candidate_list` where position_id = '{$row['position_id']}' and election_id = '{$_SESSION['election']['election_id']}' {$cwhere} ")->fetchArray()['count'];
                            if($count > 0):
                    ?>
                    <h3 class="text-center"><?php echo $row['name'] ?></h3>
                    <hr>
                    <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-3 gx-3 gy-3 justify-content-center vote-position py-4 my-3" data-id ="<?php echo $row['position_id'] ?>">
                    <?php
                            while($crow = $candidate->fetchArray()):
                                $candidate_arr[] = $crow['candidate_id'];
                                

                    ?>
                    <div class="col">
                        <div class="card candidate-item shadow-sm d-flex flex-row" data-max="<?php echo $row['max'] ?>" data-position='<?php echo $row['position_id'] ?>' data-id='<?php echo $crow['candidate_id'] ?>'>
                            <div class="candidate-img-holder col-3 position-relative">
                                <img src="<?php echo is_file('./avatars/'.$crow['candidate_id'].'.png') ? './avatars/'.$crow['candidate_id'].'.png' : './images/no-image-available.png' ?>" alt="" class="img-top bg-dark bg-gradient">
                            </div>
                            <div class="card-body col-auto d-flex flex-column justify-content-between">
                                <h5 class="card-title"><?php echo $crow['fullname'] ?></h5>
                                <span class="w-100 text-end vote-count" data-id='<?php echo $crow['candidate_id'] ?>'><?php echo number_format(0) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                     </div>

                    <?php endif; ?>
                    <?php endwhile; ?>
                
            </div>
    </div>
    </main>
    <script>
        var resultInterval;
        var candidates = $.parseJSON('<?php echo json_encode($candidate_arr) ?>')
        
        $(function(){
            $('.select2').select2({
                width:'100%'
            })
            resultInterval = setInterval(() => {
                if(candidates.length <= 0){
                    clearInterval(resultInterval)
                    return false;
                }

                $.ajax({
                    url:'Actions.php?a=updated_result',
                    method:'POST',
                    data:{candidates:candidates},
                    dataType:'json',
                    error:err=>{
                        console.log(err)
                        clearInterval(resultInterval)
                        alert("Result Update Result has been stop due to some error. Please refresh the page.")
                    },
                    success:function(resp){
                        if(resp.length > 0){
                            Object.keys(resp).map(k=>{
                                $('.vote-count[data-id="'+resp[k].id+'"]').text(parseFloat(resp[k].count).toLocaleString('en-US'))
                            })
                        }
                    }
                })
            }, 750);
            $('#filter').submit(function(e){
                e.preventDefault();
                location.replace("./result.php?" + $(this).serialize())
            })
        })
    </script>
</body>
</html>