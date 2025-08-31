<style>
    #uni_modal .modal-footer{
        display:none !important
    }
</style>
<?php 
require_once('./../DBConnection.php');
$qry = $conn->query("SELECT *,(firstname || ' ' || middlename || ' '|| lastname) as fullname FROM `voter_list` where voter_id = '{$_GET['id']}'")->fetchArray();
foreach($qry as $k => $v){
    if(!is_numeric($k))
    $$k = $v;
}

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
<div class="cotainer-flui">
    <div class="col-12">
        <dl>
            <dt class="text-muted">Voter's Name</dt>
            <dd class="ps-4"><?php echo $fullname ?></dd>
            <dt class="text-muted">Gnder</dt>
            <dd class="ps-4"><?php echo $gender ?></dd>
            <dt class="text-muted">Date of Birth</dt>
            <dd class="ps-4"><?php echo date("F d, Y",strtotime($dob)) ?></dd>
            <dt class="text-muted">Contact #</dt>
            <dd class="ps-4"><?php echo $contact ?></dd>
            <dt class="text-muted">Username</dt>
            <dd class="ps-4"><?php echo $username ?></dd>
            <dt class="text-muted">Voting Address</dt>
            <dd class="ps-4"><?php echo isset($scope_arr[5][$city_id]['name']) ? $scope_arr[5][$city_id]['name'] : '' ?></dd>
            <dt class="text-muted">Status</dt>
            <dd class="ps-4">
                <?php if($status== 1): ?>
                    <span class="badge bg-success rounded-pill">Validated</span>
                <?php else: ?>
                    <span class="badge bg-primary rounded-pill">Pending</span>
                <?php endif; ?>
            </dd>
        </dl>
        <div class="col-12">
            <div class="row justify-content-end mt-3">
                <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>