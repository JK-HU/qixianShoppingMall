CREATE TABLE `mtt_express` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`name` varchar(128) NOT NULL DEFAULT '' COMMENT '物流名称',`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `mtt_auth_rule` VALUES ('328', 'express', '物流管理', '1', '1', '0', 'icon-list', '', '0', '50', '1555924081', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('329', 'express/index', '物流列表', '1', '1', '0', '', '', '328', '50', '1555924167', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('330', 'express/edit', '增加/编辑物流', '1', '1', '0', '', '', '329', '50', '1555982427', null, '0');
INSERT INTO `mtt_auth_rule` VALUES ('331', 'express/delete', '物流删除', '1', '1', '0', '', '', '329', '50', '1555982453', null, '0');
INSERT INTO `mtt_auth_rule` VALUES ('332', 'express/delall', '批量删除', '1', '1', '0', '', '', '329', '50', '1555982473', null, '0');