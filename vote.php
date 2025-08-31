<?php 
$is_vote = $conn->query("SELECT count(vote_id) as `count` FROM `vote_list` where election_id = '{$_SESSION['election']['election_id']}' and voter_id = '{$_SESSION['voter_id']}' ")->fetchArray()[0];
if($is_vote > 0){
    echo "<script>alert('You have already submitted your vote ballot.');location.replace('./');</script>";
}
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
        $candidate = $conn->query("SELECT *,(firstname || ' ' || middlename || ' '|| lastname|| ' '|| suffix) as fullname FROM `candidate_list` $swhere and position_id = '{$row['position_id']}' and election_id = '{$_SESSION['election']['election_id']}' order by fullname asc");
        if($count> 0):
    ?>
    <h3 class="text-center"><?php echo $row['name'] ?></h3>
    <hr>
    <center><small>You are only allowed to choose <?php echo $row['max'] ?> candidate</small></center>
    <div class="row row-cols-1 row-cols-sm-1 row-cols-md-3 row-cols-xl-5 gx-3 gy-3 justify-content-center vote-position py-4 my-3" data-id ="<?php echo $row['position_id'] ?>">
        <?php while($crow = $candidate->fetchArray()): ?>
            <div class="col">
                <div class="card candidate-item shadow-sm" data-max="<?php echo $row['max'] ?>" data-position='<?php echo $row['position_id'] ?>' data-id='<?php echo $crow['candidate_id'] ?>'>
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
    <?php endif; ?>
    <?php endwhile; ?>
</div>
<hr>
<center><small class='text-muted'><i>Please Review your selected candidates for each positions before submitting your vote.</i></small></center>
<center>
    <button class="btn btn-primary btn-lg rounded-pill px-5" id="submit_btn" type="button">Submit Vote</button>
</center>
<script>
    var check_icon = '<span class="position-absolute check-icon"><i class="fa fa-check"></i></span>';

    $(function(){
        $('.candidate-item').click(function(){
            var pos = $(this).attr('data-position')
            var max = $(this).attr('data-max')
            var checked = $('.candidate-item.checked[data-position="'+pos+'"]').length
            if($(this).hasClass('checked') == false){
                if(checked < max){
                    $(this).addClass('checked')
                    $(this).find('.candidate-img-holder').append(check_icon)
                }else{
                    alert("You have reach the maximum selection for the position")
                }
            }else{
                $(this).removeClass('checked')
                $(this).find('.check-icon').remove()
            }
        })
        $('#submit_btn').click(function(){
            if($('.candidate-item.checked').length <= 0){
                alert('Your Ballot is empty');
                return false;
            }
            var _this = $(this)
            var btn_txt = _this.text()
            _this.text('Saving Ballot ...').attr('disabled',true)
            var votes = {}
            var i = 0;
            $('.candidate-item.checked').each(function(){
                if(!votes[$(this).attr('data-position')])
                    votes[$(this).attr('data-position')] = [];
                votes[$(this).attr('data-position')][votes[$(this).attr('data-position')].length] = $(this).attr('data-id');
            })

            $.ajax({
                url:'./Actions.php?a=save_vote',
                method:'POST',
                data:{votes:votes},
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert('An error occured')
                    _this.text(btn_txt).attr('disabled',false)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.replace('./?page=ballot_preview');
                    }else if(!!resp.msg){
                        alert('An error occured. Error: '+resp.msg)
                    }
                    _this.text(btn_txt).attr('disabled',false)
                }
            })

        })
    })
</script>