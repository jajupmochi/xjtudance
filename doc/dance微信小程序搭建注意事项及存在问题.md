# 存在问题：

1. Post页面提交文章表单时必须先输入标题，再输入内容，否则无法提交或内容为空。
问题由placeholder属性造成，如果改为value属性则不存在这个问题。

2. 所有请求均考虑使用post方法。

3. getData方法使用过于频繁。

4. 手机测试时，有时候文章content无法从article页面传到modify页面（无论长短）。添加一些特殊字符后会报错无法传递。前者在添加console.log(this.data.feed);语句后好像问题消失了。

5. 从bmybbs获取文章时，标题、作者昵称等内容没有做特殊字符转换，正文特殊字符转换可能亦不完善，繁体字转换为utf-8时出现错误。合集处理地有问题。

6. 从bmybbs clone全部内容时，未考虑版面没有文章的情况，未考虑在clone过程中版面有修改、插入、删除文章的情况，开始几篇插入顺序有错误，为考虑合集情况

7. 从bmybbs clone全部内容时，php文件会超时。
php脚本默认超时时间为30秒，在php脚本中加入语句set_time_limit(0);可设置超时时间为无限。（对chrom浏览器无效）
此外浏览器可能有超时时间（如chrome为约30秒），此时可在服务器中执行php 文件目录/文件名.php。（执行到约200秒时依然超时）
使用IE 11，其超时时间为60分钟（实际1600秒时超时），而且即使关闭IE后数据库依然会自动执行一会儿。或使用edge浏览器（时间依然不够）。

8. 未禁止从网页端访问php文件及其他安全属性。

9. 从数据库调用用户信息时未采用安全措施，微信openid直接存在了用户数据库里，可能会有安全问题，同时openid有直接在网上传输的安全隐患。
可考虑采用类似微信openid的做法

10. 用户信息传输未加密。
参考微信小程序wx.userInfo()的加密方式。

11. 小程序端onlogin时嵌套层数过多，不知道会不会影响登录结果，最好改为promise模式。

12. 时间设置可能会导致bmy文章时间和数据库中文章发布时间不一致。

13. 为了给用户提供更好的小程序环境，我们约定在一段时间后（具体时间会做通知），若还出现以下情况（包括但不限于），将无法通过审核
初次打开小程序就弹框授权用户信息
未处理用户拒绝授权的情况
强制要求用户授权
已经上线的小程序不会受到影响。
参考：https://developers.weixin.qq.com/blogdetail?action=get_post_info&lang=zh_CN&token=1532172553&docid=c45683ebfa39ce8fe71def0631fad26b

14. 在php + mongodb中使用正则表达式时，只可使用MongoRegex类创建正则表达式，但官方文档建议使用MongoDB\BSON\Regex替换，然而后者会报错。
$regex = new MongoRegex('/^jajupmochi/i'); 正确
$regex = new MongoDB\BSON\Regex('^jajupmochi', 'i'); 报错
参考：http://www.php.net/manual/zh/class.mongoregex.php

15. 【重要！！！】在wx_writeDiary.php中，获取刚刚发布到兵马俑BBS的文章的url的方法不够严密，仅仅通过判断最近一次的作者文章。如果作者同时在兵马俑BBS发文，可能会出错。另外，如果兵马俑BBS创建文章的速度较慢（如包含图），wx_writeDiary.php可能会在查找BBS文章列表时找不到刚刚发布的文章。

16. 【重要！！！】应建立网站防护，如防水帖：同名同内容帖子一定时间内不可重复发送；连续发表两篇文章要隔一段时间等。注意和兵马俑的发帖规则相适应，否则可能会出现bmyurl获取不到的情况。

17. 【重要！！！】刷新微信小程序日记列表的方式有问题，如果其他人发表了文章，加载更多文章的时候就会有问题。

# 搭建注意事项：

1. 一定要创建备份数据库！血泪的教训！

2. 重要数据不要写在php文件里，如小程序的id和密匙，可存储在数据库中，每次使用时提取。

3. 禁止从网页端访问php文件的方法：

4. 在数据库中创建用户，便于不同程序设计人员协作。

5. 上传到代码到github便于版本维护，注意不要上传敏感信息。

6. 后台服务器代码和数据库建立test版本，便于调试，同时与用户体验版分开，限制设计人员过于频繁地提交体验版程序。

7. 将服务器域名等内容设置为全局变量。

8. 参考bmy_wap，将常用函数单独存放调用。

9. 注意！php中调用mongodb，$set等参数只能用单引号！不能用单引号！会报错！
如$collection->update(array("_id" => new MongoId($_id)), array('$set' => array("KEY" => CONTENT))); 正确
$collection->update(array("_id" => new MongoId($_id)), array("$set" => array("KEY" => CONTENT))); 错误

10. PHP、小程序中，如无特殊原因字符串尽量使用单引号，便于统一。考虑到效率，PHP中具体规则可参见：
首先，表示简单的数据时（不需要转义），尽量用单引号。
   'Cal: Are you good at long jump?'
但如果因为使用单引号，而需要进行转义（即包含单引号），考虑使用双引号。
   'Cal: Yes. But, you know, it\'s written.' => "Cal: Yes. But, you know, it's written."
如果需要表示变量，应尽量使用花括号。
   "Cal: $to" => "Cal: {$to}, you shouldn't go shopping."
尽量使其连续：
   'Cal: '.$calsaid => "Cal: $calsaid" => "Cal: {$calsaid}"
http://blog.sina.com.cn/s/blog_640b03390100sham.html

11. php + mongoDB update多条数据一定要加array('multiple' => true)！