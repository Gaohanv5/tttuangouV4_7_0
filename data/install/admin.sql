/*
SQLyog Enterprise - MySQL GUI v8.1 
MySQL - 5.0.51b-community-nt : Database - tgmax
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Data for the table `{prefix}system_members` */

insert  into `{prefix}system_members`(`uid`,username,password,secques,gender,adminid,regip,regdate,lastip,lastvisit,lastactivity,lastpost,oltime,pageviews,credits,extcredits1,extcredits2,email,bday,sigstatus,tpp,ppp,styleid,dateformat,timeformat,pmsound,showemail,newsletter,invisible,timeoffset,newpm,accessmasks,face,tag_count,role_id,role_type,new_msg_count,tag,own_tags,login_count,truename,phone,last_year_rank,last_month_rank,last_week_rank,this_year_rank,this_month_rank,this_week_rank,last_year_credit,last_month_credit,last_week_credit,this_year_credit,this_month_credit,this_week_credit,view_times,use_tag_count,create_tag_count,image_count,noticenum,ucuid,invite_count,invitecode,province,city,topic_count,at_count,follow_count,fans_count,email2,qq,msn,aboutme,at_new,comment_new,fans_new,topic_favorite_count,tag_favorite_count,disallow_beiguanzhu,`validate`,favoritemy_new,money,checked,finder,findtime,totalpay,privs) values (1,'{$username}','{$password}','',0,0,'',0,'127.0.0.1',1303797472,1303797472,0,0,2400,0,0,0,'{$email}','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'admin',0,'',0,1,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0',0,0,0,'0','all');