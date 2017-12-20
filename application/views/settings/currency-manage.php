<?php $this->load->view('header');?>

<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo sys_skin()?>';
}catch(e){
}
//ctrl+F5 增加版本号来清空iframe的缓存的
$(document).keydown(function(event) {
	/* Act on the event */
	if(event.keyCode === 116 && event.ctrlKey){
		var defaultPage = Public.getDefaultPage();
		var href = defaultPage.location.href.split('?')[0] + '?';
		var params = Public.urlParam();
		params['version'] = Date.parse((new Date()));
		for(i in params){
			if(i && typeof i != 'function'){
				href += i + '=' + params[i] + '&';
			}
		}
		defaultPage.location.href = href;
		event.preventDefault();
	}
});
</script>

<style>
body{background: #fff;}
.manage-wrap{margin: 20px auto 10px;width: 400px;}
.manage-wrap .ui-input{width: 100px;font-size:12px;}
.row-item{float:left ; width:50%;}
.mod-form-rows .label-wrap {font-size: 12px;}
</style>
</head>
<body>
<div id="manage-wrap" class="manage-wrap">
	<form id="manage-form" action="">
		<ul class="mod-form-rows">
			<li class="row-item">
				<div class="label-wrap"><label for="name">名称:</label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="name" id="name"></div>
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="code">代码:</label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="code" id="code"></div>
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="symbol">符号:</label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="symbol" id="symbol"></div>
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="rate">默认汇率:</label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="rate" id="rate"></div>
			</li>
			<li class="row-item" style="width:100%;" >
				<div class="label-wrap"><label for="note">备注:</label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" style="width:300px;" name="note" id="note"></div>
			</li>
		</ul>
	</form>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/currencyManager.js?v=20170918"></script>
</body>
</html>

 