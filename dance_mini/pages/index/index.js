//index.js

var util = require('../../utils/util.js')
var app = getApp()
Page({
  data: {
    feed: [],
    feed_length: 0
  },
  // 发表文章事件
  postArticle: function () {
    wx.navigateTo({
      url: '../post/post'
    })
  },
  // 事件处理函数
  bindItemTap: function () {
    wx.navigateTo({
      url: '../answer/answer'
    })
  },
  bindQueTap: function () {
    wx.navigateTo({
      url: '../question/question'
    })
  },
  // 载入页面事件
  onLoad: function () {
    // console.log('onLoad')
    //  var that = this
    //  this.getData(); // 获取全局数据
  },
  // onShow事件，每次回到该页面时都会调用
  onShow: function () {
    // var that = this
    this.getData(); // 获取全局数据
  },
  // 上拉刷新函数
  upper: function () {
    wx.showNavigationBarLoading()
    this.refresh();
    // console.log("upper");
    setTimeout(function () { 
      wx.hideNavigationBarLoading(); 
      wx.stopPullDownRefresh(); 
      }, 2000);
  },
  // 下拉刷新函数
  lower: function (e) {
    wx.showNavigationBarLoading();
    var that = this;
    setTimeout(function () {
      wx.hideNavigationBarLoading();
      that.nextLoad();
    }, 1000);
    // console.log("lower")
  },
  //scroll: function (e) {
  //  console.log("scroll")
  //},

  //网络请求数据, 实现首页刷新
  refresh0: function () {
    var index_api = '';
    util.getData(index_api)
      .then(function (data) {
        //this.setData({
        //
        //});
        console.log(data);
      });
  },

  // 从服务器数据库获取数据
  getData: function () {
    var that = this;
    wx.request({
      url: 'https://57247578.qcloud.la/php/listArticle.php',
      data: {},
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        that.setData({
          feed: res.data // 将数据传给全局变量feed
        });
        console.log(that.data.feed)
      },
      fail: function (res) {
        console.log("获取数据失败！")
      }
    });
    var feed = util.getData2();
    console.log("loaddata");
    var feed_data = feed.data;
    console.log(feed_data);
  },
  // 刷新函数，用于上拉刷新
  refresh: function () {
    wx.showToast({
      title: '刷新中',
      icon: 'loading',
      duration: 3000
    });
    this.getData(); // 获取全局数据
    setTimeout(function () {
      wx.showToast({
        title: '刷新成功',
        icon: 'success',
        duration: 2000
      })
    }, 3000)

  },

  //使用本地 fake 数据实现继续加载效果
  nextLoad: function () {
    wx.showToast({
      title: '加载中',
      icon: 'loading',
      duration: 4000
    })
    var next = util.getNext();
    console.log("continueload");
    var next_data = next.data;
    this.setData({
      feed: this.data.feed.concat(next_data),
      feed_length: this.data.feed_length + next_data.length
    });
    setTimeout(function () {
      wx.showToast({
        title: '加载成功',
        icon: 'success',
        duration: 2000
      })
    }, 3000)
  }


})
