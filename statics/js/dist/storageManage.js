function initField() {
	rowData.id && ($("#name").val(rowData.name),
	$("#address").val(rowData.address),$("#manager").val(rowData.manager),$("#email").val(rowData.email),$("#phone").val(rowData.phone))
}
function initEvent() {
	Public.bindEnterSkip($("#manage-wrap"), postData, oper, rowData.id), initValidator()
}
function initPopBtns() {
	var a = "add" == oper ? ["保存", "关闭"] : ["确定", "取消"];
	api.button({
		id: "confirm",
		name: a[0],
		focus: !0,
		callback: function() {
			return postData(oper, rowData.id), !1
		}
	}, {
		id: "cancel",
		name: a[1]
	})
}
function initValidator() {
	$("#manage-form").validate({
		rules: {
			name: {
				required: !0
			},
			address: {
				required: !0
			},
			manager: {
				required: !0
			},
			phone: {
				required: !0
			}
		},
		messages: {
			name: {
				required: "仓库名称不能为空"
			},
			address: {
				required: "仓库地址不能为空"
			},
			manager: {
				required: "仓库联系人不能为空"
			},
			phone: {
				required: "联系人手机不能为空"
			}
		},
		errorClass: "valid-error"
	})
}
function postData(a, b) {
	if (!$("#manage-form").validate().form()) return void $("#manage-form").find("input.valid-error").eq(0).focus();
	var d = $.trim($("#name").val()),
		e = "add" == a ? "新增仓库" : "修改仓库";
	params = rowData.id ? {
		locationId: b,
		name: d,
		address: $.trim($("#address").val()),
		manager: $.trim($("#manager").val()),
		email: $.trim($("#email").val()),
		phone: $.trim($("#phone").val()),
		isDelete: rowData["delete"]
	} : {
		name: d,
		address: $.trim($("#address").val()),
		manager: $.trim($("#manager").val()),
		email: $.trim($("#email").val()),
		phone: $.trim($("#phone").val()),
		isDelete: !1
	}, Public.ajaxPost("../basedata/invlocation/" + ("add" == a ? "add" : "update"), params, function(b) {
		200 == b.status ? (parent.parent.Public.tips({
			content: e + "成功！"
		}), callback && "function" == typeof callback && callback(b.data, a, window)) : parent.parent.Public.tips({
			type: 1,
			content: e + "失败！" + b.msg
		})
	})
}
function resetForm(a) {
	$("#manage-form").validate().resetForm(), $("#name").val("")
}
var api = frameElement.api,
	oper = api.data.oper,
	rowData = api.data.rowData || {},
	callback = api.data.callback;
initPopBtns(), initField(), initEvent();