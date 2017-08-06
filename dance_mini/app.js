//app.js
App({
  global_data: {
    server_url: "https://57247578.qcloud.la/test/", // 服务器地址
    userInfo: null, // 用户信息
  },
  openid: null,
  onLaunch: function () {
    //调用API从本地缓存中获取数据
    //var logs = wx.getStorageSync('logs') || []
    //logs.unshift(Date.now())
    //wx.setStorageSync('logs', logs)

    // 登录并获取用户信息
    var that = this;
    wx.getNetworkType({ // 获取网络类型
      success: function (res) {
        var net_type = res.networkType;
        wx.login({
          success: function (res) {
            wx.request({
              url: that.global_data.server_url + 'php/wx_onLogin.php',
              data: {
                code: res.code,
                "web.net_type": net_type,
              },
              header: {
                'content-type': 'application/json'
              },
              method: "POST",
              success: function (res) {
                if (res.data !== null) {
                  that.global_data.userInfo = res.data;
                  console.log(that.global_data.userInfo);
                  console.log("hohoho");
                } else { // 如果用户不存在则注册
                  wx.login({ // 重新登录获取code
                    success: function (res) {
                      var code = res.code;
                      wx.getUserInfo({ // 从微信获取用户信息并提交给数据库
                        success: function (res) {
                          // console.log(res);
                          wx.request({
                            url: that.global_data.server_url + 'php/wx_signup.php',
                            data: {
                              code: code,
                              avatar_url: res.userInfo.avatarUrl,
                              nickname: res.userInfo.nickName,
                              gender: res.userInfo.gender,
                              "individualized.langue": res.userInfo.language,
                              "web.net_type": net_type,
                            },
                            header: {
                              'content-type': 'application/json'
                            },
                            method: "POST",
                            success: function (res) {
                              that.global_data.userInfo = res.data;
                              // console.log(that.global_data.userInfo);
                            }
                          })
                        }
                      })
                    }
                  })
                }
              }
            })
          }
        });
      },
    });

    // 监听网络状态变化
    wx.onNetworkStatusChange(function (res) {
      var net_type = res.networkType;
      if (!res.isConnected) {
        wx.showToast({ // 断网提示
          title: 'oops！貌似断网了...',
          duration: 1000,
        });
      };
      if (that.global_data.userInfo != null) {
        that.global_data.userInfo.web.net_type = net_type;
        wx.request({
          url: that.global_data.server_url + 'php/wx_updateUser.php',
          data: {
            "_id": that.global_data.userInfo._id.$id,
            "web.net_type": net_type,
          },
          header: {
            'content-type': 'application/json'
          },
          method: "POST",
          success: function (res) {
            console.log(res.data);
          }
        });
      };
    })



    // 播放背景音乐
    //wx.playBackgroundAudio({
    //  dataUrl: that.global_data.server_url + 'Pillowtalk_Zayn.mp3',
    //  title: '',
    //  coverImgUrl: ''
    //})
  },
  getUserInfo: function (cb) {
    var that = this
    if (this.globalData.userInfo) {
      typeof cb == "function" && cb(this.globalData.userInfo)
    } else {
      //调用登录接口
      wx.login({
        success: function () {
          wx.getUserInfo({
            success: function (res) {
              console.log(res);
              that.globalData.userInfo = res.userInfo
              typeof cb == "function" && cb(that.globalData.userInfo)
            }
          })
        }
      })
    }
  },
  globalData: {
    userInfo: null
  }
})