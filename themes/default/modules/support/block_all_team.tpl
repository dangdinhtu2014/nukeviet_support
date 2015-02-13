<!-- BEGIN: main -->
<style>
ul.byteam{list-style: none}
ul.byteam li.group {
	text-align: center;
	font-size: 16px;
	color: #fff;
	font-weight: bold;
	padding-top: 0;
}
ul.byteam li .name, ul.byteam li .email {
	text-align: center;
	padding-top: 0;
	font-weight: bold;
	color: #fff
}
ul.byteam li .yahoo {
	text-align: center;
	padding-top: 2px;
}
ul.byteam li .skype {
	text-align: center;
	padding-top: 2px;
}
ul.byteam li{border-bottom: 1px #ccc solid;padding: 10px 0;}
</style>
<!-- BEGIN: team -->
<ul class="byteam">
	<!-- BEGIN: show_team -->
	<li class="group">
		{TEAM.name}  
	</li>
	<!-- END: show_team -->
	<!-- BEGIN: loop -->
	<li>
		<div class="name">{LOOP.name}</div>	
		<!-- BEGIN: show_email --><div class="email">{LOOP.email}</div><!-- END: show_email -->	
		<!-- BEGIN: show_yahoo --><div class="yahoo"><img id="yahoo_icon_change" class="fixlogo" src="http://opi.yahoo.com/online?u={LOOP.yahoo}&amp;m=g&amp;t={LOOP.yahoo_icon}"></div><!-- show_END: yahoo -->	
		<!-- BEGIN: show_skype --><div class="skype"><img id="skype_icon_change" class="fixlogo" src="http://mystatus.skype.com/{LOOP.skype_icon}/{LOOP.skype}" ></div><!-- END: show_skype -->		
 	</li>
	<!-- END: loop -->
</ul>
<div class="clear"></div> 
<!-- END: team -->
<!-- END: main -->