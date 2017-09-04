// salsaInfo.js
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
    wx.showLoading({
      title: '加载中...',
      mask: true,
    });
    this.setData({
      pic_title: '../../images/salsa-logo.jpg',
      pic2: app.global_data.server_url + 'data/images/salsa/pic2.png',
      pic3: app.global_data.server_url + 'data/images/salsa/pic3.png',
      pic4: app.global_data.server_url + 'data/images/salsa/pic4.jpg',
      pic5: app.global_data.server_url + 'data/images/salsa/pic5.png',
      pic6: app.global_data.server_url + 'data/images/salsa/pic6.png',
      pic7: app.global_data.server_url + 'data/images/salsa/pic7.jpg',
      pic8: app.global_data.server_url + 'data/images/salsa/pic8.png',
      pic9: app.global_data.server_url + 'data/images/salsa/pic9.jpg',
      pic10: app.global_data.server_url + 'data/images/salsa/pic10.png',
      wechat_qr: app.global_data.server_url + 'data/images/salsa/wechat_qr.png',
      mini_qr: app.global_data.server_url + 'data/images/salsa/mini_qr.jpg',
    });
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    //this.audioCtx = wx.createAudioContext('myAudio')
    //this.videoContext = wx.createVideoContext('myVideo')
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
      title: '爱尚salsa，爱上salsa！',
      path: '/pages/salsaInfo/salsaInfo',
      imageUrl: app.global_data.server_url + 'data/images/salsa/salsa-logo2.jpg',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 预览图片
   */
  previewImage: function (e) {
    console.log(e);
    var current = e.target.dataset.src;
    wx.previewImage({
      current: current,
      urls: [current],
    });
  },

  /**
   * 图片加载完成回调
   */
  finishLoadImg: function () {
    wx.hideLoading();
    this.setData({
      showword: true,
    });
  }
})