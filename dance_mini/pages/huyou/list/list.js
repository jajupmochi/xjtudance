// list.js
var app = getApp();
var base_fun = require('../../../libs/base_fun.js');

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
      huyou_list: app.global_data.huyou_list,
      huyous_length: app.global_data.huyou_list ? app.global_data.huyou_list.length : 0,
      isBanban: app.global_data.userInfo ? app.global_data.userInfo.rights.banban.is : false,
      pic_default: 'data/images/dance/huyou-default-720.jpg',
    });
    if (this.data.huyou_list == null) {
      this.listHuyous();
    }
    this.userLogin();
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
      title: '舞会忽悠',
      path: '/pages/huyou/list/list',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 下拉刷新函数
   */
  lower: function (e) {
    this.listHuyous();
  },

  /**
   * 用户登录
   */
  userLogin: function () {
    var that = this;
    wx.showLoading({
      title: '正在跳转...',
      mask: true,
    });
    wx.login({
      success: function (res) {
        wx.request({
          url: app.global_data.server_url + 'php/wx_getUser.php',
          data: {
            'code': res.code,
            '_id': '',
            'getValues': '_id/nickname/dance.baodao/rights.banban.is',
          },
          header: {
            'content-type': 'application/json'
          },
          method: "POST",
          success: function (res) {
            wx.hideLoading();
            if (res.data !== null) {
              that.setData({
                isBanban: res.data.rights.banban.is,
              });
              app.global_data.userInfo = res.data;
            } else { // 用户不在数据库中
              wx.showToast({
                title: '奇怪，你不在我们的数据库中...',
                image: '../../images/more.png',
                duration: 2000,
                mask: true,
              });
            }
          },
          fail: function () {
            wx.hideLoading();
            wx.showToast({
              title: 'oops，网络bug了，再试一次吧',
              image: '../../images/more.png',
              duration: 2000,
              mask: true,
            });
          }
        });
      },
      fail: function () { // 获取微信code失败
        wx.hideLoading();
        wx.showToast({
          title: 'oops，网络bug了，再试一次吧',
          image: '../../images/more.png',
          duration: 2000,
          mask: true,
        });
      }
    });
  },

  /**
   * 跳转到发忽悠页
   */
  toPostHuyou: function () {
    wx.navigateTo({
      url: '../post/post',
    });
  },

  /**
   * 跳转到用户页面
   */
  openHuyou: function (e) {
    var _id = e.currentTarget.dataset._id.$id;
    wx.navigateTo({
      url: '../detail/detail?_id=' + _id,
    });
  },

  /**
   * 从服务器数据库获取报到舞友列表
   */
  listHuyous: function () {
    var that = this;
    var limit = 10;
    base_fun.listData({ // 该函数为获取数据列表的通用函数
      collection_name: 'activities',
      skip: this.data.huyous_length,
      limit: limit,
      list_order: 'updated',
      getValues: '_id/name/start_time/place/comment/photos', // 用/号分隔需要获取的value
      success(res) {
        // console.log('CALLBACK_SUCCESS');
        for (var item in res.data) {
          var photo_list = res.data[item]['photos'];
          var photoUrl = photo_list[photo_list.length - 1];
          res.data[item]['photos'] = app.global_data.server_url + (photoUrl || that.data.pic_default);
        }
        that.setData({
          huyou_list: that.data.huyou_list ? Object.assign(that.data.huyou_list, res.data) : res.data, // 将数据传给全局变量huyou_list
          huyous_length: that.data.huyous_length + limit,
        });
        app.global_data.huyou_list = that.data.huyou_list;
        //console.log(that.data.huyou_list);
      }
    });
  },

  /**
  * 报名参加忽悠
  */
  hopin: function (e) {
    wx.showToast({
      title: '建设中，请等下一版~',
      image: '../../../images/more.png',
      duration: 1000,
      mask: true,
    });
  },

  /**
  * 修改忽悠内容
  */
  update: function (e) {
    wx.showToast({
      title: '建设中，请等下一版~',
      image: '../../../images/more.png',
      duration: 1000,
      mask: true,
    });
  },
})