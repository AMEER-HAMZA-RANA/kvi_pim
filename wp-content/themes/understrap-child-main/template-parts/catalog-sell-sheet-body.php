<?php 
    // ini_set('max_execution_time', 0);
    // error_reporting(0);
    global $errorsCatalog;
    global $successCatalog;

    $sm = StateManager::GI();
    $all_retailers = $sm->get_retailers();
    $all_groups = $sm->pm->get_product_groups_taxonomy_of_brand();

        // echo "<pre>";
        // print_r($all_groups);
        // die();


    $catalog_id = isset($_GET['ct_id']) ? $_GET['ct_id'] : '';
    if($catalog_id !== ''){
        $product_ids = get_post_meta($catalog_id, 'product_ids', true);
        $retailer_id = get_post_meta($catalog_id, 'retailer_id', true);
        $output_type = get_post_meta($catalog_id, 'output_type', true);
        $price_books = get_post_meta($catalog_id, 'price_books', true);
        $customer_logo = get_post_meta($catalog_id, 'customer_logo', true);
        $sku_pricing = get_post_meta($catalog_id, 'sku_pricing', true);
        $sku_attributes = get_post_meta($catalog_id, 'sku_attributes', true);
        $catalog_name = get_post_meta($catalog_id, 'catalog_name', true);
        $category_ids = get_post_meta($catalog_id, 'category_ids', true);
        $category_ids = explode(",", $category_ids);
        
        // echo "<pre>";
        // print_r($all_groups);
        // die();

    }


?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>

        .overlayWait {
        position: relative;
        }
        .overlayWait:before {
        content: "";
        position: absolute;
        background: rgba(255, 255, 255, 0.8); /* translucent white background */
        backdrop-filter: blur(10px) saturate(120%); /* blur and saturate the background */
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        }
        .overlayWait:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 4px solid rgba(0, 0, 255, 0.2);
        border-top-color: rgba(0, 0, 255, 0.8);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        }

        @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
        }

        .catalogprice {
            background-color: #f8f8f8;
            padding: 0px 30px;
        }

        .catalogprice .headtxt h2 {
            color: #76251f;
            font-weight: bold;
            font-size: 30px;
        }

        .catalogprice .pagebody .form-group>select {
            width: 170px;
            height: 40px;
            margin: 10px 0px;
            text-align: center;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
            color: #4e4f4f;
        }

        .catalogprice .pagebody .book-sku-wrapp {
            display: flex;
            margin: 0 auto;
            align-items: baseline;
        }

        .catalogprice .pagebody .book-sku-wrapp p {
            margin-bottom: 0px;
            font-size: 18px;
            margin-left: 5px;
            font-style: italic;
        }

        .catalogprice .pagebody .table-product-data>table {
            width: 90%;
            color: #4e4f4f;
            border-collapse: separate;
            border-spacing: 0 1em;
        }

        .catalogprice .pagebody .table-product-data>table th {
            text-align: start;
            font-size: 16px;
            font-weight: 600;

            color: #4e4f4f;
        }

        .catalogprice .pagebody .table-product-data>table td {
            color: #4e4f4f;
            font-size: 17px;
            font-weight: 500;
        }

        .catalogprice .pagebody .table-product-data img {
            max-width: 50px;
        }
        .catalogprice .books-sku-details{
            padding-top:20px;
        }
        .catalogprice .books-sku-details .books-options input {
            margin-right: 10px;
        }


        .books-sku-details label{
            font-size: 18px;
        }

        .catalogprice .pagebody .books-sku-details .price-output {
            display: flex;
            justify-content: space-between;
            width: 80%;
        }

        .descriptiontxt .desc-item {
            display: flex;
            padding: 10px 0px;
        }

        .descriptiontxt .desc-item label {
            margin: 0px 10px;
            font-size: 18px;
        }

        .table-product-data .thumbnail-td {
            width: 100px;
            text-align: center;
        }

        .table-product-data {
            height: 300px;
            overflow-y: scroll;
        }
        .sku h3 {
            margin-bottom: 10px !important;
        }
        .table-product-data thead {
            margin-bottom: 10px;
        }

        .catalogprice .pagebody .books-sku-details .email-parent {
            display: flex;
            align-items: baseline;
        }

        .catalogprice .pagebody .books-sku-details .email-parent .email-txt {
            width: 100%;
            display: flex;
        }

        .catalogprice .pagebody .books-sku-details .email-parent .email-txt input {
            width: 100%;
            border: 1px solid #ae9e9e;
            background: transparent;
        }

        .catalogprice .pagebody .books-sku-details .email-parent label {
            font-size: 18px;
            margin: 0 10px;
        }

        .catalogprice .pagebody .mainsku {
            display: flex;
            border: 1px solid black;
            max-width: max-content;
            justify-content: center;
            padding: 3px;
            background: #fff;
        }

        .catalogprice .pagebody .mainsku .inputf input {
            border-style: none;
        }

        .catalogprice .pagebody .mainsku .inputf input:focus-visible {
            outline: none;
        }

        .catalogprice .pagebody .mainsku .sku-icon {
            color: #fff;
            background-color: brown;
            padding: 2px 5px;
        }

        .books-sku-details .books-sku-btns .file-btns {
            border: none;
            border-radius: 3px;
            min-width: 70px;
            text-align: center;
            height: 30px;
            color: #fff;
            font-weight: bold;
            font-size: 16px;

            margin: 5px 0;
        }

        .books-sku-details .books-sku-btns .file-btns i {
            font-size: 12px;
        }

        .books-sku-details .books-sku-btns .save-btn {
            background: #5dac3a;
        }

        .books-sku-details .books-sku-btns .export-btn {
            background: #ac3a3f;
        }

        .catalogprice .pagebody .books-sku-details  .customer-logo .customer-logo-wrapp {
            min-height: 100px;
        }

        .custom-filter a {
            font-size: 16px;
            font-weight: 600;
            color: #4e4f4f;
            text-decoration: none;
        }
        .catalogprice .pagebody  .books-sku-details  .books-options{
            display: flex;
            align-items: baseline;
        }

        .rowFlex{
            display: flex;
            gap: 70px;
        }

        .rowFlex .categories{
            border: 2px solid brown;
            width: 100%;
            padding: 10px 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .rowFlex .items{
            border: 2px solid brown;
            width: 100%;
            padding: 10px 20px;
        }

        .rowFlex .customerLogo{
            width: 100%;
        }

        .rowFlex .categories-options{
            margin-bottom: 10px;
        }

        .rowFlex .categories-options label{
            margin: 0 0 0 10px;
            font-size: 15px;
        }

        .rowFlex h3{
            margin: 10px 0;
        }

        .catalogprice .pagebody .form-group>select{
            margin-top: 0;
        }

        .headtxt p{
            margin-bottom: 10px;
            font-size: 15px;
        }

        .headtxt{
            margin-bottom: 20px;
        }


        .customer-logo input{
            outline: none;
            margin-bottom: 10px;
            width: 100%;
        }


        .customerLogo label{
        margin-left: 10px;
        margin-bottom: 10px;
        }

        .customer-logo .save-btn{
            border: none;
            border-radius: 3px;
            min-width: 70px;
            text-align: center;
            height: 30px;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            margin: 5px 0;
            background: #5dac3a;
        }

        .wrapper-product .catalogprice h3{
            margin-bottom: 5px;
        }

        .distribution{
            margin-bottom: 30px;
        }
        .wrapper-product input{
            padding: 4px 10px;
        }

        .fa-circle-info{
            font-size: 14px;
            margin-left: 5px;
        }
        .btn-arrow{
            background-color: #ac3a3f;
            color: white;
        }
        #customer_logo{
            padding: 0;
        }
        .highlight{
            background-color: #a52a2a1a;
        }
    </style>

<!-- parent div -->
<div class="catalogprice">
    <!-- main page body -->
    <?php
        if(count($errorsCatalog) !== 0){
            ?>
            <div class="alert alert-warning">
                <?php echo implode('<br>', $errorsCatalog); ?>
            </div>
            <?php
        }
        if(count($successCatalog) !== 0){
            ?>
            <div class="alert alert-primary">
                <?php echo implode('<br>', $successCatalog); ?>
            </div>
            <?php
        }
    ?>
    <form id="main_form"  action="" method="post" enctype="multipart/form-data" >
        <div class="pagebody">
            <div class="headtxt">
                <h2>Sell Sheet Catalog Export</h2>
                <p>
                Sell Sheet Catalog allows you to generate a single multi-sheet PDF files for specific products and/or pre-defined categories and associated Price Book(s).
                </p>
                <p>
                Because of the associated render time, your catalog will be sent to you via email with a link to download.
                </p>
            </div>
            <div class="rowFlex">
                <div class="form-group">
                    <h3>Select</h3>
                    <select onchange="fetch_data_ret_products(event)" name="brand" id="brand">
                        <option value="arrow" selected>Arrow</option>
                        <?php
                        foreach($all_retailers as $retailer){
                            ?>
                            <option value="<?php echo $retailer['id'] ?>" data-pid="<?php echo $retailer['pricebook_id'] ?>" data-pcode="<?php echo $retailer['price_code'] ?>" <?php echo $catalog_id !== '' && $retailer_id == $retailer['id'] ? "selected" : ""; ?> ><?php echo $retailer['name'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="cat_wrapper">
                    <h3>
                        Arrow Fastener Categories <b style="font-weight: 500;">Top 200</b>
                    </h3>
                    <div class="categories">

                        <?php
                        foreach($all_groups as $key => $group){
                            ?>
                            <div class="categories-options all_cats">
                                <input onChange="highlightProducts(event, value)" type="checkbox" id="sku-category-<?php echo $key; ?>" name="sku_category[]" value="<?php echo $group['id']; ?>" <?php echo $catalog_id !== '' && in_array($group['id'], $category_ids) ? "checked" : ""; ?> >
                                <label for="sku-category-<?php echo $key; ?>"><?php echo $group['name']; ?></label>
                                <!-- Button trigger modal -->
        
                                <i onClick="get_products('<?php echo $group['name']; ?>', '<?php echo $group['id']; ?>')" type="button" data-bs-toggle="modal" data-bs-target="#categoryModal" class="fa-solid fa-circle-info"></i>
                            </div>
                            <?php
                        }
                        ?>

                        <div class="categories-options">
                            <input onChange="get_all_cateProd(event, value)" type="checkbox" id="sku-category-all" name="sku_category-all" value="all" >
                            <label for="sku-category-all">All Categories</label>
                            <!-- Button trigger modal -->
                        </div>

                        <div class="categories-options">
                        </div>

                        <div class="categories-options all_cats_uncats">
                            <input onChange="highlightProducts(event, value)" type="checkbox" id="sku_category-uncategorized" name="sku_category-uncategorized" value="uncategorized" >
                            <label for="sku_category-uncategorized">Uncategorized</label>
                            <!-- Button trigger modal -->
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="categoryModalLabel">Manual Traders</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-product-data overlayWait">
                                            <table id="price-book-table">
                                                <thead class="mb-4">
                                                    <tr>
                                                        <th class="thumbnail-td text-center">Thumbnail</th>
                                                        <th>Name</th>
                                                        <th>SKU</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cat_products_catalog" >
                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <h5 style="text-align: right;" class="mt-2">Manage Category SKUs</h5>
                </div>
            </div>


            <div class="book-sku-wrapp mt-4">
                <div class="sku">
                    <h3>Select SKU(s)</h3>
                </div>
                <div class="sku-data">
                    <p>(0 Selected)</p>
                </div>
            </div>
            <div class="mainsku mb-4">
                <div class="inputf">
                    <input type="search" placeholder="search" onkeyup="filter_data_products(event, value)">
                </div>
                <div class="sku-icon">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
            <!-- table started -->

            <div class="table-product-data overlayWait">
                <table id="price-book-table">
                    <thead class="mb-4">
                        <tr>
                            <th></th>
                            <th class="thumbnail-td text-center">Thumbnail</th>
                            <th>Name</th>
                            <th>SKU</th>
                        </tr>
                    </thead>
                    <tbody id="all_products_catalog" >
          
                    </tbody>
                </table>
            </div>
            <!-- table closed -->

            <div class="books-sku-details" id="books-sku-details">
                <!-- price & output -->
                <div class="price-output my-4">
                    <div class="priceandoutput">
                        <div class="headtext">
                            <h3>Sell Sheet Catalogs</h3>
                        </div>
                        <div class="descriptiontxt">
                            <div class="desc-item">
                                <input type="checkbox" id="m1" name="pricebook[]" value="M1" <?php echo $catalog_id !== '' && strstr($price_books, 'M1') ? "checked" : ""; ?> >
                                <label for="m1">M1</label>
                            </div>
                            <div class="desc-item">
                                <input type="checkbox" id="d1" name="pricebook[]" value="D1" <?php echo $catalog_id !== '' && strstr($price_books, 'D1') ? "checked" : ""; ?>>
                                <label for="d1">D1</label>
                            </div>
                            <div class="desc-item">
                                <input type="checkbox" id="d2" name="pricebook[]" value="D2" <?php echo $catalog_id !== '' && strstr($price_books, 'D2') ? "checked" : ""; ?> >
                                <label for="d2">D2</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="distribution-sec">
            </div>
            <!-- last btns -->
            <div class="rowFlex mb-5">
                <div class="customerLogo">
                    <h3>Customer Logo</h3>
                    <div class="items">
                        <?php
                        $query = new WP_Query(array(
                            'post_type' => 'company_logos',
                            'orderby' => 'title',
                            'order' => 'ASC',
                            'post_status' => 'publish'
                        ));
                        
                        
                        while ($query->have_posts()) {
                            $thumbnail_url = '';
                            $query->the_post();
                            $post_id = get_the_ID();
                            $thumbnail_url = get_post_thumbnail_id($post_id);
                            $random = rand(10,150);
                            ?>
                            <div class="logo-options">
                                <input type="radio" id="customer_logo_<?php echo $random; ?>" name="customer_logo" value="<?php echo $thumbnail_url; ?>"
                                <?php echo $catalog_id !== '' && $customer_logo['ID'] == $thumbnail_url ? "checked" : ""; ?>
                                >
                                <label for="customer_logo_<?php echo $random; ?>"><?php echo get_post_meta($post_id, 'logo_title', true); ?></label>
                            </div>
                            <?php
                        }
                        
                        wp_reset_query();
                        ?>
                    </div>
                </div>
                <div class="customer-logo">
                    <div class="email-parent mt-4">
                        <h3>Add Logo</h3>
                    </div>
                    <div class="customer-logo-wrapp">
                        <input type="file" name="customer_logo" id="customer_logo">
                        <input type="text" name="logoName" placeholder="Logo Name" id="custom_logo_name" >
                        <button onClick=addImageAjax(event) type="submit" name="Add" class="file-btns save-btn">Add</button>
                    </div>
                </div>
            </div>
            <div class="distribution">
                <div class="headtext">
                    <h3>Save Configuration</h3>
                </div>
                <div class="email-parent my-3">
                    <div class="email-txt">
                        <input placeholder="Enter Name" type="text" name="catlog_name" value="<?php echo $catalog_id !== '' ? $catalog_name : ""; ?>" >
                    </div>
                </div>

            </div>
            <?php if($catalog_id !== '') : ?>
                <div class="distribution">
                    <div class="headtext">
                        <h3>Distribution</h3>
                    </div>
                    <div class="email-parent my-3">
                        <div class="email-txt">
                            <label for="c-email">eMail</label>
                            <input placeholder="(Comma Separate email addresses)" type="text" name="dist_email" >
                        </div>
                    </div>

                </div>
            <?php endif; ?>
            <!-- btns -->
            <div class="books-sku-btns">
                <div>
                    <button type="submit" name="save" class="file-btns save-btn">Save <i class="fa-solid fa-arrow-right"></i></button>
                </div>
                <?php if($catalog_id !== '') : ?>
                <div>
                    <button type="submit" name="export" class="file-btns export-btn">Export <i class="fa-solid fa-arrow-right"></i></button>
                </div>
                <?php endif; ?>
            </div>
            </div>
        </div>
    </form>

</div>

<script>
    window.catalogID = '<?php echo $catalog_id !== '' ? $catalog_id : ""; ?>';
    window.selectedProducts = `<?php echo $catalog_id !== '' ? strip_tags($product_ids) : ''; ?>`;
    window.selectedProductsArr = selectedProducts.split(",");
    window.selectedCategories = '<?php echo $catalog_id !== '' ? get_post_meta($catalog_id, "category_ids", true) : ""; ?>';
    window.selectedCategoriesArr = selectedCategories.split(",");

    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        data: { action: 'ajax_all_products_catalog', brand_id: '87' },
        success: function(data) {
           window.allCatalog = JSON.parse(data);
            if(catalogID !== '' && selectedCategoriesArr[0] !== ''){
                const ret_id = document.querySelector('#brand').value;
               jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: { action: 'fetch_category_data_for_brand', brand_id: '87', sku_category: selectedCategoriesArr, ret_id: ret_id },
                    success: function(data) {
                        //window.allCatalog = JSON.parse(data); 
                        let res = JSON.parse(data);
                        show_all_products(res);
                    }
                });
            }else{
                show_all_products();
            }
        }
    });

    // Retailer Products
    function fetch_data_ret_products(event) {
        jQuery('.sku-data').html(`<p>(0 Selected)</p>`);
        jQuery('.table-product-data').addClass('overlayWait');
        const selectElement  = event.target;
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        const pricebookId = selectedOption.getAttribute('data-pid');
        const priceCode   = selectedOption.getAttribute('data-pcode');
        const ret_id       = selectElement.value;
        let p_html = `<div class="desc-item">
                                <input type="checkbox" id="m1" name="pricebook[]" value="M1">
                                <label for="m1">M1</label>
                            </div>
                            <div class="desc-item">
                                <input type="checkbox" id="d1" name="pricebook[]" value="D1">
                                <label for="d1">D1</label>
                            </div>
                            <div class="desc-item">
                                <input type="checkbox" id="d2" name="pricebook[]" value="D2">
                                <label for="d2">D2</label>
                            </div>`;
        if(priceCode !== null){
             p_html = `<div class="desc-item">
                                    <input type="checkbox" id="${priceCode}" name="pricebook[]" value="${priceCode}">
                                    <label for="${priceCode}">${priceCode}</label>
                                </div>`;
        }
        jQuery('.priceandoutput .descriptiontxt').html(p_html);

        if(ret_id !== 'arrow'){
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: { action: 'fetch_data_ret_products', brand_id: '87', pricebookId: pricebookId, priceCode: priceCode, ret_id: ret_id},
                success: function(data) {
                    //window.allCatalog = JSON.parse(data); 
                    let res = JSON.parse(data);
                    var html = `<tr>
                                        <td>
                                            <input onchange="checkAllCheckbox(event)" type="checkbox" id="productsAll" name="productsAll">
                                        </td>
                                        <td class="text-center">
                                            <img src="<?php echo get_template_directory_uri();?>/images/box.png"
                                                alt="Thumbnail 1">
                                        </td>
                                        <td>
                                            <label for="productsAll">Select All</label>
                                        </td>
                                        <td></td>
                                    </tr>`;
    
                                    if (typeof res === 'object' && Object.keys(res).length > 0) {
                                        for (var key in res) {
                                            if (res.hasOwnProperty(key)) {
                                                var prod = allCatalog[res[key]];
                                                let post = `<tr>
                                                                <td>
                                                                    <input onchange="countCheckedCheckboxes()" id="books${prod['id']}" type="checkbox" name="products[]" value="${prod['id']}" ${ selectedProductsArr.includes(prod['id'].toString()) && "checked"  } >
                                                                </td>
                                                                <td class="text-center">
                                                                    <img src="${prod['thumb_url']}" alt="Thumbnail 1">
                                                                </td>
                                                                <td>
                                                                    <label for="books${prod['id']}">${prod['title']}</label>
                                                                </td>
                                                                <td>${prod['sku']}</td>
                                                            </tr>`;
                                                        
                                                        html += post;
                                            }
                                        }
                                    }
    
    
                                    jQuery('#all_products_catalog').html(html);
                                    jQuery('.overlayWait').removeClass('overlayWait');
    
                }
            });
        }else{
            show_all_products();
        }
    }

    // Search Filter
    function filter_data_products(event, value){
      //  event.preventDefault();
      jQuery('.table-product-data').addClass('overlayWait');
        var html = `<tr>
                        <td>
                            <input onchange="checkAllCheckbox(event)" type="checkbox" id="productsAll" name="productsAll">
                        </td>
                        <td class="text-center">
                            <img src="<?php echo get_template_directory_uri();?>/images/box.png"
                                alt="Thumbnail 1">
                        </td>
                        <td>
                            <label for="productsAll">Select All</label>
                        </td>
                        <td></td>
                    </tr>`;


                        if (typeof allCatalog === 'object' && Object.keys(allCatalog).length > 0) {
                            for (var key in allCatalog) {
                                if (allCatalog.hasOwnProperty(key)) {
                                    var prod = allCatalog[key];
                                    if (prod['title'].toLowerCase().includes(value.toLowerCase())) {
                                        let post = `<tr>
                                                        <td>
                                                            <input onchange="countCheckedCheckboxes()" id="books${prod['id']}" type="checkbox" name="products[]" value="${prod['id']}" ${ selectedProductsArr.includes(prod['id'].toString()) && "checked"  } >
                                                        </td>
                                                        <td class="text-center">
                                                            <img src="${prod['thumb_url']}"  alt="Thumbnail 1">
                                                        </td>
                                                        <td>
                                                            <label for="books${prod['id']}">${prod['title']}</label>
                                                        </td>
                                                        <td>${prod['sku']}</td>
                                                    </tr>`;
                                                
                                                html += post;
                                    }
                                }
                            }
                        }


                        jQuery('#all_products_catalog').html(html);
                        jQuery('.overlayWait').removeClass('overlayWait');
    }

    function countCheckedCheckboxes() {
        // select all checkboxes on the page
        const checkboxes = document.querySelectorAll('#all_products_catalog input[type="checkbox"]');
        
        // count the number of checkboxes that are checked
        let checkedCount = 0;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
            checkedCount++;
            }
        });
        
        // display the count in the console or on the page
        jQuery('.sku-data').html(`<p>(${checkedCount} Selected)</p>`);
    }

    function checkAllCheckbox(event){
        event.preventDefault();
        
        var unchecked = jQuery("#productsAll").is(":checked");
        if (unchecked) {
        // If there are unchecked checkboxes, check them all
        jQuery("#all_products_catalog input[type='checkbox']").prop('checked', true);
        jQuery("label[for='productsAll']").html('Unselect All');
        } else {
        // If all checkboxes are checked, uncheck them all
        jQuery("#all_products_catalog input[type='checkbox']").prop('checked', false);
        jQuery("label[for='productsAll']").html('Select All');
        }
        var count = jQuery("#all_products_catalog input[type='checkbox']").filter(':checked').length;
        jQuery('.sku-data').html(`<p>(${count} Selected)</p>`);

    }

    function addImageAjax(event){
        event.preventDefault();
        let formData = new FormData( jQuery('#main_form')[0] );
        formData.append('action', 'add_image_to_catalogMedia');
            
        // Send the image data to PHP via AJAX
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if(data !== 'error'){
                    data = JSON.parse(data);
                    let random_no = Math.floor(Math.random(100));
                    let logo_html = `<div class="logo-options">
                            <input type="radio" id="customer_logo_${random_no}" name="customer_logo" value="${data.url}">
                            <label for="customer_logo_${random_no}">${data.name}</label>
                        </div>`;

                        jQuery('.rowFlex .items').append(logo_html);
                }
            }
        })
    
    }

    function highlightProducts(event, value){
        jQuery('.table-product-data').addClass('overlayWait');
        const checkboxes = document.querySelectorAll('input[name="sku_category[]"]');
        const checkedValues = [];
        let uncat = '';


        checkboxes.forEach((checkbox) => {
            if( value === 'uncategorized' ){
                checkedValues.push(checkbox.value);
                uncat = "uncat";
                jQuery("#sku-category-all:checkbox").prop('checked', false);
                jQuery(".all_cats input:checkbox").prop('checked', false);
            }else{
                if (checkbox.checked) {
                    checkedValues.push(checkbox.value);
                }
            }
        });
        const ret_id = document.querySelector('#brand').value;

        let data = '';
        if(ret_id == 'arrow'){
            data = { action: 'fetch_category_data_for_brand', brand_id: '87', sku_category: checkedValues, ret_id: ret_id, uncat: uncat }
        }else{
            data = { action: 'fetch_data_ret_products', brand_id: '87', sku_category: checkedValues, ret_id: ret_id, uncat: uncat }
        }


        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            data: data,
            success: function(data) {
                //window.allCatalog = JSON.parse(data); 
                let res = JSON.parse(data);
                if(checkedValues.length > 0){
                    show_all_products(res);
                }else{
                    show_all_products();
                }


            }
        });
    }

    function get_products(cat_name, cat_id){
        const ret_id       = document.querySelector('#brand').value;
        jQuery("#categoryModalLabel").html(jQuery('#brand :selected').text() + ' - ' + cat_name);

        jQuery('.modal-body .table-product-data').addClass('overlayWait');
        let data = '';
        if(ret_id == 'arrow'){
            data = { action: 'fetch_category_data_for_brand', brand_id: '87', sku_category: [cat_id], ret_id: ret_id }
        }else{
            data = { action: 'fetch_data_ret_products', brand_id: '87', sku_category: [cat_id], ret_id: ret_id }
        }



        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            data: data,
            success: function(data) {
                //window.allCatalog = JSON.parse(data); 
                let res = JSON.parse(data);
                var html = ``;

                                if (typeof res === 'object' && Object.keys(res).length > 0) {
                                    for (var key in res) {
                                        if (res.hasOwnProperty(key)) {
                                            var prod = allCatalog[res[key]];
                                            let post = `<tr>
                                                            <td class="text-center">
                                                                <img src="${prod['thumb_url']}" alt="Thumbnail 1">
                                                            </td>
                                                            <td>
                                                                <label for="books${prod['id']}">${prod['title']}</label>
                                                            </td>
                                                            <td>${prod['sku']}</td>
                                                        </tr>`;
                                                    
                                                    html += post;
                                        }
                                    }
                                }


                                jQuery('#cat_products_catalog').html(html);
                                jQuery('.modal-body .overlayWait').removeClass('overlayWait');
                                

            }
        });

        
    }

    function show_all_products(filterdProd = false){
        var html = `<tr>
            <td>
                <input onchange="checkAllCheckbox(event)" type="checkbox" id="productsAll" name="productsAll" ${filterdProd === false || filterdProd === 'all' ? "checked" : ''} >
            </td>
            <td class="text-center">
                <img src="<?php echo get_template_directory_uri();?>/images/box.png"
                    alt="Thumbnail 1">
            </td>
            <td>
                <label for="productsAll">Select All</label>
            </td>
            <td></td>
        </tr>`;


        if (typeof allCatalog === 'object' && Object.keys(allCatalog).length > 0) {
            let otherArray = filterdProd === false || filterdProd === 'all' ? allCatalog : filterdProd;
            for (var key in otherArray) {
                key = filterdProd === false || filterdProd === 'all' ? key : otherArray[key];
                if (allCatalog.hasOwnProperty(key)) {
                    var prod = allCatalog[key];
                    let post = `<tr>
                                    <td>
                                        <input onchange="countCheckedCheckboxes()" id="books${prod['id']}" type="checkbox" name="products[]" value="${prod['id']}" ${ filterdProd !== false && "checked"  } >
                                    </td>
                                    <td class="text-center">
                                        <img src="${prod['thumb_url']}"  alt="Thumbnail 1">
                                    </td>
                                    <td>
                                        <label for="books${prod['id']}">${prod['title']}</label>
                                    </td>
                                    <td>${prod['sku']}</td>
                                </tr>`;
                            
                            html += post;
                }
            }
        }


        jQuery('#all_products_catalog').html(html);
        jQuery('.overlayWait').removeClass('overlayWait');
        countCheckedCheckboxes();
    }

    function get_all_cateProd(event, value){
        if(jQuery(event.target).prop('checked')){
            jQuery(".all_cats input:checkbox").prop('checked', true);
            jQuery(".all_cats_uncats input:checkbox").prop('checked', false);
        }else{
            jQuery(".all_cats input:checkbox").prop('checked', false);
        }
        highlightProducts(event, value);
    }

    //We need to bind click handler as well
    //as FF sets button checked after mousedown, but before click
    jQuery('input:radio').bind('click mousedown', (function() {
        //Capture radio button status within its handler scope,
        //so we do not use any global vars and every radio button keeps its own status.
        //This required to uncheck them later.
        //We need to store status separately as browser updates checked status before click handler called,
        //so radio button will always be checked.
        var isChecked;

        return function(event) {

            if(event.type == 'click') {

                if(isChecked) {
                    //Uncheck and update status
                    isChecked = this.checked = false;
                } else {
                    //Update status
                    //Browser will check the button by itself
                    isChecked = true;

                    //Do something else if radio button selected
                    /*
                    if(this.value == 'somevalue') {
                        doSomething();
                    } else {
                        doSomethingElse();
                    }
                    */
                }
        } else {
            //Get the right status before browser sets it
            //We need to use onmousedown event here, as it is the only cross-browser compatible event for radio buttons
            isChecked = this.checked;
        }
    }})());


</script>

