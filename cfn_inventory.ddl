CREATE TABLE `cfn_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku_id` int DEFAULT NULL,
  `asin_id` int DEFAULT NULL,
  `product_name` varchar(450) COLLATE utf8_bin DEFAULT NULL,
  `condition` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `your_price` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `cfn_warehouse_quantity` int DEFAULT NULL,
  `cfn_fulfillable_quantity` int DEFAULT NULL,
  `cfn_unsellable_quantity` int DEFAULT NULL,
  `cfn_reserved_quantity` int DEFAULT NULL,
  `cfn_total_quantity` int DEFAULT NULL,
  `date` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cfn_sku_idx` (`sku_id`),
  KEY `fk_cfn_asin_idx` (`asin_id`),
  CONSTRAINT `fk_cfn_asin` FOREIGN KEY (`asin_id`) REFERENCES `asin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cfn_sku` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin