var curRow, curCol, loading, SYSTEM = system = parent.SYSTEM, billRequiredCheck = system.billRequiredCheck,
    hideCustomerCombo = !1, urlParam = Public
        .urlParam(), disEditable = urlParam.disEditable, defaultPage = Public
        .getDefaultPage(), qtyPlaces = Number(parent.SYSTEM.qtyPlaces), THISPAGE = {
        init: function (a) {
            this.mod_PageConfig = Public.mod_PageConfig.init("salesDischarge"), SYSTEM.isAdmin !== !1, this
                .loadGrid(a), this.initDom(a), this.initCombo(), a.id > 0
            && a.checked
                ? this.disableEdit()
                : (this.editable = !0, $("#grid").jqGrid("setGridParam", {
                    cellEdit: !0
                })), this.addEvent()
        },
        initDom: function (a) {
            var b = this;
            this.$_date = $("#date").val(system.endDate),
                // billNo
                this.$_billNo = $("#number"), this.$_note = $("#note"), this.$_toolTop = $("#toolTop"), this.$_toolBottom = $("#toolBottom"), this.$_userName = $("#userName"), this.$_modifyTime = $("#modifyTime"), this.$_checkName = $("#checkName"), this.$_note
                .placeholder(), parent.$("#page-tab").find("li.l-selected")
                .children("a").html("销售出库单");

            this.$_date.datepicker({
                onSelect: function (a) {
                    if (!(originalData.id > 0)) {
                        var c = a.format("yyyy-MM-dd");
                        b.$_billNo.text(""), Public
                            .ajaxPost(
                                "../basedata/systemProfile/generateDocNo?action=generateDocNo",
                                {
                                    billType: "SALE",
                                    billDate: c
                                }, function (a) {
                                    200 === a.status
                                        ? b.$_billNo
                                            .text(a.data.billNo)
                                        : parent.Public.tips({
                                            type: 1,
                                            content: a.msg
                                        })
                                })
                    }
                }
            });

            a.description && this.$_note.val(a.description);

            var c = '<a id="save" class="ui-btn">保存</a>', d = '<a href="../scm/invSd/toPdf?action=toPdf&id='
                + a.id
                + '" target="_blank" id="print" class="ui-btn">打印</a><a id="edit" class="ui-btn">保存</a>',
                e = '<a href="../scm/invSd/toPdf?action=toPdf&id='
                    + a.id + '" target="_blank" id="print" class="ui-btn">打印</a>', f = "", g = "";
            billRequiredCheck
                ? (f = '<a class="ui-btn" id="audit" ">审核</a>', g = '<a class="ui-btn" id="reAudit">反审核</a>')
                : this.$_checkName.parent().hide();
            var h = '<a class="ui-btn-prev" id="prev" title="上一张"><b></b></a><a class="ui-btn-next" id="next" title="下一张"><b></b></a>';
            this.btn_save = c, this.btn_edit = d, this.btn_audit = f, this.btn_view = e, this.btn_reaudit = g;
            if (a.id > 0) {
                (this.$_billNo.text(a.billNo), this.$_date.val(a.date),

                    "edit" === a.status ? this.$_toolBottom.html("<span id=groupBtn>"
                        + d + f + "</span>" + h) : a.checked
                        ? ($("#mark").addClass("has-audit"), this.$_toolBottom
                            .html('<span id="groupBtn">' + e + g + "</span>"
                                + h))
                        : this.$_toolBottom.html('<span id="groupBtn">' + e
                            + "</span>" + h), this.idList = parent.cacheList.purchaseId
                    || [], this.idPostion = $
                    .inArray(String(a.id), this.idList), this.idLength = this.idList.length, 0 === this.idPostion
                && $("#prev").addClass("ui-btn-prev-dis"), this.idPostion === this.idLength
                - 1
                && $("#next").addClass("ui-btn-next-dis"), this.$_userName
                    .html(a.userName), this.$_modifyTime.html(a.modifyTime), this.$_checkName
                    .html(a.checkName));

                $("#orderid").parent().remove();

            } else {
                // this.$_toolBottom.html(billRequiredCheck ?
                // "<span id=groupBtn>" + c + f + "</span>" :
                // '<span id="groupBtn">' + c + "</span>");

                $("#orderid").autocomplete({
                    minLength: 0,
                    source: function (request, response) {
                        Public.ajaxPost("../scm/invSd?action=unDeliveryList", {},
                            function (a) {
                                if (null !== a) {
                                    var matcher = new RegExp(
                                        "^"
                                        + $.ui.autocomplete
                                            .escapeRegex(request.term),
                                        "i");
                                    response($.grep(a, function (item) {
                                        return matcher
                                            .test(item.billNo);
                                    }));
                                }
                            });
                    },
                    focus: function (event, ui) {
                        // $( "#orderid" ).val(
                        // ui.item.billNo );
                        return false;
                    },
                    select: function (event, ui) {
                        $("#orderid").val(ui.item.billNo);
                        if ($("#orderid").val() != this.$_billNo) {
                            this.$_billNo = $("#orderid").val();
                            if (this.$_billNo.length > 0) {
                                THISPAGE.loadOrder($("#orderid").val());
                            }
                        }
                        return false;
                    }
                }).blur(function () {
                    if ($("#orderid").val() != this.$_billNo) {
                        this.$_billNo = $("#orderid").val();
                        if (this.$_billNo.length > 0) {
                            THISPAGE.loadOrder($("#orderid").val());
                        }
                    }
                    return false;
                }).data("ui-autocomplete")._renderItem = function (ul, item) {
                    return $("<li>").append("<a>" + item.billNo + "<br>"
                        + item.userName + "	 " + item.billDate + "</a>")
                        .appendTo(ul);
                };

                this.$_toolBottom.html('<span id="groupBtn"></span>');
                this.$_userName.html(system.realName || "");
                this.$_modifyTime.parent().hide();
                this.$_checkName.parent().hide();
                disEditable && (THISPAGE.disableEdit(), this.$_toolBottom.hide());
            }
        },
        // 加载新的采购单
        loadOrder: function (billNo) {
            loading = $.dialog.tips("数据加载中...", 1e3, "loading.gif", !0);
            Public.ajaxGet("../scm/invSd/sale?action=sale", {
                billNo: billNo
            }, function (b) {
                loading && loading.close();
                if (b.status != -1) {
                    THISPAGE.reloadData(b.data);
                }
            });
        },
        loadGrid: function (a) {
            function b(a) {
            }

            function c(a, b, c) {
                return a ? (p(b.rowId), a) : c.invNumber ? c.invSpec ? c.invNumber
                    + " " + c.invName + "_" + c.invSpec : c.invNumber + " "
                    + c.invName : "&#160;"
            }

            function d() {
                var a = $(".storageAuto")[0];
                return a
            }

            function e(a, b, c) {
                if ("get" === b) {
                    if ("" !== $(".storageAuto").getCombo().getValue())
                        return $(a).val();
                    var d = $(a).parents("tr");
                    return d.removeData("storageInfo"), ""
                }
                "set" === b && $("input", a).val(c)
            }

            function f() {
                $("#initCombo").append($(".storageAuto").val(""))
            }

            function j() {
                var a = $(".dateAuto")[0];
                return a
            }

            function k(a, b, c) {
                return "get" === b ? a.val() : void("set" === b && $("input", a)
                    .val(c))
            }

            function l() {
                $("#initCombo").append($(".dateAuto"))
            }

            function m() {
                var a = $(".batchAuto")[0];
                return a
            }

            function n(a, b, c) {
                return "get" === b ? a.val() : void("set" === b && $("input", a)
                    .val(c))
            }

            function o() {
                $("#initCombo").append($(".batchAuto").val(""))
            }

            function p(a) {
                var b = $("#" + a).data("goodsInfo");
                if (b) {
                    b.batch || $("#grid").jqGrid("setCell", a, "batch", "&#160;"), b.safeDays
                    || ($("#grid").jqGrid("setCell", a, "prodDate",
                        "&#160;"), $("#grid").jqGrid("setCell", a,
                        "safeDays", "&#160;"), $("#grid").jqGrid(
                        "setCell", a, "validDate", "&#160;")), 1 == b.isWarranty
                    && $("#grid").jqGrid("showCol", "batch"), b.safeDays > 0
                    && ($("#grid").jqGrid("showCol", "prodDate"), $("#grid")
                        .jqGrid("showCol", "safeDays"), $("#grid")
                        .jqGrid("showCol", "validDate"));
                    // 初始化赋值
                    var c = {
                        qty: b.qty || 1,
                        locationName: b.locationName,
                        locationId: b.locationId,
                        outingQty: b.outingQty,
                        unOutQty: b.unOutQty
                    };
                    var k = $("#grid").jqGrid("setRowData", a, c);
                }
            }

            var q = this, r = (new Date).format();
            if (a.id) {
                var s = 8 - a.entries.length;
                if (s > 0)
                    for (var t = 0; s > t; t++)
                        a.entries.push({})
            }
            q.newId = 9;
            var u = !1;
            1 === SYSTEM.siType && (u = !0);
            var v = [{
                name: "goods",
                label: "商品",
                width: 300,
                classes: "goods",
                formatter: c,
                editable: 0
            }, {
                name: "spec",
                label: "商品型号",
                editable: 0,
                hidden: 0
            }, {
                name: "mainUnit",
                label: "单位",
                width: 30,
                editable: 0
            }, {
                name: "batch",
                label: "批次",
                width: 90,
                classes: "ui-ellipsis batch",
                hidden: !0,
                title: !1,
                editable: !0,
                align: "left",
                edittype: "custom",
                editoptions: {
                    custom_element: m,
                    custom_value: n,
                    handle: o,
                    trigger: "ui-icon-ellipsis"
                }
            }, {
                name: "prodDate",
                label: "生产日期",
                width: 90,
                hidden: !0,
                title: !1,
                editable: !0,
                edittype: "custom",
                editoptions: {
                    custom_element: j,
                    custom_value: k,
                    handle: l
                }
            }, {
                name: "safeDays",
                label: "保质期(天)",
                width: 90,
                hidden: !0,
                title: !1,
                align: "left"
            }, {
                name: "validDate",
                label: "有效期至",
                width: 90,
                hidden: !0,
                title: !1,
                align: "left"
            }, {
                name: "stockQty",
                label: "库存数量",
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalPlaces: qtyPlaces
                },
                editable: 0
            }, {
                name: "qty",
                label: "销售数量",
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalPlaces: qtyPlaces
                },
                editable: 0
            }, {
                name: "unOutQty",
                label: "未发货数量",
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalPlaces: qtyPlaces
                },
                editable: 0
            }, {
                name: "locationName",
                label: "仓库",
                width: 100,
                editable: !0,
                edittype: "custom",
                editoptions: {
                    custom_element: d,
                    custom_value: e,
                    handle: f,
                    trigger: "ui-icon-triangle-1-s"
                }
            }, {
                name: "outingQty",
                label: "本次发货数量",
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalPlaces: qtyPlaces
                },
                editable: !0
            }];
            v.push({
                name: "description",
                label: "备注",
                width: 300,
                title: !0,
                editable: !0
            }, {
                name: "srcEntryId",
                label: "源单分录ID",
                width: 0,
                hidden: !0
            }, {
                name: "srcId",
                label: "源单ID",
                width: 0,
                hidden: !0
            }, {
                name: "srcBillNo",
                label: "源单号",
                width: 120,
                fixed: !0,
                hidden: u,
                formatter: function (a) {
                    return a && (hideCustomerCombo = !0), a || "&#160;"
                }
            });
            var w = "grid";
            q.mod_PageConfig.gridReg(w, v), v = q.mod_PageConfig.conf.grids[w].colModel, $("#grid")
                .jqGrid({
                    data: a.entries,
                    datatype: "clientSide",
                    autowidth: !0,
                    height: "100%",
                    rownumbers: !0,
                    gridview: !0,
                    onselectrow: !1,
                    colModel: v,
                    cmTemplate: {
                        sortable: !1,
                        title: !1
                    },
                    shrinkToFit: !1,
                    forceFit: !0,
                    rowNum: 1e3,
                    cellEdit: !1,
                    cellsubmit: "clientArray",
                    localReader: {
                        root: "rows",
                        records: "records",
                        repeatitems: !1,
                        id: "id"
                    },
                    jsonReader: {
                        root: "data.entries",
                        records: "records",
                        repeatitems: !1,
                        id: "id"
                    },
                    loadComplete: function (a) {
                        THISPAGE.loadComplete(a.rows);
                    },
                    gridComplete: function () {
                        setTimeout(function () {
                            Public.autoGrid($("#grid"))
                        }, 10)
                    },
                    afterEditCell: function (a, b, c, d, e) {
                        function f() {
                            var b = $("#" + a).data("goodsInfo");
                            if (b) {
                                var c = $("#grid").jqGrid("getRowData", a);
                                b = $.extend(!0, {}, b), b.mainUnit = c.mainUnit, b.qty = c.qty, b.locationName = c.locationName, b.outingQty = c.outingQty, b.unOutQty = c.unOutQty, $("#"
                                    + a).data("goodsInfo", b)
                            }
                        }

                        if ("goods" === b
                            && (f(), $("#" + d + "_goods", "#grid").val(c), THISPAGE.goodsCombo
                                .selectByText(c), THISPAGE.curID = a), "qty" === b) {
                            f();
                            var g = $("#" + a).data("goodsInfo");
                            if (!g)
                                return;
                            if (SYSTEM.ISSERNUM && 1 == g.isSerNum) {
                                $("#grid").jqGrid("restoreCell", d, e), THISPAGE.curID = a;
                                var h = g.serNumList;
                                Business.serNumManage({
                                    row: $("#" + a),
                                    data: g,
                                    serNumUsedList: h,
                                    creatable: "150602" != originalData.transType
                                })
                            }
                        }
                        if ("locationName" === b
                            && ($("#" + d + "_locationName", "#grid")
                                .val(c), THISPAGE.storageCombo
                                .selectByText(c), "150602" == originalData.transType)) {
                            f();
                            var g = $("#" + a).data("goodsInfo"), i = $("#" + a)
                                    .data("storageInfo")
                                || {};
                            if (!g || !i.id)
                                return;
                            if (SYSTEM.ISSERNUM && 1 == g.isSerNum) {
                                $("#grid").jqGrid("restoreCell", d, e), THISPAGE.curID = a;
                                var h = g.serNumList;
                                Business.serNumManage({
                                    row: $("#" + a),
                                    data: g,
                                    serNumUsedList: h,
                                    enableStorage: !0,
                                    creatable: "150602" != originalData.transType
                                })
                            }
                        }
                        if ("batch" === b) {
                            var g = $("#" + a).data("goodsInfo");
                            if (!g)
                                return $("#grid").jqGrid("restoreCell", d, e), curCol = e
                                    + 1, void $("#grid").jqGrid("nextCell",
                                    d, e + 1);
                            $("#" + d + "_batch", "#grid").val(c), THISPAGE.batchCombo
                                .selectByText(c), THISPAGE.curID = a
                        }
                        if ("prodDate" === b) {
                            var g = $("#" + a).data("goodsInfo");
                            if (!g)
                                return $("#grid").jqGrid("restoreCell", d, e), curCol = e
                                    + 1, void $("#grid").jqGrid("nextCell",
                                    d, e + 1);
                            if (!g.safeDays)
                                return $("#grid").jqGrid("restoreCell", d, e), curCol = e
                                    + 1, void $("#grid").jqGrid("nextCell",
                                    d, e + 1);
                            THISPAGE.cellPikaday.setDate(c
                                ? c
                                : THISPAGE.cellPikaday.getDate()
                                || new Date), THISPAGE.curID = a
                        }
                        if ("mainUnit" === b) {
                            $("#" + d + "_mainUnit", "#grid").val(c);
                            var j = $("#" + a).data("unitInfo") || {};
                            if (!j.unitId || "0" === j.unitId)
                                return $("#grid").jqGrid("restoreCell", d, e), curCol = e
                                    + 1, void $("#grid").jqGrid("nextCell",
                                    d, e + 1);
                            THISPAGE.unitCombo.enable(), THISPAGE.unitCombo
                                .loadData(function () {
                                    for (var a = {}, b = 0; b < SYSTEM.unitInfo.length; b++) {
                                        var c = SYSTEM.unitInfo[b], d = j.unitId;
                                        j.unitId == c.id && (j = c), j.unitId = d;
                                        var e = c.unitTypeId || b;
                                        a[e] || (a[e] = []), a[e].push(c)
                                    }
                                    return j.unitTypeId
                                        ? a[j.unitTypeId]
                                        : [j]
                                }), THISPAGE.unitCombo.selectByText(c)
                        }
                    },
                    formatCell: function () {
                    },
                    beforeSaveCell: function (a, b, c) {
                        if ("goods" === b) {
                            var d = $("#" + a).data("goodsInfo");
                            if (d)
                                return d.skuClassId
                                && SYSTEM.enableAssistingProp
                                && (q.skey = c, setTimeout(function () {
                                    $("#grid").jqGrid(
                                        "restoreCell",
                                        curRow, 2), $("#grid")
                                        .jqGrid("editCell",
                                            curRow, 2,
                                            !0), $("#grid")
                                        .jqGrid("setCell",
                                            curRow, 2,
                                            "")
                                }, 10)), c;
                            q.skey = c;
                            var e, f = function (b) {
                                SYSTEM.ISSERNUM && b.isSerNum ? (Business
                                        .serNumManage({
                                            row: $("#" + a),
                                            data: b,
                                            creatable: "150602" != originalData.transType
                                        }), e = "&#160;")
                                    : b.skuClassId
                                    && SYSTEM.enableAssistingProp
                                    ? (Business.billSkuManage($("#"
                                        + a), b), e = "&#160;")
                                    : ($("#" + a).data("goodsInfo",
                                        b).data("storageInfo",
                                        {
                                            id: b.locationId,
                                            name: b.locationName
                                        }).data("unitInfo", {
                                        unitId: b.unitId,
                                        name: b.unitName
                                    }), e = Business
                                        .formatGoodsName(b))
                            };
                            return THISPAGE.$_barCodeInsert
                            && THISPAGE.$_barCodeInsert
                                .hasClass("active")
                                ? Business.cacheManage
                                    .getGoodsInfoByBarCode(c, f, !0)
                                : Business.cacheManage
                                    .getGoodsInfoByNumber(c, f, !0);
                            e ? e : ($.dialog({
                                width: 775,
                                height: 510,
                                title: "选择商品",
                                content: "url:../settings/goods_batch",
                                data: {
                                    skuMult: SYSTEM.enableAssistingProp,
                                    skey: q.skey,
                                    callback: function (a, b, c) {
                                        "" === b
                                        && ($("#grid").jqGrid(
                                            "addRowData", a, {},
                                            "last"), q.newId = a
                                            + 1), setTimeout(
                                            function () {
                                                $("#grid").jqGrid(
                                                    "editCell", c, 2,
                                                    !0)
                                            }, 10)
                                    }
                                },
                                init: function () {
                                    q.skey = ""
                                },
                                lock: !0,
                                button: [{
                                    name: "选中",
                                    defClass: "ui_state_highlight fl",
                                    focus: !0,
                                    callback: function () {
                                        return this.content.callback
                                        && this.content
                                            .callback("purchaseWarehouse"), !1
                                    }
                                }, {
                                    name: "选中并关闭",
                                    defClass: "ui_state_highlight",
                                    callback: function () {
                                        return this.content
                                            .callback("purchaseWarehouse"), this
                                            .close(), !1
                                    }
                                }, {
                                    name: "关闭",
                                    callback: function () {
                                        return !0
                                    }
                                }]
                            }), setTimeout(function () {
                                $("#grid").jqGrid("editCell", curRow, 2, !0), $("#grid")
                                    .jqGrid("setCell", curRow, 2, "")
                            }, 10), "&#160;")
                        }
                    },
                    afterSaveCell: function (a, c, d, e, f) {
                        switch (c) {
                            case "goods" :
                                break;
                            case "outingQty" : {
                                // 入库数量
                                var d = parseFloat(d);
                                var g = parseFloat($("#grid").jqGrid("getCell",
                                    a, f - 4));
                                var h = parseFloat($("#grid").jqGrid("getCell",
                                    a, f - 2));
                                if (d > h) {
                                    alert("本次出库数量大于未出库数量");
                                    $("#grid").jqGrid("restoreCell", a, f);
                                    return;
                                }
                                if (d > g) {
                                    var ms = window.confirm("本次出库数量大于库存数量");
                                    if (!ms){
                                        $("#grid").jqGrid("restoreCell", a, f);
                                        return;
                                    }
                                }
                            }
                            break;
                            //jasonxie add 2017/11/24
                            case "locationName": {
                                $("#" + a).data("storageInfo", {
                                    id: parent.Public.ToStorageId(d),
                                    name: d
                                });
                            }
                                break;
                            default:
                                break;
                        }
                    },
                    loadonce: !0,
                    resizeStop: function (a, b) {
                        q.mod_PageConfig
                            .updatePageConfig(
                                "grid",
                                [
                                    "width",
                                    q.mod_PageConfig.conf.grids.grid.defColModel[b
                                    - 1].name, a])
                    },
                    footerrow: !0,
                    userData: {
                        goods: "合计：",
                        qty: a.totalQty
                    },
                    userDataOnFooter: !0,
                    loadError: function (a, b) {
                        Public.tips({
                            type: 1,
                            content: "Type: " + b + "; Response: "
                            + a.status + " " + a.statusText
                        })
                    }
                }), $("#grid").jqGrid("setGridParam", {
                cellEdit: !0
            })
        },
        loadComplete: function (rows) {
            if (rows != null) {
                // var b = a.rows,
                var b = rows, c = b.length;
                for (var d = 0; c > d; d++) {
                    var e = d + 1, f = b[d];
                    if ($.isEmptyObject(b[d]))
                        break;
                    var g = $.extend(!0, {
                        id: f.invId,
                        outingQty: f.outingQty,
                        siid: f.siid,
                        qty: f.qty,
                        unOutQty: f.unOutQty
                    }, f);
                    Business.cacheManage.getGoodsInfoByNumber(g.number,
                        function (a) {
                            g.isSerNum = a.isSerNum, g.isWarranty = f.isWarranty = a.isWarranty, g.safeDays = f.safeDays = a.safeDays, g.id = f.invId, $("#"
                                + e).data("goodsInfo", g).data(
                                "storageInfo", {
                                    id: f.locationId,
                                    name: f.locationName
                                })
                        }), 1 == f.isWarranty
                    && $("#grid").jqGrid("showCol", "batch"), f.safeDays > 0
                    && ($("#grid").jqGrid("showCol", "prodDate"), $("#grid")
                        .jqGrid("showCol", "safeDays"), $("#grid")
                        .jqGrid("showCol", "validDate"))
                }
                //
                {
                    var c = this;
                    $("#groupBtn").html(c.btn_save);
                }
            }
        },
        reloadData: function (a) {
            function b() {
                // c.$_date.val(a.date),
                // c.$_billNo.text(a.billNo),
                // c.$_note.val(a.description),
                // c.$_userName.html(a.userName),
                // c.$_modifyTime.html(a.modifyTime),
                // c.$_checkName.html(a.checkName)
            }

            $("#grid").clearGridData();
            var c = this;
            originalData = a;
            var d = 8 - a.entries.length;
            if (d > 0)
                for (var e = 0; d > e; e++) {
                    try {
                        a.entries.push({});
                    } catch (error) {
                        alert(error.message);
                    }
                }
            $("#grid").jqGrid("setGridParam", {
                data: a.entries,
                userData: {
                    qty: a.totalQty
                },
                gridComplete: function () {
                    THISPAGE.loadComplete(originalData.entries);
                }
            }).trigger("reloadGrid"), b(), "edit" === a.status
                ? this.editable
                || (c.enableEdit(), $("#groupBtn").html(c.btn_edit
                    + c.btn_audit), $("#mark")
                    .removeClass("has-audit"))
                : this.editable
                && (c.disableEdit(), $("#groupBtn").html(c.btn_view
                    + c.btn_reaudit), $("#mark")
                    .addClass("has-audit"))
        },
        initCombo: function () {
            this.goodsCombo = Business.billGoodsCombo($(".goodsAuto"), {
                userData: {
                    creatable: "150602" != originalData.transType
                }
            }), this.storageCombo = Business
                .billStorageCombo($(".storageAuto")), this.unitCombo = Business
                .unitCombo($(".unitAuto"), {
                    defaultSelected: -1,
                    forceSelection: !1
                }), this.cellPikaday = new Pikaday({
                field: $(".dateAuto")[0],
                editable: !1
            }), this.batchCombo = Business.batchCombo($(".batchAuto"))
        },
        disableEdit: function () {
            this.$_date.attr("disabled", "disabled").addClass("ui-input-dis"), this.$_note
                .attr("disabled", "disabled").addClass("ui-input-dis"), $("#grid")
                .jqGrid("setGridParam", {
                    cellEdit: !1
                }), this.editable = !1
        },
        enableEdit: function () {
            disEditable
            || (this.$_date.removeAttr("disabled")
                .removeClass("ui-input-dis"), this.$_note
                .removeAttr("disabled").removeClass("ui-input-dis"), $("#grid")
                .jqGrid("setGridParam", {
                    cellEdit: !0
                }), this.editable = !0);
        },
        addEvent: function () {
            var a = this;
            this.$_date.bind("keydown", function (a) {
                13 === a.which && $("#grid").jqGrid("editCell", 1, 2, !0)
            }).bind("focus", function () {
                a.dateValue = $(this).val()
            }).bind("blur", function () {
                var b = /((^((1[8-9]\d{2})|([2-9]\d{3}))(-)(10|12|0?[13578])(-)(3[01]|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))(-)(11|0?[469])(-)(30|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))(-)(0?2)(-)(2[0-8]|1[0-9]|0?[1-9])$)|(^([2468][048]00)(-)(0?2)(-)(29)$)|(^([3579][26]00)(-)(0?2)(-)(29)$)|(^([1][89][0][48])(-)(0?2)(-)(29)$)|(^([2-9][0-9][0][48])(-)(0?2)(-)(29)$)|(^([1][89][2468][048])(-)(0?2)(-)(29)$)|(^([2-9][0-9][2468][048])(-)(0?2)(-)(29)$)|(^([1][89][13579][26])(-)(0?2)(-)(29)$)|(^([2-9][0-9][13579][26])(-)(0?2)(-)(29)$))/;
                b.test($(this).val()) || (parent.Public.tips({
                    type: 2,
                    content: "日期格式有误！如：2012-08-08。"
                }), $(this).val(a.dateValue))
            }), this.$_note.enterKey(), $(".grid-wrap").on("click",
                ".ui-icon-triangle-1-s", function () {
                    var b = $(this).siblings();
                    setTimeout(function () {
                        b.hasClass("unitAuto")
                            ? b.trigger("click")
                            : (a.storageCombo.active = !0, a.storageCombo
                                .doQuery())
                    }, 10)
                }), Business.billsEvent(a, "purchaseWarehouse"), $(".wrapper")
                .on("click", "#save", function (b) {
                    b.preventDefault();
                    var c = $(this), d = THISPAGE.getPostData();
                    d
                    && ("edit" === originalData.status
                    && (d.id = originalData.id, d.status = "edit"), c
                        .ajaxPost("../scm/invSd/add?action=add", {
                            postData: JSON.stringify(d)
                        }, function (b) {
                            200 === b.status
                                ? (a.$_modifyTime
                                    .html((new Date)
                                        .format("yyyy-MM-dd hh:mm:ss"))
                                    .parent()
                                    .show(), originalData.id = b.data.id, a.$_toolBottom
                                    .html(billRequiredCheck
                                        ? '<span id="groupBtn">'
                                        + a.btn_view
                                        + a.btn_audit
                                        + "</span>"
                                        : '<span id="groupBtn">'
                                        + a.btn_view
                                        + "</span>"), parent.Public
                                    .tips({
                                        content: "保存成功！"
                                    }))
                                : parent.Public.tips({
                                    type: 1,
                                    content: b.msg
                                })
                        }))
                }), $(".wrapper").on("click", "#edit", function (b) {
                if (b.preventDefault(), Business.verifyRight("SALE_UPDATE")) {
                    var c = $(this), d = THISPAGE.getPostData();
                    d
                    && c.ajaxPost(
                        "../scm/invSd/updateInvSd?action=updateInvSd",
                        {
                            postData: JSON.stringify(d)
                        }, function (b) {
                            200 === b.status
                                ? (a.$_modifyTime
                                    .html((new Date)
                                        .format("yyyy-MM-dd hh:mm:ss"))
                                    .parent().show(), originalData.id = b.data.id, parent.Public
                                    .tips({
                                        content: "修改成功！"
                                    }))
                                : parent.Public.tips({
                                    type: 1,
                                    content: b.msg
                                })
                        })
                }
            }), $(".wrapper").on("click", "#audit", function (b) {
                if (b.preventDefault(), Business.verifyRight("SALE_CHECK")) {
                    var c = $(this), d = THISPAGE.getPostData({
                        checkSerNum: !0
                    });
                    d && c.ajaxPost("../scm/invSd/checkInvSd?action=checkInvSd", {
                        postData: JSON.stringify(d)
                    }, function (b) {
                        200 === b.status
                            ? (originalData.id = b.data.id, $("#mark")
                                .addClass("has-audit"), a.$_checkName
                                .html(SYSTEM.realName).parent()
                                .show(), $("#edit").hide(), a
                                .disableEdit(), $("#groupBtn")
                                .html(a.btn_view + a.btn_reaudit), parent.Public
                                .tips({
                                    content: "审核成功！"
                                }))
                            : parent.Public.tips({
                                type: 1,
                                content: b.msg
                            })
                    })
                }
            }), $(".wrapper").on("click", "#reAudit", function (b) {
                if (b.preventDefault(), Business.verifyRight("SALE_UNCHECK")) {
                    var c = $(this), d = THISPAGE.getPostData();
                    d
                    && c
                        .ajaxPost(
                            "../scm/invSd/revsCheckInvSd?action=revsCheckInvSd",
                            {
                                postData: JSON.stringify(d)
                            }, function (b) {
                                200 === b.status
                                    ? ($("#mark").removeClass(), $("#edit")
                                        .show(), a.$_checkName
                                        .html(""), a
                                        .enableEdit(), $("#groupBtn")
                                        .html(a.btn_view
                                            + a.btn_audit), parent.Public
                                        .tips({
                                            content: "反审核成功！"
                                        }))
                                    : parent.Public.tips({
                                        type: 1,
                                        content: b.msg
                                    })
                            })
                }
            }), $(".wrapper").on("click", "#print", function (a) {
                a.preventDefault(), Business.verifyRight("SALE_PRINT")
                && Public.print({
                    title: "采购入库单列表",
                    $grid: $("#grid"),
                    pdf: "../scm/invSd/toPdf?action=toPdf",
                    billType: 10101,
                    filterConditions: {
                        id: originalData.id
                    }
                })
            }), $("#prev").click(function (b) {
                return b.preventDefault(), $(this).hasClass("ui-btn-prev-dis")
                    ? (parent.Public.tips({
                        type: 2,
                        content: "已经没有上一张了！"
                    }), !1)
                    : (a.idPostion = a.idPostion - 1, 0 === a.idPostion
                    && $(this).addClass("ui-btn-prev-dis"), loading = $.dialog
                        .tips("数据加载中...", 1e3, "loading.gif", !0), Public
                        .ajaxGet("../scm/invSd/sale?action=sale", {
                            id: a.idList[a.idPostion]
                        }, function (b) {
                            originalData.id = a.idList[a.idPostion], THISPAGE
                                .reloadData(b.data), $("#next")
                                .removeClass("ui-btn-next-dis"), loading
                            && loading.close()
                        }), void 0)
            }), $("#next").click(function (b) {
                return b.preventDefault(), $(this).hasClass("ui-btn-next-dis")
                    ? (parent.Public.tips({
                        type: 2,
                        content: "已经没有下一张了！"
                    }), !1)
                    : (a.idPostion = a.idPostion + 1, a.idLength === a.idPostion
                    + 1
                    && $(this).addClass("ui-btn-next-dis"), loading = $.dialog
                        .tips("数据加载中...", 1e3, "loading.gif", !0), Public
                        .ajaxGet("../scm/invSd/sale?action=sale", {
                            id: a.idList[a.idPostion]
                        }, function (b) {
                            originalData.id = a.idList[a.idPostion], THISPAGE
                                .reloadData(b.data), $("#prev")
                                .removeClass("ui-btn-prev-dis"), loading
                            && loading.close()
                        }), void 0)
            }), $(document).on("click", "#ldg_lockmask", function (a) {
                a.stopPropagation()
            }), $("#grid").on("click", 'tr[role="row"]', function () {
                if ($("#mark").hasClass("has-audit")) {
                    var a = $(this), b = (a.prop("id"), a.data("goodsInfo"));
                    if (!b)
                        return;
                    if (SYSTEM.ISSERNUM && 1 == b.isSerNum) {
                        var c = b.serNumList;
                        Business.serNumManage({
                            row: a,
                            data: b,
                            serNumUsedList: c,
                            view: !0
                        })
                    }
                }
            }), $("#config").click(function () {
                a.mod_PageConfig.config()
            }), $(window).resize(function () {
                Public.autoGrid($("#grid"))
            })
        },
        resetData: function () {
            var a = this;
            $("#grid").clearGridData();
            for (var b = 1; 8 >= b; b++)
                $("#grid").jqGrid("addRowData", b, {}), $("#grid").jqGrid(
                    "footerData", "set", {
                        qty: 0,
                        amount: 0
                    });
            a.$_note.val("")
        },
        _getEntriesData: function (a) {
            a = a || {};
            for (var b = [], c = $("#grid").jqGrid("getDataIDs"), d = 0, e = c.length; e > d; d++) {
                var f, g = c[d], h = $("#grid").jqGrid("getRowData", g);
                if ("" !== h.goods) {
                    var i = $("#" + g).data("goodsInfo");
                    if (i) {
                        var j = $("#" + g).data("storageInfo");
                        if (!j || !j.id)
                            return parent.Public.tips({
                                type: 2,
                                content: "请选择相应的仓库！"
                            }), $("#grid").jqGrid("editCellByColName", g,
                                "locationName"), !1;
                        f = {
                            siid: i.siid,
                            locationId: j.id,
                            description: h.description,
                            outingQty: h.outingQty
                        }, b.push(f)
                    }
                }
            }
            return b
        },
        getPostData: function (a) {
            var b = this, c = this;
            null !== curRow
            && null !== curCol
            && ($("#grid").jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null);
            var f = this._getEntriesData(a);
            if (!f)
                return !1;
            if (f.length > 0) {
                var g = $.trim(b.$_note.val()), h = {
                    id: originalData.id,
                    date: $.trim(b.$_date.val()),
                    billNo: $.trim(b.$_billNo.text()),
                    entries: f,
                    userName: b.$_userName.text(),
                    description: g === b.$_note[0].defaultValue ? "" : g
                };
                return h
            }
            return parent.Public.tips({
                type: 2,
                content: "商品信息不能为空！"
            }), $("#grid").jqGrid("editCell", 1, 2, !0), !1
        }
    }, hasLoaded = !1, originalData;
$(function () {
    if (urlParam.id) {
        if (!hasLoaded) {
            var a = $(".bills").hide();
            urlParam.turn ? Public.ajaxGet(
                "../scm/invSd/queryDetails?action=queryDetails", {
                    id: urlParam.id
                }, function (b) {
                    200 === b.status
                        ? (originalData = b.data, originalData.id = -1, originalData.orderId = b.data.id, originalData.orderNo = b.data.billNo, originalData.status = "add", THISPAGE
                            .init(b.data), a.show(), hasLoaded = !0)
                        : (parent.Public.tips({
                            type: 1,
                            content: b.msg
                        }), a.show(), originalData = {
                            id: -1,
                            status: "add",
                            // customer: 0,
                            transType: 150601,
                            entries: [{
                                id: "1"
                            }, {
                                id: "2"
                            }, {
                                id: "3"
                            }, {
                                id: "4"
                            }, {
                                id: "5"
                            }, {
                                id: "6"
                            }, {
                                id: "7"
                            }, {
                                id: "8"
                            }],
                            description: "",
                            totalQty: 0,
                            totalDiscount: 0,
                            totalAmount: 0,
                            totalTax: 0,
                            totalTaxAmount: 0,
                            disRate: 0,
                            disAmount: 0,
                            amount: "0.00",
                            rpAmount: "0.00",
                            arrears: "0.00",
                            accId: 0
                        }, originalData.transType = "150602" === urlParam.transType
                        ? "150602"
                        : "150601", THISPAGE.init(originalData))
                })
                : Public.ajaxGet("../scm/invSd/getSalesDischarge?action=getSalesDischarge", {
                    id: urlParam.id
                }, function (b) {
                    200 === b.status
                        ? (originalData = b.data, THISPAGE
                            .init(b.data), a.show(), hasLoaded = !0)
                        : parent.Public.tips({
                            type: 1,
                            content: b.msg
                        })
                })
        }
    } else
        originalData = {
            id: -1,
            status: "add",
            transType: 150601,
            entries: [{
                id: "1"
            }, {
                id: "2"
            }, {
                id: "3"
            }, {
                id: "4"
            }, {
                id: "5"
            }, {
                id: "6"
            }, {
                id: "7"
            }, {
                id: "8"
            }],
            description: "",
            totalQty: 0,
            totalDiscount: 0,
            totalAmount: 0,
            totalTax: 0,
            totalTaxAmount: 0,
            disRate: 0,
            disAmount: 0,
            amount: "0.00",
            rpAmount: "0.00",
            arrears: "0.00",
            accId: 0
        }, originalData.transType = "150602" === urlParam.transType
            ? "150602"
            : "150601", THISPAGE.init(originalData)
});