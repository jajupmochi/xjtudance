var app = getApp()
Page({
  data: {
    post2bmy: true,
    showConnectBmy: false,
    imgUrl_formswitch: app.global_data.server_url + "images/bmy1.png",
  },
  onLoad: function () {
    if (app.global_data.userInfo.bmy !== null) {
      this.setData({
        post2bmy: app.global_data.userInfo.bmy.id === "" ? false : true,
        imgUrl_formswitch: app.global_data.server_url + (app.global_data.userInfo.bmy.id === "" ? "images/bmy1.png" : "images/bmy2.png"),
      });
    }
  },

  // 关联兵马俑账户
  connectBmy: function (e) {
    var id = e.detail.value.id;
    var password = e.detail.value.password;
    if (id === "" || password === "") { // 没输入
      wx.showToast({
        title: '这位刁民你账号密码都丢了吗？',
        image: '../../images/icon8.jpg',
        duration: 2000,
      });
    } else { // 设置成功
      app.global_data.userInfo.bmy.id = id;
      app.global_data.userInfo.bmy.password = password;
      this.setData({
        post2bmy: true,
        imgUrl_formswitch: app.global_data.server_url + "images/bmy2.png",
      });
      this.tobmy_anim(this.data.showConnectBmy);
    }
  },
  // switch改变事件
  switchChange: function (e) {
    // console.log("切换开关");
    if (this.data.post2bmy) { // 关闭开关
      this.setData({
        post2bmy: false,
        imgUrl_formswitch: app.global_data.server_url + "images/bmy1.png",
      });
    } else { // 打开开关
      if (app.global_data.userInfo.bmy.id === "") {
        this.tobmy_anim(this.data.showConnectBmy); // 关联bmy账户
      } else {
        this.setData({
          post2bmy: true,
          imgUrl_formswitch: app.global_data.server_url + "images/bmy2.png",
        });
      }
    }
  },
  // 将用户发表的文章数据传给后台服务器
  submitArticle: function (e) {
    // console.log(e);
    var formId = e.detail.formId;
    var values = e.detail.value;
    var title = values.title;
    var content = values.content;

    var openid = app.openid;

    wx.request({
      url: app.global_data.server_url + 'php/acceptArticle.php',
      data: {
        openid: openid,
        formId: formId,
        title: title, // 标题
        content: content, // 内容
        source: "wxmini" // 来源：包括bbs（兵马俑bbs）、bbswap（bbs wap版）、wxmini（微信小程序）
      },
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        console.log(res.data);
        wx.showToast({ // 显示成功提示
          title: '发表成功！',
          icon: 'success',
          duration: 1000
        }),
          setTimeout(function () {
            wx.navigateBack({ // 返回index页
              delta: 1
            })
          }, 1000)
        //}
      }
    })
  },

  // 连接兵马俑bbs账户的弹出窗口 的动画
  tobmy_anim: function (currentStatu) {
    /* 动画部分 */
    // 第1步：创建动画实例   
    var animation = wx.createAnimation({
      duration: 200,  //动画时长  
      timingFunction: "linear", //线性  
      delay: 0  //0则不延迟  
    });

    // 第2步：这个动画实例赋给当前的动画实例  
    this.animation = animation;

    // 第3步：执行第一组动画  
    animation.opacity(0).rotateX(-100).step();

    // 第4步：导出动画对象赋给数据对象储存  
    this.setData({
      animationData: animation.export()
    })

    // 第5步：设置定时器到指定时候后，执行第二组动画  
    setTimeout(function () {
      // 执行第二组动画  
      animation.opacity(1).rotateX(0).step();
      // 给数据对象储存的第一组动画，更替为执行完第二组动画的动画对象  
      this.setData({
        animationData: animation
      })

      //关闭  
      if (currentStatu == true) {
        this.setData(
          {
            showConnectBmy: false
          }
        );
      }
    }.bind(this), 200)

    // 显示  
    if (currentStatu == false) {
      this.setData(
        {
          showConnectBmy: true
        }
      );
    }
  }

})
