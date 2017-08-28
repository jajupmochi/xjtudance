// dancerPro.js
var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    isBanban: app.global_data.userInfo ? app.global_data.userInfo.rights.banban.is : false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (e) {
    var _id = e._id;
    this.getDancerInfo(_id);
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
      title: '虫虫 ' + this.data.dancer_info.nickname + ' 报到',
      path: '/pages/dancerPro/dancerPro?_id=' + this.data._id,
      imageUrl: this.data.photo,
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 获取用户信息
   */
  getDancerInfo: function (_id) {
    this.setData({
      _id: _id,
    });
    var that = this;
    wx.showLoading({
      title: '正在跳转...',
      mask: true,
    });
    if (app.global_data.userInfo.rights.banban.is) {
      var getValues = '_id/nickname/gender/person_info.eggday/person_info.grade/person_info.major/person_info.height/person_info.hometown/dance.danceLevel/dance.selfIntro/dance.photos/dance.baodao/wechat.id/person_info.QQ/person_info.contact/dance.knowdancefrom';
    } else {
      var getValues = '_id/nickname/gender/person_info.eggday/person_info.grade/person_info.major/person_info.height/person_info.hometown/dance.danceLevel/dance.selfIntro/dance.photos/dance.baodao';
    }
    wx.request({
      url: app.global_data.server_url + 'php/wx_getUser.php',
      data: {
        'code': '',
        '_id': that.data._id,
        'getValues': getValues,
      },
      header: {
        'content-type': 'application/json'
      },
      method: "POST",
      success: function (res) {
        wx.hideLoading();
        if (res.data !== null) {
          console.log(res.data);
          that.setData({
            dancer_info: res.data,
            photo: app.global_data.server_url + res.data.dance.photos[res.data.dance.photos.length - 1],
          });
          wx.setNavigationBarTitle({
            title: '虫虫' + res.data.nickname + '的信息',
          });
        } else { // 用户不在数据库中
          wx.showToast({
            title: 'ohoh，这位舞友的信息好像丢了~',
            image: '../../images/more.png',
            duration: 1500,
            mask: true,
          });
          setTimeout(function () {
            wx.navigateTo({
              url: '../dancers/dancers',
            });
          }, 1500);
        }
      },
      fail: function () {
        wx.hideLoading();
        wx.showToast({
          title: 'oops，网络bug了，再试一次吧',
          image: '../../images/more.png',
          duration: 1500,
          mask: true,
        });
        setTimeout(function () {
          wx.navigateTo({
            url: '../dancers/dancers',
          });
        }, 1500);
      }
    });
  }
})