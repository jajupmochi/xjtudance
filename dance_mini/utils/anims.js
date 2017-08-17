/*******************************************************************************
动画函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-16
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

/**
* 打开和关闭书写日记区域
* @param boolean hideWriteArea 是否隐藏书写日记区域
* @return animation 动画实例
*/
function switchWriteArea(hideWriteArea) {
  var anim = wx.createAnimation({ // 第1步：创建动画实例
    duration: 600,  // 动画时长
    timingFunction: "ease-in-out",
    delay: 0,  // 0则不延迟
    transformOrigin: "left top 0",
  });
  if (hideWriteArea) {
    anim.height("1200rpx").scaleY(1).step().opacity(1).step(); // 第3步：执行打开动画
  } else {
    anim.opacity(0).step().height(0).scaleY(0).step(); // 第3步：执行关闭动画
  }
  return anim;
};

/**
* 打开和关闭连接兵马俑bbs账户的弹出窗口
* @return animation 动画实例
*/
function switchConBmyArea() { 
  var anim = wx.createAnimation({
    duration: 1000,  //动画时长  
    timingFunction: "linear", // 线性
    delay: 0,  // 0则不延迟
    transformOrigin: "center center 0",
  });
  anim.height(0).step();
  return anim;

}

module.exports = {
  switchWriteArea: switchWriteArea,
  switchConBmyArea: switchConBmyArea,
};