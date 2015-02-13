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
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-person" class="form-horizontal">
				<input type="hidden" name ="person_id" value="{DATA.person_id}" />
 				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-2 control-label" for="input-name">{LANG.person_team}</label>
					<div class="col-sm-10">
						<select name="team_id" class="form-control">
							<option value="0">{LANG.person_select_team}</option>
							<!-- BEGIN: team -->
							<option value="{TEAM.key}" {TEAM.selected}>{TEAM.name}</option>
							<!-- END: team -->
						</select>
						<!-- BEGIN: error_team --><div class="text-danger">{error_team}</div><!-- END: error_team -->
					</div>
				</div>
                <div class="form-group required">
					<label class="col-sm-2 control-label" for="input-name">{LANG.person_name}</label>
					<div class="col-sm-10">
						<input type="text" name="name" value="{DATA.name}" placeholder="{LANG.person_name}" id="input-name" class="form-control" />
						<!-- BEGIN: error_name --><div class="text-danger">{error_name}</div><!-- END: error_name -->
					</div>
				</div>
                <div class="form-group">
					<label class="col-sm-2 control-label" for="input-phone">{LANG.person_phone}</label>
					<div class="col-sm-10">
						<input type="text" name="phone" value="{DATA.phone}" placeholder="{LANG.person_phone}" id="input-phone" class="form-control" />
						<!-- BEGIN: error_phone --><div class="text-danger">{error_phone}</div><!-- END: error_phone -->
					</div>
				</div>
                <div class="form-group">
					<label class="col-sm-2 control-label" for="input-email">{LANG.person_email}</label>
					<div class="col-sm-10">
						<input type="text" name="email" value="{DATA.email}" placeholder="{LANG.person_email}" id="input-email" class="form-control" maxlength="128" />
						<!-- BEGIN: error_email --><div class="text-danger">{error_email}</div><!-- END: error_email -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-yahoo">{LANG.person_yahoo}</label>
					<div class="col-sm-10">
						<input type="text" name="yahoo" value="{DATA.yahoo}" placeholder="{LANG.person_yahoo}" id="input-yahoo" class="form-control" maxlength="128" />
						<!-- BEGIN: error_yahoo --><div class="text-danger">{error_yahoo}</div><!-- END: error_yahoo -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-yahoo">{LANG.person_yahoo_icon}</label>
					<div class="col-sm-10">
						<div class="form-inline">
							<div class="form-group fixgroup" style="margin-left: 0px;">
								<select name="yahoo_icon" class="form-control" style="min-width: 120px">
									<!-- BEGIN: yahoo_icon -->
									<option value="{YAHOO.key}" {YAHOO.selected}>{YAHOO.name}</option>
									<!-- END: yahoo_icon -->
								</select>
								<img id="yahoo_icon_change" class="fixlogo" src="http://opi.yahoo.com/online?u=dangdinhtu&m=g&t=2" style="max-height:40px">
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-yahoo">{LANG.person_skype}</label>
					<div class="col-sm-10">
						<input type="text" name="skype" value="{DATA.skype}" placeholder="{LANG.person_skype}" id="input-skype" class="form-control" maxlength="128" />
						<!-- BEGIN: error_skype --><div class="text-danger">{error_skype}</div><!-- END: error_skype -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-yahoo">{LANG.person_skype_icon}</label>
					<div class="col-sm-10">
						<div class="form-inline">
							<div class="form-group fixgroup" style="margin-left: 0px;">
								<select name="skype_icon" class="form-control" style="min-width: 120px">
									<!-- BEGIN: skype_icon -->
									<option value="{SKYPE.key}" {SKYPE.selected}>{SKYPE.name}</option>
									<!-- END: skype_icon -->
								</select>
								<img id="skype_icon_change" class="fixlogo" src="http://mystatus.skype.com/smallclassic/dlinhvan" style="max-height:40px">
							</div>
						</div>
						
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
$('select[name="yahoo_icon"]').on('change', function() {
	var type = $(this).val();
	$('#yahoo_icon_change').attr("src",'http://opi.yahoo.com/online?u=dangdinhtu&m=g&t=' + type);
});
$('select[name="skype_icon"]').on('change', function() {
	var type = $(this).val();
	$('#skype_icon_change').attr("src",'http://mystatus.skype.com/'+ type +'/dangdinhtu');
});
 
</script>
<!-- END: main -->