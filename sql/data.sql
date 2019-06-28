-- MySQL dump 10.13  Distrib 5.5.38, for Win32 (x86)
--
-- Host: localhost    Database: thinkphp5
-- ------------------------------------------------------
-- Server version	5.5.53

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `thinkphp5`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `linzhi_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `linzhi_db`;

--
-- Table structure for table `tk_admin`
--

DROP TABLE IF EXISTS `tk_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `nick` varchar(32) NOT NULL DEFAULT '',
  `tel` varchar(16) NOT NULL DEFAULT '' COMMENT '电话号码',
  `email` varchar(64) NOT NULL DEFAULT '' COMMENT 'Email',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `last_login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登陆ip',
  `cdate` datetime NOT NULL,
  `mdate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_admin`
--

LOCK TABLES `tk_admin` WRITE;
/*!40000 ALTER TABLE `tk_admin` DISABLE KEYS */;
INSERT INTO `tk_admin` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','管理员','15354220940','canlynet@163.com',0,'','2016-06-24 10:00:00','2017-06-28 09:52:13'),(5,'test','098f6bcd4621d373cade4e832627b4f6','普通管理员（密码test）','13988880000','',1,'','2017-02-23 15:34:59','2017-09-13 12:32:53');
/*!40000 ALTER TABLE `tk_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_config`
--

DROP TABLE IF EXISTS `tk_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `value` varchar(32) NOT NULL DEFAULT '',
  `comment` varchar(32) NOT NULL DEFAULT '' COMMENT '注释',
  `help` varchar(1024) NOT NULL DEFAULT '' COMMENT '用法',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_config`
--

LOCK TABLES `tk_config` WRITE;
/*!40000 ALTER TABLE `tk_config` DISABLE KEYS */;
INSERT INTO `tk_config` VALUES (1,'login_need_img_verify','0','登陆验证码','登陆是是否使用验证码：1开启，0关闭'),(2,'keywords','','SEO关键词','对百度搜索友好'),(3,'description','','SEO描述','对百度搜索友好'),(4,'site_name','Thinkphp5.0框架','网站名称','');
/*!40000 ALTER TABLE `tk_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_menu`
--

DROP TABLE IF EXISTS `tk_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL DEFAULT '',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父菜单',
  `route` varchar(128) NOT NULL DEFAULT '' COMMENT '路由',
  `icon` varchar(256) NOT NULL DEFAULT '' COMMENT '图标',
  `sequence` int(11) NOT NULL DEFAULT '0' COMMENT '顺序',
  `hidden` tinyint(4) NOT NULL DEFAULT '0' COMMENT '隐藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='权限明细';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_menu`
--

LOCK TABLES `tk_menu` WRITE;
/*!40000 ALTER TABLE `tk_menu` DISABLE KEYS */;
INSERT INTO `tk_menu` VALUES (1,'管理员/权限',0,'','icon-user',10,0),(2,'管理员列表',1,'/admin/admin/index','',11,0),(3,'菜单列表',1,'/admin/admin/menu','',15,0),(4,'角色列表',1,'/admin/admin/role','',16,0),(5,'站内新闻',0,'','icon-book',20,0),(6,'新闻分类',5,'/admin/news/cates','',21,0),(7,'新闻管理',5,'/admin/news/index','',22,0),(8,'网站设置',0,'/admin/config/index','icon-cog',30,0),(10,'路由列表',1,'/admin/route/index','',12,0),(11,'路由名称',1,'/admin/route/name','',13,0),(12,'路由分组',1,'/admin/route/group','',14,0),(13,'无菜单',0,'','',0,1);
/*!40000 ALTER TABLE `tk_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_news`
--

DROP TABLE IF EXISTS `tk_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) unsigned NOT NULL COMMENT '分类',
  `title` varchar(64) NOT NULL COMMENT '标题',
  `thumbnail` varchar(256) NOT NULL DEFAULT '' COMMENT '缩略图',
  `summary` varchar(1024) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` text NOT NULL COMMENT '内容',
  `admin_id` int(10) unsigned NOT NULL COMMENT '上传者id',
  `show_time` datetime NOT NULL,
  `dots` int(11) NOT NULL DEFAULT '0',
  `savepathfilename` text NOT NULL,
  `editor` varchar(32) NOT NULL DEFAULT '',
  `cdate` datetime NOT NULL COMMENT '上传时间',
  `mdate` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`title`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='新闻';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_news`
--

LOCK TABLES `tk_news` WRITE;
/*!40000 ALTER TABLE `tk_news` DISABLE KEYS */;
INSERT INTO `tk_news` VALUES (2,3,'123','','','',1,'2017-09-13 10:25:02',0,'[]','','2017-09-13 10:25:02','2017-09-13 10:28:33');
/*!40000 ALTER TABLE `tk_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_news_category`
--

DROP TABLE IF EXISTS `tk_news_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_news_category` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `pid` int(10) unsigned NOT NULL COMMENT '上级分类',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `module_id` int(11) NOT NULL DEFAULT '0' COMMENT '模块id',
  PRIMARY KEY (`cid`),
  KEY `pid` (`pid`,`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT=' 分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_news_category`
--

LOCK TABLES `tk_news_category` WRITE;
/*!40000 ALTER TABLE `tk_news_category` DISABLE KEYS */;
INSERT INTO `tk_news_category` VALUES (1,0,'111',1),(2,0,'22222',1),(3,2,'今日要闻',1);
/*!40000 ALTER TABLE `tk_news_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_role`
--

DROP TABLE IF EXISTS `tk_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(8) NOT NULL DEFAULT '' COMMENT '角色名称',
  `authority` text NOT NULL COMMENT '权限ids:route_group的ids',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_role`
--

LOCK TABLES `tk_role` WRITE;
/*!40000 ALTER TABLE `tk_role` DISABLE KEYS */;
INSERT INTO `tk_role` VALUES (0,'超级管理员',''),(1,'普通管理员','1,5,8,14,12,15');
/*!40000 ALTER TABLE `tk_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_route`
--

DROP TABLE IF EXISTS `tk_route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_route` (
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '路由名称',
  `op_name` varchar(32) NOT NULL DEFAULT '' COMMENT '操作名称',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属菜单',
  `ignore` tinyint(4) NOT NULL DEFAULT '0' COMMENT '忽略权限检查',
  PRIMARY KEY (`name`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='路由表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_route`
--

LOCK TABLES `tk_route` WRITE;
/*!40000 ALTER TABLE `tk_route` DISABLE KEYS */;
INSERT INTO `tk_route` VALUES ('/admin/admin/add','添加管理员',2,0),('/admin/admin/delete','删除管理员',2,0),('/admin/admin/get_all_menu','获取菜单',3,1),('/admin/admin/get_role_info_by_id','获取角色权限信息',4,0),('/admin/admin/index','管理员列表',2,0),('/admin/admin/menu','菜单列表',3,0),('/admin/admin/menu_delete','删除菜单',3,0),('/admin/admin/menu_save','编辑菜单',3,0),('/admin/admin/modify_nick','编辑昵称',13,1),('/admin/admin/modify_password','修改密码',13,1),('/admin/admin/role','角色列表',4,0),('/admin/admin/role_delete','删除角色',4,0),('/admin/admin/role_save','保存角色',4,0),('/admin/admin/update','修改管理员',2,0),('/admin/config/index','网站设置',8,0),('/admin/config/save','保存设置',8,0),('/admin/index/ajax_login','登陆',13,1),('/admin/index/check_auth','检查权限',13,1),('/admin/index/get_code','获取图片验证码',13,1),('/admin/index/index','后台首页',13,1),('/admin/index/login','后台登陆',13,1),('/admin/index/logout','后台注销',13,1),('/admin/news/cates','新闻分类',6,0),('/admin/news/delete','删除新闻',7,0),('/admin/news/delete_cate','删除新闻分类',6,0),('/admin/news/edit','修改新闻',7,0),('/admin/news/get_cates','获取新闻分类',6,0),('/admin/news/index','新闻管理',7,0),('/admin/news/merge_cate','合并分类',6,0),('/admin/news/save','保存新闻',7,0),('/admin/news/save_cate','保存新闻分类',6,0),('/admin/news/upload_thumbnail','上传缩略图',13,1);
/*!40000 ALTER TABLE `tk_route` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tk_route_group`
--

DROP TABLE IF EXISTS `tk_route_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_route_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `route_names` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_id` (`menu_id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='路由-操作-映射关系';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tk_route_group`
--

LOCK TABLES `tk_route_group` WRITE;
/*!40000 ALTER TABLE `tk_route_group` DISABLE KEYS */;
INSERT INTO `tk_route_group` VALUES (1,2,'管理员列表','/admin/admin/index'),(3,2,'添加、修改管理员','/admin/admin/add,/admin/admin/index,/admin/admin/update'),(4,2,'删除管理员','/admin/admin/delete,/admin/admin/index'),(5,4,'角色列表','/admin/admin/get_role_info_by_id,/admin/admin/role'),(6,4,'添加、修改角色','/admin/admin/get_role_info_by_id,/admin/admin/role,/admin/admin/role_save'),(7,4,'删除角色','/admin/admin/get_role_info_by_id,/admin/admin/role,/admin/admin/role_delete'),(8,6,'新闻分类列表','/admin/news/cates,/admin/news/get_cates'),(9,6,'添加、修改新闻分类','/admin/news/cates,/admin/news/get_cates,/admin/news/save_cate'),(10,6,'合并新闻分类','/admin/news/cates,/admin/news/get_cates,/admin/news/merge_cate'),(11,6,'删除新闻分类','/admin/news/cates,/admin/news/delete_cate,/admin/news/get_cates'),(12,7,'新闻列表','/admin/news/index'),(13,7,'添加、修改新闻','/admin/news/edit,/admin/news/index,/admin/news/save'),(14,7,'删除新闻','/admin/news/delete,/admin/news/index'),(15,8,'网站设置','/admin/config/index'),(16,8,'修改网站设置','/admin/config/index,/admin/config/save');
/*!40000 ALTER TABLE `tk_route_group` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-17 15:10:09
