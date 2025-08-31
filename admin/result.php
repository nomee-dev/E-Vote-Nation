<head>
<!-- Add in your <head> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<?php 
$pid =  isset($_GET['position_id']) ? $_GET['position_id'] : 'all';
$ptype =  isset($_GET['ptype']) ? $_GET['ptype'] : 0;
$scope_id =  isset($_GET['scope_id']) ? $_GET['scope_id'] : 0;
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

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Voting Result List</h3>
        <div class="card-tools align-middle">

            <a class="btn btn-primary btn-sm py-1 rounded-0" href="javascript:void(0)" id="" onclick="location.reload()"><i class="fa fa-retweet"></i> Reload</a>
            <a class="btn btn-success btn-sm py-1 rounded-0" href="javascript:void(0)" id="print"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
    <div class="card-body">
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
                <div class="form-group col-md-3">
                    <label for="ptype" class="control-label">Filter By</label>
                    <select class="form-select form-select-sm rounded-0 select2" name="ptype" id="ptype" required>
                        <?php
                        foreach($position_type as $k => $v): 
                        ?>
                        <option value="<?php echo $k ?>" <?php echo $ptype == $k ? 'selected' : '' ?>><?php echo $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="scope_id" class="control-label">Scope</label>
                    <select class="form-select form-select-sm rounded-0 select2" name="scope_id" id="scope_id">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <button class="btn btn-sm rounded-0 btn-primary"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
        <div id="outprint">
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
        <table class="table table-hover table-striped table-bordered" id="list">
            <!-- <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="30%">
                <col width="30%">
                <col width="15%">
            </colgroup> -->
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Position</th>
                    <th class="text-center">Candidate</th>
                    <th class="text-center">Total Vote</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $title ="<div class='lh-1'>";
                $pwhere = "";
                $vwhere = "";
                if(is_numeric($pid) && $pid > 0){
                    $pwhere .= " and position_id = '{$pid}' ";
                    $title  .= "<h5 class='text-center m-0'>{$position_arr[$pid]} Position</h5>" ;
                }
                if($ptype > 0){
                    $pwhere .= " and `type` <= '{$ptype}' ";
                    $title  .= "<h5 class='text-center m-0'>{$position_type[$ptype]} Result</h5>" ;
                }
                if($scope_id > 0){
                    if($ptype == 5){
                        $vwhere = " and vl.city_id = '{$scope_id}' ";
                    }
                    if($ptype == 4){
                        $vwhere = " and vl.city_id in (SELECT city_id FROM city_list where district_id = '{$scope_id}') ";
                    }
                    if($ptype == 3){
                        $vwhere = " and vl.city_id in (SELECT city_id FROM city_list where district_id in (SELECT district_id FROM district_list where province_id = '{$scope_id}')) ";
                    }
                    if($ptype == 2){
                        $vwhere = " and vl.city_id in (SELECT city_id FROM city_list where district_id in (SELECT district_id FROM district_list where province_id in (SELECT province_id FROM province_list where region_id = '{$scope_id}'))) ";
                    }
                    $title  .= "<h6 class='text-center m-0'>{$scope[$ptype][$scope_id]['name']}</h6>" ;
                }
                $title .="</div>";
                
                $sql = "SELECT * FROM `position_list` where status = 1 {$pwhere} order by order_by asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $candidate = $conn->query("SELECT *,(firstname || ' ' || middlename || ' '|| lastname|| ' '|| suffix) as fullname FROM `candidate_list` where position_id = '{$row['position_id']}' and election_id = '{$_SESSION['election']['election_id']}' order by fullname asc");
                        while($crow = $candidate->fetchArray()):
                            $vote = $conn->query("SELECT count(v.vote_id) as total FROM vote_list v inner join voter_list vl on v.voter_id = vl.voter_id where candidate_id = '{$crow['candidate_id']}' {$vwhere}")->fetchArray()['total'];
                            $vote = $vote >0 ? $vote: 0;

                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="lh-1">
                        <?php echo $row['name'] ?>
                    </td>
                    <td class="lh-1">
                        <span>
                            <img src="<?php echo is_file('./../avatars/'.$crow['candidate_id'].'.png') ? './../avatars/'.$crow['candidate_id'].'.png' : './../images/no-image-available.png' ?>" class="avatar-img" alt="">
                        </span>
                        <?php echo ucwords($crow['fullname']) ?>
                    </td>
                    <td class="lh-1 text-end"><?php echo number_format($vote) ?></td>
                    </th>
                </tr>
                <?php endwhile; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>



<div class="row justify-content-center mt-5">
    <div class="col-md-">
           
    </div>
    <div class="col-md-6">
<canvas id="myPieChart" width="400" height="400"></canvas>

    </div>
    <div class="col-md-">
          
    </div>

</div>

<script>
    var dtable;
    var scope,
    region_arr,
    province_arr,
    district_arr,
    city_arr;
    scope = $.parseJSON('<?php echo json_encode($scope) ?>')
    $(function(){
        $('#ptype').change(function(){
            var type= $(this).val()
            $('#scope_id').html('')
            if(type == 1){
                $('#scope_id').attr('required',false)
                $('#scope_id').val('').trigger('change')
                $('#scope-holder').addClass('d-none')
            }else{
                $('#scope_id').attr('required',true)
                $('#scope_id').val('').trigger('change')
                $('#scope-holder').removeClass('d-none')
            }
            if(!!scope[type]){
                var opt = $('<option>')
                        opt.attr('value','')
                        opt.attr('disabled',true)
                        opt.attr('selected',true)
                        opt.text('')
                    if('<?php echo isset($scope_id)? $scope_id : '' ?>' == 0)
                    $('#scope_id').append(opt)
                Object.keys(scope[type]).map(k=>{
                    var data = scope[type][k]
                    var opt = $('<option>')
                        opt.attr('value',data.id)
                        opt.text(data.name)
                        if('<?php echo isset($scope_id)? $scope_id : '' ?>' == data.id)
                        opt.attr('selected',true)
                    $('#scope_id').append(opt)
                    $('#scope_id').trigger('change')
                })
                $('#scope_id').attr('required',true)
            }else{
                $('#scope_id').attr('required',false)
            }
        })
        if('<?php echo isset($ptype)? $ptype : '' ?>' > 0){
            $('#ptype').trigger('change')
        }
        $('.view_data').click(function(){
            uni_modal("Attendance Log Details",'view_att.php?id='+$(this).attr('data-id'),'large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this attendance log of <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        $('.table th,.table td').addClass('align-middle px-2 py-1')
        dtable = $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:3 }
            ],
            lengthMenu: [ [25, 50, -1], [25, 50, "All"] ]
        })
        $('#print').click(function(){
            dtable.fnDestroy()
            var _p = $('#outprint').clone()
            var _h = $('head').clone()
            var el = $('<div>')
            if(_p.find('#list tbody tr').length <= 0){
                _p.find('#list tbody').append('<tr><th class="text-center py-1" colspan="4">No data</th></tr>')
            }
            el.append(_h)
            el.append('<h2 class="text-center fw-bold">Voting Result for <?php echo $_SESSION['election']['title'] ?></h2>')
            el.append('<hr/>')
            el.append('<?php echo addslashes($title) ?>')
            el.append(_p)
            
            var nw = window.open("","_blank","width=1000,height=900,top=50,left=250")
                     nw.document.write(el.html())
                     nw.document.close()
                     setTimeout(() => {
                        nw.print()
                        setTimeout(() => {
                            nw.close()
                            dtable = $('table').dataTable({
                                columnDefs: [
                                    { orderable: false, targets:3 }
                                ],
                                lengthMenu: [ [25, 50, -1], [25, 50, "All"] ]
                            })
                        }, 200);
                     }, 200);
        })
        $('#filter').submit(function(e){
            e.preventDefault();
            location.replace("./?page=result&" + $(this).serialize())
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_attendance',
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



<?php
// Prepare data for the pie chart
$candidate_names = [];
$candidate_votes = [];
$candidate_colors = [];

$qry = $conn->query("SELECT * FROM `position_list` WHERE status = 1 {$pwhere} ORDER BY order_by ASC");
while($row = $qry->fetchArray()):
    $candidate = $conn->query("SELECT *, (firstname || ' ' || middlename || ' ' || lastname || ' ' || suffix) as fullname FROM `candidate_list` WHERE position_id = '{$row['position_id']}' AND election_id = '{$_SESSION['election']['election_id']}' ORDER BY fullname ASC");
    while($crow = $candidate->fetchArray()):
        $vote = $conn->query("SELECT count(v.vote_id) as total FROM vote_list v INNER JOIN voter_list vl ON v.voter_id = vl.voter_id WHERE candidate_id = '{$crow['candidate_id']}' {$vwhere}")->fetchArray()['total'];
        $candidate_names[] = ucwords($crow['fullname']);
        $candidate_votes[] = (int)$vote;
        // Generate a random color for each candidate
        $candidate_colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    endwhile;
endwhile;
?>

<script>
const ctx = document.getElementById('myPieChart').getContext('2d');
const myPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($candidate_names); ?>,
        datasets: [{
            label: 'Votes',
            data: <?php echo json_encode($candidate_votes); ?>,
            backgroundColor: <?php echo json_encode($candidate_colors); ?>,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
        }
    }
});
</script>
