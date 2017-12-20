$(function() {
	function a() {
		var a = h.type - 1;
		f = $("#category").combo({
			data: [{
				id: 1,
				name: "现金"
			}, {
				id: 2,
				name: "银行存款"
			}],
			value: "id",
			text: "name",
			width: 197,
			defaultSelected: a || void 0,
			editable: !0,
			disabled: 999999 === h.id
		}).getCombo(),		
		
		currencyCombo = $("#currency").combo({
			data: function() {
				return parent.parent.SYSTEM.currencyInfo
			},
		value: "id",
		text: "name",
		width: 197,
		defaultSelected: Public.ToCurrencyIndex(h.currency),
	}).getCombo(),
		
	h.id&& ($("#name").val(h.name),
				$("#account").val(h.account),
				$("#bank").val(h.bank),
				$("#currency").val(h.currency),
				$("#balance").val(Public.numToCurrency(h.amount)), 
				$("#date").val(h.date), 999999 === h.id 
				&& ($("#date").attr("disabled", "disabled").addClass("ui-input-dis")));

		
	}
	
	function b() {
		var a = "add" == g ? ["保存", "关闭"] : ["确定", "取消"];
		api.button({
			id: "confirm",
			name: a[0],
			focus: !0,
			callback: function() {
				return e(g, h.id), !1
			}
		}, {
			id: "cancel",
			name: a[1]
		})
	}

	function c() {
		k.datepicker({
			onClose: function() {
				var a = /^\d{4}-((0?[1-9])|(1[0-2]))-\d{1,2}/;
				a.test(k.val()) || k.val("")
			}
		}), k.datepicker("setDate", new Date), k.blur(function() {
			var a = k.val();
			"" == a && k.datepicker("setDate", new Date)
		}), $("#balance").keypress(Public.numerical).focus(function() {
			this.value = Public.currencyToNum(this.value), $(this).select()
		}).blur(function() {
			this.value = Public.numToCurrency(this.value)
		}), Public.limitLength($("#name"), 30), Public.bindEnterSkip($("#manage-wrap"), e, g, h.id), d()
	}

	function d() {
		$("#manage-form").validate({
			rules: {
				name: {
					required: !0
				}
			},
			messages: {
				name: {
					required: "账户名称不能为空"
				}
			},
			errorClass: "valid-error"
		})
	}

	function e(a, b) {
		if (!$("#manage-form").validate().form()) return void $("#manage-form").find("input.valid-error").eq(0).focus();
		var c = /,/g,
			e = $.trim($("#name").val()),
			g = f.getValue(),
			j = "add" == a ? "新增账户" : "修改账户",
			k = $.trim($("#balance").val()).replace(c, ""),
			m = $.trim($("#date").val());
		params = h.id ? {
			id: b,
			name: e,
			account:$.trim($("#account").val()),
			bank:$.trim($("#bank").val()),
			currency:currencyCombo.getValue(),
			type: g,
			amount: k,
			date: m
		} : {
			name: e,
			account:$.trim($("#account").val()),
			bank:$.trim($("#bank").val()),
			currency:currencyCombo.getValue(),
			type: g,
			amount: k,
			date: m
		}, Public.ajaxPost("../basedata/settAcct/" + ("add" == a ? "add" : "update"), params, function(b) {
			if (200 == b.status) {
				if (parent.parent.Public.tips({
					content: j + "成功！"
				}), "add" == a) l.SYSTEM.accountInfo.push(b.data);
				else for (var c = l.SYSTEM.accountInfo.length - 1; c >= 0; c--) l.SYSTEM.accountInfo[c].id == b.data.id && (l.SYSTEM.accountInfo[c] = b.data);
				i && "function" == typeof i && i(b.data, a, window)
			} else parent.parent.Public.tips({
				type: 1,
				content: j + "失败！" + b.msg
			})
		})
	}
	api = frameElement.api;
	var f, g = api.data.oper,
		h = api.data.rowData || {},
		i = api.data.callback,
		k = $("#date"),
		l = Public.getDefaultPage();
		var currencyCombo;
	b(), c(), a(), resetForm = function(a) {
		$("#manage-form").validate().resetForm(), $("#name").val(""), $("#balance").val(""), k.datepicker("setDate", new Date)

	}, window.resetForm = resetForm
});