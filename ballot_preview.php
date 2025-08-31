<?php 
$scope_arr = array();
$region_arr = array();
$region_qry = $conn->query("SELECT *,region_id as id FROM `region_list` order by `name` asc");
while($row = $region_qry->fetchArray()):
    $region_arr[$row['region_id']] = $row['name'];
    $scope_arr[2][$row['region_id']]=$row;
endwhile;

$district_id = $conn->query("SELECT district_id FROM `city_list` where city_id ='{$_SESSION['city_id']}'")->fetchArray()['district_id'];
$province_id = $conn->query("SELECT province_id FROM `district_list` where district_id ='{$district_id}'")->fetchArray()['province_id'];
$region_id = $conn->query("SELECT region_id FROM `province_list` where province_id ='{$province_id}'")->fetchArray()['region_id'];
?>
<h3 class="text-center">Submitted Ballot Information</h3>
<hr>
<div class="col-12">
<?php 
    $qry = $conn->query("SELECT * FROM `position_list` where status = 1 order by order_by asc");
    while($row=$qry->fetchArray()):  
        $scope = null;
        if($row['type'] == 2){
            $scope = $region_id;
        }else if($row['type'] == 3){
            $scope = $province_id;
        }else if($row['type'] == 4){
            $scope = $district_id;
        }else if($row['type'] == 5){
            $scope = $_SESSION['city_id'];
        }
        if($scope != null)
        $swhere = "where `scope_id` = '{$scope}'";
        else
        $swhere = "where `scope_id` is NULL ";
        $count = $conn->query("SELECT count(candidate_id) as `count` FROM `candidate_list` $swhere and position_id = '{$row['position_id']}' and election_id = '{$_SESSION['election']['election_id']}'")->fetchArray()['count'];
        $candidate = $conn->query("SELECT *,(firstname || ' ' || middlename || ' '|| lastname|| ' '|| suffix) as fullname FROM `candidate_list` $swhere and position_id = '{$row['position_id']}' and candidate_id in (SELECT candidate_id FROM vote_list where position_id = '{$row['position_id']}' and election_id ='{$_SESSION['election']['election_id']}' and voter_id = '{$_SESSION['voter_id']}' ) order by fullname asc");
        
    ?>
    <?php if($count> 0): ?>
    <h3 class="text-center"><?php echo $row['name'] ?></h3>
    <hr>
        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-3 row-cols-xl-5 gx-3 gy-3 justify-content-center vote-position py-4" data-id ="<?php echo $row['position_id'] ?>">
            <?php while($crow = $candidate->fetchArray()): ?>
                <div class="col">
                    <div class="card candidate-item shadow-sm">
                        <div class="candidate-img-holder w-100 position-relative">
                            <img src="<?php echo is_file('./avatars/'.$crow['candidate_id'].'.png') ? './avatars/'.$crow['candidate_id'].'.png' : './images/no-image-available.png' ?>" alt="" class="img-top bg-dark bg-gradient">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $crow['fullname'] ?></h3>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
       
        </div>
        <?php if(!$candidate->fetchArray()): ?>
            <div class="row justify-content-center py-4" data-id ="<?php echo $row['position_id'] ?>">
                <div class="col-auto">
                    <div class="card candidate-item shadow-sm">
                        <div class="card-body">
                            <p class="m-0">You did not choose a candidate on this position</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php endwhile; ?>
</div>
