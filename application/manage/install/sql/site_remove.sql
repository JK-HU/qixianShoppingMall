DROP TABLE IF EXISTS `mtt_link`;
DROP TABLE IF EXISTS `mtt_message`;
DROP TABLE IF EXISTS `mtt_donation`;
DELETE FROM `mtt_auth_rule` WHERE `mtt_auth_rule`.`id` in(28,31,32,249,250,251,48,247,248,265);