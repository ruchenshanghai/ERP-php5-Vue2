$(function () {

  // console.log($.fn.cookie('phoneNumber'));
  // console.log($.fn.cookie('locationPicker'));
  $('#phone-number').html($.fn.cookie('phoneNumber'));

  const nameInput = $('#name-input');
  const IDInput = $('#ID-input');
  const nextButton = $('#next-button');
  const nicknameLi = $('#nickname-li');
  const confirmButton = $('#confirm-button');
  const skipButton = $('#skip-button');




  function IdentityCodeValid(code) {
    let city = {
      11: "北京",
      12: "天津",
      13: "河北",
      14: "山西",
      15: "内蒙古",
      21: "辽宁",
      22: "吉林",
      23: "黑龙江 ",
      31: "上海",
      32: "江苏",
      33: "浙江",
      34: "安徽",
      35: "福建",
      36: "江西",
      37: "山东",
      41: "河南",
      42: "湖北 ",
      43: "湖南",
      44: "广东",
      45: "广西",
      46: "海南",
      50: "重庆",
      51: "四川",
      52: "贵州",
      53: "云南",
      54: "西藏 ",
      61: "陕西",
      62: "甘肃",
      63: "青海",
      64: "宁夏",
      65: "新疆",
      71: "台湾",
      81: "香港",
      82: "澳门",
      91: "国外 "
    };
    let tip = "";
    let pass = true;

    if (!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i.test(code)) {
      tip = "身份证号格式错误";
      pass = false;
    }

    else if (!city[code.substr(0, 2)]) {
      tip = "地址编码错误";
      pass = false;
    }
    else {
      //18位身份证需要验证最后一位校验位
      if (code.length === 18) {
        code = code.split('');
        //∑(ai×Wi)(mod 11)
        //加权因子
        let factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        //校验位
        let parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
        let sum = 0;
        let ai = 0;
        let wi = 0;
        for (let i = 0; i < 17; i++) {
          ai = code[i];
          wi = factor[i];
          sum += ai * wi;
        }
        let last = parity[sum % 11];
        if (parity[sum % 11] !== code[17]) {
          tip = "校验位错误";
          pass = false;
        }
      }
    }
    console.log(tip);
    return pass;
  }

  function CheckValidFromDB(name, code) {
    // need add check method
    return true;
  }

  nextButton.on('click', function (e) {
    if (nameInput.val() === '' || !IdentityCodeValid(IDInput.val())) {
      $.alert('请输入正确的身份信息', '错误!');
    } else {
      // check from db
      if (!CheckValidFromDB(nameInput.val(), IDInput.val())) {
        $.alert('实名校验不一致', '错误!');
      } else {

        nicknameLi.removeClass('hidden-li');
        $.alert('校验通过', '成功!');
        nextButton.remove();

        confirmButton.on('click', function () {
          // connec to db to save nickname

          $.alert('校验通过，收到**元奖励，奖励已存入“我的资产', '身份创建成功!');

        });
        skipButton.on('click', function () {

          $.alert('校验通过，无奖励', '身份创建成功!');

        });
      }

    }

  });

});