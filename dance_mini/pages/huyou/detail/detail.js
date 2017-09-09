// pages/huyou/detail/detail.js
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
  onLoad: function (e) {
    var _id = e._id;
    this.setData({
      pic_default: 'data/images/dance/huyou-default-1920.jpg',
    });
    this.getHuyouInfo(_id);
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
      title: this.data.huyou_info.name + ' | ' + this.data.huyou_info.start_time + ' | ' + this.data.huyou_info.place,
      path: '/pages/huyou/detail/detail?_id=' + this.data._id,
      imageUrl: this.data.huyou_info.photos,
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 获取舞会信息
   */
  getHuyouInfo: function (_id) {
    this.setData({
      _id: _id,
    });
    var that = this;
    base_fun.listData({ // 该函数为获取数据的通用函数
      collection_name: 'activities',
      query: {
        '_id': this.data._id, // 使用id获取数据时，请使用字符串，后台会自动转为MongoId
      },
      getValues: '_id/name/start_time/end_time/place/comment/photos/tags/initator/peopleNum/isOfficial', // 用/号分隔需要获取的value
      success(res) {
        if (res.data !== []) {
          var huyou_info = res.data[Object.keys(res.data)[0]];
          var photo_list = huyou_info.photos;
          var photoUrl = photo_list[photo_list.length - 1];
          huyou_info.photos = app.global_data.server_url + (photoUrl || that.data.pic_default);
          wx.setNavigationBarTitle({
            title: huyou_info.name + ' | ' + huyou_info.start_time + ' | ' + huyou_info.place,
          });
          base_fun.listData({ // 获取发起人昵称
            collection_name: 'users',
            query: {
              '_id': huyou_info.initator.$id,
            },
            getValues: 'nickname', // 用/号分隔需要获取的value
            success(res) {
              if (res.data !== []) {
                res.data = res.data[Object.keys(res.data)[0]];
                huyou_info.initator = res.data.nickname;
                that.setData({
                  huyou_info: huyou_info,
                });
              } else { // 用户不在数据库中
                wx.showToast({
                  title: 'ohoh，这位舞友的信息好像丢了~',
                  image: '../../../images/more.png',
                  duration: 1000,
                  mask: true,
                });
                setTimeout(function () {
                  wx.navigateTo({
                    url: '../../list/list',
                  });
                }, 1000);
              }
              // console.log('CALLBACK_SUCCESS');
            },
            fail() {
              setTimeout(function () {
                wx.navigateBack();
              }, 1000);
            }
          });
        } else { // 用户不在数据库中
          wx.showToast({
            title: 'ohoh，这位舞友的信息好像丢了~',
            image: '../../../images/more.png',
            duration: 1000,
            mask: true,
          });
          setTimeout(function () {
            wx.navigateTo({
              url: '../../list/list',
            });
          }, 1000);
        }
        // console.log('CALLBACK_SUCCESS');
      },
      fail() {
        setTimeout(function () {
          wx.navigateBack();
        }, 1000);
      }
    });
  },

  /**
   * 预览照片
   */
  previewImage: function (e) {
    // console.log(e);
    var current = e.target.dataset.src;
    wx.previewImage({
      current: current,
      urls: [current],
    });
  },
})