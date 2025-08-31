<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `candidate_list` where candidate_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}

$scope = array();
$region_arr = array();
$region_qry = $conn->query("SELECT *,region_id as id FROM `region_list` order by `name` asc");
while($row = $region_qry->fetchArray()):
    $region_arr['region_id'] = $row['name'];
    $scope[2][]=$row;
endwhile;

$province_arr = array();
$province_qry = $conn->query("SELECT *,province_id as id FROM `province_list` order by `name` asc");
while($row = $province_qry->fetchArray()):
    $province_arr[$row['province_id']] = $row['name'] .(isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
    $row['name'] = $row['name']. (isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
    $scope[3][]=$row;
endwhile;

$district_arr = array();
$district_qry = $conn->query("SELECT *,district_id as id FROM `district_list` order by `name` asc");
while($row = $district_qry->fetchArray()):
    $district_arr[$row['district_id']] = $row['name'] . (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
    $row['name'] = $row['name']. (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
    $scope[4][]=$row;
endwhile;

$city_arr = array();
$city_qry = $conn->query("SELECT *,city_id as id FROM `city_list` order by `name` asc");
while($row = $city_qry->fetchArray()):
    $city_arr[$row['city_id']] = $row['name']. (isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '');
    $row['name'] = $row['name']. (isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '');
    $scope[5][]=$row;
endwhile;
?>
<style>
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
<div class="container-fluid">
    <form action="" id="candidate-form">
        <input type="hidden" name="id" value="<?php echo isset($candidate_id) ? $candidate_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="firstname" class="control-label">First Name</label>
                        <input type="text" name="firstname" id="firstname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($firstname) ? $firstname : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="middlename" class="control-label">Middle Name</label>
                        <input type="text" name="middlename" id="middlename" required class="form-control form-control-sm rounded-0" placeholder="(optional)" value="<?php echo isset($middlename) ? $middlename : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lastname" class="control-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($lastname) ? $lastname : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="suffix" class="control-label">Suffix</label>
                        <input type="text" name="suffix" id="suffix" placeholder="(optional)" class="form-control form-control-sm rounded-0" value="<?php echo isset($suffix) ? $suffix : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="position_id" class="control-label">Position</label>
                        <select name="position_id" id="position_id" class="form-select form-select-sm select2 rounded-0">
                            <option <?php echo (!isset($position_id)) ? 'selected' : '' ?> disabled>Please Select Position</option>
                            <?php
                            $position = $conn->query("SELECT * FROM position_list where `status` = 1 ".(isset($position_id) ? " or position_id ='{$position_id}'" : "")." order by `order_by` asc");
                            while($row= $position->fetchArray()):
                            ?>
                                <option value="<?php echo $row['position_id'] ?>" <?php echo (isset($position_id) && $position_id == $row['position_id'] ) ? 'selected' : '' ?> data-type= "<?php echo $row['type'] ?>"><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6" id="scope-holder">
                    <div class="form-group">
                        <label for="scope_id" class="control-label">Scope</label>
                        <select name="scope_id" id="scope_id" class="form-select form-select-sm select2 rounded-0">
                            <option <?php echo (!isset($scope_id)) ? 'selected' : '' ?> disabled>Please Select Position</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="avatar" class="control-label">Image</label>
                        <input type="file" name="avatar" id="avatar" required class="form-control form-control-sm rounded-0" accept="image/png,image/jpeg" <?php echo !isset($candidate_id) ? 'required' : '' ?> onchange="display_img(this)">
                    </div>
                    <div class="form-group text-center mt-2">
                        <img src="<?php echo isset($candidate_id) && is_file('./../avatars/'.$candidate_id.'.png') ? './../avatars/'.$candidate_id.'.png' : './../images/no-image-available.png' ?>" id="logo-img" alt="Avatar">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    var scope,
    region_arr,
    province_arr,
    district_arr,
    city_arr;
    scope = $.parseJSON('<?php echo json_encode($scope) ?>')
    region_arr = $.parseJSON('<?php echo json_encode($region_arr) ?>')
    province_arr = $.parseJSON('<?php echo json_encode($province_arr) ?>')
    district_arr = $.parseJSON('<?php echo json_encode($district_arr) ?>')
    city_arr = $.parseJSON('<?php echo json_encode($city_arr) ?>')
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
        
        $('#position_id').change(function(){
            var pid= $(this).val()
            var type = $('#position_id option[value="'+pid+'"]').attr('data-type');
            $('#scope_id').html('')
            console.log(type)
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
                    if('<?php echo !isset($scope_id) ?>' == 1)
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
            }
        })
        if('<?php echo isset($candidate_id) ?>' == 1){
            $('#position_id').trigger('change')
        }
        $('#candidate-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_candidate',
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
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($candidate_id) ?>" != 1)
                        _this.get(0).reset();
                        $('.select2').trigger('change')
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>