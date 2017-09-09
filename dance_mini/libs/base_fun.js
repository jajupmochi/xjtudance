/*******************************************************************************
基本函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-08
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

var app = getApp();

/**
 * 从数据库读取列表信息
 * @param options object 传入参数，json形式的数据，具体项包含：
 *  collection_name string 集合名称
 *  skip integer 跳过的数据数量
 *  limit integer 获取的数据数量
 *  list_order string 获取数据的顺序
 *  query string 查询条件
 *  getValues string 要获取的具体项
 *  success function request返回成功时执行的函数
 *  fail function request返回失败时执行的函数
 *  complete function request完成时执行的函数
 */
function listData(options) {
  if (typeof options !== 'object') {
    var message = '请求传参应为 object 类型，但实际传了 ' + (typeof options) + ' 类型';
    console.log(message);
    // throw new RequestError(constants.ERR_INVALID_PARAMS, message);
  }

  var collection_name = options.collection_name;
  var skip = options.skip || 0; // 默认不跳过
  var limit = options.limit || 1; // 默认读取一条信息
  var list_order = options.list_order || '_id';
  var query = options.query || '';
  var getValues = options.getValues || '';
  var noop = function noop() { };
  var success = options.success || noop;
  var fail = options.fail || noop;
  var complete = options.complete || noop;

  wx.showLoading({
    title: '正在加载，稍等一下下...',
    mask: true,
  });
  wx.request({
    url: app.global_data.server_url + 'php/wx_listData.php',
    data: {
      'collection_name': collection_name,
      'skip': skip,
      'limit': limit,
      'list_order': list_order,
      'query': query,
      'getValues': getValues, // 用/号分隔需要获取的value
    },
    header: {
      'content-type': 'application/json'
    },
    method: "POST",
    success: function (res) {
      wx.hideLoading();
      if (res.data == []) {
        wx.showToast({
          title: '没有更多了...',
          duration: 1000
        });
      }
      success(res);
    },
    fail: function (res) {
      wx.hideLoading();
      wx.showToast({
        title: 'oops，加载失败了...',
        image: '../images/more.png',
        duration: 1000,
        mask: true,
      });
    }
  });
}

module.exports = {
  listData: listData,
};