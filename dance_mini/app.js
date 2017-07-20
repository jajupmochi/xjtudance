//app.js
App({
  openid: null,
  onLaunch: function () {
    //调用API从本地缓存中获取数据
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)

    var that = this;
    // 登录并获取openid
    wx.login({
      success: function (res) {
        // console.log(res.code);
        wx.request({
          url: 'https://57247578.qcloud.la/php/onLogin.php',
          data: {
            code: res.code
          },
          header: {
            'content-type': 'application/json'
          },
          success: function (res) {
            // console.log(res.data);
            var data = res.data;
            var openid = data.openid;
            that.openid = openid;

          }
        })
      }
    });
  },
  getUserInfo:function(cb){
    var that = this
    if(this.globalData.userInfo){
      typeof cb == "function" && cb(this.globalData.userInfo)
    }else{
      //调用登录接口
      wx.login({
        success: function () {
          wx.getUserInfo({
            success: function (res) {
              that.globalData.userInfo = res.userInfo
              typeof cb == "function" && cb(that.globalData.userInfo)
            }
          })
        }
      })
    }
  },
  globalData:{
    userInfo:null
  }
})