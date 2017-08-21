var myToast = require('../../common/showToast.js')
Page({
  data: {
    //toast默认不显示 
    isShowToast: true
  },
  
  /* 点击按钮 */
  clickBtn: function () {
    console.log("你点击了按钮");
    //设置toast时间，toast内容 
//    this.setData({
//      count: 1500,
//      toastText: '发表成功！'
//    });
    myToast.showToast({
      count: 1500,
    });
  }
})
/*var feedbackApi = require('../../common/showToast');//引入消息提醒暴露的接口
Page({
  data: {

  },
  testToast: function (e) {
    var test = e.target.dataset.test;
    if (test == 1) {
      feedbackApi.showToast({ title: 'test shoToast title' })//调用
    }
    if (test == 2) {
      feedbackApi.showToast({
        title: 'test shoToast title',
        icon: '../../images/nick.png'
      })
    }
    if (test == 3) {
      feedbackApi.showToast({
        title: 'test shoToast title',
        duration: 3000
      })
    }
    if (test == 31) {
      feedbackApi.showToast({
        title: 'test shoToast title',
        duration: 10000
      })
      setTimeout(function () {
        feedbackApi.hideToast();
      }, 2000)
    }
    if (test == 4) {
      feedbackApi.showToast({
        title: 'test shoToast title',
        mask: false
      })
    }
    if (test == 5) {
      feedbackApi.showToast({
        title: 'test shoToast title',
        cb: function () {
          console.log('回调进来了，可以制定业务啦')
        }
      })
    }
  },
  onLoad: function (e) {
    wx.setNavigationBarTitle({
      title: 'test showToast'
    })
  }
})  */