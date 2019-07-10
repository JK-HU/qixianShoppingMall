DROP TABLE IF EXISTS `mtt_shop_goods`;
DROP TABLE IF EXISTS `mtt_shop_goods_type`;
DROP TABLE IF EXISTS `mtt_shop_goods_norms`;
DROP TABLE IF EXISTS `mtt_shop_goods_norms_type`;
DROP TABLE IF EXISTS `mtt_shop_goods_nt`;
DELETE FROM `mtt_auth_rule` WHERE `mtt_auth_rule`.`id` in(302,303,304,306,307,308,309,310,311,312);
DROP TABLE IF EXISTS `mtt_shop_order`;
DROP TABLE IF EXISTS `mtt_express_code`;
DELETE FROM `mtt_auth_rule` WHERE `mtt_auth_rule`.`id` in(297,298,299,300,301);