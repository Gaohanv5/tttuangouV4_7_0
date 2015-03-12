/*
SQLyog Enterprise - MySQL GUI v8.1 
MySQL - 5.0.51b-community-nt : Database - tgmax_pub
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Data for the table `{prefix}system_members` */

insert  into `{prefix}system_members`(`uid`,username,password,secques,gender,adminid,regip,regdate,lastip,lastvisit,lastactivity,lastpost,oltime,pageviews,credits,extcredits1,extcredits2,email,bday,sigstatus,tpp,ppp,styleid,dateformat,timeformat,pmsound,showemail,newsletter,invisible,timeoffset,newpm,accessmasks,face,tag_count,role_id,role_type,new_msg_count,tag,own_tags,login_count,truename,phone,last_year_rank,last_month_rank,last_week_rank,this_year_rank,this_month_rank,this_week_rank,last_year_credit,last_month_credit,last_week_credit,this_year_credit,this_month_credit,this_week_credit,view_times,use_tag_count,create_tag_count,image_count,noticenum,ucuid,invite_count,invitecode,province,city,topic_count,at_count,follow_count,fans_count,email2,qq,msn,aboutme,at_new,comment_new,fans_new,topic_favorite_count,tag_favorite_count,disallow_beiguanzhu,`validate`,favoritemy_new,money,checked,finder,findtime,totalpay) values (2,'cenwor','e10adc3949ba59abbe56e057f20f883e','',0,0,'',0,'',0,0,0,0,0,0,0,0,'demo@name.com','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'seller',0,'',0,0,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0.00',0,0,0,'0.00'),(3,'jishigou','e10adc3949ba59abbe56e057f20f883e','',0,0,'',0,'60.177.179.175',1303880709,1303880709,0,0,0,0,0,0,'demo@name.com','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'normal',0,'',0,1,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0.00',0,0,0,'0.00'),(4,'tttuangou','e10adc3949ba59abbe56e057f20f883e','',0,0,'',0,'127.0.0.1',1303880709,1303880709,0,0,0,0,0,0,'tows@apiz.org','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'nomal',0,'',0,1,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0.00',0,0,0,'0.00');

/*Data for the table `{prefix}tttuangou_address` */

insert  into `{prefix}tttuangou_address`(id,owner,name,region,address,zip,phone,lastuse) values (1,1,'神话',',3133,3134,3139,','杭州市西湖区','300013','1388888888',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_express` */

insert  into `{prefix}tttuangou_express`(id,`name`,express,firstunit,firstprice,continueunit,continueprice,regiond,dpenable,detail,`order`,enabled) values (1,'全国包邮',4,1000,'0.00',1000,'0.00',0,'false','全国包邮啦',1,'true');

/*Data for the table `{prefix}tttuangou_order` */

insert  into `{prefix}tttuangou_order`(orderid,productid,productnum,productprice,totalprice,userid,addressid,buytime,paytype,paymoney,pay,paytime,expresstype,expressprice,invoice,expresstime,extmsg,process,status,remark) values (2011042729691,2,1,'298.00','0.00',1,0,UNIX_TIMESTAMP()-3600,1,'0.00',1,UNIX_TIMESTAMP(),0,'0.00','',0,'爱聚合','TRADE_FINISHED',1,NULL),(2011042735431,3,1,'9999.00','0.00',1,1,UNIX_TIMESTAMP()-3300,1,'0.00',1,UNIX_TIMESTAMP(),1,'0.00','CN123456789',UNIX_TIMESTAMP()-1800,'记事狗','TRADE_FINISHED',1,NULL);

/*Data for the table `{prefix}tttuangou_order_clog` */

insert  into `{prefix}tttuangou_order_clog`(id,`sign`,action,`uid`,remark,`time`) values (1,2011042729691,'confirm',1,'[确认订单] 确认收到298元',UNIX_TIMESTAMP()),(2,2011042735431,'confirm',1,'[确认订单] 确认收到9999元',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_product` */

insert  into `{prefix}tttuangou_product`(id,sellerid,city,name,flag,price,nowprice,img,intro,content,cue,theysay,wesay,begintime,overtime,`type`,perioddate,weight,successnum,virtualnum,maxnum,oncemax,oncemin,multibuy,allinone,totalnum,display,addtime,status,`order`) values (1,1,1,'仅需298元！原价600元的“最全采集方式+最强伪原创功能”的网站宝系统，BBS/CMS建站必备','网站宝系统','600.00','298.00','1,2','网站宝是一套专业的智能采集和伪原创系统（采用php+mysql开发）；也是目前唯一可同时按关键词和RSS网址全自动采集内容的系统，还是目前为止拥有最全面和最强大伪原创功能的系统！ \r\n\r\n用网站宝，可无缝安装到主流CMS、BBS系统后台，几分钟即可再造一个新浪网！','<p><a href=\"http://wangzhuanbao.net/qian\" target=\"_blank\"><span style=\"color:#fe2419;font-size:18px;\">网站为什么收录少？流量小？不赚钱？<span style=\"color:#0021b0;font-size:14px;\">建站赚钱的秘密</span></span><span style=\"color:#fe2419;font-size:18px;\">！</span></a><br />\r\n<br />\r\n作为web系统的网站宝，可无缝集成到主流CMS和BBS系统网站中（<em>放在域名的一级子目录下即可</em>），当前完美支持DEDECMS、Discuz、DiscuzX1、PHPCMS、PHP168、帝国CMS、PHPwind、Wordpress等。 </p>\r\n<!--新增爱聚合与网站宝对比--><b class=\"fc\">内容原创性在网站流量中一般能占到80%的比重；从这个方面讲：</b><br />\r\n<span>网站宝不仅仅是智能采集系统和超强伪原创系统，更是网站流量制造机！</span> <br />\r\n<img src=\"http://wangzhuanbao.net/templates/images/biaoqing/fendou.gif\" /> &nbsp; <span>95%的人失败是因为他们还没有开始就放弃了，你呢？</span><br />\r\n如需试用可免费<a href=\"http://wangzhuanbao.net/download.html\" target=\"_blank\">下载网站宝</a>，有任何问题请联系在线客服。<br />\r\n<br />\r\n如还更多疑问，请查看<a href=\"http://wangzhuanbao.net/faq.html\" target=\"_blank\"><span style=\"color:#006aa8;\">网站宝常见问题</span></a>','此特价团购券将于2011年8月30日到期，请在此前开通授权！<br />\r\n<a href=\"http://wangzhuanbao.net/wzb_demo.html\" target=\"_blank\">网站宝安装、按关键词和RSS源采集、发布内容Flash演示</a><br />\r\n查看<a href=\"http://wangzhuanbao.net/qian#wzb\" target=\"_blank\">网站宝与传统采集软件的对比</a><br />','<div class=\"box4\">\r\n<div class=\"cc\"><img src=\"http://aijuhe.net/templates/aijuhe4/programs/image/money/zhanzhang.gif\" /> 站长火星人的切身感受： <br />\r\n我主要使用从指定RSS源抓内容，因为我做的是正规网站，而几乎所有的专业网站都有RSS输出，他们的内容质量好，用<a href=\"http://www.wangzhuanbao.net\" target=\"_blank\">网站宝不用写采集规则</a>，就自动从这些RSS源抓来的内容，再结合关键词替换功能，将他们网站名字替换成我的，非常方便。<br />\r\n</div>\r\n</div>\r\n<div class=\"box4\">\r\n<div class=\"cc\">\r\n<div class=\"box4\">\r\n<div class=\"cc\"><img src=\"http://aijuhe.net/templates/aijuhe4/programs/image/money/zhanzhang.gif\" /> 站长Yingcaishen的切身感受： <br />\r\n我之前在用<a href=\"http://aijuhe.net\" target=\"_blank\">爱聚合</a>感觉挺好，就是模板太少，得知<a href=\"http://wangzhuanbao.net\" target=\"_blank\">网站宝可无缝集成到主流cms</a>中，就毫不犹豫弄了一套创业版。这样网站宝在后台抓内容和伪原创，而前台用主流CMS有很多模板可以用，实践证明我的想法是对的，baidu正在疯狂收录了。 </div>\r\n</div>\r\n</div>\r\n</div>','<p>当别人在交流经验时，你还是寻找内容吗？<br />\r\n当别人在做站群时，你还为一个站发愁吗？<br />\r\n当别人有时间享乐时，你还在起早贪黑吗？</p>\r\n<p><span>试试网站宝吧，每天只需几毛钱，你的人生会大不一样！</span></p>',UNIX_TIMESTAMP()-604800,UNIX_TIMESTAMP()-86400,'ticket',UNIX_TIMESTAMP()+86400,0,1,13,0,1,1,'true','true',0,2,UNIX_TIMESTAMP(),3,1),(2,1,1,'仅需298元！原价600元的爱聚合专题网站系统--让网站流量飙升的系统','爱聚合网站系统','600.00','298.00','3,4','爱聚合是一套首创的专题建站程序，采用php+mysql开发，其最大特色是可根据指定的关键词自动聚合内容、自动定期更新、自动内容伪原创、自动组建原创专题，可有效增加搜索引擎收录和流量！ ','<p>此团购为团购券形式，团购成功会将团购券通过短信发到您的手机</p>','<p>要使用爱聚合建站，必须购买下面的系统使用授权，一次购买永久使用；</p>\r\n<p>购买爱聚合系统，如感觉无法帮你省时、省力的建站赚钱<span style=\"color:red;\">7天内可申请100%退款</span>；</p>','<div class=\"cc\"><img src=\"http://aijuhe.net/templates/aijuhe4/programs/image/money/zhanzhang.gif\" /> 站长ej12的切身感受： <br />\r\n我是因为朋友在用爱聚合，所以抱着试试态度也用爱聚合做了一个减肥网站，不用不知道一用吓一跳，后台填写些减肥相关的关键词，剩下任务爱聚合自动完成，开始还担心太热门收录不好，现在<span>百度收录30多万页面</span>，根据后台的baidu蜘蛛爬行统计看，baidu非常喜欢我这个站，收录应该还会不断增加的。网站收录详情见<a onclick=\"javascript: pageTracker._trackPageview(\'/money/chsds.com\'); \" href=\"http://www.baidu.com/s?wd=site%3Awww.chsds.com\" target=\"_blank\">http://www.baidu.com/s?wd=site%3Awww.chsds.com</a> </div>','<p>当刚可以下海经商时， 有的人不知道，有的人知道了没行动，有的人行动了，于是<span>一部分人先富了…</span><br />\r\n当刚开始炒股的时候， 有的人不知道，有的人知道没行动，有的人行动了，于是<span>又有一部分先富了…</span></p>\r\n<p>今天，你也面临一个类似的局面，你会如何选择呢？ </p>\r\n<ol>\r\n<li>你持续行动了，然后事实证明爱聚合确实能帮你省时、省力、省钱和赚钱，那么你的生活会更好了；</li>\r\n<li>你行动了，7天内发现爱聚合不合适，那么你可以100%拿走你的钱，没一分钱损失；</li>\r\n<li>你什么都不做，那么只有一种可能：你将一无所获！</li>\r\n</ol>',UNIX_TIMESTAMP()-86400,UNIX_TIMESTAMP()+259200,'ticket',UNIX_TIMESTAMP()+604800,0,1,56,0,1,1,'true','true',1,2,UNIX_TIMESTAMP(),2,2),(3,1,1,'仅9999元！原价20000元的记事狗开源微博系统商业授权，搭建自己的微博网站。','记事狗微博程序','20000.00','9999.00','5,6,7,8','记事狗微博系统是一套领先的开源微博程序，采用PHP+mysql开发，支持Web、手机、QQ等多种方式发布微博，并可在收到站内短信、评论、@我时获得QQ即时通知，极大增强互动性；同时支持通过新浪微博账户登陆、发微博同步到新浪微博等。','记事狗微博系统V4.x版本已经上线公测，界面更好看、使用更人性、功能更强大，访问<a href=\"http://t.jishigou.net\" target=\"_blank\">演示站</a>','<div id=\"com_v\" class=\"boxCenterList RelaArticle\"><p>对非营利性的个人网站，可以免费使用记事狗；</p>\r\n<p>其他应用（包括但不限于企事业或营利性网站等）需要购买记事狗商业授权方可使用；否则我们有权通过法律途径寻求经济和商业赔偿。</p>\r\n</div>','1、<a href=\"http://t.tetimes.com/\" rel=\"nofollow\" target=\"_blank\">深圳特区报</a>：&nbsp;<br />\r\n很早就知道微博，研究发现记事狗微博做的是最好的，主要是界面精致并且使用简单，并且记事狗可以与Discuz通过ucenter无缝整合，所以马上就购买了商业授权，因为仅仅几千元也就一个技术人员一个月工资而已； <br />\r\n<br />\r\n2、<a href=\"http://t.xjrb.com/\" rel=\"nofollow\" target=\"_blank\">西江日报</a>： <br />\r\n微博传播能力很强，很多大事要事都是在微博上最新发布，所以我们作为传媒企业必须上微博，否则就要受制于人，百度搜索微博系统，记事狗是排第一的，我相信他们的技术能力；<br />\r\n<br />\r\n3、<a href=\"http://t.my12371.cn\" rel=\"nofollow\" target=\"_blank\">中共重庆市大渡口区委</a>： <br />\r\n上海大火事件首先是在微博上传播开，CCTV报道都引用了微博的内容，这证明了微博是比传统论坛、博客更有效的传播方式。我们通过第三方机构推荐购买了记事狗，事实也证明记事狗的持续开发能力正是我们所需要的','<p sizcache=\"298\" sizset=\"33\">记事狗微博系统V4.x版本开源发布了，个人用户可免费使用，<a title=\"记事狗微博下载\" href=\"http://www.jishigou.net/download.html\" jquery1292312632343=\"31\"><span style=\"color:#4086c2;\">点此下载</span></a>。 </p>',UNIX_TIMESTAMP()-86400,UNIX_TIMESTAMP()+604800,'stuff',UNIX_TIMESTAMP(),1000,1,123,0,1,1,'true','true',1,2,UNIX_TIMESTAMP(),2,10);

/*Data for the table `{prefix}tttuangou_push_queue` */

insert  into `{prefix}tttuangou_push_queue`(id,`type`,target,`data`,rund,`result`,`update`,pr) values (1,'mail','admin@cenwor.com','a:2:{s:7:\"subject\";s:28:\"天天团购系统 提示您\";s:7:\"content\";s:91:\"感谢您的购买\n订单号：2011042729691\n团购券编号：870629518244\n密码：722124\";}','false',NULL,UNIX_TIMESTAMP(),9);

/*Data for the table `{prefix}tttuangou_question` */

insert  into `{prefix}tttuangou_question`(id,userid,username,content,reply,`time`) values (1,3,'记事狗','你们可以帮助安装吗？','可以的。购买后，提供ftp信息、数据库信息，几分钟即可帮你安装好。',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_seller` */

insert  into `{prefix}tttuangou_seller`(id,userid,sellername,sellerphone,selleraddress,sellerurl,sellermap,area,productnum,successnum,money,`time`) values (1,2,'杭州神话信息技术有限公司','0571-88800819','浙江杭州市西湖区
','http://cenwor.com/','',1,3,2,'10297.00',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_subscribe` */

insert  into `{prefix}tttuangou_subscribe`(id,`type`,target,city,`time`) values (1,'mail','admin@biniu.com',1,UNIX_TIMESTAMP()),(2,'sms','13888888888',1,UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_ticket` */

insert  into `{prefix}tttuangou_ticket`(ticketid,`uid`,productid,orderid,`number`,password,usetime,mutis,status) values (1,1,2,2011042729691,'870629518244','722124','0000-00-00 00:00:00',1,0);

/*Data for the table `{prefix}tttuangou_uploads` */

insert  into `{prefix}tttuangou_uploads`(id,name,intro,`path`,url,`type`,`size`,mime,extra,`uid`,ip,`update`) values (1,'91ab162b046.jpg','网站宝','./uploads/demo/1ba6cc89c53fb45e337127a668af9f5d.jpg','uploads/demo/1ba6cc89c53fb45e337127a668af9f5d.jpg','jpg',61982,'application/octet-stream','a:2:{s:5:\"width\";i:536;s:6:\"height\";i:312;}',1,1018278831,UNIX_TIMESTAMP()),(2,'69bea264a2a.jpg','网站宝','./uploads/demo/765eb031e26a8a041ae2290b0a31a928.jpg','uploads/demo/765eb031e26a8a041ae2290b0a31a928.jpg','jpg',63852,'application/octet-stream','a:2:{s:5:\"width\";i:535;s:6:\"height\";i:331;}',1,1018278831,UNIX_TIMESTAMP()),(3,'52adf1d4e71.jpg','爱聚合','./uploads/demo/977f57d7da524156d1f951791cd3892a.jpg','uploads/demo/977f57d7da524156d1f951791cd3892a.jpg','jpg',42435,'application/octet-stream','a:2:{s:5:\"width\";i:471;s:6:\"height\";i:276;}',1,1018278831,UNIX_TIMESTAMP()),(4,'af8670f9020.jpg','爱聚合','./uploads/demo/5fae400ed88f0ec3d1b97642a883cba2.jpg','uploads/demo/5fae400ed88f0ec3d1b97642a883cba2.jpg','jpg',48755,'application/octet-stream','a:2:{s:5:\"width\";i:526;s:6:\"height\";i:299;}',1,1018278831,UNIX_TIMESTAMP()),(5,'f6135c3e3ec.jpg','记事狗','./uploads/demo/22665b22ff0c66a493c1dec3ea7ee7c9.jpg','uploads/demo/22665b22ff0c66a493c1dec3ea7ee7c9.jpg','jpg',26687,'application/octet-stream','a:2:{s:5:\"width\";i:478;s:6:\"height\";i:301;}',1,1018278831,UNIX_TIMESTAMP()),(6,'8edf76e191e.jpg','记事狗','./uploads/demo/4175419d8938c9adbea980358d48ad82.jpg','uploads/demo/4175419d8938c9adbea980358d48ad82.jpg','jpg',39663,'application/octet-stream','a:2:{s:5:\"width\";i:493;s:6:\"height\";i:300;}',1,1018278831,UNIX_TIMESTAMP()),(7,'97ef578542b.jpg','记事狗','./uploads/demo/61baf7aa85d980548def226e3b429e85.jpg','uploads/demo/61baf7aa85d980548def226e3b429e85.jpg','jpg',29816,'application/octet-stream','a:2:{s:5:\"width\";i:497;s:6:\"height\";i:316;}',1,1018278831,UNIX_TIMESTAMP()),(8,'9858554f923.jpg','记事狗','./uploads/demo/499606bbf6a3d27558dbb67ceea407c6.jpg','uploads/demo/499606bbf6a3d27558dbb67ceea407c6.jpg','jpg',27784,'application/octet-stream','a:2:{s:5:\"width\";i:503;s:6:\"height\";i:291;}',1,1018278831,UNIX_TIMESTAMP());

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
