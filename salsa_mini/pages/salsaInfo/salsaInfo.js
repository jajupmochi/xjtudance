// salsaInfo.js
var app = getApp();
var touchDot = 0;//触摸时的原点
var time = 0;//  时间记录，用于滑动时且时间小于1s则执行左右滑动
var interval = "";// 记录/清理 时间记录

Page({

  /**
   * 页面的初始数据
   */
  data: {
    pic_title: '../../images/salsa-logo.jpg',
    pic5: app.global_data.server_url + 'data/images/salsa/pic5.jpg',
    pic8: app.global_data.server_url + 'data/images/salsa/pic8.jpg',
    wechat_qr: app.global_data.server_url + 'data/images/salsa/wechat_qr.jpg',
    video_src: app.global_data.server_url + 'data/videos/jumpgrace.mp4',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.showLoading({
      title: '加载中...',
      mask: true,
    });
    console.log("OnLoad salsainfo");
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

/*// 触摸开始事件
touchStart: function(e){ 
   touchDot = e.touches[0].pageX; // 获取触摸时的原点
   // 使用js计时器记录时间    
   interval = setInterval(function(){
       time++;
   },100); 
},
// 触摸移动事件
touchMove: function(e){ 
   var touchMove = e.touches[0].pageX;
   console.log("Move:"+touchMove+" Dot:"+touchDot+" diff:"+(touchMove - touchDot)+" time:"+time);
   // 向左滑动   
   if(touchMove - touchDot <= -40 && time < 10){
       ;
       }
   // 向右滑动
   if(touchMove - touchDot >= 40 && time < 10){
         wx.switchTab({
           url: '../baodao/baodao',
       });
   }
   // touchDot = touchMove; //每移动一次把上一次的点作为原点（好像没啥用）
},
 // 触摸结束事件
touchEnd: function(e){
   clearInterval(interval); // 清除setInterval
   time = 0;
   tmpFlag = true; // 回复滑动事件
},*/

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