/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : hxb

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-12-14 14:01:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tk_cate
-- ----------------------------
DROP TABLE IF EXISTS `tk_cate`;
CREATE TABLE `tk_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` int(11) NOT NULL,
  `hot` varchar(11) NOT NULL,
  `cdate` datetime NOT NULL,
  `mdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_cate
-- ----------------------------
INSERT INTO `tk_cate` VALUES ('1', '北京市', '0', '否', '0000-00-00 00:00:00', '2018-12-14 13:28:31');
INSERT INTO `tk_cate` VALUES ('2', '清华大学', '3', '是', '0000-00-00 00:00:00', '2018-12-14 13:26:59');
INSERT INTO `tk_cate` VALUES ('3', '海淀区', '1', '否', '0000-00-00 00:00:00', '2018-12-14 13:28:26');
INSERT INTO `tk_cate` VALUES ('5', '北京大学', '3', '是', '2018-12-14 13:43:33', '2018-12-14 13:43:33');
INSERT INTO `tk_cate` VALUES ('6', '北京电影学院', '3', '是', '2018-12-14 13:43:44', '2018-12-14 13:43:44');
