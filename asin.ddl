CREATE TABLE `asin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asin` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asin_UNIQUE` (`asin`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin