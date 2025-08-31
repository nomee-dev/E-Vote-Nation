<?php 
if(!isset($_SESSION['election']['election_id'])){
    echo "<script>alert('No Current Election set in Settings.'); location.replace('./');</script>";
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
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Voters List for <?php echo  isset($_SESSION['election']['title']) ? $_SESSION['election']['title'] : '' ?> Election</h3>
        <div class="card-tools align-middle">
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="35%">
                <col width="35%">
                <col width="15%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Voter Name</th>
                    <th class="text-center p-0">Info</th>
                    <th class="text-center p-0">Status</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT v.*,(v.firstname || ' ' || v.middlename || ' '|| v.lastname) as fullname FROM `voter_list` v where v.election_id = '{$_SESSION['election']['election_id']}' order by `status` asc,fullname asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $scope = "";
                ?>
                <tr>
                    <td class="text-center p-1"><?php echo $i++; ?></td>
                    <td class="p1"><?php echo $row['fullname'] ?></td>
                    <td class="p1 lh-1">
                        <small><span class="text-muted">Username: </span><?php echo $row['username'] ?></small><br>
                        <small><span class="text-muted">Contact: </span><?php echo $row['contact'] ?></small><br>
                        <small><span class="text-muted">Address: </span><?php echo isset($scope_arr[5][$row['city_id']]['name']) ? $scope_arr[5][$row['city_id']]['name'] : '' ?></small>
                    </td>
                    <td class="p1 text-center">
                        <?php if($row['status'] == 1): ?>
                            <span class="badge bg-success rounded-pill">Validated</span>
                        <?php else: ?>
                            <span class="badge bg-primary rounded-pill">Pending</span>
                        <?php endif; ?>
                    </td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['voter_id'] ?>' href="javascript:void(0)">View</a></li>
                            <?php if($row['status'] != 1): ?>
                            <li><a class="dropdown-item validate_data" data-id = '<?php echo $row['voter_id'] ?>' data-name = '<?php echo $row['fullname'] ?>' href="javascript:void(0)">Validate</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['voter_id'] ?>' data-name = '<?php echo $row['fullname'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
               
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('.validate_data').click(function(){
            _conf("Are you sure to validate <b>"+$(this).attr('data-name')+"</b> as registered voter?",'validate_voter',[$(this).attr('data-id')])
        })
        $('.view_data').click(function(){
            uni_modal('voter Details',"view_voter.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle')
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:4 }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_voter',
            method:'POST',
            data:{voter_id : $id},
            dataType:'json', // Ensure response is parsed as JSON
            error:err=>{
                console.log(err)
                alert("An error occurred (AJAX).")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred. " + (resp.error ? resp.error : ""))
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function validate_voter($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=validate_voter',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                consolre.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>