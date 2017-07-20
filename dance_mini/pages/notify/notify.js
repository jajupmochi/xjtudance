//logs.js
var util = require('../../utils/util.js')
Page({
  data: {
    navTab: ["通知", "顶帖", "喜欢"],
    currentNavtab: "0"
  },
  onLoad: function () {

  },
  switchTab: function(e){
    this.setData({
      currentNavtab: e.currentTarget.dataset.idx
    });
  }
})
