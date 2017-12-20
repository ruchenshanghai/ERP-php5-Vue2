<?php $this->load->view('header');?>

<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
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

<link href="<?php echo base_url()?>/statics/css/authority.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="wrapper authority-wrap">
  <div class="mod-inner">
       
      <div class="authority-ctn-wrap">
         
        <div class="register-wrap">
            <h3>编辑用户</h3>
            <form action="#" id="modifyForm" class="register-form">
              <ul class="mod-form-rows">
                <li class="row-item">
                  <div class="label-wrap">
                    <label for="userName">用户名</label>
                  </div>
                  <div class="ctn-wrap">
                    <input type="text" class="ui-input" id="userName" name="userName"/>
                    <p class="msg">用户名由4-20个英文字母或数字组成（不支持中文，不区分大小写字母）。一旦创建成功，不可修改。</p>
                  </div>
                </li>
                <li class="row-item">
                  <div class="label-wrap">
                    <label for="realName">真实姓名</label>
                  </div>
                  <div class="ctn-wrap">
                      <input type="text" class="ui-input" id="realName" name="realName"/>
                      <p class="msg">真实姓名将应用在单据和账表打印中，请如实填写</p>
                  </div>
                </li>
                  <li class="row-item">
                      <div class="label-wrap">
                          <label for="">邮箱</label>
                      </div>
                      <div class="ctn-wrap">
                          <input type="text" class="ui-input" id="email" name="email"/>
                      </div>
                  </li>
                <li class="row-item">
                  <div class="label-wrap">
                    <label for="">常用手机</label>
                  </div>
                  <div class="ctn-wrap">
                      <input type="text" class="ui-input" id="userMobile" name="userMobile"/>
                      <p class="msg">手机将作为找回密码的重要依据</p>
                  </div>
                </li>
              </ul>
              <div class="btn-row">
                <a href="authority" class="ui-btn mrb">返回列表</a>
                  <a href="#" class="ui-btn ui-btn-sp" id="modifyBtn">确定</a>
              </div>
            </form>
        </div>
      <div>
  </div>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/authorityEditor.js?ver=20171020"></script>
</body>
</html>