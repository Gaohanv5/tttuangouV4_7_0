/*
SQLyog Enterprise - MySQL GUI v8.1 
MySQL - 5.0.51b-community-nt : Database - tgmax
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Data for the table `{prefix}tttuangou_city` */

insert  into `{prefix}tttuangou_city`(cityid,cityname,shorthand,display) values (1,'全国','cn',1);

/*Data for the table `{prefix}tttuangou_express_corp` */

insert  into `{prefix}tttuangou_express_corp`(id,flag,name,site,enabled) values (1,'CNEMS','中国邮政','http://www.ems.com.cn/','true'),(2,'CNST','申通快递','http://www.sto.cn/','true'),(3,'CNYT','圆通速递','http://www.yto.net.cn/','true'),(4,'CNSF','顺丰速运','http://www.sf-express.com/','true'),(5,'CNTT','天天快递','http://www.ttkd.cn/','true'),(6,'CNYD','韵达快递','http://www.yundaex.com/','true'),(7,'CNZT','中通速递','http://www.zto.cn/','true'),(8,'CNLB','龙邦物流','http://www.lbex.com.cn/','true'),(9,'CNZJS','宅急送','http://www.zjs.com.cn/','true'),(10,'CNQY','全一快递','http://www.apex100.com/','true'),(11,'CNHT','汇通速递','http://www.htky365.com/','true'),(12,'CNMH','民航快递','http://www.cae.com.cn/','true'),(13,'CNYF','亚风速递','http://www.airfex.cn/','true'),(14,'CNKJ','快捷速递','http://www.fastexpress.com.cn/','true'),(15,'DDS','DDS快递','http://www.qc-dds.net/','true'),(16,'CNHY','华宇物流','http://www.hoau.net/','true'),(17,'CNZY','中铁快运','http://www.cre.cn/','true'),(18,'FEDEX','FedEx','http://www.fedex.com/cn/','true'),(19,'UPS','UPS','http://www.ups.com/','true'),(20,'DHL','DHL','http://www.cn.dhl.com/','true');

/*Data for the table `{prefix}tttuangou_payment` */

insert  into `{prefix}tttuangou_payment`(id,code,name,detail,`order`,config,enabled) values (1,'self','余额支付','【推荐】使用本站账户余额支付',2,'','true'),(2,'cod','货到付款','【不推荐】送货上门，当面付款',3,'N;','false'),(3,'alipay','支付宝','【推荐】费率高（1.2%），但最多人使用的支付方式',1,'N;','false'),(4,'tenpay','财付通','【不推荐】费率高（1%），需购买年费套餐',5,'N;','false'),(5,'bank','线下转帐汇款','【不推荐】通过ATM机或银行转帐（付款周期长）',4,'N;','false'),(6,'recharge','充值卡','【不推荐】本站自有充值卡充值（用于无网银区域）',6,'N;','false');

/*Data for the table `{prefix}tttuangou_service` */

insert  into `{prefix}tttuangou_service`(id,`type`,flag,name,weight,`count`,config,enabled,`update`) values (1,'sms','ls','电信通道',100,0,'a:3:{s:6:\"driver\";s:2:\"ls\";s:7:\"account\";s:8:\"10580000\";s:8:\"password\";s:9:\"123456789\";}','false',1300958075);
insert  into `{prefix}tttuangou_service`(id,`type`,flag,name,weight,`count`,config,enabled,`update`) values (2,'sms','qxt','GD106通道',100,0,'a:3:{s:6:\"driver\";s:3:\"qxt\";s:7:\"account\";s:8:\"10580000\";s:8:\"password\";s:9:\"123456789\";}','false',1300958075);
insert  into `{prefix}tttuangou_service`(id,`type`,flag,name,weight,`count`,config,enabled,`update`) values (3,'sms','wnd','WN106通道',100,0,'a:3:{s:6:\"driver\";s:3:\"wnd\";s:7:\"account\";s:8:\"10580000\";s:8:\"password\";s:9:\"123456789\";}','false',1300958075);
insert  into `{prefix}tttuangou_service`(id,`type`,flag,name,weight,`count`,config,enabled,`update`) values (4,'sms','zt','ZT106通道',100,0,'a:3:{s:6:\"driver\";s:2:\"zt\";s:7:\"account\";s:8:\"10580000\";s:8:\"password\";s:9:\"123456789\";}','false',1300958075);
insert  into `{prefix}tttuangou_service`(id,`type`,flag,name,weight,`count`,config,enabled,`update`) values (5,'sms','ums','ZJ165通道',100,0,'a:3:{s:6:\"driver\";s:3:\"ums\";s:7:\"account\";s:8:\"10580000\";s:8:\"password\";s:9:\"123456789\";}','false',1300958075);
insert  into `{prefix}tttuangou_service`(id,`type`,flag,name,weight,`count`,config,enabled,`update`) values (6,'sms','tyx','TP106通道',100,0,'a:3:{s:6:\"driver\";s:3:\"tyx\";s:7:\"account\";s:8:\"10580000\";s:8:\"password\";s:9:\"123456789\";}','false',1300958075);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
