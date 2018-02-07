let tempObj = [
  {
    "date": "2017-09-16",
    "amount": 10000000,
    "del": false,
    "id": 1,
    "name": "\u82cf\u5dde\u4e5d\u4e94\u5c0a\u6613\u7f51\u7edc\u6709\u9650\u516c\u53f8",
    "account": "5129 0603 0710 301",
    "bank": "\u62db\u5546\u94f6\u884c\u80a1\u4efd\u6709\u9650\u516c\u53f8\u82cf\u5dde\u5206\u884c",
    "currency": 1,
    "number": "1",
    "type": 2
  },
  {
    "date": "2017-09-16",
    "amount": 2000000,
    "del": false,
    "id": 2,
    "name": "\u4e0a\u6d77\u4e5d\u543e\u5c0a\u6613\u4fe1\u606f\u79d1\u6280\u6709\u9650\u516c\u53f8",
    "account": "1219 2345 0610 601",
    "bank": "\u62db\u5546\u94f6\u884c\u80a1\u4efd\u6709\u9650\u516c\u53f8\u4e0a\u6d77\u95f5\u884c\u652f\u884c",
    "currency": 1,
    "number": "2",
    "type": 2
  },
  {
    "date": "2016-08-18",
    "amount": 0,
    "del": false,
    "id": 3,
    "name": "95isee Technology (HK) Co., Limited",
    "account": "801-519182-838",
    "bank": "The Hongkong and Shanghai Banking Corporation Limited",
    "currency": 2,
    "number": "3",
    "type": 1
  },
  {
    "date": "2016-12-27",
    "amount": 2180000,
    "del": false,
    "id": 5,
    "name": "\u4e0a\u6d77\u8d5b\u8fde\u9884\u4ed8\u6b3e",
    "account": "",
    "bank": "",
    "currency": 1,
    "number": "4",
    "type": 1
  },
  {
    "date": "2017-09-30",
    "amount": 0,
    "del": false,
    "id": 7,
    "name": "\u8c2d\u79c0\u4e3d",
    "account": "\u5fae\u4fe1\/\u652f\u4ed8\u5b9d\/\u73b0\u91d1",
    "bank": "",
    "currency": 1,
    "number": "6",
    "type": 1
  },
  {
    "date": "2017-09-30",
    "amount": 0,
    "del": false,
    "id": 8,
    "name": "\u82cf\u5dde\u4e5d\u4e94\u652f\u4ed8\u5b9d",
    "account": "\u5fae\u4fe1\/\u652f\u4ed8\u5b9d\/\u73b0\u91d1",
    "bank": "",
    "currency": 1,
    "number": "7",
    "type": 1
  },
  {
    "date": "2017-10-30",
    "amount": 0,
    "del": false,
    "id": 11,
    "name": "Greatwintech Technology (HK) C",
    "account": "534-631809-838",
    "bank": "\u9999\u6e2f\u4e0a\u6d77\u6c47\u4e30\u94f6\u884c\u6709\u9650\u516c\u53f8",
    "currency": 2,
    "number": "95ZYAC0009",
    "type": 2
  }
];
console.log('name');
for (let index in tempObj) {
  console.log(tempObj[index].name)
}
console.log('bank');
for (let index in tempObj) {
  console.log(tempObj[index].bank)
}
// console.log('contacter');
// for (let index in tempObj) {
//   console.log(tempObj[index].contacter)
// }
// console.log('deliveryAddress');
// for (let index in tempObj) {
//   console.log(tempObj[index].deliveryAddress)
// }

