<style>
    .avatar-img{
        width:35px;
        height:35px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
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
        <h3 class="card-title">Cadidate List for <?php echo  isset($_SESSION['election']['title']) ? $_SESSION['election']['title'] : '' ?> Election</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="10%">
                <col width="30%">
                <col width="15%">
                <col width="25%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Image</th>
                    <th class="text-center p-0">Candidate Name</th>
                    <th class="text-center p-0">Position</th>
                    <th class="text-center p-0">Scope</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT c.*,(c.firstname || ' ' || c.middlename || ' '|| c.lastname|| ' '|| c.suffix) as fullname,p.name as position,p.type FROM `candidate_list` c inner join position_list p on c.position_id = p.position_id where c.election_id ='{$_SESSION['election']['election_id']}' ";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $scope = "";
                    switch($row['type']){
                        case 1:
                            $scope = "All";
                            break;
                        default:
                            $scope = isset($scope_arr[$row['type']][$row['scope_id']]) ? $scope_arr[$row['type']][$row['scope_id']]['name'] : "N/A";
                            break;
                    }
                ?>
                <tr>
                    <td class="text-center p-1"><?php echo $i++; ?></td>
                    <td class="p1 text-center">
                        <img src="<?php echo is_file('./../avatars/'.$row['candidate_id'].'.png') ? './../avatars/'.$row['candidate_id'].'.png' : './../images/no-image-available.png' ?>" class="avatar-img" alt="">
                    </td>
                    <td class="p1"><?php echo $row['fullname'] ?></td>
                    <td class="p1"><?php echo $row['position'] ?></td>
                    <td class="p1"><?php echo $scope ?></td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['candidate_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['candidate_id'] ?>' data-name = '<?php echo $row['fullname'] ?>' href="javascript:void(0)">Delete</a></li>
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
        $('#create_new').click(function(){
            uni_modal('Add New Candidate',"manage_candidate.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Candidate Details',"manage_candidate.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Candidate Details',"view_candidate.php?id="+$(this).attr('data-id'),'mid-large')
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
            url:'./../Actions.php?a=delete_candidate',
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