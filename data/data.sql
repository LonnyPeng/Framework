-- ----------------------------
-- Table structure for t_member admin@123456
-- ----------------------------
DROP TABLE IF EXISTS `t_member`;
CREATE TABLE `t_member` (
  `member_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '人员ID',
  `member_name` varchar(90) NOT NULL UNIQUE KEY COMMENT '用户名',
  `member_password` char(35) NOT NULL COMMENT '密码',
  `member_regtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `member_logtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一次登录时间',
  `member_logip` char(15) NOT NULL DEFAULT '' COMMENT '最后一次登录ip',
  `member_lognum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `member_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='后台管理人员表';

INSERT INTO `t_member` (`member_name`, `member_password`) VALUES('admin', '5f0bcb3a8f4a5b4381a25acb90168d74:30');