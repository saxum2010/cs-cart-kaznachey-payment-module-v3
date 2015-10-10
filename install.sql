INSERT INTO `cscart_language_values` (`lang_code`, `name`, `value`) VALUES('EN', 'kaznachey_merchantGuid', 'Merchant ID');
INSERT INTO `cscart_language_values` (`lang_code`, `name`, `value`) VALUES('RU', 'kaznachey_merchantGuid', 'Merchant ID');
INSERT INTO `cscart_language_values` (`lang_code`, `name`, `value`) VALUES('EN', 'kaznachey_merchnatSecretKey', 'Secret key');
INSERT INTO `cscart_language_values` (`lang_code`, `name`, `value`) VALUES('RU', 'kaznachey_merchnatSecretKey', 'Секретный ключ');
INSERT INTO `cscart_language_values` (`lang_code`, `name`, `value`) VALUES('RU', 'kaznachey_currency', 'Курс валюты по умолчанию к грн. (=1 если основная валюта - гривна)');
INSERT INTO `cscart_language_values` (`lang_code`, `name`, `value`) VALUES('EN', 'kaznachey_currency', 'Currency by default to UAH (=1 if main currency - UAH)');

INSERT INTO `cscart_payment_processors` (`processor_id`, `processor`, `processor_script`, `processor_template`, `admin_template`, `callback`, `type`) VALUES(NULL, 'kaznachey', 'kaznachey.php', 'kaznachey.tpl', 'kaznachey.tpl', 'Y', 'P');