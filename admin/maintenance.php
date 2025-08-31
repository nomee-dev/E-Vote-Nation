
<style>
    .update_stat_elec:hover,.update_stat_position:hover{
        color:inherit !important;
        opacity: .95 !important;
        transform:scale(.8);
    }
    .sort-icon{
        cursor: move;
    }
</style>
<div class="card h-100 d-flex flex-column">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Maintenance</h3>
        <div class="card-tools align-middle">
            <!-- <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button> -->
        </div>
    </div>
    <div class="card-body flex-grow-1  overflow-auto">
        <div class="col-12 h-100  py-3">
            <div class="row h-100">
                <!-- Region -->
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Region List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_region" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add Region"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $region_arr = array();
                            $region_qry = $conn->query("SELECT * FROM `region_list` order by `name` asc");
                            while($row = $region_qry->fetchArray()):
                                $region_arr[$row['region_id']] = $row['name'];
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <?php echo $row['name'] ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_region btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Region Details" data-id="<?php echo $row['region_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_region btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete Region" data-id="<?php echo $row['region_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$region_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Province -->
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Province List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_province" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add Province"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $province_arr = array();
                            $province_qry = $conn->query("SELECT * FROM `province_list` order by `name` asc");
                            while($row = $province_qry->fetchArray()):
                                $province_arr[$row['province_id']] = $row['name']. (isset($region_arr[$row['region_id']]) ? ", ". $region_arr[$row['region_id']] : '');
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <?php echo $row['name'] ?> [Region: <?php echo isset($region_arr[$row['region_id']]) ? $region_arr[$row['region_id']] : '' ?>]
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_province btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Region Details" data-id="<?php echo $row['province_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_province btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete Region" data-id="<?php echo $row['province_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$province_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- District -->
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>District List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_district" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add District"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $district_arr = array();
                            $district_qry = $conn->query("SELECT * FROM `district_list` order by `name` asc");
                            while($row = $district_qry->fetchArray()):
                                $district_arr[$row['district_id']] = $row['name']. (isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '');
                            ?>
                            <li class="list-group-item d-flex align-items-center">
                                <div class="col-auto flex-grow-1">
                                    <div class="w-100 text-truncate"><?php echo $row['name'] ?><?php echo isset($province_arr[$row['province_id']]) ? ", ". $province_arr[$row['province_id']] : '' ?></div>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_district btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit District Details" data-id="<?php echo $row['district_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_district btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete District" data-id="<?php echo $row['district_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$district_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- City/Municipal -->
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>City/Municipal List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_city" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add City/Municipal"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $city_arr = array();
                            $city_qry = $conn->query("SELECT * FROM `city_list` order by `name` asc");
                            while($row = $city_qry->fetchArray()):
                                $city_arr[$row['city_id']] = $row['name']. (isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '');
                            ?>
                            <li class="list-group-item d-flex align-items-center">
                                <div class="col-10 flex-grow-1">
                                    <div>
                                        <div class="col-12 text-truncate"><?php echo $row['name'] ?><?php echo isset($district_arr[$row['district_id']]) ? ", ". $district_arr[$row['district_id']] : '' ?></div>
                                    </div>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_city btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit City/Municipal Details" data-id="<?php echo $row['city_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_city btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete City/Municipal" data-id="<?php echo $row['city_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$city_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Election -->
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Election List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_election" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add Election"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $dept_qry = $conn->query("SELECT * FROM `election_list` order by `title` asc");
                            while($row = $dept_qry->fetchArray()):
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <?php echo $row['title'] ?>
                                </div>
                                <div class="col-auto">
                                    <?php if($row['status'] == 1): ?>
                                        <a href="javascript:void(0)" class=" update_stat_elec badge bg-success bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "0" data-id="<?php echo $row['election_id'] ?>" data-name="<?php echo $row['title'] ?>"><small>Active</small></a>
                                        <?php else: ?>
                                        <a href="javascript:void(0)" class=" update_stat_elec badge bg-secondary bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "1" data-id="<?php echo $row['election_id'] ?>"  data-name="<?php echo $row['title'] ?>"><small>Inactive</small></a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_election btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Election Details" data-id="<?php echo $row['election_id'] ?>"  data-name="<?php echo $row['title'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_election btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete Election" data-id="<?php echo $row['election_id'] ?>"  data-name="<?php echo $row['title'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$dept_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Position -->
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Position List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="save_order" class="btn btn-dark btn-sm bg-gradient rounded-2 me-2" title="Save Position Order">Save Order</a>
                            <a href="javascript:void(0)" id="new_position" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add position"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group" id="position-list">
                            <?php 
                            $dept_qry = $conn->query("SELECT * FROM `position_list` order by `order_by` asc");
                            while($row = $dept_qry->fetchArray()):
                            ?>
                            <li class="list-group-item d-flex align-items-center" data-id="<?php echo $row['position_id'] ?>">
                                <div class="col-auto">
                                    <span class="fa fa-braille me-1 text-muted sort-icon"></span>
                                </div>
                                <div class="col-9 flex-grow-1">
                                    <div class="text-truncate">
                                        <span class="badge bg-primary rounded-circle me-2"><?php echo $row['max'] ?></span>
                                    <?php echo $row['name'] ?> <small class="text-muted">

                                    <?php 
                                        switch($row['type']){
                                            case 1:
                                                echo "National";
                                                break;
                                            case 2:
                                                echo "Regional";
                                                break;
                                            case 3:
                                                echo "Provincial";
                                                break;
                                            case 4:
                                                echo "District";
                                                break;
                                            case 5:
                                                echo "City";
                                                break;
                                        }
                                    ?>

                                    </small></div>
                                </div>
                                <div class="col-auto">
                                    <?php if($row['status'] == 1): ?>
                                        <a href="javascript:void(0)" class="update_stat_position badge bg-success bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "0" data-id="<?php echo $row['position_id'] ?>" data-name="<?php echo $row['name'] ?>"><small>Active</small></a>
                                        <?php else: ?>
                                        <a href="javascript:void(0)" class="update_stat_position badge bg-secondary bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "1" data-id="<?php echo $row['position_id'] ?>"  data-name="<?php echo $row['name'] ?>"><small>Inactive</small></a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_position btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Position Details" data-id="<?php echo $row['position_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>
                                    
                                    <a href="javascript:void(0)" class="delete_position btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete position" data-id="<?php echo $row['position_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$dept_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $("#position-list").sortable()
        $('#save_order').click(function(){
            var position_ids = [];
            $("#position-list li").each(function(){
                position_ids.push($(this).attr('data-id'))
            })
            if(position_ids.length <= 0)
            return false;
            $.ajax({
                url:'./../Actions.php?a=save_position_order',
                method:'POST',
                data:{order:position_ids},
                dataType:'json',
                error:err=>{
                    console.log(err)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else if(!!resp.msg){
                        alert("An error occured. Error:"+resp.msg)
                    }else{
                        alert("An error occured.")
                    }
                }
            })
        })
        // Region
        $('#new_region').click(function(){
            uni_modal('Add New Region',"manage_region.php")
        })
        $('.edit_region').click(function(){
            uni_modal('Edit Region Details',"manage_region.php?id="+$(this).attr('data-id'))
        })
        $('.delete_region').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Region List?",'delete_region',[$(this).attr('data-id')])
        })
        // Province
        $('#new_province').click(function(){
            uni_modal('Add New Province',"manage_province.php")
        })
        $('.edit_province').click(function(){
            uni_modal('Edit Province Details',"manage_province.php?id="+$(this).attr('data-id'))
        })
        $('.delete_province').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Province List?",'delete_province',[$(this).attr('data-id')])
        })

        // District
        $('#new_district').click(function(){
            uni_modal('Add New District',"manage_district.php")
        })
        $('.edit_district').click(function(){
            uni_modal('Edit District Details',"manage_district.php?id="+$(this).attr('data-id'))
        })
        $('.delete_district').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from District List?",'delete_district',[$(this).attr('data-id')])
        })

        // City
        $('#new_city').click(function(){
            uni_modal('Add New City/Municipal',"manage_city.php")
        })
        $('.edit_city').click(function(){
            uni_modal('Edit City/Municipal Details',"manage_city.php?id="+$(this).attr('data-id'))
        })
        $('.delete_city').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from City/Municipal List?",'delete_city',[$(this).attr('data-id')])
        })

        // Election
        $('#new_election').click(function(){
            uni_modal('Add New Election',"manage_election.php")
        })
        $('.edit_election').click(function(){
            uni_modal('Edit Election Details',"manage_election.php?id="+$(this).attr('data-id'))
        })
        $('.update_stat_elec').click(function(){
            var changeTo = $(this).attr('data-toStat') == 1 ? "Active" : "Inactive";
            _conf("Are you sure to change status of <b>"+$(this).attr('data-name')+"</b> to "+changeTo+"?",'update_stat_elec',[$(this).attr('data-id'),$(this).attr('data-toStat')])
        })
        $('.delete_election').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Election List?",'delete_election',[$(this).attr('data-id')])
        })
        // position
        $('#new_position').click(function(){
            uni_modal('Add New Position',"manage_position.php")
        })
        $('.edit_position').click(function(){
            uni_modal('Edit Position Details',"manage_position.php?id="+$(this).attr('data-id'))
        })
        $('.update_stat_position').click(function(){
            var changeTo = $(this).attr('data-toStat') == 1 ? "Active" : "Inactive";
            _conf("Are you sure to change status of <b>"+$(this).attr('data-name')+"</b> to "+changeTo+"?",'update_stat_position',[$(this).attr('data-id'),$(this).attr('data-toStat')])
        })
        $('.delete_position').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Position List?",'delete_position',[$(this).attr('data-id')])
        })
       
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:6 }
            ]
        })
    })
    function  update_stat_elec($id,$status){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=update_stat_elec',
            method:'POST',
            data:{id:$id,status:$status},
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
    function update_stat_position($id,$status){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=update_stat_position',
            method:'POST',
            data:{id:$id,status:$status},
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
    function delete_election($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_election',
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
    function delete_position($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_position',
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
    function delete_region($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_region',
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
    function delete_province($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_province',
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
    function delete_district($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_district',
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
    
    function delete_city($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_city',
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