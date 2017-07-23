//answer.js
var util = require('../../utils/util.js')

var app = getApp()
Page({
  data: {
    motto: '西交DANCE',
    userInfo: {},
    loadText: '加载更多',
    nickname: {},
    userbbsid: {},
    title: {},
    article: {},
    test: [],
    feed: []
  },
  // 载入页面事件
  onLoad: function (e) {
    var that = this;
    // 从服务器读取文章详细内容
    var current_id = e.id;
    wx.request({
      url: app.global_data.server_url + 'php/getArticle.php',
      data: {
        id: current_id,
      },
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        that.setData({
          feed: res.data // 将数据传给全局变量feed
        });
      }
    })
  },

  // 修改文章
  modifyArticle: function () {
    var that = this;
    wx.navigateTo({
      url: '../modify/modify?feed=' + JSON.stringify(that.data.feed),
    })
  },
  //事件处理函数
  toQuestion: function () {
    wx.navigateTo({
      url: '../question/question'
    })
  },
  //    onLoad: function () {
  //    console.log('onLoad')
  //    var that = this
  //    //调用应用实例的方法获取全局数据
  //    app.getUserInfo(function (userInfo) {
  //      //更新数据
  //      that.setData({
  //        userInfo: userInfo
  //      })
  //    })
  //  },
  tapName: function (event) {
    console.log(event)
  }
})


/*   var that = this;
    // 网络请求数据, 实现刷新
    wx.request({
      url: 'https://bbs.xjtu.edu.cn/BMY/con?B=dance&F=M.1499092691.A',
      data: {},
      success: function (res) {
        console.log(res.data);
        // 正则表达式匹配
        // bbs id和昵称
        var re = /发信人: ([\s\S]*) \(([\s\S]*)\), 信区: dance/;
        var result_user = re.exec(res.data);
        if (result_user) {
          console.log("ok");
          console.log(result_user);
        }
        else {
          console.log(err);
        };
        // 帖子题目
        re = /题: ([\s\S]*)\n<br>发信站: 兵马俑BBS/;
        var result_title = re.exec(res.data);
        if (result_title) {
          console.log("ok");
          console.log(result_title);
        }
        else {
          console.log(err);
        };
        // 帖子正文
        re = /本站\(bbs.xjtu.edu.cn\)\n<br>\n([\s\S]*)\n<br><div class="con_sig">--/;
        var result_article = re.exec(res.data);
        if (result_article) {
          console.log("ok");
          console.log(result_article);
        }
        else {
          console.log(err);
        };
//        console.log(result_article.replace(/\n/g, ''));
        // 设置数据
        that.setData({
          userbbsid: result_user[1],
          nickname: result_user[2],
          title: result_title[1],
          article: result_article[1],
          test: res.data
        })
      },
      fail: function (err) {
        console.log(err)
      }
    })*/