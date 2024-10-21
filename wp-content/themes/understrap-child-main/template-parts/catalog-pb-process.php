<?php

global $errorsCatalog;
global $successCatalog;
$errorsCatalog = [];
$successCatalog = [];
if(isset($_POST['export']) || isset($_POST['save'])){
    ini_set('max_execution_time', 0);
    error_reporting(0);
    

    $brand = $_POST['brand'];
    $product_ids = isset( $_POST['products']) && !empty( $_POST['products']) ?  $_POST['products'] : [];
    $sku_category = isset( $_POST['sku_category']) && !empty( $_POST['sku_category']) ?  $_POST['sku_category'] : [];
    $retailer_id = isset( $_POST['brand']) && $_POST['brand'] !== '' ?  $_POST['brand'] : '';
    $file_size = isset( $_POST['attributes_sku']) ?  $_POST['attributes_sku'] : '';
    $pricebooks = isset( $_POST['pricebook']) && !empty( $_POST['pricebook']) ?  $_POST['pricebook'] : [];
    $distribution = isset( $_POST['distribution']) && !empty( $_POST['distribution']) ?  $_POST['distribution'] : '';
    $dist_email = isset( $_POST['dist_email']) && !empty( $_POST['dist_email']) ?  $_POST['dist_email'] : '';
    $output = isset( $_POST['output']) ? $_POST['output'] : '';
    $language = isset( $_POST['language']) ? $_POST['language'] : 'en';

    // echo "<pre>";
    // print_r($_POST);
    // die();

    function get_product_info($id, $pricebooks, $p_thumbnail_url, $file_size, $product, $price_books ){
        $product_info = [];
        $product_name = $product['product_name'];
        $sku = $product['sku'];

        if($file_size == 'marketing'){

          $language = isset( $_POST['language']) ? $_POST['language'] : 'en';
          // SKU Marketting
          $jt_online_title = $product['jt_online_title_'.$language];
          $online_description = $product['online_description_'.$language];
          $marketing_copy = $product['marketing_copy_'.$language];
          $bullet_1 = $product['bullet_1_'.$language];
          $bullet_2 = $product['bullet_2_'.$language];
          $bullet_3 = $product['bullet_3_'.$language];
          $bullet_4 = $product['bullet_4_'.$language];
          $bullet_5 = $product['bullet_5_'.$language];
          $bullet_6 = $product['bullet_6_'.$language];
          $bullet_7 = $product['bullet_7_'.$language];
          $bullet_8 = $product['bullet_8_'.$language];
          $compatible_fasteners = $product['compatible_fasteners'];
          $compatible_tools = $product['tool_size'];
          $handle_type = $product['handle_type'];
          $material_type = $product['material_type'];
          $primary_trade = $product['primary_trade'];

          array_push(
              $product_info,
              $sku,
              $jt_online_title,
              $online_description,
              $marketing_copy,
              $bullet_1,
              $bullet_2,
              $bullet_3,
              $bullet_4,
              $bullet_5,
              $bullet_6,
              $bullet_7,
              $bullet_8,
              $compatible_fasteners,
              $compatible_tools,
              $handle_type,
              $material_type,
              $primary_trade,
          );

          if(!empty($price_books)){
              foreach($pricebooks as $bookId){
                  $price = '';
                  foreach($price_books as $price_book){
                      if(("M1" == $bookId) && ("M1" == $price_book["book_code"] )){
                      $price = $price_book["price"];
                      array_push(
                          $product_info,
                          $price
                      );
                      break;
                      }
                      else if(("D1" == $bookId) && ("D1" == $price_book["book_code"] )){
                      $price = $price_book["price"];
                      array_push(
                          $product_info,
                          $price
                      );
                      break;
                      }
                      else if(("D2" == $bookId) && ("D2" == $price_book["book_code"] )){
                      $price = $price_book["price"];
                      array_push(
                          $product_info,
                          $price
                      );
                      break;
                      }
                  }
              }
          }else{
              array_push(
                  $product_info,
                  ''
              );
          }

          array_push(
              $product_info,
              $product_name,
              $p_thumbnail_url
          );

        }else{
          $pack_quantity = $product['pack_quantity'];
          $pack_upc_12_digit_unique = $product['pack_upc_12_digit_unique'];
          $ean_13_digit_unique = $product['ean_13_digit_unique'];
          $pack_cube_quantity = $product['pack_cube_quantity'];
          $item_weight = $product['item_weight'];
          $item_height = $product['item_height'];
          $item_depth = $product['item_depth'];
          $item_width = $product['item_width'];
          $inner_quantity = $product['inner_quantity'];
          $inner_upc = $product['inner_upc'];
          $inner_weight = $product['inner_weight'];
          $inner_height = $product['inner_height'];
          $inner_width = $product['inner_width'];
          $inner_depth = $product['inner_depth'];
          $inner_cube_quantity = $product['inner_cube_quantity'];
          $master_weight = $product['master_weight'];
          $master_height = $product['master_height'];
          $master_width = $product['master_width'];
          $master_depth = $product['master_depth'];
          $master_cube = $product['master_cube'];
          $unit_cube = $product['unit_cube'];
          $skid_quantity = $product['skid_quantity'];
          $skid_layers = $product['skid_layers'];
          $packaging_zone = $product['packaging_zone'];
          
          array_push(
              $product_info,
              $sku
          );
          

          if($file_size  == 'large'){
          array_push(
              $product_info,
              $packaging_zone,
              $pack_upc_12_digit_uniq,
              $item_width,
              $item_height,
              $pack_cube_quantity,
              $item_weight,
              $item_depth,
              $pack_quantity,
              $inner_quantity,
              $inner_width,
              $inner_height,
              $inner_cube_quantity,
              $inner_weight,
              $inner_depth,
              $inner_upc,
              $master_width,
              $master_height,
              $master_weight,
              $master_depth,
              $master_cube,
              $unit_cube,
              $skid_quantity,
              $skid_layers,
              $ean_13_digit_unique
          );
          }

          
          if(!empty($price_books)){
          foreach($pricebooks as $bookId){
              $price = '';
              foreach($price_books as $price_book){
                  if(("M1" == $bookId) && ("M1" == $price_book["book_code"] )){
                  $price = $price_book["price"];
                  array_push(
                      $product_info,
                      $price
                  );
                  break;
                  }
                  else if(("D1" == $bookId) && ("D1" == $price_book["book_code"] )){
                  $price = $price_book["price"];
                  array_push(
                      $product_info,
                      $price
                  );
                  break;
                  }
                  else if(("D2" == $bookId) && ("D2" == $price_book["book_code"] )){
                  $price = $price_book["price"];
                  array_push(
                      $product_info,
                      $price
                  );
                  break;
                  }
              }
          }
          }else{
          array_push(
              $product_info,
              ''
          );
          }

          array_push(
              $product_info,
              $product_name,
              $p_thumbnail_url
          );

        }
        
        return $product_info;
    }

    if(isset($_POST['export'])){
        $catalog_id = isset($_GET['ct_id']) ? $_GET['ct_id'] : '';
        if($catalog_id !== ''){
            $catlog_name = isset($_POST['catlog_name']) && $_POST['catlog_name'] !== '' ? $_POST['catlog_name'] : '';
            $dist_email =  isset($_POST['dist_email']) && $_POST['dist_email'] !== '' ? $_POST['dist_email'] : '';
            if($dist_email == ''){
                $errorsCatalog[] = 'Please Provide the Valid Email Address!';
                // header("Refresh:0");
                // die("<h1 style='color:red;'>Please Provide the Valid Email Address!</h1>");
            }else{
                $product_ids =  implode(",", $product_ids);
                $category_ids =  implode(",", $sku_category);
                $pricebooksS =  implode(",", $pricebooks);
                $title =  $catlog_name !== '' ? $catlog_name : $output . "-" . $dist_email . "-" . date('Y-m-d') . "-" . count($product_ids);
                $post_args = array(
                    'post_title' =>  $title, // use the 'Title' field value for the post title
                    'post_type' => 'catalog_queue', // use the 'custom_post' post type
                    'post_status' => 'publish' // set the post status to 'publish'
                );
      
                // Create the post
                $post_id = wp_insert_post( $post_args );
    
                if(false !== $post_id){
                    $sheet_type = isset($_GET['ctype']) && $_GET['ctype'] == 'pb' ? 'pricebook' : 'sellsheet';
                    update_post_meta( $post_id, 'catalog', $catalog_id );
                    update_post_meta( $post_id, 'queue_status', 'In Process - Local' );
                    update_post_meta( $post_id, 'email_list', $dist_email );
        
                }
                
                // header("Refresh:0");
                $successCatalog[] = 'Queue is started, and you will be notified, once it will be completed!';
                // die("<h1 style='color:green;'>Queue is started, and you will be notified, once it will be completed!</h1>");
            }
        }


    }
    else if(isset($_POST['save'])){
        $catlog_name = isset($_POST['catlog_name']) && $_POST['catlog_name'] !== '' ? $_POST['catlog_name'] : '';
        $product_ids =  implode(",", $product_ids);
        $category_ids =  implode(",", $sku_category);
        $pricebooksS =  implode(",", $pricebooks);
        $title =  $catlog_name !== '' ? $catlog_name : $output . "-" . $dist_email . "-" . date('Y-m-d') . "-" . count($product_ids);
        $post_args = array(
            'post_title' =>  $title, // use the 'Title' field value for the post title
            'post_type' => 'catalog', // use the 'custom_post' post type
            'post_status' => 'publish' // set the post status to 'publish'
        );
        $catalog_id = isset($_GET['ct_id']) ? $_GET['ct_id'] : '';
        if($catalog_id !== ''){
            $post_id = $catalog_id;
            // Create an array of post data to update the post title
            $update_post = array(
                'ID' => $post_id,
                'post_title' =>  $title
            );

            // Update the post with the new title
            wp_update_post($update_post);
        }else{
            // Create the post
            $post_id = wp_insert_post( $post_args );
        }      

        if(false !== $post_id){
            $sheet_type = isset($_GET['ctype']) && $_GET['ctype'] == 'pb' ? 'pricebook' : 'sellsheet';
            update_post_meta( $post_id, 'catalog_type', $sheet_type );
            update_post_meta( $post_id, 'product_ids', $product_ids );
            update_post_meta( $post_id, 'category_ids', $category_ids );
            update_post_meta( $post_id, 'retailer_id', $retailer_id );
            update_post_meta( $post_id, 'brand', '87' );
            update_post_meta( $post_id, 'email_list', $dist_email );
            update_post_meta( $post_id, 'price_books', $pricebooksS );
            update_post_meta( $post_id, 'catalog_name', $catlog_name );

            if($file_size == 'small'){
                update_post_meta( $post_id, 'sku_pricing', true );
                update_post_meta( $post_id, 'sku_attributes', false );
                update_post_meta( $post_id, 'sku_marketing', false );
            }
            if($file_size == 'large'){
                update_post_meta( $post_id, 'sku_attributes', true );
                update_post_meta( $post_id, 'sku_pricing', false );
                update_post_meta( $post_id, 'sku_marketing', false );
            }
            if($file_size == 'marketing'){
                update_post_meta( $post_id, 'sku_marketing', true );
                update_post_meta( $post_id, 'sku_attributes', false );
                update_post_meta( $post_id, 'sku_pricing', false );
                update_post_meta( $post_id, 'language', $language );
            }
            if($output == 'csv'){
                update_post_meta( $post_id, 'output_type', 'CSV' );
            }
            if($output == 'pdf'){
                update_post_meta( $post_id, 'output_type', 'PDF' );
            }

            if (isset($_POST['customer_logo']) && $_POST['customer_logo'] !== '') {
            
            
                // Insert the attachment
                $attachment_id = $_POST['customer_logo'];
            
                // Set the attachment as the featured image of the post
                update_post_meta($post_id, 'customer_logo', $attachment_id);

                $attachment_data = wp_get_attachment_metadata($attachment_id);
               
                $file_name = 'catalog-logos/' . $title . '-' . $post_id;

                // Get the image URL using the image ID
                $image_url = wp_get_attachment_url($attachment_id);

                // Get the image path from the image URL
                $image_path = parse_url($image_url, PHP_URL_PATH);

                // Get the absolute path to the image file on the server
                $target_file = $_SERVER['DOCUMENT_ROOT'] . $image_path;


                $sm = StateManager::GI();
                $file_uploaded = $sm->mam->put_queue_object_in_bucket($target_file, $file_name);
                if($file_uploaded){
                    update_post_meta( $post_id, 'customer_logo_aws', $file_name );
                }
            }
            
        }

        $successCatalog[] = 'Saved successfully!';
    }

}
