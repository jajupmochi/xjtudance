// danceIntro.js
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
    var audio_src_path = app.global_data.server_url + 'data/audios/dance/';
    var audio_poster_path = app.global_data.server_url + 'data/images/dance/audio-posters/';
    var video_poster_path = app.global_data.server_url + 'data/images/dance/dance-logo-5_4.jpg';
    this.setData({
      bgImg: app.global_data.server_url + 'data/images/dance/dance.jpg',
      dance_list: [
        {
          id: 1,
          name: '水兵',
          audio_poster: audio_poster_path + 'Beethoven_Virus-Diana_Boncheva.jpg',
          audio_name: 'Beethoven Virus',
          audio_author: 'Diana Boncheva',
          audio_src: audio_src_path + 'Beethoven_Virus-Diana_Boncheva.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 2,
          name: '吉特巴',
          audio_poster: audio_poster_path + 'Booty_Music-Deep_Side.jpg',
          audio_name: 'Booty Music',
          audio_author: 'Deep Side',
          audio_src: audio_src_path + 'Booty_Music-Deep_Side.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 3,
          name: '慢三',
          audio_poster: audio_poster_path + 'aidehuaerzi-yuhaoming-zhengshuang.jpg',
          audio_name: '爱的华尔兹',
          audio_author: '郑爽 俞灏明',
          audio_src: audio_src_path + 'aidehuaerzi-yuhaoming-zhengshuang.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 4,
          name: '舞厅伦巴',
          audio_poster: audio_poster_path + 'youdiantian-wangsulong-By2.jpg',
          audio_name: '有点甜',
          audio_author: '汪苏泷 By2',
          audio_src: audio_src_path + 'youdiantian-wangsulong-By2.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 5,
          name: '慢四',
          audio_poster: audio_poster_path + 'tashuo-linjunjie.jpg',
          audio_name: '她说',
          audio_author: '林俊杰',
          audio_src: audio_src_path + 'tashuo-linjunjie.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 6,
          name: 'salsa',
          audio_poster: audio_poster_path + 'Hey_Soul_Sister_Salsa_Version-Hotel_Buenavida.jpg',
          audio_name: 'Hey Soul Sister (Salsa Version)',
          audio_author: 'Hotel Buenavida',
          audio_src: audio_src_path + 'Hey_Soul_Sister_Salsa_Version-Hotel_Buenavida.mp3',
          video_poster: video_poster_path,
          video_src: 'http://123.151.38.139/vhot2.qqvideo.tc.qq.com/AXZ7KremZ4zP8keGocU_noKjei9Lry1whi6M2oYd3cSA/e03626p6w13.p703.1.mp4?sdtfrom=v1010&guid=22330636a8fa136a95d6e9c8fd035c57&vkey=E66D82A058D9DD55ED33D03E9292A6DE3710D226719A8774A6FB9B07701E857CB02E7A8418A65824DCC61175CA33E6D1F39713E6686EB5ABE8DD8FD5EA8F47C7966CEBC406D86F1D2DC45992480216D21B9754340C4C61ABE84F2DB39F0526DD674B738133BAD5AC095639909883EF1D765C44D21E2F1906&platform=2&ocid=442439084&ocid=548148652&ocid=2677137930',
        },
        {
          id: 7,
          name: '快三',
          audio_poster: audio_poster_path + 'Larrons_En_Foire-Raphael_Beau.jpg',
          audio_name: 'Larrons En Foire',
          audio_author: 'Raphaël Beau',
          audio_src: audio_src_path + 'Larrons_En_Foire-Raphael_Beau.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 8,
          name: '国标摩登',
          audio_poster: audio_poster_path + 'Three_Times_A_Lady-Lionel_Richie.jpg',
          audio_name: 'Three Times A Lady',
          audio_author: 'Lionel Richie',
          audio_src: audio_src_path + 'Three_Times_A_Lady-Lionel_Richie.mp3',
          video_poster: video_poster_path,
          video_src: '',
        },
        {
          id: 9,
          name: '国标拉丁',
          audio_poster: audio_poster_path + 'Thinking_Out_Loud-Ed_Sheeran.jpg',
          audio_name: 'Thinking Out Loud',
          audio_author: 'Ed Sheeran',
          audio_src: audio_src_path + 'Thinking_Out_Loud-Ed_Sheeran.mp3',
          video_poster: video_poster_path,
          video_src: 'http://123.151.41.76/vhot2.qqvideo.tc.qq.com/AG26U2pEeEJeIMn4DSg4eXMzs3gbz2dkErvi_dwtOl78/i03546xpqj2.p703.1.mp4?sha=2AE5953965866E9D4627BC748044F74D80549895&sdtfrom=v1010&guid=22330636a8fa136a95d6e9c8fd035c57&vkey=B7C5C3E2FD93F9C83246B64388C3585B8C8BA6E0D5541B74056E15536CB87EB1089EE4404E99EEE482B4AF070893782766FCE38E7C01855679F1FC0C8B5FEAE4B869D72E86B952E8531E3CFF1C9A904392CDFDD37E36DB5215603D073D8632A4FBDD34494227DCF050734D5067EB3F35DFD0F260568CC73F&platform=2&ocid=442439084&ocid=783029676&ocid=797505290',
        },
        {
          id: 10,
          name: '其他舞种：阿根廷tango',
          audio_poster: audio_poster_path + 'Three_Times_A_Lady-Carlos-Gardel.jpg',
          audio_name: 'One Step Away',
          audio_author: 'Carlos Gardel',
          audio_src: audio_src_path + 'Three_Times_A_Lady-Carlos-Gardel.mp3',
          video_poster: video_poster_path,
          video_src: 'http://123.151.38.141/vhot2.qqvideo.tc.qq.com/ANHdYsk099x1rncnQ6QTSjM0jiSKsuSiiSM7vBJiKMMI/b0353hsfkag.p703.1.mp4?sha=&sdtfrom=v1010&guid=22330636a8fa136a95d6e9c8fd035c57&vkey=B313C1AE435D91CC2455A0CDA3834E447E62B9BD8AD2C08151D3F2B75A7EF5679348EC9B7C1FCF606D8CB24CE1D45664A9DEC3DBC93B13D3CFEEEBEC4575B21F652AF03014CDFDBDA9DF20FBD4F7FC19F494E43BA36FF6BDD9E454E1685AB5A1137E835C44B51C1EF6CD8E6C68750AFAD7B8B624FD10CA4C&platform=2&ocid=2648053932&ocid=252712364&ocid=360969738',
        },
        {
          id: 11,
          name: '其他舞种：bachata',
          audio_poster: audio_poster_path + 'Pillow_Talk_Bachata_Remix-Zayn.jpg',
          audio_name: 'Pillow Talk (Bachata Remix)',
          audio_author: 'Zayn',
          audio_src: audio_src_path + 'Pillow_Talk_Bachata_Remix-Zayn.mp3',
          video_poster: video_poster_path,
          video_src: 'http://123.151.93.160/vhot2.qqvideo.tc.qq.com/A0WpbZLpfQKx4I02oMrUWB_V6Jhu-viTBOaAXZE28RoU/v0358ptkkw3.p703.1.mp4?sha=&sdtfrom=v1010&guid=22330636a8fa136a95d6e9c8fd035c57&vkey=F395F96913E35822B870FA2D626AF8D0ECF5B8EEFECA87A8589FCF4B973CC80F82B82DE2D3D7A91417E5A2118EFE9FE072E0AB7B6DB00976EB4645DD65A45E9884B12C19377255C19EAD1FE965CB608EB391D1905BE199EBB3D1D23BA4FE2835C82968D40B293C88C291EB7AD13B3B04A42DD3A3C3877541&platform=2&ocid=2664831148&ocid=252712364&ocid=360969738',
        },
      ], 
      audioAction: {
        method: 'pause'
      }
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
  onShareAppMessage: function () {
    return {
      title: '不Dance，怎么嗨！',
      path: '/pages/danceIntro/danceIntro',
      imageUrl: app.global_data.server_url + 'data/images/dance/dance-logo-5_4.jpg',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  }
})