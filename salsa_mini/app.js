//app.js
App({
  global_data: {
    server_url: "https://xjtudance.top/aishangsalsa/", // 服务器地址
    userInfo: null, // 用户信息
    systemInfo: null, // 系统信息
    dancer_list: null, // 报到舞友列表
  },

  onLaunch: function () {

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
          