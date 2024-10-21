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

    //     echo "<pre>";
    // print_r($_POST);
    // die();

    if(isset($_POST['export'])){

        $catalog_id = isset($_GET['ct_id']) ? $_GET['ct_id'] : '';
        if($catalog_id !== ''){
            $catlog_name = isset($_POST['catlog_name']) && $_POST['catlog_name'] !== '' ? $_POST['catlog_name'] : '';
            $dist_email =  isset($_POST['dist_email']) && $_POST['dist_email'] !== '' ? $_POST['dist_email'] : '';
            if($dist_email == ''){
                $errorsCatalog[] = 'Please Provide the Valid Email Address!';
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
                $successCatalog[] = 'Queue is started, and you will be notified, once it will be completed!';
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
            }
            if($file_size == 'large'){
                update_post_meta( $post_id, 'sku_attributes', true );
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



