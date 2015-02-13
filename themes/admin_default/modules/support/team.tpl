<!-- BEGIN: main -->
<div id="content"> 
<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.team_list}</h3> 
			 <div class="pull-right">
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-primary"><i class="fa fa-plus"></i></a>
				<button type="button" data-toggle="tooltip" data-placement="top" title="{LANG.delete}" class="btn btn-danger" id="button-delete">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="#" method="post" enctype="multipart/form-data" id="form-category">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-md-0 text-center" ><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
								<td class="col-md-3 text-left"><a href="{URL_NAME}">{LANG.team_name}</a> </td>
								<td class="col-md-3 text-left"> <strong>{LANG.team_phone} </strong></td>
								<td class="col-md-2 text-left"> <strong>{LANG.team_email} </strong></td>
								<td class="col-md-2 text-left"> <strong>{LANG.weight} </strong></td>
								<td class="col-md-2 text-right"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.team_id}">
								<td class="text-left"> <input type="checkbox" name="selected[]" value="{LOOP.team_id}"> </td>
								<td class="text-left">{LOOP.name}</td>
								<td class="text-left">{LOOP.phone}</td>
								<td class="text-left">{LOOP.email}</td>
								<td class="text-left">
									<select id="change_weight_{LOOP.team_id}" onchange="nv_change_team('{LOOP.team_id}','weight');" class="form-control">
										<!-- BEGIN: weight -->
										<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.name}</option>
										<!-- END: weight -->
									</select>
								</td>
								<td class="text-right">
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
									&nbsp;&nbsp;
									<a href="javascript:void(0);" onclick="delete_team('{LOOP.team_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger"><i class="fa fa-trash-o"></i>
								</td>
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
			</form>
			<!-- BEGIN: generate_page -->
			<div class="row">
				<div class="col-sm-12 text-left">
				
				<div style="clear:both"></div>
				{GENERATE_PAGE}
				
				</div>
				 
			</div>
			<!-- END: generate_page -->
		</div>
		<div id="cat-delete-area">&nbsp;</div>
	</div>
</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/footer.js"></script>
<script type="text/javascript">

function delete_team(team_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=team&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'team_id=' + team_id + '&token=' + token,
			beforeSend: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-delete').prop('disabled', true);
			},	
			complete: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				$('.alert').remove();

				if (json['error']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					 $.each(json['id'], function(i, id) {
						$('#group_' + id ).remove();
					});
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

$('#button-delete').on('click', function() {
	if(confirm('{LANG.confirm}')) 
	{
		var listid = [];
		$("input[name=\"selected[]\"]:checked").each(function() {
			listid.push($(this).val());
		});
		if (listid.length < 1) {
			alert('{LANG.confirm}');
			return false;
		}
	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=team&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'listid=' + listid + '&token={TOKEN}',
			beforeSend: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-delete').prop('disabled', true);
			},	
			complete: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				$('.alert').remove();
				
				
				
				if (json['error']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					 $.each(json['id'], function(i, id) {
						$('#group_' + id ).remove();
					});
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}	
});
function nv_change_team(team_id, mod) {
    var nv_timer = nv_settimeout_disable('change_' + mod + '_' + team_id, 5000);
    var new_vid = $('#change_' + mod + '_' + team_id).val();
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=team&action=' + mod + '&nocache=' + new Date().getTime(), 'team_id=' + team_id + '&new_vid=' + new_vid, function(res) {
        var r_split = res.split("_");
        if (r_split[0] != 'OK') {
            alert(nv_is_change_act_confirm[2]);
            clearTimeout(nv_timer);
        } else {
            window.location.href = window.location.href;
        }
    });
    return;
}
$('button[type=\'submit\']').on('click', function() {
	$("form[id*='form-']").submit();
});
</script>
<!-- END: main -->