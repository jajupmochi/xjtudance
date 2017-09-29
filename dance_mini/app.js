//app.js
App({
  global_data: {
    server_url: 'https://41375612.qcloud.la/test/', // 服务器地址
    userInfo: null, // 用户信息
    systemInfo: null, // 系统信息
    dancer_list: null, // 报到舞友列表
    huyou_list: null, // 忽悠列表
  },

  onLaunch: function () {
    // 获取可用的服务器url
/*    var that = this;
    wx.showLoading({
      title: '正在连接服务器...',
      mask: true,
    });
    wx.request({
      url: 'https://41375612.qcloud.la/php/wx_connectServer.php',
      method: "POST",
      success: function (res) {
        that.global_data.server_url = res.data.msg == 'SERVER_CON_SUCCESS' ? 'https://41375612.qcloud.la/' : 'https://xjtudance.top/';
      },
      fail: function (res) {
        that.global_data.server_url = 'https://xjtudance.top/';
      },
      complete: function () {
        console.log('server_url: ' + that.global_data.server_url);
        wx.hideLoading();
        if (getCurrentPages().length != 0) {
          getCurrentPages()[getCurrentPages().length - 1].onLoad()
        }
      },
    });*/
    wx.request({
      url: this.global_data.server_url + 'php/testMongo.php',
      method: "POST",
      success: function (res) {
        console.log(res.data);
      },
    });
    console.log('server_url: ' + this.global_data.server_url);

    // 监听网络状态变化
    wx.onNetworkStatusChange(function (res) {
      var net_type = res.networkType;
      if (!res.isConnected) {
        wx.showToast({ // 断网提示
          title: 'oops！貌似断网了...',
          duration: 1000,
        });
      };
    });
  }
})