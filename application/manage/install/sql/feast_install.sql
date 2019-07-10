CREATE TABLE `mtt_feast` (`id` int(4) NOT NULL AUTO_INCREMENT COMMENT '自增ID',`title` varchar(120) DEFAULT '' COMMENT '标题',`open` int(1) DEFAULT '1' COMMENT '是否开启',`sort` int(4) DEFAULT '50' COMMENT '排序',`addtime` varchar(15) DEFAULT NULL COMMENT '添加时间',`feast_date` varchar(20) DEFAULT '' COMMENT '节日日期',`type` int(1) DEFAULT '1' COMMENT '1阳历 2农历',PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='节日列表';
CREATE TABLE `mtt_feast_element` (`id` int(5) NOT NULL AUTO_INCREMENT COMMENT '自增ID',`pid` int(4) DEFAULT NULL COMMENT '父级ID',`title` varchar(120) DEFAULT NULL COMMENT '标题',`css` text COMMENT 'CSS',`js` text COMMENT 'JS',`sort` int(5) DEFAULT '50' COMMENT '排序',`open` int(1) DEFAULT '1' COMMENT '是否开启',`addtime` varchar(15) DEFAULT NULL COMMENT '添加时间',PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='节日元素表';
INSERT INTO `mtt_auth_rule` VALUES ('281', 'festival', '节日管理', '1', '1', '1', 'icon-list', '', '0', '50', '1547695179', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('282', 'feast/index', '节日列表', '1', '1', '1', '', '', '281', '50', '1547695302', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('283', 'feast/add', '  操作-添加', '1', '1', '1', '', '', '282', '50', '1547695351', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('284', 'feast/edit', '  操作-修改', '1', '1', '1', '', '', '282', '50', '1547695369', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('285', 'feast/editState', '  操作-状态', '1', '1', '1', '', '', '282', '50', '1547695403', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('286', 'feast/feastOrder', '操作-排序', '1', '1', '1', '', '', '282', '50', '1547695440', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('287', 'feast/del', '操作-删除', '1', '1', '1', '', '', '282', '50', '1547695604', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('288', 'feast/element', '节日元素', '1', '1', '1', '', '', '281', '50', '1547695646', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('289', 'feast/add_element', '操作-添加', '1', '1', '1', '', '', '288', '50', '1547695713', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('290', 'feast/edit_element', '操作-修改', '1', '1', '1', '', '', '288', '50', '1547695735', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('291', 'feast/elementState', '操作-状态', '1', '1', '1', '', '', '288', '50', '1547695764', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('292', 'feast/elementOrder', '操作-排序', '1', '1', '1', '', '', '288', '50', '1547695786', null, '1');
INSERT INTO `mtt_auth_rule` VALUES ('293', 'feast/delElement', '操作-删除', '1', '1', '1', '', '', '288', '50', '1547695805', null, '1');