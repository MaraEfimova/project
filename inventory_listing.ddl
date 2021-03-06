CREATE TABLE `inventory_listing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku_id` int DEFAULT NULL COMMENT ',,asin,product-name,condition,your-price,mfn-listing-exists,mfn-fulfillable-quantity,afn-listing-exists,afn-warehouse-quantity,afn-fulfillable-quantity,afn-unsellable-quantity,afn-reserved-quantity,afn-total-quantity,per-unit-volume,afn-inbound-working-quantity,afn-inbound-shipped-quantity,afn-inbound-receiving-quantity,afn-researching-quantity,afn-reserved-future-supply,afn-future-supply-buyable',
  `fnsku` varchar(100) DEFAULT NULL,
  `asin_id` int DEFAULT NULL,
  `product_name` varchar(450) DEFAULT NULL,
  `condition` varchar(10) DEFAULT NULL,
  `your_price` varchar(45) DEFAULT NULL,
  `mfn_listing_exists` varchar(45) DEFAULT NULL,
  `mfn_fulfillable_quantity` varchar(45) DEFAULT NULL,
  `afn_listing_exists` varchar(45) DEFAULT NULL,
  `afn_warehouse_quantity` int DEFAULT NULL,
  `afn_fulfillable_quantity` int DEFAULT NULL,
  `afn_unsellable_quantity` int DEFAULT NULL,
  `afn_reserved_quantity` int DEFAULT NULL,
  `afn_total_quantity` int DEFAULT NULL,
  `per_unit_volume` varchar(45) DEFAULT NULL,
  `afn_inbound_working_quantity` int DEFAULT NULL,
  `afn_inbound_shipped_quantity` int DEFAULT NULL,
  `afn_inbound_receiving_quantity` int DEFAULT NULL,
  `afn_researching_quantity` int DEFAULT NULL,
  `afn_reserved_future_supply` int DEFAULT NULL,
  `date` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_inv_sku_idx` (`sku_id`),
  KEY `fk_inv_asin_idx` (`asin_id`),
  CONSTRAINT `fk_inv_asin` FOREIGN KEY (`asin_id`) REFERENCES `asin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_sku` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8