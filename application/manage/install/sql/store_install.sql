CREATE TABLE IF NOT EXISTS `mtt_shop_store` (`id` int(11) NOT NULL,`title` varchar(30) NOT NULL DEFAULT '' COMMENT '门店名称',`logo` varchar(255) NOT NULL DEFAULT '' COMMENT '门店logo',`money` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '保证金',`introduction` varchar(300) NOT NULL DEFAULT '' COMMENT '门店简介',`province` int(11) NOT NULL DEFAULT '0' COMMENT '省',`city` int(11) NOT NULL DEFAULT '0' COMMENT '市',`district` int(11) NOT NULL DEFAULT '0' COMMENT '区',`address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='门店管理' AUTO_INCREMENT=4;
ALTER TABLE `mtt_shop_store` ADD PRIMARY KEY (`id`);
INSERT INTO `mtt_auth_rule` VALUES (313, 'store', '门店管理', 1, 1, 1, 'icon-list', '', 0, 50, 1553669090, NULL, 1);
INSERT INTO `mtt_auth_rule` VALUES (314, 'store/store_lists', '门店列表', 1, 1, 1, '', '', 313, 50, 1553669183, NULL, 1);
INSERT INTO `mtt_auth_rule` VALUES (315, 'store/add_store', '增加门店', 1, 1, 1, '', '', 314, 50, 1553669297, NULL, 1);
INSERT INTO `mtt_auth_rule` VALUES (316, 'store/edit_store', '编辑门店', 1, 1, 1, '', '', 314, 50, 1553669330, NULL, 1);
INSERT INTO `mtt_auth_rule` VALUES (317, 'store/store_del', '删除门店', 1, 1, 1, '', '', 314, 50, 1553669373, NULL, 1);
INSERT INTO `mtt_auth_rule` VALUES (318, 'store/store_all_del', '批量删除', 1, 1, 1, '', '', 314, 50, 1553669395, NULL, 1);