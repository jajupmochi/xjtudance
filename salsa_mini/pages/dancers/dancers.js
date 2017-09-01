// dancers.js
var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    dancer_list: app.global_data.dancer_list,
    dancers_length: app.global_data.dancer_list ? app.global_data.dancer_list.length : 0,

    imgUrl_boy: '../../images/boy-500.png',
    imgUrl_girl: '../../images/girl-500.png',

    isBanban: app.global_data.userInfo ? app.global_data.userInfo.rights.banban.is : false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
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
    if (this.data.dancer_list == null) {
      this.listDancers();
    }
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
      title: '萨友美照',
      path: '/pages/dancers/dancers',
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
    this.listDancers();
  },

  /**
   * 用户登录
   */
  userLogin: function () {
    var that = this;
    wx.showLoading({
      title: '正在加载...',
      mask: true,
    });
    wx.login({
      success: function (res) {
        wx.request({
          url: app.global_data.server_url + 'php/wx_getUser.php',
          data: {
            'code': res.code,
            '_id': '',
            'getValues': '_id/dance.baodao/rights.banban.is',
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
                title: '点击下方报名啦！',
                image: '../../images/more.png',
                duration: 1000,
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
   * 跳转到用户页面
   */
  openDancerProfile: function (e) {
    var _id = e.currentTarget.dataset._id.$id;
    if (app.global_data.userInfo != null) {
        if (app.global_data.userInfo.dance.baodao != '') { // 跳转
        wx.navigateTo({
          url: '../dancerPro/dancerPro?_id=' + _id,
        });
      } else { // 没有报到
        wx.showToast({
          title: '先点击下方报名吧！',
          image: '../../images/more.png',
          duration: 1500,
          mask: true,
        });
      }
    } else { // 没有用户信息
      wx.showToast({
        title: '先点击下方报名吧！',
        image: '../../images/more.png',
        duration: 1500,
        mask: true,
      });
    }
  },

  /**
   * 从服务器数据库获取报到舞友列表
   */
  listDancers: function () {
    wx.showLoading({
      title: '正在加载，稍等一下下...',
      mask: true,
    });
    var that = this;
    var limit = 10; // 获取舞友数量
    wx.request({
      url: app.global_data.server_url + 'php/wx_listDancers.php',
      data: {
        'skip': that.data.dancers_length,
        'limit': limit,
        'list_order': 'dance.baodao',
        'getValues': '_id/nickname/gender/person_info.QQ/wechat.id/dance.selfIntro', // 用/号分隔需要获取的value
      },
      header: {
        'content-type': 'application/json'
      },
      method: "POST",
      success: function (res) {
        wx.hideLoading();
        if (res.data == []) {
          wx.showToast({
            title: '这是全部舞友了...',
            duration: 2000
          });
        }
        that.setData({
          dancer_list: that.data.dancer_list ? Object.assign(that.data.dancer_list, res.data) : res.data, // 将数据传给全局变量dancer_list
          dancers_length: that.data.dancers_length + limit,
        });
        console.log(that.data.dancer_list);
        app.global_data.dancer_list = that.data.dancer_list;
      },
      fail: function (res) {
        wx.hideLoading();
        wx.showToast({
          title: 'oops，加载失败了...',
          image: '../../images/more.png',
          duration: 2000,
          mask: true,
        });
      }
    });
  },

  /**
  * 复制QQ号到剪切板
  */
  copyQQ: function (e) {
    wx.setClipboardData({
      data: e.currentTarget.dataset.qq,
      success: function (res) {
        wx.showToast({
          title: 'qq号已复制到手机剪切板',
          icon: 'success',
          duration: 1500,
          mask: true,
        });
      },
      fail: function (res) {
        wx.showToast({
          title: 'oops，复制失败了...',
          image: '../../images/more.png',
          duration: 1500,
          mask: true,
        });
      }
    });
  },

  /**
  * 复制微信id到剪切板
  */
  copyWxid: function (e) {
    wx.setClipboardData({
      data: e.currentTarget.dataset.wxid,
      success: function (res) {
        wx.showToast({
          title: '微信id已复制到手机剪切板',
          icon: 'success',
          duration: 1500,
          mask: true,
        });
      },
      fail: function (res) {
        wx.showToast({
          title: 'oops，复制失败了...',
          image: '../../images/more.png',
          duration: 1500,
          mask: true,
        });
      }
    });
  },
})