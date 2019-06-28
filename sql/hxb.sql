/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : hxb

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-12-13 17:26:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tk_admin
-- ----------------------------
DROP TABLE IF EXISTS `tk_admin`;
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

-- ----------------------------
-- Records of tk_admin
-- ----------------------------
INSERT INTO `tk_admin` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '管理员', '15354220940', 'canlynet@163.com', '0', '', '2016-06-24 10:00:00', '2017-06-28 09:52:13');
INSERT INTO `tk_admin` VALUES ('5', 'test', '098f6bcd4621d373cade4e832627b4f6', '普通管理员（密码test）', '13988880000', '', '1', '', '2017-02-23 15:34:59', '2017-09-13 12:32:53');

-- ----------------------------
-- Table structure for tk_bltoken
-- ----------------------------
DROP TABLE IF EXISTS `tk_bltoken`;
CREATE TABLE `tk_bltoken` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `blToken` varchar(255) NOT NULL,
  `overtime` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mdate` datetime NOT NULL,
  `cdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_bltoken
-- ----------------------------
INSERT INTO `tk_bltoken` VALUES ('2', '56a03d4823584b28fbad30c1b510ee26', '1575623958', '6', '2018-12-11 17:19:18', '2018-12-11 16:48:20');

-- ----------------------------
-- Table structure for tk_cate
-- ----------------------------
DROP TABLE IF EXISTS `tk_cate`;
CREATE TABLE `tk_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` int(11) NOT NULL,
  `hot` int(11) NOT NULL,
  `cdate` datetime NOT NULL,
  `mdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_cate
-- ----------------------------
INSERT INTO `tk_cate` VALUES ('1', '悉尼', '0', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tk_cate` VALUES ('2', '墨尔本', '0', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tk_cate` VALUES ('3', '悉尼区域', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for tk_collection
-- ----------------------------
DROP TABLE IF EXISTS `tk_collection`;
CREATE TABLE `tk_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `house_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_collection
-- ----------------------------
INSERT INTO `tk_collection` VALUES ('3', '1', '6');

-- ----------------------------
-- Table structure for tk_config
-- ----------------------------
DROP TABLE IF EXISTS `tk_config`;
CREATE TABLE `tk_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `value` varchar(32) NOT NULL DEFAULT '',
  `comment` varchar(32) NOT NULL DEFAULT '' COMMENT '注释',
  `help` varchar(1024) NOT NULL DEFAULT '' COMMENT '用法',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='配置表';

-- ----------------------------
-- Records of tk_config
-- ----------------------------
INSERT INTO `tk_config` VALUES ('1', 'login_need_img_verify', '0', '登陆验证码', '登陆是是否使用验证码：1开启，0关闭');
INSERT INTO `tk_config` VALUES ('2', 'keywords', '', 'SEO关键词', '对百度搜索友好');
INSERT INTO `tk_config` VALUES ('3', 'description', '', 'SEO描述', '对百度搜索友好');
INSERT INTO `tk_config` VALUES ('4', 'site_name', 'Thinkphp5.0框架', '网站名称', '');

-- ----------------------------
-- Table structure for tk_houses
-- ----------------------------
DROP TABLE IF EXISTS `tk_houses`;
CREATE TABLE `tk_houses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `title` varchar(255) DEFAULT NULL COMMENT '房源标题',
  `address` varchar(255) DEFAULT NULL COMMENT '房源详细地址',
  `price` int(10) DEFAULT NULL,
  `source` varchar(20) DEFAULT NULL COMMENT '来源',
  `type` varchar(20) DEFAULT NULL COMMENT '出租方式',
  `sex` varchar(20) DEFAULT NULL,
  `pet` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '宠物',
  `smoke` varchar(255) DEFAULT NULL,
  `bill` varchar(255) DEFAULT NULL COMMENT '包含bill, 不包含Bill',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '押金',
  `live_date` date DEFAULT NULL COMMENT '可入住时间',
  `lease_term` varchar(20) DEFAULT NULL COMMENT '租期：少于3个月，3-6个月，6-12个月 租期可议',
  `house_type` varchar(20) DEFAULT NULL COMMENT '房屋类型：公寓、别墅、不限',
  `furniture` varchar(20) DEFAULT NULL COMMENT '家具',
  `house_room` varchar(20) DEFAULT NULL COMMENT '户型：一室，两室，三室，三室以上',
  `car` varchar(25) DEFAULT NULL COMMENT '车位：1个；2个；3个；3个以上',
  `toilet` varchar(20) DEFAULT NULL COMMENT '卫生间：1个；2个；3个；3个以上；',
  `home` varchar(255) DEFAULT NULL COMMENT '设施：游泳池、健身房；车位',
  `sation` varchar(255) DEFAULT NULL COMMENT '交通：火车站；电车站；免费电车',
  `province` varchar(100) DEFAULT NULL COMMENT '省',
  `city` varchar(100) DEFAULT NULL COMMENT '市',
  `area` varchar(100) DEFAULT NULL COMMENT '区',
  `street` varchar(255) DEFAULT NULL COMMENT '街道',
  `school` varchar(255) DEFAULT NULL COMMENT '校区',
  `real_name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `wchat` varchar(255) DEFAULT NULL COMMENT '微信号',
  `tel` varchar(11) DEFAULT NULL,
  `thumnail` varchar(255) DEFAULT NULL COMMENT '封面图',
  `images` varchar(255) DEFAULT NULL COMMENT '详情图',
  `content` text COMMENT '描述房源',
  `status` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0：未发布，1：已发布；2：下线',
  `view` int(11) DEFAULT '0' COMMENT '查看次数',
  `publish_date` datetime DEFAULT NULL COMMENT '发布时间',
  `collection` int(11) NOT NULL DEFAULT '0' COMMENT '收藏数量',
  `cdate` datetime DEFAULT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_houses
-- ----------------------------
INSERT INTO `tk_houses` VALUES ('1', '0', '房源标题', '河北省保定市莲池区名人国际小区', '300', '个人', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-27', '少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', '河北省', '保定市', '竞秀区', '天鹅路', '保定美术中学', '张三', '1234555', '13484325746', null, null, '详细描述', '1', '8', '2018-12-13 14:26:27', '2', '2018-12-11 13:48:59', '2018-12-11 13:49:02');
INSERT INTO `tk_houses` VALUES ('6', '6', '名人国际', '河北省保定市莲池区横向北大街', '200', '个人', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-12', '不少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', '河北省', '保定市', '莲池区', '横向北大街', '保定美术中学', '张三', '酒精发酵微信号', '13483257463', null, null, '内容描述', '0', null, null, '0', '2018-12-12 09:34:12', '2018-12-12 09:34:12');
INSERT INTO `tk_houses` VALUES ('7', '6', '名人国际', '河北省保定市莲池区横向北大街', '200', '个人', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-12', '不少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', '河北省', '保定市', '莲池区', '横向北大街', '保定美术中学', '张三', '酒精发酵微信号', '13483257463', '/uploads/houses/2018121212270315445888234938.jpg', null, '内容描述', '0', null, null, '0', '2018-12-12 12:27:03', '2018-12-12 12:27:03');
INSERT INTO `tk_houses` VALUES ('8', '6', '名人国际', '河北省保定市莲池区横向北大街', '200', '个人', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-12', '不少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', '河北省', '保定市', '莲池区', '横向北大街', '保定美术中学', '张三', '酒精发酵微信号', '13483257463', '/uploads/houses/2018121212281815445888984578.jpg', '/uploads/houses/2018121212281815445888984608.jpg,/uploads/houses/2018121212281815445888984658.jpg', '内容描述', '0', null, null, '0', '2018-12-12 12:28:18', '2018-12-12 12:28:18');
INSERT INTO `tk_houses` VALUES ('9', '6', '名人国际', '河北省保定市莲池区横向北大街', '200', '个人', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-12', '不少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', '河北省', '保定市', '莲池区', '横向北大街', '保定美术中学', '张三', '', '13483257463', '/uploads/houses/2018121212285315445889330248.jpg', '/uploads/houses/2018121212285315445889330278.jpg,/uploads/houses/2018121212285315445889330358.jpg', '内容描述', '0', null, null, '0', '2018-12-12 12:28:53', '2018-12-12 12:28:53');
INSERT INTO `tk_houses` VALUES ('10', '6', '名人国际1', '河北省保定市莲池区横向北大街', '200', '个人', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-12', '不少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', null, null, '莲池区', null, '保定美术中学', '', '', '13483257466', '/uploads/houses/2018121212514015445903007528.jpg', '/uploads/houses/2018121212514015445903007588.jpg,/uploads/houses/2018121212514015445903007628.jpg', '内容描述', '0', null, null, '0', '2018-12-12 12:51:40', '2018-12-12 12:51:40');
INSERT INTO `tk_houses` VALUES ('11', '6', '名人国际', '河北省保定市莲池区横向北大街', '200', '中介', '整租', '男女不限', '接受', '接受', '包含Bill', '2000', '2018-12-12', '不少于3个月', '公寓', '包家具', '一室', '1个', '1个', '健身房,车位', '火车站,电车站', null, null, '莲池区', null, '保定美术中学', '张三', '', '13483257466', '/uploads/houses/2018121212585515445907354478.jpg', '/uploads/houses/2018121212585515445907354538.jpg,/uploads/houses/2018121212585515445907354578.jpg', '内容描述', '1', null, null, '0', '2018-12-12 12:53:01', '2018-12-12 12:58:55');

-- ----------------------------
-- Table structure for tk_menu
-- ----------------------------
DROP TABLE IF EXISTS `tk_menu`;
CREATE TABLE `tk_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL DEFAULT '',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父菜单',
  `route` varchar(128) NOT NULL DEFAULT '' COMMENT '路由',
  `icon` varchar(256) NOT NULL DEFAULT '' COMMENT '图标',
  `sequence` int(11) NOT NULL DEFAULT '0' COMMENT '顺序',
  `hidden` tinyint(4) NOT NULL DEFAULT '0' COMMENT '隐藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='权限明细';

-- ----------------------------
-- Records of tk_menu
-- ----------------------------
INSERT INTO `tk_menu` VALUES ('1', '管理员/权限', '0', '', 'icon-user', '10', '0');
INSERT INTO `tk_menu` VALUES ('2', '管理员列表', '1', '/admin/admin/index', '', '11', '0');
INSERT INTO `tk_menu` VALUES ('3', '菜单列表', '1', '/admin/admin/menu', '', '15', '0');
INSERT INTO `tk_menu` VALUES ('4', '角色列表', '1', '/admin/admin/role', '', '16', '0');
INSERT INTO `tk_menu` VALUES ('5', '租房锦囊', '0', '', 'icon-book', '20', '0');
INSERT INTO `tk_menu` VALUES ('7', '锦囊列表', '5', '/admin/news/index', '', '22', '0');
INSERT INTO `tk_menu` VALUES ('8', '网站设置', '0', '/admin/config/index', 'icon-cog', '30', '0');
INSERT INTO `tk_menu` VALUES ('10', '路由列表', '1', '/admin/route/index', '', '12', '0');
INSERT INTO `tk_menu` VALUES ('11', '路由名称', '1', '/admin/route/name', '', '13', '0');
INSERT INTO `tk_menu` VALUES ('12', '路由分组', '1', '/admin/route/group', '', '14', '0');
INSERT INTO `tk_menu` VALUES ('13', '无菜单', '0', '', '', '0', '1');
INSERT INTO `tk_menu` VALUES ('14', '用户管理', '0', '', 'icon-user', '0', '0');
INSERT INTO `tk_menu` VALUES ('15', '用户列表', '14', '/admin/user/index', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('16', '房东列表', '14', '/admin/user/house', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('17', '常见问题', '0', '', 'icon-list', '0', '0');
INSERT INTO `tk_menu` VALUES ('18', '租房相关', '17', '/admin/questions/renting', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('19', '房东相关', '17', '/admin/questions/house', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('20', '联系我们', '0', '', 'icon-list', '0', '0');
INSERT INTO `tk_menu` VALUES ('21', '关于我们', '20', '/admin/questions/contact', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('22', '平台声明', '20', '/admin/questions/platform', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('23', '直播看房', '20', '/admin/questions/live', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('24', '房源申请', '20', '/admin/questions/sign', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('25', '闪电招租', '20', '/admin/questions/bolt', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('26', '周边服务', '20', '/admin/questions/near', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('29', '房源管理', '0', '', 'icon-list', '0', '0');
INSERT INTO `tk_menu` VALUES ('30', '房源列表', '29', '/admin/houses/index', '', '0', '0');
INSERT INTO `tk_menu` VALUES ('32', '下拉管理', '0', '', 'icon-list', '0', '0');
INSERT INTO `tk_menu` VALUES ('34', '城市区域校区', '32', '/admin/cate/index', '', '0', '0');

-- ----------------------------
-- Table structure for tk_news
-- ----------------------------
DROP TABLE IF EXISTS `tk_news`;
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='新闻';

-- ----------------------------
-- Records of tk_news
-- ----------------------------
INSERT INTO `tk_news` VALUES ('2', '3', '123', '/uploads/thumbnail/2018121011271615444124364180.jpg', '22222', '<p>222</p>', '1', '2017-09-13 10:25:02', '0', '[]', '', '2017-09-13 10:25:02', '2018-12-10 11:28:18');
INSERT INTO `tk_news` VALUES ('3', '0', '5555', '/uploads/thumbnail/2018121011283915444125198750.jpg', '', '<p>5555</p>', '1', '2018-12-10 11:28:33', '0', '', '', '0000-00-00 00:00:00', '2018-12-10 11:28:42');

-- ----------------------------
-- Table structure for tk_news_category
-- ----------------------------
DROP TABLE IF EXISTS `tk_news_category`;
CREATE TABLE `tk_news_category` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `pid` int(10) unsigned NOT NULL COMMENT '上级分类',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `module_id` int(11) NOT NULL DEFAULT '0' COMMENT '模块id',
  PRIMARY KEY (`cid`),
  KEY `pid` (`pid`,`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT=' 分类表';

-- ----------------------------
-- Records of tk_news_category
-- ----------------------------
INSERT INTO `tk_news_category` VALUES ('1', '0', '墨尔本', '1');
INSERT INTO `tk_news_category` VALUES ('2', '0', '悉尼', '1');
INSERT INTO `tk_news_category` VALUES ('3', '0', '塔斯马尼亚', '1');
INSERT INTO `tk_news_category` VALUES ('5', '0', '布里斯班', '1');
INSERT INTO `tk_news_category` VALUES ('6', '1', '1', '1');

-- ----------------------------
-- Table structure for tk_news_user
-- ----------------------------
DROP TABLE IF EXISTS `tk_news_user`;
CREATE TABLE `tk_news_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_news_user
-- ----------------------------
INSERT INTO `tk_news_user` VALUES ('2', '2', '6');

-- ----------------------------
-- Table structure for tk_questions
-- ----------------------------
DROP TABLE IF EXISTS `tk_questions`;
CREATE TABLE `tk_questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `summary` varchar(255) DEFAULT NULL COMMENT '简介',
  `content` text,
  `type` varchar(30) DEFAULT NULL COMMENT '租客、房东',
  `cdate` datetime DEFAULT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_questions
-- ----------------------------
INSERT INTO `tk_questions` VALUES ('2', '囍家健康平台', '囍家健康平台', '<p>111囍家健康平台<img src=\"/tmp/20181208031817709241.jpg\" title=\"SS:网站文件：20181208031817709241.jpg；原始文件：2.jpg\" alt=\"2.jpg\"/></p>', '租房', '2018-12-08 01:31:17', '2018-12-08 03:18:19');
INSERT INTO `tk_questions` VALUES ('3', '囍家健康平台', '囍家健康平台', '<p>囍家健康平台</p>', '租房', '2018-12-08 01:34:00', '2018-12-08 03:18:06');
INSERT INTO `tk_questions` VALUES ('5', '平台声明', '平台声明1', '<p>平台声明1</p>', '平台声明', '2018-12-08 01:45:23', '2018-12-08 03:19:07');
INSERT INTO `tk_questions` VALUES ('14', '直播看房', '直播看房1', '<p>直播看房1</p>', '直播看房', '2018-12-08 02:20:02', '2018-12-08 03:19:15');
INSERT INTO `tk_questions` VALUES ('8', '房东相关', '房东相关', '<p>房东相关</p>', '租房', '2018-12-08 01:49:13', '2018-12-08 03:18:49');
INSERT INTO `tk_questions` VALUES ('12', '关于我们1', '关于我们1', '<p>关于我们</p>', '关于我们', '2018-12-08 02:06:09', '2018-12-08 03:18:58');
INSERT INTO `tk_questions` VALUES ('16', '房源申请', '房源申请', '<p>房源申请</p>', '房源申请', '2018-12-08 02:39:38', '2018-12-08 03:19:31');
INSERT INTO `tk_questions` VALUES ('18', '周边服务', '周边服务2', '<p>周边服务2</p>', '周边服务', '2018-12-08 02:47:03', '2018-12-08 03:20:10');
INSERT INTO `tk_questions` VALUES ('19', '闪电招租', '闪电招租', '', '闪电招租', '2018-12-08 03:19:54', '2018-12-08 03:19:54');

-- ----------------------------
-- Table structure for tk_role
-- ----------------------------
DROP TABLE IF EXISTS `tk_role`;
CREATE TABLE `tk_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(8) NOT NULL DEFAULT '' COMMENT '角色名称',
  `authority` text NOT NULL COMMENT '权限ids:route_group的ids',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of tk_role
-- ----------------------------
INSERT INTO `tk_role` VALUES ('0', '超级管理员', '');
INSERT INTO `tk_role` VALUES ('1', '普通管理员', '1,5,8,14,12,15');

-- ----------------------------
-- Table structure for tk_route
-- ----------------------------
DROP TABLE IF EXISTS `tk_route`;
CREATE TABLE `tk_route` (
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '路由名称',
  `op_name` varchar(32) NOT NULL DEFAULT '' COMMENT '操作名称',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属菜单',
  `ignore` tinyint(4) NOT NULL DEFAULT '0' COMMENT '忽略权限检查',
  PRIMARY KEY (`name`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='路由表';

-- ----------------------------
-- Records of tk_route
-- ----------------------------
INSERT INTO `tk_route` VALUES ('/admin/admin/add', '添加管理员', '2', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/delete', '删除管理员', '2', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/get_all_menu', '获取菜单', '3', '1');
INSERT INTO `tk_route` VALUES ('/admin/admin/get_role_info_by_id', '获取角色权限信息', '4', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/index', '管理员列表', '2', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/menu', '菜单列表', '3', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/menu_delete', '删除菜单', '3', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/menu_save', '编辑菜单', '3', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/modify_nick', '编辑昵称', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/admin/modify_password', '修改密码', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/admin/role', '角色列表', '4', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/role_delete', '删除角色', '4', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/role_save', '保存角色', '4', '0');
INSERT INTO `tk_route` VALUES ('/admin/admin/update', '修改管理员', '2', '0');
INSERT INTO `tk_route` VALUES ('/admin/config/index', '网站设置', '8', '0');
INSERT INTO `tk_route` VALUES ('/admin/config/save', '保存设置', '8', '0');
INSERT INTO `tk_route` VALUES ('/admin/index/ajax_login', '登陆', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/index/check_auth', '检查权限', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/index/get_code', '获取图片验证码', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/index/index', '后台首页', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/index/login', '后台登陆', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/index/logout', '后台注销', '13', '1');
INSERT INTO `tk_route` VALUES ('/admin/news/cates', '新闻分类', '6', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/delete', '删除新闻', '7', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/delete_cate', '删除新闻分类', '6', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/edit', '修改新闻', '7', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/get_cates', '获取新闻分类', '6', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/index', '新闻管理', '7', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/merge_cate', '合并分类', '6', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/save', '保存新闻', '7', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/save_cate', '保存新闻分类', '6', '0');
INSERT INTO `tk_route` VALUES ('/admin/news/upload_thumbnail', '上传缩略图', '13', '1');

-- ----------------------------
-- Table structure for tk_route_group
-- ----------------------------
DROP TABLE IF EXISTS `tk_route_group`;
CREATE TABLE `tk_route_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `route_names` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_id` (`menu_id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='路由-操作-映射关系';

-- ----------------------------
-- Records of tk_route_group
-- ----------------------------
INSERT INTO `tk_route_group` VALUES ('1', '2', '管理员列表', '/admin/admin/index');
INSERT INTO `tk_route_group` VALUES ('3', '2', '添加、修改管理员', '/admin/admin/add,/admin/admin/index,/admin/admin/update');
INSERT INTO `tk_route_group` VALUES ('4', '2', '删除管理员', '/admin/admin/delete,/admin/admin/index');
INSERT INTO `tk_route_group` VALUES ('5', '4', '角色列表', '/admin/admin/get_role_info_by_id,/admin/admin/role');
INSERT INTO `tk_route_group` VALUES ('6', '4', '添加、修改角色', '/admin/admin/get_role_info_by_id,/admin/admin/role,/admin/admin/role_save');
INSERT INTO `tk_route_group` VALUES ('7', '4', '删除角色', '/admin/admin/get_role_info_by_id,/admin/admin/role,/admin/admin/role_delete');
INSERT INTO `tk_route_group` VALUES ('8', '6', '新闻分类列表', '/admin/news/cates,/admin/news/get_cates');
INSERT INTO `tk_route_group` VALUES ('9', '6', '添加、修改新闻分类', '/admin/news/cates,/admin/news/get_cates,/admin/news/save_cate');
INSERT INTO `tk_route_group` VALUES ('10', '6', '合并新闻分类', '/admin/news/cates,/admin/news/get_cates,/admin/news/merge_cate');
INSERT INTO `tk_route_group` VALUES ('11', '6', '删除新闻分类', '/admin/news/cates,/admin/news/delete_cate,/admin/news/get_cates');
INSERT INTO `tk_route_group` VALUES ('12', '7', '新闻列表', '/admin/news/index');
INSERT INTO `tk_route_group` VALUES ('13', '7', '添加、修改新闻', '/admin/news/edit,/admin/news/index,/admin/news/save');
INSERT INTO `tk_route_group` VALUES ('14', '7', '删除新闻', '/admin/news/delete,/admin/news/index');
INSERT INTO `tk_route_group` VALUES ('15', '8', '网站设置', '/admin/config/index');
INSERT INTO `tk_route_group` VALUES ('16', '8', '修改网站设置', '/admin/config/index,/admin/config/save');

-- ----------------------------
-- Table structure for tk_user
-- ----------------------------
DROP TABLE IF EXISTS `tk_user`;
CREATE TABLE `tk_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(256) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `avaurl` varchar(255) DEFAULT NULL COMMENT '头像',
  `tel` varchar(11) DEFAULT NULL,
  `wchat` varchar(255) DEFAULT NULL COMMENT '微信号',
  `real_name` varchar(100) DEFAULT NULL COMMENT '姓名',
  `count` int(11) DEFAULT '0' COMMENT '收藏房源数',
  `status` tinyint(3) DEFAULT '0' COMMENT '0：用户；1：房东',
  `cdate` datetime DEFAULT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_user
-- ----------------------------
INSERT INTO `tk_user` VALUES ('4', '12222', '侧身听雨1', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJ5exQKIhibFicKELuiaf3SxpnhfvFDKCO1RQdp69argsB2YiccqtB6SYq8uG0AvOkD5GdziahzptfNwlg/132', '13483257463', 'qwd13483257463', '钱卫东', '0', '0', '2018-12-08 03:16:39', '2018-12-08 03:16:42');
INSERT INTO `tk_user` VALUES ('2', '12222', '侧身听雨', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJ5exQKIhibFicKELuiaf3SxpnhfvFDKCO1RQdp69argsB2YiccqtB6SYq8uG0AvOkD5GdziahzptfNwlg/132', '13483257463', 'qwd13483257463', '钱卫东', '0', '1', '2018-12-05 12:11:57', '2018-12-05 12:12:00');
INSERT INTO `tk_user` VALUES ('6', '11', '测试', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJ5exQKIhibFicKELuiaf3SxpnhfvFDKCO1RQdp69argsB2YiccqtB6SYq8uG0AvOkD5GdziahzptfNwlg/132', '13483257466', '酒精发酵微信号22222222222', '张三22222222222222222222', '0', '1', '2018-12-11 16:48:20', '2018-12-11 16:48:20');
