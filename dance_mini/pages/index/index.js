//index.js

var util = require('../../utils/util.js')
var anims = require('../../utils/anims.js');

var app = getApp()
Page({
  data: {
    user_nickname: '',

    diaries: {},
    diaries_length: 0,
    imgUrl_write: app.global_data.server_url + "images/pen1-s.png",
    pen_x: 0,
    pen_y: 0,
    hideWriteArea: true,

    post2bmy: false,
    showConBmyArea: false,
    hideSend: false,
    imgUrl_formswitch: app.global_data.server_url + "images/bmy1.png",
    imgUrl_formswitch2: app.global_data.server_url + "images/bmy3-s.png",

    showReplyArea: false,
  },
  // 载入页面事件
  onLoad: function () {
    this.listDiaries(); // 获取文章列表
    this.animation = wx.createAnimation({ // 将写日记区的透明度设为0
      duration: 0,  // 动画时长  
      delay: 0,  // 0则不延迟
    });
    this.animation.opacity(0).step(); // 第3步：执行打开动画
    this.setData({
      pen_x: app.global_data.systemInfo.windowWidth - 60,
      pen_y: app.global_data.systemInfo.windowHeight - 80,
      anim_openWrite: this.animation.export(),
    });
    if (app.global_data.userInfo !== null) {
      this.setData({
        post2bmy: app.global_data.userInfo.bmy.id === "" ? false : true,
        imgUrl_formswitch: app.global_data.server_url + (app.global_data.userInfo.bmy.id === "" ? "images/bmy1.png" : "images/bmy2.png"),
      });
    }
  },
  // onShow事件，每次回到该页面时都会调用
  onShow: function () {
    // var that = this
    // this.writeDiary(); // 获取全局数据
  },

  // 写文章事件
  write_sth: function () {
    this.setData({
      anim_openWrite: anims.switchWriteArea(this.data.hideWriteArea).export(),
      user_nickname: app.global_data.userInfo.nickname,
      hideWriteArea: !this.data.hideWriteArea,
      pen_y: !this.data.hideWriteArea ? app.global_data.systemInfo.windowHeight - 80 : 0,
    });
  },
  // 删除文章事件
  deleteArticle: function (e) {
    /*    var that = this;
        var current_id = e.target.dataset.id.$id;
        wx.request({
          url: app.global_data.server_url + 'php/deleteArticle.php',
          data: {
            id: current_id,
          },
          header: {
            'content-type': 'application/json'
          },
          success: function (res) {
            // console.log(res.data);
            that.onShow(); // 重新加载页面
            wx.showToast({ // 显示成功提示
              title: '删除成功！',
              icon: 'success',
              duration: 1000
            });
          }
        })*/
  },
  // 点击显示文章全文事件
  showArticle: function (e) {
    //    wx.navigateTo({
    //      url: '../article/article?id=' + e.currentTarget.dataset.id.$id, // 进入文章详情页
    //    })
  },
  bindQueTap: function () {
    wx.navigateTo({
      url: '../question/question'
    })
  },
  // 上拉刷新函数
  upper: function () {
    /*    wx.showNavigationBarLoading()
        this.refresh();
        // console.log("upper");
        setTimeout(function () {
          wx.hideNavigationBarLoading();
          wx.stopPullDownRefresh();
        }, 2000); */
  },
  // 下拉刷新函数
  lower: function (e) {
    wx.showNavigationBarLoading();
    this.refresh();
    var that = this;
    setTimeout(function () {
      wx.hideNavigationBarLoading();
      //that.nextLoad();
    }, 1000);
    // console.log("lower")
  },
  //scroll: function (e) {
  //  console.log("scroll")
  //},

  // 从服务器数据库获取文章列表
  listDiaries: function () {
    var that = this;
    var limit = 10; // 获取日记数量
    wx.request({
      url: app.global_data.server_url + 'php/wx_listDiaries.php',
      data: {
        'skip': that.data.diaries_length,
        'limit': limit,
        'list_order': 'mama',
      },
      header: {
        'content-type': 'application/json'
      },
      method: "POST",
      success: function (res) {
        console.log(res.data);
        if (res.data == []) {
          wx.showToast({
            title: '这是日记的最后一页了',
            duration: 2000
          });
        }
        that.setData({
          diaries: Object.assign(that.data.diaries, res.data), // 将数据传给全局变量diaries
          diaries_length: that.data.diaries_length + limit,
        });

      },
      fail: function (res) {
        console.log("获取数据失败！")
      }
    });
  },
  // 刷新函数，用于下拉刷新
  refresh: function () {
    wx.showToast({
      title: '刷新中',
      icon: 'loading',
      duration: 3000
    });
    this.listDiaries(); // 刷新获取全局数据
    setTimeout(function () {
      wx.showToast({
        title: '刷新成功',
        icon: 'success',
        duration: 2000
      })
    }, 3000)

  },

  // 关联兵马俑账户
  connectBmy: function (e) {
    var that = this;
    var id = e.detail.value.id;
    var password = e.detail.value.password;
    if (id === "" || password === "") { // 没输入
      wx.showToast({
        title: '这位刁民你账号密码都丢了吗？',
        image: '../../images/nick1-100.png',
        duration: 1500,
      });
    } else {
      wx.request({
        url: app.global_data.server_url + 'php/wx_connectBmy.php',
        data: {
          "author": app.global_data.userInfo._id.$id, // 作者
          "bmy_id": id,
          "bmy_password": password,
        },
        header: {
          'content-type': 'application/json'
        },
        method: "POST",
        success: function (res) {
          // console.log(res.data);
          if (res.data.msg == "ERR_WRONG_INFO") {
            wx.showToast({
              title: '账号或密码错误!',
              icon: '../../images/dance1-200.png',
              duration: 1000
            });
          } else { // 连接成功
            app.global_data.userInfo.bmy.id = id;
            app.global_data.userInfo.bmy.password = password;
            app.global_data.userInfo.bmy.nickname = res.data.bmy_nickname;
            app.global_data.userInfo.degree.level = res.data.degree_level;
            app.global_data.userInfo.degree.credit = res.data.degree_credit;
            that.setData({
              post2bmy: true,
              imgUrl_formswitch: app.global_data.server_url + "images/bmy2.png",
            });
            that.tobmy_anim(that.data.showConBmyArea);
            wx.showToast({ // 显示成功提示
              title: '兵马俑BBS欢迎你！积分+200',
              icon: '../../images/bmy5-200.png',
              duration: 2000
            });
          }
        }
      });
    }
  },
  // 退出关联兵马俑账户界面
  quitConnectBmy: function () {
    this.tobmy_anim(this.data.showConBmyArea);
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
        this.tobmy_anim(this.data.showConBmyArea); // 关联bmy账户
      } else {
        this.setData({
          post2bmy: true,
          imgUrl_formswitch: app.global_data.server_url + "images/bmy2.png",
        });
      }
    }
  },
  // 将日记传给后台服务器
  writeDiary: function (e) {
    // console.log(e);
    var that = this;
    var formId = e.detail.formId;
    var values = e.detail.value;
    var title = values.title;
    var content = values.content;
    if (title != "" || content != "") {
      var openid = app.openid;
      wx.request({
        url: app.global_data.server_url + 'php/wx_writeDiary.php',
        data: {
          "post2bmy": that.data.post2bmy, // 是否发到兵马俑bbs
          'title': title, // 标题
          "author": app.global_data.userInfo._id.$id, // 作者
          'content': content, // 内容

        },
        header: {
          'content-type': 'application/json'
        },
        method: "POST",
        success: function (res) {
          if (res.data.msg == "ERR_WRONG_INFO") {
            this.tobmy_anim(this.data.showConBmyArea);
            wx.showToast({
              title: '兵马俑BBS账号或密码错误!',
              icon: '../../images/dance1-200.png',
              duration: 1000
            });
          } else {
            that.setData({
              anim_openWrite: anims.switchWriteArea(false).export(),
              hideWriteArea: true,
              pen_y: app.global_data.systemInfo.windowHeight - 80,
              diaries: Object.assign(res.data, that.data.diaries), // 将数据传给全局变量diaries
              diaries_length: that.data.diaries_length + 1,
            });
            var curr_diary = res.data[Object.keys(res.data)[0]];
            app.global_data.userInfo.degree.level = curr_diary.author.degree.level;
            app.global_data.userInfo.degree.credit = curr_diary.author.degree.credit;
            wx.showToast({ // 显示成功提示
              title: '发表成功！积分+' + 5 * (that.data.post2bmy ? 2 : 1),
              icon: '../../images/dance1-200.png',
              duration: 2000
            });
          }
        }
      });
    } else {
      wx.showToast({
        title: '标题内容至少写一个吧？',
        image: '../../images/smiley-6_64.png',
        duration: 2000,
      });
    }
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
      anim_connectBmy: animation.export()
    })
    // 第5步：设置定时器到指定时候后，执行第二组动画  
    setTimeout(function () {
      // 执行第二组动画  
      animation.opacity(1).rotateX(0).step();
      // 给数据对象储存的第一组动画，更替为执行完第二组动画的动画对象  
      this.setData({
        anim_connectBmy: animation
      })
      //关闭  
      if (currentStatu == true) {
        this.setData({
          showConBmyArea: false
        });
      }
    }.bind(this), 200)
    // 显示  
    if (currentStatu == false) {
      this.setData({
        showConBmyArea: true,
      });
    }
  },

  // 点击回复打开回复窗口
  openReplyArea: function (e) {
    console.log(e);
    this.setData({
      diaryId_replying: e.currentTarget.dataset.id.$id,
      //anim_openReplyArea: anims.switchReplyArea(false).export(),
      showReplyArea: true,
      pen_y: 0,
    });
  },

  // 点击回复窗口空白区域关闭回复窗口
  closeReplyArea: function () {
    this.setData({
      diaryId_replying: null,
      // anim_openReplyArea: anims.switchReplyArea(true).export(),
      showReplyArea: false,
      pen_y: app.global_data.systemInfo.windowHeight - 80,
    });
  },

  // 回复日记
  replyDiary: function (e) {
    var that = this;
    var formId = e.detail.formId;
    var values = e.detail.value;
    var title = values.title;
    var content = values.content;
    if (title != "" || content != "") {
      wx.request({
        url: app.global_data.server_url + 'php/wx_replyDiary.php',
        data: {
          'title': title, // 标题
          "author": app.global_data.userInfo._id.$id, // 作者
          'content': content, // 内容
          'fatherId': this.data.diaryId_replying, // 被回复帖子的id
        },
        header: {
          'content-type': 'application/json'
        },
        method: "POST",
        success: function (res) {
          if (res.data.msg == "ERR_WRONG_INFO") {
            /*  this.tobmy_anim(this.data.showConBmyArea);
              wx.showToast({
                title: '兵马俑BBS账号或密码错误!',
                icon: '../../images/dance1-200.png',
                duration: 1000
              });*/
          } else {
            var diary_list = that.data.diaries;
            var cur_mama = diary_list[that.data.diaryId_replying];
            cur_mama.reply = res.data.concat(cur_mama.reply);
            var obj = new Object();
            obj[that.data.diaryId_replying] = cur_mama;
            delete diary_list[that.data.diaryId_replying];
            that.setData({
              diaryId_replying: null,
              showReplyArea: false,
              pen_y: app.global_data.systemInfo.windowHeight - 80,
              diaries: Object.assign(obj, diary_list), // 将数据传给全局变量diaries
            });
            wx.showToast({ // 显示成功提示
              title: '回复成功！积分+' + 5 * (false ? 2 : 1),
              icon: '../../images/dance1-200.png',
              duration: 2000
            });
          }
        }
      });
    } else {
      wx.showToast({
        title: '标题内容至少写一个吧？',
        image: '../../images/smiley-6_64.png',
        duration: 2000,
      });
    }
  },

  // 转发本页
  onShareAppMessage: function (res) {
    return {
      title: 'The Diary of DANCE',
      path: '/page/index',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

})