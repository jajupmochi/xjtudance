var app = getApp()
Page({
  data: {
    feed: [],
  },
  // 载入页面事件
  onLoad: function (e) {
    var that = this;
    this.setData({
      feed: JSON.parse(e.feed),
    });
  },

  // 修改文章
  submitArticle: function (e) {
    var id = this.data.feed._id.$id;
    var formId = e.detail.formId;
    var values = e.detail.value;
    var title = values.title;
    var content = values.content;

    var openid = app.openid;

    wx.request({
      url: app.global_data.server_url + 'php/updateArticle.php',
      data: {
        id: id, // 文章id
        openid: openid,
        formId: formId,
        title: title, // 标题
        content: content, // 内容
        source: "wxmini" // 来源：包括bbs（兵马俑bbs）、bbswap（bbs wap版）、wxmini（微信小程序）
      },
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
          wx.showToast({ // 显示成功提示
            title: '修改成功！',
            icon: 'success',
            duration: 1000
          }),
          setTimeout(function () {
            wx.navigateBack({ // 返回index页
              delta: 2
            })
          }, 500)
        //}
      }
    })

  }
})