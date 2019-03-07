
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `account`
-- ----------------------------
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '后台账号ID',
  `email` varchar(128) NOT NULL DEFAULT '' COMMENT '后台登录邮箱',
  `password` varchar(255) NOT NULL DEFAULT '',
  `real_name` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态',
  `is_manager` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否管理员',
  `is_readonly` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否只允许读操作',
  `create_act_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者ID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Records of `account`
-- ----------------------------
BEGIN;
INSERT INTO `account` VALUES ('21', 'hts', '$2y$10$OYZ68skKlM.0oeKJlwOqa.YPA6bf1tO14UkZmMIn24wgN3i4E94.y', 'hts', '', '0', '0', '1', '0', '1', '1525384712', '0');
COMMIT;

-- ----------------------------
--  Table structure for `account_role`
-- ----------------------------
DROP TABLE IF EXISTS `account_role`;
CREATE TABLE `account_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL COMMENT '后台账号ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '权限角色ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_account_role` (`account_id`,`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Records of `account_role`
-- ----------------------------
BEGIN;
INSERT INTO `account_role` VALUES ('7', '20', '3'), ('10', '24', '3');
COMMIT;

-- ----------------------------
--  Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限角色ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '角色名称',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 1：启用 0：禁用',
  `nodes` text NOT NULL COMMENT '角色拥有的权限节点，多个节点用逗号(,)分隔',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Records of `role`
-- ----------------------------
BEGIN;
INSERT INTO `role` VALUES ('3', '管理员', '1', 'index,setting'), ('5', '对对对', '0', 'setting:account,setting:role');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
