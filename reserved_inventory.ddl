CREATE TABLE `reserved_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku_id` int DEFAULT NULL,
  `asin_id` int DEFAULT NULL,
  `product_name` varchar(450) DEFAULT NULL,
  `reserved_qty` int DEFAULT NULL,
  `reserved_customerorders` int DEFAULT NULL,
  `reserved_fc_transfers` int DEFAULT NULL,
  `reserved_fc_processing` int DEFAULT NULL,
  `date` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_res_sku_idx` (`sku_id`),
  KEY `fk_res_asin_idx` (`asin_id`),
  CONSTRAINT `fk_res_asin` FOREIGN KEY (`asin_id`) REFERENCES `asin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_res_sku` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8