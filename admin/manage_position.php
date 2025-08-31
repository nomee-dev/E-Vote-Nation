<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `position_list` where position_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}

?>
<div class="container-fluid">
    <form action="" id="position-form">
        <input type="hidden" name="id" value="<?php echo isset($position_id) ? $position_id : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" autofocus name="name" id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
        </div>
        <div class="form-group">
            <label for="max" class="control-label">Max Selection</label>
            <input type="number" min ="1" max="20" name="max" id="max" required class="form-control form-control-sm rounded-0" value="<?php echo isset($max) ? $max : 1 ?>">
        </div>
        <div class="form-group">
            <label for="type" class="control-label">Scope</label>
            <select name="type" id="type" class="form-select form-select-sm rounded-0">
                <option value="1" <?php echo (isset($type) && $type == 1 ) ? 'selected' : '' ?>>National</option>
                <option value="2" <?php echo (isset($type) && $type == 2 ) ? 'selected' : '' ?>>Regional</option>
                <option value="3" <?php echo (isset($type) && $type == 3 ) ? 'selected' : '' ?>>Provincial</option>
                <option value="4" <?php echo (isset($type) && $type == 4 ) ? 'selected' : '' ?>>District</option>
                <option value="5" <?php echo (isset($type) && $type == 5 ) ? 'selected' : '' ?>>City</option>
            </select>
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="form-select form-select-sm rounded-0">
                <option value="1" <?php echo (isset($status) && $status == 1 ) ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo (isset($status) && $status == 0 ) ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#position-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_position',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
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
                        if("<?php echo isset($position_id) ?>" != 1)
                        _this.get(0).reset();
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