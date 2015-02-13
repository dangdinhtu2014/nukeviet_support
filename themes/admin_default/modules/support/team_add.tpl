<!-- BEGIN: main -->
<div id="content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <br>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="Save"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="Cancel"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-team" class="form-horizontal">
				<input type="hidden" name ="team_id" value="{DATA.team_id}" />
 				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-2 control-label" for="input-full-name">{LANG.team_name}</label>
					<div class="col-sm-10">
						<input type="text" name="name" value="{DATA.name}" placeholder="{LANG.team_name}" id="input-full-name" class="form-control" />
						<!-- BEGIN: error_name --><div class="text-danger">{error_name}</div><!-- END: error_name -->
					</div>
				</div>
				 
                <div class="form-group">
					<label class="col-sm-2 control-label" for="input-phone">{LANG.team_phone}</label>
					<div class="col-sm-10">
						<input type="text" name="phone" value="{DATA.phone}" placeholder="{LANG.team_phone}" id="input-phone" class="form-control" />
						<!-- BEGIN: error_phone --><div class="text-danger">{error_phone}</div><!-- END: error_phone -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-email">{LANG.team_email}</label>
					<div class="col-sm-10">
						<input type="text" name="email" value="{DATA.email}" placeholder="{LANG.team_email}" id="input-email" class="form-control" maxlength="128" />
						<!-- BEGIN: error_email --><div class="text-danger">{error_email}</div><!-- END: error_email -->
					</div>
				</div>
				<div align="center">
					<input class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-primary" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>       
			</form>
		</div>
	</div>
</div>									
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/footer.js"></script>
<script type="text/javascript">
$('button[type=\'submit\']').on('click', function() {
	$("form[id*='form-']").submit();
});
</script>
<!-- END: main -->