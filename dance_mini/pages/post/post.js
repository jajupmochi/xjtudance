var app = getApp()
Page({
  // 将用户发表的文章数据传给后台服务器
  submitArticle: function (e) {
    // console.log(e);
    var formId = e.detail.formId;
    var values = e.detail.value;
    var title = values.title;
    var content = values.content;

    var openid = app.openid;

    wx.request({
      url: 'https://57247578.qcloud.la/php/acceptArticle.php',
      data: {
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
        if (res.data == 'success') { // 'success'代表数据成功存入后台数据库
          wx.showToast({ // 显示成功提示
            title: '发表成功！',
            icon: 'success',
            duration: 1000
          }),
          setTimeout(function () {
            wx.navigateBack({ // 返回index页
              delta: 1
            })
          }, 1000)
        }
      }
    })

  }
})