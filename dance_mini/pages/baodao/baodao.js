// pages/baodao/baodao.js
var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    gender_items: [
      { name: 'gentleman', value: 'Boy' },
      { name: 'lady', value: 'Girl' },
      { name: 'else', value: 'Gender Questionning' },
    ],
    knowfrom_items: [
      { name: '同学、朋友、教研室等', value: '同学、朋友、教研室等' },
      { name: '宣传单、海报等', value: '宣传单、海报等' },
      { name: '微信朋友圈、公众号、小程序等线上宣传', value: '微信朋友圈、公众号、小程序等线上宣传' },
      { name: '兵马俑BBS', value: '兵马俑BBS' },
      { name: '看到了舞会（思源、宪梓堂、四大发明广场）', value: '看到了舞会（思源、宪梓堂、四大发明广场）' },
      { name: '元旦游园会', value: '元旦游园会' },
      { name: '其他', value: '其他' },
    ],
    imgUrl_dancers: '../../images/dance1-200.png',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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
      title: '不Dance，怎么嗨！',
      path: '/pages/baodao/baodao',
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
   * 用户选择性别
   */
  genderChange: function (e) {
    this.setData({
      gender: e.detail.value,
    });
    // console.log('radio发生change事件，携带value值为：', e.detail.value)
  },

  eggdayChange: function (e) {
    // console.log('picker发送选择改变，携带值为', e.detail.value)
    this.setData({
      eggday: e.detail.value,
    })
  },

  knowfromChange: function (e) {
    this.setData({
      knowdancefrom: e.detail.value,
    })
    // console.log(this.data.knowdancefrom);
  },



  /**
   * 选择照片
   */
  choosePhotos: function () {
    var that = this;
    wx.chooseImage({
      count: 1,
      success: function (res) {
        //console.log(res);
        //var tempFilePaths = res.tempFilePaths;
        that.setData({
          photo_items: res.tempFilePaths,
        });
      }
    });
  },

  /**
  * 上传报到信息
  */
  baodao: function (e) {
    // console.log(e.detail.value);
    var isSend = true;
    if (e.detail.value.nickname == '') {
      this.setData({
        showmiss_nickname: true,
      });
      isSend = false;
    }
    if (this.data.gender == null) {
      this.setData({
        showmiss_gender: true,
      });
      isSend = false;
    }
    if (this.data.eggday == null) {
      this.setData({
        showmiss_eggday: true,
      });
      isSend = false;
    }
    if (e.detail.value.grade == '') {
      this.setData({
        showmiss_grade: true,
      });
      isSend = false;
    }
    if (e.detail.value.major == '') {
      this.setData({
        showmiss_major: true,
      });
      isSend = false;
    }
    if (e.detail.value.height == '') {
      this.setData({
        showmiss_height: true,
      });
      isSend = false;
    }
    if (e.detail.value.hometown == '') {
      this.setData({
        showmiss_hometown: true,
      });
      isSend = false;
    }
    if (e.detail.value.wechat_id == '') {
      this.setData({
        showmiss_wechat_id: true,
      });
      isSend = false;
    }
    if (e.detail.value.QQ == '') {
      this.setData({
        showmiss_QQ: true,
      });
      isSend = false;
    }
    if (e.detail.value.danceLevel == '') {
      this.setData({
        showmiss_danceLevel: true,
      });
      isSend = false;
    }
    if (this.data.knowdancefrom == null && e.detail.value.knowfromElse == '') {
      this.setData({
        showmiss_knowdancefrom: true,
      });
      isSend = false;
    }
    if (e.detail.value.selfIntro == '') {
      this.setData({
        showmiss_selfIntro: true,
      });
      isSend = false;
    }
    if (this.data.photo_items == null) {
      this.setData({
        showmiss_photo: true,
      });
      isSend = false;
    }

    if (!isSend) {
      wx.showToast({
        title: '请完善表格再提交~',
        image: '../../images/smiley-6_64.png',
        duration: 2000,
        mask: true,
      });
    } else {
      wx.showLoading({
        title: '上传中...',
        mask: true,
      });
      var formId = e.detail.formId;
      var that = this;
      var knowdancefrom = this.data.knowdancefrom;
      if (knowdancefrom != null) {
        knowdancefrom.push(e.detail.value.knowfromElse);
        knowdancefrom = knowdancefrom.toString();
      } else {
        knowdancefrom = e.detail.value.knowfromElse;
      }

      wx.login({
        success: function (res) {
          wx.uploadFile({
            url: app.global_data.server_url + 'php/wx_baodao.php',
            filePath: that.data.photo_items[0],
            name: 'photo',
            formData: {
              'formId': formId,
              'code': res.code,
              'nickname': e.detail.value.nickname,
              'gender': that.data.gender,
              'eggday': that.data.eggday,
              'grade': e.detail.value.grade,
              'major': e.detail.value.major,
              'height': e.detail.value.height,
              'hometown': e.detail.value.hometown,
              'wechat_id': e.detail.value.wechat_id,
              'QQ': e.detail.value.QQ,
              'contact': e.detail.value.contact,
              'danceLevel': e.detail.value.danceLevel,
              'knowdancefrom': knowdancefrom,
              'selfIntro': e.detail.value.selfIntro,
            },
            success: function (res) { // 报到成功
              app.global_data.userInfo = res.data;
              wx.hideLoading();
              wx.showToast({
                title: '报到成功！欢迎加入dance大家庭！',
                image: '../../images/dance1-200.png',
                duration: 2000,
                mask: true,
              });
              setTimeout(function () {
                wx.navigateTo({
                  url: '../dancers/dancers',
                });
              }, 2000);
            },
            fail: function () {
              wx.hideLoading(); // 网络错误
              wx.showToast({
                title: '报到失败！请点击重试！',
                image: '../../images/more.png',
                duration: 3000,
                mask: true,
              });
            }
          });
        },
        fail: function () { // 获取微信code失败
          wx.hideLoading();
          wx.showToast({
            title: '报到失败！请点击重试！',
            image: '../../images/more.png',
            duration: 3000,
            mask: true,
          });
        }
      });

    }
  },

  /**
  * 跳转到dancer列表页
  */
  toDancersPage: function () {
    wx.navigateTo({
      url: '../dancers/dancers',
    });
  },
})