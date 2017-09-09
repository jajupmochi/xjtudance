// post.js
var app = getApp();

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
      name: '舞会',
      time: '今晚9:30',
      place: '思源',
      comment: '行程，注意事项，心情',
      tag: '舞会',
      peopleNum: '不限',
      initator: (!app || !app.global_data.userInfo) ? '匿名' : app.global_data.userInfo.nickname,
    });
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
  onShareAppMessage: function (res) {
    return {
      title: '发忽悠',
      path: '/pages/huyou/post/post',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 选择照片
   */
  choosePhotos: function () {
    var that = this;
    wx.chooseImage({
      count: 1,
      success: function (res) {
        that.setData({
          photo_items: res.tempFilePaths,
        });
      }
    });
  },

  /**
  * 发布忽悠
  */
  baodao: function (e) {
    if (!app || !app.global_data.userInfo || !app.global_data.userInfo.rights.banban.is) {
      wx.showToast({
        title: '登录才能发忽悠哦~',
        image: '../../../images/smiley-6_64.png',
        duration: 1000,
        mask: true,
      });
      // return;
    }

    wx.showLoading({
      title: '忽悠忽悠忽悠...',
      mask: true,
    });
    var value = e.detail.value;
    var that = this;
    wx.login({
      success: function (res) {
        if (that.data.photo_items) { // 上传了图片
          wx.uploadFile({
            url: app.global_data.server_url + 'php/wx_huyou.php',
            filePath: that.data.photo_items[0],
            name: 'photo',
            formData: {
              'code': res.code,
              'name': value.name == '' ? that.data.name : value.name,
              'time': value.time == '' ? that.data.time : value.time,
              'place': value.place == '' ? that.data.place : value.place,
              'comment': value.comment == '' ? 'rt' : value.comment,
              'tag': value.tag == '' ? that.data.tag : value.tag,
              'initator': value.initator == '' ? that.data.initator : value.initator,
              'peopleNum': value.peopleNum == '' ? that.data.peopleNum : value.peopleNum,
            },
            success: function (res) { // 忽悠发送成功
              console.log(res.data);
              app.global_data.userInfo = res.data;
              wx.hideLoading();
              wx.showToast({
                title: '忽悠成功！',
                image: '../../../images/dance1-200.png',
                duration: 1000,
                mask: true,
              });
              setTimeout(function () {
                wx.navigateTo({
                  url: '../list/list',
                });
              }, 1000);
            },
            fail: function () {
              wx.hideLoading(); // 网络错误
              wx.showToast({
                title: '忽悠失败！',
                image: '../../../images/more.png',
                duration: 1000,
                mask: true,
              });
            }
          });
        } else { // 没有上传图片
          wx.request({
            url: app.global_data.server_url + 'php/wx_huyou.php',
            data: {
              'code': res.code,
              'name': value.name == '' ? that.data.name : value.name,
              'time': value.time == '' ? that.data.time : value.time,
              'place': value.place == '' ? that.data.place : value.place,
              'comment': value.comment == '' ? 'rt' : value.comment,
              'tag': value.tag == '' ? that.data.tag : value.tag,
              'initator': value.initator == '' ? that.data.initator : value.initator,
              'peopleNum': value.peopleNum == '' ? that.data.peopleNum : value.peopleNum,
            },
            header: {
              'content-type': 'application/x-www-form-urlencoded',
            },
            method: "POST",
            success: function (res) { // 忽悠发送成功
            console.log(res.data);
              app.global_data.userInfo = res.data;
              wx.hideLoading();
              wx.showToast({
                title: '忽悠成功！',
                image: '../../../images/dance1-200.png',
                duration: 1000,
                mask: true,
              });
              setTimeout(function () {
                wx.navigateTo({
                  url: '../list/list',
                });
              }, 1000);
            },
            fail: function () {
              wx.hideLoading(); // 网络错误
              wx.showToast({
                title: '忽悠失败！',
                image: '../../../images/more.png',
                duration: 1000,
                mask: true,
              });
            }
          });
        }
      },
      fail: function () { // 获取微信code失败
        wx.hideLoading();
        wx.showToast({
          title: '忽悠失败！',
          image: '../../../images/more.png',
          duration: 1000,
          mask: true,
        });
      }
    });
  },
})