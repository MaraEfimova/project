<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$sql = new PDO('mysql:host=localhost; dbname=test_1666', 'root', 'root');

if (isset($_POST["submit"]) ) {
    if (isset($_FILES["file"])) {
        $date = date('o-m-d');
        $time = date('G:i:s');
        $time_valid = date('G.i.s');
        $date_audit =  date('o-m-d')." ".date('G:i:s');

        foreach ($_FILES["file"]["name"] as $key => $value) {
            echo "Upload: ".$_FILES["file"]["name"][$key]."<br />";
            echo "Size: ".($_FILES["file"]["size"][$key] / 1024) ."<br /><br />";

            move_uploaded_file($_FILES["file"]["tmp_name"][$key], "../upload/".$_FILES["file"]["name"][$key]);
        };
        
        if (file_exists("../upload/cfn_inventory.csv") && file_exists("../upload/inventory_listing.csv") && file_exists("../upload/reserved_inventory.csv")) {
            $cfn_inventory = file_get_contents("../upload/cfn_inventory.csv", "r");
            $inventory_listing = file_get_contents("../upload/inventory_listing.csv", "r");
            $reserved_inventory = file_get_contents("../upload/reserved_inventory.csv", "r");

            $cfn_inventory = rtrim($cfn_inventory, "\n" );
            $inventory_listing = rtrim($inventory_listing, "\n" );
            $reserved_inventory = rtrim($reserved_inventory, "\n" );

            $cfn_inventory = explode("\n", $cfn_inventory);
            $inventory_listing = explode("\n", $inventory_listing);
            $reserved_inventory = explode("\n", $reserved_inventory);

            array_shift($cfn_inventory);
            array_shift($inventory_listing);
            array_shift($reserved_inventory);

            
            $sqlInsSku = "INSERT INTO test_1666.sku(sku) VALUES ";
            $sqlInsAsin = "INSERT INTO test_1666.asin(asin) VALUES ";

            $curItemsCount = 0;
            $totalCount = 0;
            $curValuesSku = '';
            $curValuesAsin = '';
            
            // write SKUs and ASINs - cfn
            foreach ($cfn_inventory as $key => $value) {
                $rows = str_getcsv($value, ",", '"');
                
                $curValuesSku .= '(';
                if ($rows[0] == null) {$curValuesSku .= 'NULL, ';} 
                else {$curValuesSku .= "'".$rows[0]."'";}
                $curValuesSku .= ')';

                $curValuesAsin .= '(';
                if ($rows[1] == null) {$curValuesAsin .= 'NULL, ';} 
                else {$curValuesAsin .= "'".$rows[1]."'";}
                $curValuesAsin .= ')';
                
                $curItemsCount = $curItemsCount + 1;
                $totalCount = $totalCount + 1;
                
                if ($curItemsCount == 1000 || $totalCount === count($cfn_inventory)) {

                    $tempQueSku = $sqlInsSku.$curValuesSku.' ON DUPLICATE KEY UPDATE sku=VALUES(sku);';
                    $sthm = $sql->prepare($tempQueSku);
                    $sthm->execute();

                    $tempQueAsin = $sqlInsAsin.$curValuesAsin.' ON DUPLICATE KEY UPDATE asin=VALUES(asin);';
                    $sthm = $sql->prepare($tempQueAsin);
                    $sthm->execute();

                    $curItemsCount = 0;
                    $curValuesSku = '';
                    $curValuesAsin ='';
                } else {
                    $curValuesSku .= ',';
                    $curValuesAsin .= ',';
                };
            };
            $totalCount = 0;

            //write SKUs and ASINs - listing 
            foreach ($inventory_listing as $key => $value) {
                $rows = str_getcsv($value, ",", '"');
                
                $curValuesSku .= '(';
                if ($rows[0] == null) {$curValuesSku .= 'NULL, ';} 
                else {$curValuesSku .= "'".$rows[0]."'";}
                $curValuesSku .= ')';

                $curValuesAsin .= '(';
                if ($rows[2] == null) {$curValuesAsin .= 'NULL, ';} 
                else {$curValuesAsin .= "'".$rows[2]."'";}
                $curValuesAsin .= ')';
                
                $curItemsCount = $curItemsCount + 1;
                $totalCount = $totalCount + 1;
                
                if ($curItemsCount == 1000 || $totalCount === count($inventory_listing)) {

                    $tempQueSku = $sqlInsSku.$curValuesSku.' ON DUPLICATE KEY UPDATE sku=VALUES(sku);';
                    $sthm = $sql->prepare($tempQueSku);
                    $sthm->execute();

                    $tempQueAsin = $sqlInsAsin.$curValuesAsin.' ON DUPLICATE KEY UPDATE asin=VALUES(asin);';
                    $sthm = $sql->prepare($tempQueAsin);
                    $sthm->execute();

                    $curItemsCount = 0;
                    $curValuesSku = '';
                    $curValuesAsin = '';
                } else {
                    $curValuesSku .= ',';
                    $curValuesAsin .= ',';
                };
            };
            $totalCount = 0;

            foreach ($reserved_inventory as $key => $value) {
                $rows = str_getcsv($value, ",", '"');
                
                $curValuesSku .= '(';
                if ($rows[0] == null) {$curValuesSku .= 'NULL, ';} else {$curValuesSku .= "'".$rows[0]."'";}
                $curValuesSku .= ')';

                $curValuesAsin .= '(';
                if ($rows[1] == null) {$curValuesAsin .= 'NULL, ';} else {$curValuesAsin .= "'".$rows[1]."'";}
                $curValuesAsin .= ')';
                
                $curItemsCount = $curItemsCount + 1;
                $totalCount = $totalCount + 1;
                
                if ($curItemsCount == 1000 || $totalCount === count($reserved_inventory)) {

                    $tempQueSku = $sqlInsSku.$curValuesSku.' ON DUPLICATE KEY UPDATE sku=VALUES(sku);';
                    $sthm = $sql->prepare($tempQueSku);
                    $sthm->execute();

                    $tempQueAsin = $sqlInsAsin.$curValuesAsin.' ON DUPLICATE KEY UPDATE asin=VALUES(asin);';
                    $sthm = $sql->prepare($tempQueAsin);
                    $sthm->execute();

                    $curItemsCount = 0;
                    $curValuesSku = '';
                    $curValuesAsin = '';
                } else {
                    $curValuesSku .= ',';
                    $curValuesAsin .= ',';
                };
            };


            $stmt = $sql->prepare("SELECT `id`, `sku` FROM test_1666.sku");
            $stmt->execute(); 
            $sku_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $sql->prepare("SELECT `id`, `asin` FROM test_1666.asin");
            $stmt->execute(); 
            $asin_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $sqlIns1 = "INSERT INTO test_1666.cfn_inventory(sku_id, asin_id, product_name, `condition`, your_price, cfn_warehouse_quantity, cfn_fulfillable_quantity, cfn_unsellable_quantity, cfn_reserved_quantity, cfn_total_quantity, `date`) VALUES ";
            $curValues = '';

            foreach ($cfn_inventory as $key => $value) {
                $rows = str_getcsv($value, ",", '"');
                $curValues .= '(';

                foreach ($sku_data as $key1 => $valueSKU) {
                    if ($rows[0] == $valueSKU['sku']) {
                        if ($rows[0] != null) {$curValues .= $valueSKU['id'].", ";}  else {$curValues .= 'NULL, ';};
                        break;
                    };
                };

                foreach ($asin_data as $key1 => $valueASIN) {
                    if ($rows[1] == $valueASIN['asin']) {
                        if ($rows[1] != null) {$curValues .= $valueASIN['id'].", ";} else {$curValues .= 'NULL, ';};
                        break;
                    };
                };
                
                if ($rows[2] == null) {$curValues .= 'NULL, ';} else {$curValues .= "N'".str_replace("'", "\'", $rows[2])."', ";}
                if ($rows[3] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".str_replace("'", "\'", $rows[3])."', ";}
                if ($rows[4] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".str_replace("'", "\'", $rows[4])."', ";}
                if ($rows[5] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[5].", ";}
                if ($rows[6] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[6].", ";}
                if ($rows[7] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[7].", ";}
                if ($rows[8] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[8].", ";}
                if ($rows[9] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[9].", ";}

                $curValues .= "'".$date_audit."'";
                $curValues .= ')';

                $curItemsCount = $curItemsCount + 1;
                $totalCount = $totalCount + 1;
                
                if ($curItemsCount == 1000 || $totalCount === count($cfn_inventory)) {

                    $tempQue = $sqlIns1.$curValues.';';
                    $sthm = $sql->prepare($tempQue);
                    $sthm->execute();
                    // echo $tempQue;
                    // echo '<br><br><br>';

                    $curItemsCount = 0;
                    $curValues = '';
                } else {
                    $curValues .= ',';
                };

            };

            $sqlIns2 = "INSERT INTO test_1666.inventory_listing(sku_id, fnsku, asin_id, product_name, `condition`, your_price, mfn_listing_exists, mfn_fulfillable_quantity, afn_listing_exists, afn_warehouse_quantity, afn_fulfillable_quantity, afn_unsellable_quantity, afn_reserved_quantity, afn_total_quantity, per_unit_volume, afn_inbound_working_quantity, afn_inbound_shipped_quantity, afn_inbound_receiving_quantity, afn_researching_quantity, afn_reserved_future_supply, `date`) VALUES ";
            $totalCount = 0;

            foreach ($inventory_listing as $key => $value) {
                $rows = str_getcsv($value, ",", '"');
                
                $curValues .= '(';

                foreach ($sku_data as $key1 => $valueSKU) {
                    if ($rows[0] == $valueSKU['sku']) {
                        if ($rows[0] != null) {$curValues .= $valueSKU['id'].", ";} else {$curValues .= 'NULL, ';};
                        break;
                    };
                };

                if ($rows[1] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[1]."', ";}

                foreach ($asin_data as $key1 => $valueASIN) {
                    if ($rows[2] == $valueASIN['asin']) {
                        if ($rows[2] != null) {$curValues .= $valueASIN['id'].", ";} else {$curValues .= 'NULL, ';};
                        break;
                    };
                };

                if ($rows[3] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".str_replace("'", "\'", $rows[3])."', ";}
                if ($rows[4] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[4]."', ";}
                if ($rows[5] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[5]."', ";}
                if ($rows[6] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[6]."', ";}
                if ($rows[7] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[7]."', ";}
                if ($rows[8] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[8]."', ";}
                if ($rows[9] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[9].", ";}
                if ($rows[10] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[10].", ";}
                if ($rows[11] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[11].", ";}
                if ($rows[12] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[12].", ";}
                if ($rows[13] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[13].", ";}
                if ($rows[14] == null) {$curValues .= 'NULL, ';} else {$curValues .= "'".$rows[14]."', ";}
                if ($rows[15] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[15].", ";}
                if ($rows[16] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[16].", ";}
                if ($rows[17] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[17].", ";}
                if ($rows[18] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[18].", ";}
                if ($rows[19] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[19].", ";}

                $curValues .= "'".$date_audit."'";
                $curValues .= ')';
                $curItemsCount = $curItemsCount + 1;
                $totalCount = $totalCount + 1;
                
                if ($curItemsCount == 1000 || $totalCount === count($inventory_listing)) {

                    $tempQue = $sqlIns2.$curValues.';';
                    $sthm = $sql->prepare($tempQue);
                    $sthm->execute();
                    // echo $tempQue;
                    // echo '<br><br><br>';

                    $curItemsCount = 0;
                    $curValues = '';
                } else {
                    $curValues .= ',';
                }
                
            };

            $sqlIns3 = "INSERT INTO test_1666.reserved_inventory(sku_id, asin_id, product_name, reserved_qty, reserved_customerorders, reserved_fc_transfers, reserved_fc_processing, `date`) VALUES ";
            $totalCount = 0;

            foreach ($reserved_inventory as $key => $value) {
                $rows = str_getcsv($value, ",", '"');
            
                $curValues .= '(';

                foreach ($sku_data as $key1 => $valueSKU) {
                    if ($rows[0] == $valueSKU['sku']) {
                        if ($rows[0] != null) {$curValues .= $valueSKU['id'].", ";} else {$curValues .= 'NULL, ';};
                        break;
                    };
                };

                foreach ($asin_data as $key1 => $valueASIN) {
                    if ($rows[1] == $valueASIN['asin']) {
                        if ($rows[1] != null) {$curValues .= $valueASIN['id'].", ";} else {$curValues .= 'NULL, ';};
                        break;
                    };
                };

                if ($rows[2] == null) {$curValues .= 'NULL, ';} else {$curValues .= "N'".str_replace("'", "\'", $rows[2])."', ";}
                if ($rows[3] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[3].", ";}
                if ($rows[4] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[4].", ";}
                if ($rows[5] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[5].", ";}
                if ($rows[6] == null) {$curValues .= 'NULL, ';} else {$curValues .= "".$rows[6].", ";}

                $curValues .= "'".$date_audit."'";
                $curValues .= ')';

                $curItemsCount = $curItemsCount + 1;
                $totalCount = $totalCount + 1;
                
                if ($curItemsCount == 1000 || $totalCount === count($reserved_inventory)) {

                    $tempQue = $sqlIns3.$curValues.';';
                    $sthm = $sql->prepare($tempQue);
                    $sthm->execute();
                    // echo $tempQue;
                    // echo '<br><br><br>';

                    $curItemsCount = 0;
                    $curValues = '';
                } else {
                    $curValues .= ',';
                };
            };
            
            // return CSV file as result
            $stmt = $sql->prepare("SELECT  sk.sku, il.`fnsku`, asi.asin, il.`product_name`, il.`condition`, il.`your_price` , il.`mfn_listing_exists`, il.`mfn_fulfillable_quantity`, il.`afn_listing_exists`, il.`afn_warehouse_quantity`, il.`afn_fulfillable_quantity`, il.`afn_unsellable_quantity`, il.`afn_reserved_quantity`, il.`afn_total_quantity`, il.`per_unit_volume`, il.`afn_inbound_working_quantity`, il.`afn_inbound_shipped_quantity`, il.`afn_inbound_receiving_quantity`, il.`afn_researching_quantity`, il.`afn_reserved_future_supply`, ci.`cfn_warehouse_quantity`, ci.`cfn_fulfillable_quantity`, ci.`cfn_unsellable_quantity`, ci.`cfn_reserved_quantity`, ci.`cfn_total_quantity`
                FROM TEST_1666.CFN_INVENTORY 		AS ci
                JOIN TEST_1666.INVENTORY_LISTING 	AS il 	ON ci.sku_id = il.sku_id AND ci.date = '$date_audit'
                JOIN TEST_1666.RESERVED_INVENTORY 	AS ri 	ON ci.sku_id = ri.sku_id AND ri.date = '$date_audit' 
                JOIN test_1666.sku 					AS sk 	ON ci.sku_id = sk.id
                JOIN test_1666.asin 				AS asi 	ON ci.asin_id = asi.id
                WHERE il.Date = '$date_audit' AND il.`condition` = 'New';"
            );
            $stmt->execute(); 
            $resultData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // print_r($resultData);

            // foreach ($resultData as $key => $value) {
            //     $value
            // }

            
            $fp = fopen('../files/'.$date.'_'.$time_valid.'.csv', 'w');

            $temp_arr = array();
            foreach ($resultData as $key => $value) {
                $temp_arr[$key][0] = $value['sku'];
                $temp_arr[$key][1] = $value['fnsku'];
                $temp_arr[$key][2] = $value['asin'];
                $temp_arr[$key][3] = $value['product_name'];
                $temp_arr[$key][4] = $value['condition'];
                $temp_arr[$key][5] = $value['your_price'];
                $temp_arr[$key][6] = $value['mfn_listing_exists'];
                $temp_arr[$key][7] = $value['mfn_fulfillable_quantity'];
                $temp_arr[$key][8] = $value['afn_listing_exists'];
                $temp_arr[$key][9] = $value['afn_warehouse_quantity'];
                $temp_arr[$key][10] = $value['afn_fulfillable_quantity'];
                $temp_arr[$key][11] = $value['afn_unsellable_quantity'];
                $temp_arr[$key][12] = $value['afn_reserved_quantity'];
                $temp_arr[$key][13] = $value['afn_total_quantity'];
                $temp_arr[$key][14] = $value['per_unit_volume'];
                $temp_arr[$key][15] = $value['afn_inbound_working_quantity'];
                $temp_arr[$key][16] = $value['afn_inbound_shipped_quantity'];
                $temp_arr[$key][17] = $value['afn_inbound_receiving_quantity'];
                $temp_arr[$key][18] = $value['afn_researching_quantity'];
                $temp_arr[$key][19] = $value['afn_reserved_future_supply'];
                $temp_arr[$key][20] = $value['cfn_warehouse_quantity'];
                $temp_arr[$key][21] = $value['cfn_fulfillable_quantity'];
                $temp_arr[$key][22] = $value['cfn_unsellable_quantity'];
                $temp_arr[$key][23] = $value['cfn_reserved_quantity'];
                $temp_arr[$key][24] = $value['cfn_total_quantity'];
                
                fputcsv($fp, $temp_arr[$key]);
            };
            fclose($fp);

            unlink("../upload/cfn_inventory.csv");
            unlink("../upload/inventory_listing.csv");
            unlink("../upload/reserved_inventory.csv");

        } else {
            echo "One of the files is not loaded, the report cannot be generated"."<br />";
        };
    } else {
        echo "No file selected <br />";
    };
};
?>
<!DOCTYPE html>
<html lang='ru'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    </head>
    <body>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="file[]" multiple=""/>
            <input type="submit" name="submit"/>
        </form>
        <br/>
    </body>
</html>