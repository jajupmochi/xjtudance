// pages/index/index.js
var app = getApp();
var base_fun = require('../../libs/base_fun.js');
var img_base64 = require('../../images/img_base64.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      swiper_list: [
        { imgUrl: app.global_data.server_url + 'data/images/dance/dance3.jpg', },
        { imgUrl: app.global_data.server_url + 'data/images/dance/dance4-1920.jpg', },
        { imgUrl: app.global_data.server_url + 'data/images/dance/dance5-1920.jpg', },
        { imgUrl: app.global_data.server_url + 'data/images/dance/dance7-1920.jpg', },
      ],
      indicatorDots: false,
      autoplay: true,
      interval: 8000,
      duration: 1000,
      circular: true,

      bg_white_wall: img_base64.bg_white_wall, // 白墙背景
      img_dance: '../../images/dance-logo.jpg', //app.global_data.server_url + 'data/images/dance/dance-logo-5_4.jpg',
    });
    this.getSwiperContent();
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    return {
      title: '不Dance，怎么嗨！',
      path: '/pages/index/index',
      imageUrl: app.global_data.server_url + 'data/images/dance/dance-logo-5_4.jpg',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 从服务器数据库获取swiper所需内容
   */
  getSwiperContent: function () {
    var that = this;
    var limit = 4;
    base_fun.listData({ // 该函数为获取数据列表的通用函数
      collection_name: 'activities',
      limit: limit,
      list_order: 'updated',
      getValues: '_id/photos', // 用/号分隔需要获取的value
      success(res) {
        if (res.data != '') {
          var swiper_list = [];
          for (var item in res.data) {
            var photo_list = res.data[item]['photos'];
            var photoUrl = photo_list[photo_list.length - 1];
            if (photoUrl) {
              var swiper_item = {
                '_id': res.data[item]['_id']['$id'],
                'imgUrl': app.global_data.server_url + photoUrl,
              };
              swiper_list.push(swiper_item);
            }
          }
          that.setData({
            swiper_list: swiper_list,
          });
        }
      }
    });
  },

  /**
   * 跳转到swiper的对应页
   */
  toSwiperContent: function (e) {
    if (e.currentTarget.dataset._id) {
      var _id = e.currentTarget.dataset._id;
      wx.navigateTo({
        url: '../huyou/detail/detail?_id=' + _id,
      });
    }
  },

  /**
   * 跳转到报到页
   */
  toBaodao: function (e) {
    wx.navigateTo({
      url: '../baodao/baodao',
    });
  },

  /**
   * 跳转到忽悠页
   */
  toHuyou: function (e) {
    wx.navigateTo({
      url: '../huyou/list/list',
    });
  },

  /**
   * 跳转到舞友列表页
   */
  toDancerList: function (e) {
    wx.navigateTo({
      url: '../dancers/dancers',
    });
  },

  /**
   * 跳转到dance介绍页
   */
  toDanceIntro: function (e) {
    wx.navigateTo({
      url: '../danceIntro/danceIntro',
    });
  },
})