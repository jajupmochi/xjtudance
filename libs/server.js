/*******************************************************************************
服务器相关函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-04
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/
var server_url = null;

/**
 * 获取可用的服务器url
 * @return string 服务器url
 */
function getServerUrl() {
  wx.showLoading({
    title: '正在连接服务器...',
    mask: true,
  });
  wx.request({
    url: 'https://xjtudance.top/aishangsalsa/php/wx_connectServer.php',
    method: "POST",
    success: function (res) {
      console.log(res);
      return res.data.msg == 'SERVER_CON_SUCCESS' ? 'https://xjtudance.top/aishangsalsa/' : 'https://57247578.qcloud.la/aishangsalsa/';
      this.server_url = res.data.msg == 'SERVER_CON_SUCCESS' ? 'https://xjtudance.top/aishangsalsa/' : 'https://57247578.qcloud.la/aishangsalsa/';
      wx.hideLoading();
    },
    fail: function (res) {
      console.log(res);
      return 'https://57247578.qcloud.la/aishangsalsa/';
      this.server_url = 'https://57247578.qcloud.la/aishangsalsa/';
      wx.hideLoading();
    }
  });
}

module.exports = {
  server_url: server_url,
  getServerUrl: getServerUrl,
};