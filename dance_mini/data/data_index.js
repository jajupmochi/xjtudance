/**
 * Created by Rebecca_Han on 16/10/26.
 */
module.exports = {

}

//用于创建XMLHttpRequest对象 
function createXmlHttp() {
  //根据window.XMLHttpRequest对象是否存在使用不同的创建方式 
  if (window.XMLHttpRequest) {
    xmlHttp = new XMLHttpRequest(); //FireFox、Opera等浏览器支持的创建方式 
  } else {
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE浏览器支持的创建方式 
  }
}
//直接通过XMLHttpRequest对象获取远程网页源代码 
function getSource() {
  var url = document.getElementById("url").value; //获取目标地址信息 
  //地址为空时提示用户输入 
  if (url == "") {
    alert("请输入网页地址。");
    return;
  }
  document.getElementById("source").value = "正在加载……"; //提示正在加载 
  createXmlHttp(); //创建XMLHttpRequest对象 
  xmlHttp.onreadystatechange = writeSource; //设置回调函数 
  xmlHttp.open("GET", url, true);
  xmlHttp.send(null);
}
//将远程网页源代码写入页面文字区域 
function writeSource() {
  if (xmlHttp.readyState == 4) {
    document.getElementById("source").value = xmlHttp.responseText;
  }
} 

var index= {
    "id": 1,
        "data": [
        {
            "question_id": 1,
            "answer_id": 3,
            "feed_source_id": 23,
            "feed_source_name": "小温",
            "feed_source_txt": "（Williance）",
            "feed_source_img": "../../images/icon1.jpeg",
            "question": "忽悠又来了",
            "answer_ctnt": "忽悠又来了：\n还是9点，还是思源南阶，愿今晚的舞步能充盈每一个人的心",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 2,
            "answer_id": 25,
            "feed_source_id": 24,
            "feed_source_name": "小温",
            "feed_source_txt": "（Williance）",
            "feed_source_img": "../../images/icon8.jpg",
            "question": "又一波忽悠帖",
            "answer_ctnt": "忽悠：\n2017过去一半了，\n你的小目标还好吗？\n又是一个新周末，\n不变的还是今晚的标配版思源，\n还是老时间8点半。",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 3,
            "answer_id": 61,
            "feed_source_id": 25,
            "feed_source_name": "小温",
            "feed_source_txt": "（Williance）",
            "feed_source_img": "../../images/icon9.jpeg",
            "question": "[忽悠：8点半思源南阶]",
            "answer_ctnt": "忽悠：\n不敢奢求太多，只想把瞬间当成永远，把现在都变成回忆，一点一滴\n今晚思源8点半，期待见到舞池中的你",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 4,
            "answer_id": 3,
            "feed_source_id": 23,
            "feed_source_name": "小昭",
            "feed_source_txt": "（mchellew）",
            "feed_source_img": "../../images/icon1.jpeg",
            "question": "迟到的夏至",
            "answer_ctnt": "之前，版上有一个长发飘飘、特别文艺的妹纸，叫夏至，是北北的小徒弟。 \n每到夏至时，mm会发个小帖，dance会借着sy或者基地的时候小小的聚一下。 \n\n一晃也离开学校好多年了。...",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 5,
            "answer_id": 25,
            "feed_source_id": 24,
            "feed_source_name": "穹傲",
            "feed_source_txt": "（zhboss）",
            "feed_source_img": "../../images/icon8.jpg",
            "question": "【拜师贴】穹傲拜荷叶为师 ",
            "answer_ctnt": "走过路过不要错过啦~ \n\n有钱的捧个钱场，没钱的回家取钱捧个钱场啦~ \n\n白驹过隙...",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 6,
            "answer_id": 61,
            "feed_source_id": 25,
            "feed_source_name": "绝世",
            "feed_source_txt": "（jueshi）",
            "feed_source_img": "../../images/icon9.jpeg",
            "question": "拉四布鲁斯随便记",
            "answer_ctnt": "1.拉四手上的力不能一直紧绷，而是轻轻松松的引带，推拉都应该带动的是对方的重心，而不是切线方向。整体跳下来可以很轻松自然。 \n2.拉四也要注意架型，手上不能掉下来。带女生转圈应该提在高处引带转圈。拉四和平四的最大区别在于身体的延伸感觉，动作结束后应该再向外延伸一段。...",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 7,
            "answer_id": 3,
            "feed_source_id": 23,
            "feed_source_name": "mixia",
            "feed_source_txt": "（atlobrw）",
            "feed_source_img": "../../images/icon1.jpeg",
            "question": "新手atlobrw(可乐)报到",
            "answer_ctnt": "您的id是: \natlobrw \n\n昵称呢?:\n\n可乐...",
            "good_num": "112",
            "comment_num": "18"
        },
        {
            "question_id": 8,
            "answer_id": 25,
            "feed_source_id": 24,
            "feed_source_name": "小来",
            "feed_source_txt": "（MJyongyuan）",
            "feed_source_img": "../../images/icon8.jpg",
            "question": "【毕业小聚】后会无期——2017毕业留声",
            "answer_ctnt": "【毕业小聚】后会无期——2017毕业留声 \n——君拂\n\n& nbsp; 2017年6月16日& nbsp; 夜\n\n\n消逝的是海枯石爤，...",
            "good_num": "112",
            "comment_num": "18"
        }

    ]

}

module.exports.index = index;