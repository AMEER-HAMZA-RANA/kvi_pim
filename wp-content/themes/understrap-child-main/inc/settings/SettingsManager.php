<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
// require_once( get_template_directory() ."/inc/settings/QueueManager.php");
require_once( get_theme_file_path() ."/inc/filters/ProductsFilters.php");
require_once( get_theme_file_path() ."/inc/filters/MAMFilters.php");


class SettingsManager {
	private static $instance = null;

	public $media_url_prefix;
		// private $field_groups_data;

	public	$current_product_completion_weightage;

	public $current_user_roles_and_permissions;

	public $permissionsEnum = [
		'4' => 'VIEW',
		'5' => 'EDIT',
		'0' => 'HIDE'
	];

	public $products;

	public $seller_apps_names;

	public $products_filters;
	public $mam_filters;

	// Private constructor to prevent direct object creation
	private function __construct($load_ajax = true) {
		if($load_ajax) {
			add_action( "wp_ajax_delete_media", array($this, "ajax_delete_media"));
			add_action( "wp_ajax_refresh_media_html", array($this, "ajax_refresh_media_html"));
			add_action('wp_ajax_get_product_information', array($this, "ajax_get_product_information"));
			add_action('wp_ajax_get_latest_revisions', array($this, "ajax_get_latest_revisions"));
			add_action('wp_ajax_restore_revision', array($this, "ajax_restore_revision"));
			add_action('wp_ajax_save_dynamic_table_data', array($this, "ajax_save_dynamic_table_data"));

			// prod index
add_action('wp_ajax_fetch_product_items', [$this, 'fetch_product_items']);

// error_log( "___________________________________abhi9" );


// prod fav
// add_action('wp_ajax_prod_toggle_favorite_item', [$this, 'prod_toggle_favorite_item']);
// add_action('wp_ajax_nopriv_prod_toggle_favorite_item', [$this, 'prod_toggle_favorite_item']);

// media index
add_action('wp_ajax_fetch_media_items', [$this, 'fetch_media_items']);

// media & prod fav
add_action('wp_ajax_toggle_favorite_item', [$this,'toggle_favorite_item']);

// single asset data update
add_action('wp_ajax_update_asset_data', [$this,'update_asset_data']);


// get_related_media_items
add_action('wp_ajax_get_related_media_items', [$this,'get_related_media_items']);

// get_all_favs
add_action('wp_ajax_get_all_favs', [$this,'get_all_favs']);

// mark_product_complete
add_action('wp_ajax_mark_product_complete', [$this,'mark_product_complete']);

// assign_product_parent
add_action('wp_ajax_assign_product_parent', [$this,'assign_product_parent']);

// remove_parent
add_action('wp_ajax_remove_parent', [$this,'remove_parent']);

// sync field
add_action('wp_ajax_sync_field_to_syndication', [$this,'sync_field_to_syndication']);


// load permissions
add_action('wp_ajax_loadPermissions', [$this,'loadPermissions']);

// submit Permissions Data
add_action('wp_ajax_submitPermissionsData', [$this,'submitPermissionsData']);

// get sku search items (on new sku search page)
add_action('wp_ajax_get_sku_search_items', [$this,'get_sku_search_items']);

// get all products
add_action('wp_ajax_get_all_products', [$this,'get_all_products']);

// get specific revisions
add_action('wp_ajax_get_specific_revision', [$this,'get_specific_revision']);

}

$this->media_url_prefix = "https://d31il057o05hvr.cloudfront.net/raw-folder/";

		// $this->send_field_sync_request_to_syndication();


		// new QueueManager();

$this->load_seller_apps();

$this->get_current_user_permissions();


// $this->dump($this->current_user_roles_and_permissions);

// $this->dump($this->permissionsEnum[$this->find_permission_for_current_item('fields', 149)]);
	}


	public static function GI()
	{
		if(self::$instance == null)
		{
			self::$instance = new SettingsManager();
		}
		return self::$instance;
	}

	public function dd($str = null) {
			$str = $str ?? "__ DONE __";
			echo "<pre>";
			var_dump($str);
		echo "</pre>";
			exit;
	}

	public function dump($str = null) {
		$str = $str ?? "__ DONE __";
		echo "<pre>";
		var_dump($str);
		echo "</pre>";
}



	// function get_current_asset_id() {
	// 	// Parse the URL to get the path
	// 	// $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	// 	// // Split the path by '/' and get the last segment
	// 	// $segments = explode('/', rtrim($path, '/'));
	// 	// $id = end($segments);

	// 	$id = get_query_var('asset_id');

	// 	return $id;
	// }

	// Helper function to format file size
// function formatSizeUnits($bytes) {
// 	if ($bytes >= 1073741824) {
// 			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
// 	} elseif ($bytes >= 1048576) {
// 			$bytes = number_format($bytes / 1048576, 2) . ' MB';
// 	} elseif ($bytes >= 1024) {
// 			$bytes = number_format($bytes / 1024, 2) . ' KB';
// 	} elseif ($bytes > 1) {
// 			$bytes = $bytes . ' bytes';
// 	} elseif ($bytes == 1) {
// 			$bytes = $bytes . ' byte';
// 	} else {
// 			$bytes = '0 bytes';
// 	}

// 	return $bytes;
// }

// function filterByProductId($array1, $array2) {
// 	// Create an array of IDs from array2 for comparison
// 	$ids = array_column($array2, 'product_id');

// 	// Filter array1 based on matching product_id in array2
// 	$filtered = array_filter($array1, function($item) use ($ids) {
// 			return in_array($item['id'], $ids);
// 	});

// 	return $filtered;
// }

public function get_all_products() {
	check_ajax_referer('get_all_products');

	$products = $this->get_all_rows_and_cols_from_table('pim_products');

	wp_send_json_success([
		'products' => $products
	]);
}

public function get_current_user_permissions() {
	global $wpdb;

// Step 1: Get the current user
$current_user = wp_get_current_user();

// Step 2: Get the user's role(s)
$user_roles = $current_user->roles;

// Step 3: Initialize an array to store permissions
$user_permissions = [];

// Step 4: Loop through each role and fetch permissions from pim_roles_permissions table
foreach ($user_roles as $role_key) {
    $permissions_data = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM pim_roles_permissions WHERE role_key = %s",
            $role_key
        )
    );

    // Step 5: Organize the permission data for each role
    $role_permissions = [];
    foreach ($permissions_data as $permission_row) {
        $role_permissions[] = [
            'item_id' => $permission_row->item_id,
            'item_type' => $permission_row->item_type,
            'permission' => $permission_row->permission,
        ];
    }

    // Step 6: Add the role's permissions to the final array
    $user_permissions[$role_key] = $role_permissions;
}

$this->current_user_roles_and_permissions = $user_permissions;
// At this point, $user_permissions contains an array of permissions for each role

}

public function find_permission_for_current_item($item_type, $item_id) {
	// Get the current user's permissions
	$user_permissions = $this->get_current_user_permissions();

	// Loop through all roles' permissions
	foreach ($this->current_user_roles_and_permissions as $role_key => $permissions) {
			foreach ($permissions as $permission) {
					// Check if item_type and item_id match the current item's details
					if ($permission['item_type'] === $item_type && (int)$permission['item_id'] === (int)$item_id) {
							// Return the permission value (or true if permission exists)
							return $permission['permission'];
					}
			}
	}

	// Return false if no matching permission is found
	return false;
}


public function submitPermissionsData() {
	check_ajax_referer('submitPermissionsData');

	$data = json_decode(stripslashes($_POST['data']));
	$role_key = isset($_POST['role_key']) ? sanitize_text_field($_POST['role_key']) : '';
	$item_type = isset($_POST['item_type']) ? sanitize_text_field($_POST['item_type']) : '';

	global $wpdb;

	foreach($data as $i => $item) {
		$id = $item->id;
		$permission = $item->permission;

		$data = [
			'role_key' => $role_key,
			'item_id' => $id,
			'item_type' => $item_type,
			'permission' => $permission
		];


		$records = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_roles_permissions WHERE item_id = %d", $id));
		// $result = null;

		if(sizeof($records) > 0) {
			$where = ['item_id' => $id];
			$result = $wpdb->update('pim_roles_permissions', $data, $where);
		} else {
			$result = $wpdb->insert('pim_roles_permissions', $data);
		}

	}

	// wp_send_json_success(["data_saved" => $result]);
	wp_send_json_success();

}

public function loadPermissions() {
	check_ajax_referer('loadPermissions');

	$role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';
	$item_type = isset($_POST['itemType']) ? sanitize_text_field($_POST['itemType']) : '';

	if(!$role || !$item_type) {
		wp_send_json_error(["message" => "Role or item type is missing."]);
	}

	$allowed_item_types = ['fields', 'tables', 'pages'];

	if(!in_array($item_type, $allowed_item_types)) {
		wp_send_json_error(["message" => "this item type is NOT allowed."]);
	}

	global $wpdb;

	$results_item_type = null;

	if($item_type == 'fields') {

		$results = $wpdb->get_results("SELECT id, table_meta_id, field_name, title FROM pim_field_metas");
		$results_item_type = 'fields';

	} elseif($item_type == 'tables') {

		$results = $wpdb->get_results("SELECT id, table_name, title FROM pim_table_metas");
		$results_item_type = 'tables';

	} elseif($item_type == 'pages') {

// Define specific pages manually
// $pages_to_protect = [
// 'Brands',
// 'SKUs',
// 'MAM',
// 'Favorites',
// 'Retail Channels',
// 'Sales Territories',
// 'User Guide Wiki',
// 'Admin Settings',
// 'Report Issue/Request',
// 'Catalogue',
// 'Import',
// 'Permissions'
// ];

// $defined_pages = array_map(function($pg, $i) {
// 	return ['id' => $i + 1, 'title' => $pg, 'slug' => $this->create_slug($pg)];
// }, $pages_to_protect);

// $defined_pages = array_map(function($pg, $i) {
// 	return (object) ['id' => $i + 1, 'title' => $pg, 'slug' => $this->create_slug($pg)];
// }, $pages_to_protect, array_keys($pages_to_protect));


		// $defined_pages = [
		// 	['id' => 1, 'title' => 'Brands', 'slug' => 'brands'],
		// 	['id' => 2, 'title' => 'SKUs', 'slug' => 'skus'],
		// 	['id' => 3, 'title' => 'MAM', 'slug' => 'mam'],
		// 	['id' => 4, 'title' => 'Favorites', 'slug' => 'favorites'],
		// 	['id' => 3, 'title' => 'Retail Channels', 'slug' => 'retail-channels'],
		// 	['id' => 3, 'title' => 'Sales Territories', 'slug' => 'sales-territories'],
		// 	['id' => 3, 'title' => 'User Guide Wiki', 'slug' => 'user-guide-wiki'],
		// 	['id' => 3, 'title' => 'Admin Settings', 'slug' => 'admin-settings'],
		// 	['id' => 3, 'title' => 'Report Issue/Request', 'slug' => 'report-issue-request'],
		// 	['id' => 3, 'title' => 'Catalogue', 'slug' => 'catalogue'],
		// 	['id' => 3, 'title' => 'Import', 'slug' => 'import'],
		// ];

		// $defined_pages = array_map(function($item) {
		// 	return (object) $item;
		// }, $defined_pages);

		$results = $this->get_all_rows_and_cols_from_table('pim_pages');

		$results_item_type = 'pages';

	}

	$roles_permissions_data = $this->get_all_rows_and_cols_from_table('pim_roles_permissions');


	// $index = 0;
	// load current permissions
	foreach($roles_permissions_data as $i => $rp_item) {
		foreach($results as $j => &$r_item) {

			// if($index== 0) {
				// $index++;
				// }

				if($rp_item->item_id == $r_item->id && $rp_item->item_type == $results_item_type && $rp_item->role_key == $role) {
					$r_item->permission = $rp_item->permission;

					if($item_type == 'fields') {
						$field_table = $this->get_full_row_from_table('pim_table_metas', $r_item->table_meta_id);
						$r_item->table_meta_id = $field_table->id;
					}

				break;
			}
		}
	}

	$data = [
		"results" => $results,
		"table_metas" => $this->get_all_rows_and_cols_from_table('pim_table_metas'),
	];

	if($item_type == 'fields') {
		wp_send_json_success($data);
	} else {
		wp_send_json_success(["results" => $results]);
	}

}


function filterByProductId($array1, $array2) {
	// Check if arrays are not empty
	// if (empty($array1) || empty($array2)) {
	// 	return [];
	// }

	// Ensure 'product_id' exists in $array2 (filtered products) and 'id' exists in $array1 (all products)
	$ids = array_column($array2, 'product_id');

	// Filter $array1 (all products) based on matching 'id' in $array2
	$filtered = array_filter($array1, function($item) use ($ids) {
		return isset($item['id']) && in_array($item['id'], $ids);
	});

	return $filtered;
}

public function increase_ar_prod_viewed_times($product_id) {
	if (!empty($product_id)) {
		global $wpdb;
		$n =intval($product_id);
		$wpdb->query(
				"UPDATE pim_products
				 SET viewed_times = viewed_times + 1
				 WHERE id = $n",

		);
}
}

public function get_specific_revision() {
	check_ajax_referer('get_specific_revision');

	$revision_id = sanitize_text_field($_POST['revision_id']);

	if(empty($revision_id)) {
		wp_send_json_error(["message" => "Revision Id not found."]);
	}

	$result = $this->get_specific_row_from_table('pim_revisions', 'id', $revision_id);

	wp_send_json_success(['result' => $result]);
}


public function get_matching_product_items(string $search, bool $exact_search, int $user_id, int $limit, int $offset): array
{
    global $wpdb;

    if ($exact_search) {
        // Exact search using '='
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM pim_products WHERE sku = %s AND product_status = 'ACTIVE' LIMIT %d OFFSET %d",
            $search, $limit, $offset
        ), ARRAY_A);
    } else {
        // Partial search using 'LIKE'
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM pim_products WHERE sku LIKE %s AND product_status = 'ACTIVE' LIMIT %d OFFSET %d",
            '%' . $wpdb->esc_like($search) . '%', $limit, $offset
        ), ARRAY_A);
    }

    // Fetch total count for pagination
    $total_product_count = $exact_search
        ? $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM pim_products WHERE sku = %s AND product_status = 'ACTIVE'", $search))
        : $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM pim_products WHERE sku LIKE %s AND product_status = 'ACTIVE'", '%' . $wpdb->esc_like($search) . '%'));

    // Check if each product item is a favorite
    if (!empty($results)) {
        foreach ($results as &$product_item) {
            $favorite_count = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = %d",
                $user_id, $product_item['id'], $product_item['brand_id']
            ));
            $product_item['is_favorite'] = $favorite_count > 0;
            $product_item['main_image'] = $this->get_media_url($product_item['main_image'], 'thumbnail');
        }
    }

    return [
        'product_items' => $results,
        'total_product_count' => $total_product_count,
    ];
}

// public function get_matching_product_items($search, $exact_search, $user_id, $limit, $offset) {

// 	global $wpdb;

// 	if ($exact_search) {
// 		// Exact search using '='
// 		$query = "SELECT * FROM pim_products WHERE sku = %s AND product_status = 'ACTIVE' LIMIT %d OFFSET %d";
// 		$results = $wpdb->get_results($wpdb->prepare($query, $search, $limit, $offset), ARRAY_A);
// } else {
// 		// Partial search using 'LIKE'
// 		$query = "SELECT * FROM pim_products WHERE sku LIKE %s AND product_status = 'ACTIVE' LIMIT %d OFFSET %d";
// 		$results = $wpdb->get_results($wpdb->prepare($query, '%' . $wpdb->esc_like($search) . '%', $limit, $offset), ARRAY_A);
// }

// // Fetch total count for pagination
// if ($exact_search) {
// 		$count_query = "SELECT COUNT(*) FROM pim_products WHERE sku = %s AND product_status = 'ACTIVE'";
// 		$total_product_count = $wpdb->get_var($wpdb->prepare($count_query, $search));
// } else {
// 		$count_query = "SELECT COUNT(*) FROM pim_products WHERE sku LIKE %s AND product_status = 'ACTIVE'";
// 		$total_product_count = $wpdb->get_var($wpdb->prepare($count_query, '%' . $wpdb->esc_like($search) . '%'));
// }


// // Check if each product item is a favorite
// if (!empty($results)) {
// foreach ($results as &$product_item) {
// 	$favorite_count = $wpdb->get_var($wpdb->prepare(
// 			"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = %d",
// 			$user_id, $product_item['id'], $product_item['brand_id']
// 	));
// 	$product_item['is_favorite'] = $favorite_count > 0;
// 	$product_item['main_image'] = $this->get_media_url($product_item['main_image'], 'thumbnail');
// }
// }

// return [
// 'product_items' => $results,
// 'total_product_count' => $total_product_count,
// ];

// // die();
// }

public function get_sku_search_items() {
	check_ajax_referer('get_sku_search_items', '_wpnonce');

	$user_id = get_current_user_id();

	$searched_sku = isset($_POST['searched_sku']) ? htmlspecialchars($_POST['searched_sku']) : null;

	if(!$searched_sku) {
		wp_send_json_success([
			'product_items' => [],
			'total_product_count' => 0,
			'message' => 'No Items Found (search might be empty string)'
	]);
	}

	$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 28;
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$offset = ($page - 1) * $limit;

	global $wpdb;

	$escaped_sku = '%' .  $wpdb->esc_like($searched_sku) . '%';

	$results_query = $wpdb->prepare("SELECT * FROM `pim_products` WHERE sku LIKE %s LIMIT %d OFFSET %d", $escaped_sku, $limit, $offset);
	$results = $wpdb->get_results($results_query);

	$count_query = $wpdb->prepare("SELECT COUNT(*) FROM `pim_products` WHERE sku LIKE %s LIMIT %d OFFSET %d", $escaped_sku, $limit, $offset);
	$count = $wpdb->get_var($count_query);

	if (!empty($results)) {
		// Check if each product item is a favorite
		foreach ($results as &$product_item) {
			$favorite_count = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = %d",
				$user_id, $product_item->id, $product_item->brand_id
			));
			$product_item->is_favorite = $favorite_count > 0;
			$product_item->main_image = $this->get_media_url($product_item->main_image, 'thumbnail');
		}
	}

	wp_send_json_success([
		'product_items' => $results,
		'total_product_count' => $count,
]);
}

// 	function fetch_product_items() {
// 		check_ajax_referer('fetch_product_items', '_wpnonce');
// 		$user_id = get_current_user_id();

// 		$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 28;
// 		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
// 		$offset = ($page - 1) * $limit;

// 		$filter = isset($_POST['filter']) ? $_POST['filter'] : 0;

// 		$sub_brand_category_id = isset($_POST['sub_brand_category_id']) ? $_POST['sub_brand_category_id'] : 0;

// 		$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

// 		$exact_search = isset($_POST['exact_search']) ? $_POST['exact_search'] : 0; // Checking if exact search is true

// 		global $wpdb;


// 		if($sub_brand_category_id) {

// 			// 	if (!empty($search)) {

// 		// 		$searched_data = $this->get_matching_sub_brand_product_items($sub_brand_category_id, $search, $exact_search, $user_id, $limit, $offset);

// 		// 	$results = $searched_data['product_items'];
// 		// 	$total_product_count = $searched_data['total_product_count'];

// 		// } else {

// 			// $sub_brand_category_id = $wpdb->get_var($wpdb->prepare("SELECT id from pim_taxonomy_terms WHERE slug = %s", $sub_brand_category));

// 		// 	$results = $wpdb->get_results($wpdb->prepare("
// 		//     SELECT p.*
// 		//     FROM pim_products p
// 		//     INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 		//     WHERE tp.term_id = %d
// 		// 		LIMIT %d
// 		// 		OFFSET %d
// 		// ", intval($sub_brand_category_id), $limit , $offset), ARRAY_A);
// 		// Fetch the limited results (with pagination)
// 		$results = $wpdb->get_results($wpdb->prepare("
// 		SELECT p.*
// 		FROM pim_products p
// 				INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 				WHERE tp.term_id = %d AND sku LIKE %s AND p.product_status = 'ACTIVE'
// 				LIMIT %d
// 				OFFSET %d
// 		", intval($sub_brand_category_id), '%' . $wpdb->esc_like($search) . '%', $limit, $offset), ARRAY_A);

// 		// Fetch the total count of products without pagination
// 		$total_product_count = $wpdb->get_var($wpdb->prepare("
// 				SELECT COUNT(p.id)
// 				FROM pim_products p
// 				INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 				WHERE tp.term_id = %d AND sku LIKE %s AND p.product_status = 'ACTIVE'
// 		",intval($sub_brand_category_id), '%' . $wpdb->esc_like($search) . '%' ));


// 			// $sub_brand_prods = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_taxonomies_products WHERE term_id = %d", intval($sub_brand_category_id)), ARRAY_A);

// 			// $all_prods = $wpdb->get_results("SELECT * FROM pim_products", ARRAY_A);

// 			// $results = array_values($this->filterByProductId($all_prods, $sub_brand_prods));
// 		// }



// 		} else {
// 			if (!empty($search)) {

// 				$searched_data = $this->get_matching_product_items($search, $exact_search, $user_id, $limit, $offset);

// 				$results = $searched_data['product_items'];
// 				$total_product_count = $searched_data['total_product_count'];

// 		} else {


// 			if($filter && $filter != 0) {

// 				// $filtered_prods = $this->get_all_cols_in_one_to_many('pim_taxonomies_products', 'tax_id', $filter);
// 				// $filtered_prods = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_taxonomies_products WHERE tax_id = %d LIMIT %d OFFSET %d", intval($filter), $limit ,$offset), ARRAY_A);

// 			// Fetch filtered products based on taxonomy filter
// 		// $filtered_prods = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_taxonomies_products WHERE term_id = %d", intval($filter)), ARRAY_A);

// 		// $all_prods = $wpdb->get_results("SELECT * FROM pim_products", ARRAY_A);

// 		// // Apply filter function
// 		// $results = array_values($this->filterByProductId($all_prods, $filtered_prods));
// 	// Fetch the limited results (with pagination)
// 	$results = $wpdb->get_results($wpdb->prepare("
// 	SELECT p.*
// 	FROM pim_products p
// 	INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 	WHERE tp.term_id = %d
// 	AND p.product_status = 'ACTIVE'
// 	LIMIT %d
// 	OFFSET %d
// 	", intval($filter), $limit, $offset), ARRAY_A);

// 	// Fetch the total count of products without pagination
// 	$total_product_count = $wpdb->get_var($wpdb->prepare("
// 	SELECT COUNT(p.id)
// 	FROM pim_products p
// 	INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 	WHERE tp.term_id = %d
// 	AND p.product_status = 'ACTIVE'
// 	", intval($filter)));






// 			} elseif($filter == 'inactive_filter') {

// 				$results = $wpdb->get_results($wpdb->prepare(
// 						"SELECT * FROM pim_products WHERE product_status = 'INACTIVE' LIMIT %d OFFSET %d",
// 						$limit,
// 						$offset
// 				));

// 				$total_product_count = count($wpdb->get_results($wpdb->prepare(
// 					"SELECT * FROM pim_products WHERE product_status = 'INACTIVE'"
// 			)));

// 			} elseif($filter == 'most_viewed') {

// 				$results = $wpdb->get_results($wpdb->prepare(
// 					"SELECT *
// 					FROM pim_products
// 					WHERE product_status = 'ACTIVE'
// 					ORDER BY viewed_times DESC
// 					LIMIT %d OFFSET %d",
// 					$limit,
// 					$offset
// 			));

// 			$total_product_count = count($wpdb->get_results($wpdb->prepare(
// 				"SELECT *
// 					FROM pim_products
// 					WHERE product_status = 'ACTIVE'
// 					ORDER BY viewed_times DESC"
// 		)));


// 			} else {

// 				$results = $wpdb->get_results($wpdb->prepare(
// 						"SELECT * FROM pim_products WHERE product_status = 'ACTIVE' LIMIT %d OFFSET %d",
// 						$limit,
// 						$offset
// 				));

// 				$total_product_count = count($wpdb->get_results($wpdb->prepare(
// 					"SELECT * FROM pim_products WHERE product_status = 'ACTIVE'"
// 			)));

// 			}


// 		}
// 	}


// // 	$results = $wpdb->get_results($wpdb->prepare(
// // 		"SELECT * FROM pim_products WHERE product_status = 'ACTIVE' LIMIT %d OFFSET %d",
// // 		$limit,
// // 		$offset
// // ));

// // $total_product_count = count($wpdb->get_results($wpdb->prepare(
// // 	"SELECT * FROM pim_products WHERE product_status = 'ACTIVE'"
// // )));



// 		if (!empty($results)) {
// 		// Check if each product item is a favorite
// 		foreach ($results as &$product_item) {
// 			// $favorite_count = $wpdb->get_var($wpdb->prepare(
// 			// 	"SELECT COUNT(*) FROM bui_pods_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = 1",
// 			// 	$user_id, $product_item->id
// 			// ));
// 			$favorite_count = $wpdb->get_var($wpdb->prepare(
// 				"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = %d",
// 				$user_id, $product_item->id, $product_item->brand_id
// 			));
// 			$product_item->is_favorite = $favorite_count > 0;
// 			$product_item->main_image = $this->get_media_url($product_item->main_image, 'thumbnail');
// 		}
// 	}
// 		// $total_product_count = $wpdb->get_var("SELECT COUNT(*) FROM pim_products");
// 		// $total_product_count = count($results);

// 		wp_send_json_success([
// 				'product_items' => $results,
// 				'total_product_count' => $total_product_count,
// 		]);
// 	}

function fetch_product_items() {
	check_ajax_referer('fetch_product_items', '_wpnonce');
	
	$user_id = get_current_user_id();
	global $wpdb;

	// Define variables with null coalescing operators for cleaner syntax
	$limit = intval($_POST['limit'] ?? 28);
	$page = intval($_POST['page'] ?? 1);
	$offset = ($page - 1) * $limit;
	$filter = $_POST['filter'] ?? 0;
	$sub_brand_category_id = $_POST['sub_brand_category_id'] ?? 0;
	$search = sanitize_text_field($_POST['search'] ?? '');
	$exact_search = $_POST['exact_search'] ?? 0;

	$results = [];
	$total_product_count = 0;

	// Sub-brand category filtering
	if ($sub_brand_category_id) {
			// Handle filtered query based on sub-brand category
			$results = $wpdb->get_results($wpdb->prepare(
					"SELECT p.* 
					 FROM pim_products p 
					 INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id 
					 WHERE tp.term_id = %d 
					 AND sku LIKE %s 
					 AND p.product_status = 'ACTIVE' 
					 LIMIT %d 
					 OFFSET %d",
					intval($sub_brand_category_id), '%' . $wpdb->esc_like($search) . '%', $limit, $offset
			), ARRAY_A);

			$total_product_count = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(p.id) 
					 FROM pim_products p 
					 INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id 
					 WHERE tp.term_id = %d 
					 AND sku LIKE %s 
					 AND p.product_status = 'ACTIVE'",
					intval($sub_brand_category_id), '%' . $wpdb->esc_like($search) . '%'
			));
	} else {
			if (!empty($search)) {
					// Use the helper function to handle searching
					$searched_data = $this->get_matching_product_items($search, $exact_search, $user_id, $limit, $offset);
					$results = $searched_data['product_items'];
					$total_product_count = $searched_data['total_product_count'];
			} else {
					// Handle various filters
					switch ($filter) {
						case 'inactive_filter':
							$results = $wpdb->get_results($wpdb->prepare(
											"SELECT * FROM pim_products 
											 WHERE product_status = 'INACTIVE' 
											 LIMIT %d OFFSET %d",
											$limit, $offset
									), ARRAY_A);
									
									$total_product_count = $wpdb->get_var(
											"SELECT COUNT(*) FROM pim_products WHERE product_status = 'INACTIVE'"
									);
									break;

							case 'most_viewed':
									$results = $wpdb->get_results($wpdb->prepare(
											"SELECT * FROM pim_products 
											 WHERE product_status = 'ACTIVE' 
											 ORDER BY viewed_times DESC 
											 LIMIT %d OFFSET %d",
											$limit, $offset
									), ARRAY_A);
									
									$total_product_count = $wpdb->get_var(
											"SELECT COUNT(*) FROM pim_products 
											 WHERE product_status = 'ACTIVE' 
											 ORDER BY viewed_times DESC"
									);
									break;
									
									default:
									if (!empty($filter)) {
											$results = $wpdb->get_results($wpdb->prepare(
													"SELECT p.* 
													 FROM pim_products p 
													 INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id 
													 WHERE tp.term_id = %d 
													 AND p.product_status = 'ACTIVE' 
													 LIMIT %d OFFSET %d",
													intval($filter), $limit, $offset
											), ARRAY_A);
											
											$total_product_count = $wpdb->get_var($wpdb->prepare(
													"SELECT COUNT(p.id) 
													 FROM pim_products p 
													 INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id 
													 WHERE tp.term_id = %d 
													 AND p.product_status = 'ACTIVE'",
													intval($filter)
											));
									} else {
											$results = $wpdb->get_results($wpdb->prepare(
													"SELECT * FROM pim_products 
													 WHERE product_status = 'ACTIVE' 
													 LIMIT %d OFFSET %d",
													$limit, $offset
											), ARRAY_A);
											
											$total_product_count = $wpdb->get_var(
													"SELECT COUNT(*) FROM pim_products WHERE product_status = 'ACTIVE'"
											);
									}
					}
			}
	}

	// Check and mark favorite products
	if (!empty($results)) {
			foreach ($results as &$product_item) {
					$favorite_count = $wpdb->get_var($wpdb->prepare(
							"SELECT COUNT(*) 
							 FROM pim_favorites 
							 WHERE user_id = %d 
							 AND item_id = %d 
							 AND item_type = 'product' 
							 AND brand_id = %d",
							$user_id, $product_item['id'], $product_item['brand_id']
					));
					
					$product_item['is_favorite'] = $favorite_count > 0;
					$product_item['main_image'] = $this->get_media_url($product_item['main_image'], 'thumbnail');
			}
	}

	// Send the response back to the client
	wp_send_json_success([
			'product_items' => $results,
			'total_product_count' => $total_product_count,
	]);
}


// 	function fetch_product_items_2() {
//     check_ajax_referer('fetch_product_items', '_wpnonce');
//     $user_id = get_current_user_id();

//     $limit = intval($_POST['limit'] ?? 28);
//     $page = intval($_POST['page'] ?? 1);
//     $offset = ($page - 1) * $limit;

//     $filter = $_POST['filter'] ?? 0;
//     $sub_brand_category_id = $_POST['sub_brand_category_id'] ?? 0;
//     $search = sanitize_text_field($_POST['search'] ?? '');
//     $exact_search = boolval($_POST['exact_search'] ?? 0); // Boolean cast for exact search

//     global $wpdb;

//     if ($sub_brand_category_id) {
//         $results = $wpdb->get_results($wpdb->prepare(
//             "SELECT p.* FROM pim_products p
//              INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
//              WHERE tp.term_id = %d AND sku LIKE %s AND p.product_status = 'ACTIVE'
//              LIMIT %d OFFSET %d",
//              intval($sub_brand_category_id), '%' . $wpdb->esc_like($search) . '%', $limit, $offset
//         ), ARRAY_A);

//         $total_product_count = $wpdb->get_var($wpdb->prepare(
//             "SELECT COUNT(p.id) FROM pim_products p
//              INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
//              WHERE tp.term_id = %d AND sku LIKE %s AND p.product_status = 'ACTIVE'",
//              intval($sub_brand_category_id), '%' . $wpdb->esc_like($search) . '%'
//         ));
//     } else {
//         if (!empty($search)) {
// 					$searched_data = $this->get_matching_product_items($search, $exact_search, $user_id, $limit, $offset);
// 					$results = $searched_data['product_items'];
// 					$total_product_count = $searched_data['total_product_count'];
//         } else if ($filter) {
// 					if($filter && $filter != 0) {

// 						// $filtered_prods = $this->get_all_cols_in_one_to_many('pim_taxonomies_products', 'tax_id', $filter);
// 						// $filtered_prods = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_taxonomies_products WHERE tax_id = %d LIMIT %d OFFSET %d", intval($filter), $limit ,$offset), ARRAY_A);

// 					// Fetch filtered products based on taxonomy filter
// 				// $filtered_prods = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_taxonomies_products WHERE term_id = %d", intval($filter)), ARRAY_A);

// 				// $all_prods = $wpdb->get_results("SELECT * FROM pim_products", ARRAY_A);

// 				// // Apply filter function
// 				// $results = array_values($this->filterByProductId($all_prods, $filtered_prods));
// 			// Fetch the limited results (with pagination)
// 			$results = $wpdb->get_results($wpdb->prepare("
// 			SELECT p.*
// 			FROM pim_products p
// 			INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 			WHERE tp.term_id = %d
// 			AND p.product_status = 'ACTIVE'
// 			LIMIT %d
// 			OFFSET %d
// 			", intval($filter), $limit, $offset), ARRAY_A);

// 			// Fetch the total count of products without pagination
// 			$total_product_count = $wpdb->get_var($wpdb->prepare("
// 			SELECT COUNT(p.id)
// 			FROM pim_products p
// 			INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 			WHERE tp.term_id = %d
// 			AND p.product_status = 'ACTIVE'
// 			", intval($filter)));






// 					} elseif($filter == 'inactive_filter') {

// 						$results = $wpdb->get_results($wpdb->prepare(
// 								"SELECT * FROM pim_products WHERE product_status = 'INACTIVE' LIMIT %d OFFSET %d",
// 								$limit,
// 								$offset
// 						));

// 						$total_product_count = count($wpdb->get_results($wpdb->prepare(
// 							"SELECT * FROM pim_products WHERE product_status = 'INACTIVE'"
// 					)));

// 					} elseif($filter == 'most_viewed') {

// 						$results = $wpdb->get_results($wpdb->prepare(
// 							"SELECT *
// 							FROM pim_products
// 							WHERE product_status = 'ACTIVE'
// 							ORDER BY viewed_times DESC
// 							LIMIT %d OFFSET %d",
// 							$limit,
// 							$offset
// 					));

// 					$total_product_count = count($wpdb->get_results($wpdb->prepare(
// 						"SELECT *
// 							FROM pim_products
// 							WHERE product_status = 'ACTIVE'
// 							ORDER BY viewed_times DESC"
// 				)));


// 					} else {

// 						$results = $wpdb->get_results($wpdb->prepare(
// 								"SELECT * FROM pim_products WHERE product_status = 'ACTIVE' LIMIT %d OFFSET %d",
// 								$limit,
// 								$offset
// 						));

// 						$total_product_count = count($wpdb->get_results($wpdb->prepare(
// 							"SELECT * FROM pim_products WHERE product_status = 'ACTIVE'"
// 					)));

// 					}
//         } else {
//             $results = $wpdb->get_results($wpdb->prepare(
//                 "SELECT * FROM pim_products WHERE product_status = 'ACTIVE' LIMIT %d OFFSET %d",
//                 $limit, $offset
//             ), ARRAY_A);

//             $total_product_count = $wpdb->get_var($wpdb->prepare(
//                 "SELECT COUNT(*) FROM pim_products WHERE product_status = 'ACTIVE'"
//             ));
//         }
//     }

//     if (!empty($results)) {
//         foreach ($results as &$product_item) {
//             $favorite_count = $wpdb->get_var($wpdb->prepare(
//                 "SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = %d",
//                 $user_id, $product_item['id'], $product_item['brand_id']
//             ));
//             $product_item['is_favorite'] = $favorite_count > 0;
//             $product_item['main_image'] = $this->get_media_url($product_item['main_image'], 'thumbnail');
//         }
//     }

//     wp_send_json_success([
//         'product_items' => $results,
//         'total_product_count' => $total_product_count,
//     ]);
// }





// function fetch_product_items_old() {
// 	check_ajax_referer('fetch_product_items', '_wpnonce');
// 	$user_id = get_current_user_id();

// 	$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 28;
// 	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
// 	$offset = ($page - 1) * $limit;

// 	$filter = isset($_POST['filter']) ? $_POST['filter'] : 0;
// 	$sub_brand_category_id = isset($_POST['sub_brand_category_id']) ? $_POST['sub_brand_category_id'] : 0;
// 	$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
// 	$exact_search = isset($_POST['exact_search']) && $_POST['exact_search'] == 'true'; // Checking if exact search is true

// 	global $wpdb;

// 	// Modify query based on search input
// 	if (!empty($search)) {
// 			if ($exact_search) {
// 					// Exact search using '='
// 					$query = "SELECT * FROM pim_products WHERE sku = %s LIMIT %d OFFSET %d";
// 					$results = $wpdb->get_results($wpdb->prepare($query, $search, $limit, $offset), ARRAY_A);
// 			} else {
// 					// Partial search using 'LIKE'
// 					$query = "SELECT * FROM pim_products WHERE sku LIKE %s LIMIT %d OFFSET %d";
// 					$results = $wpdb->get_results($wpdb->prepare($query, '%' . $wpdb->esc_like($search) . '%', $limit, $offset), ARRAY_A);
// 			}

// 			// Fetch total count for pagination
// 			if ($exact_search) {
// 					$count_query = "SELECT COUNT(*) FROM pim_products WHERE sku = %s";
// 					$total_product_count = $wpdb->get_var($wpdb->prepare($count_query, $search));
// 			} else {
// 					$count_query = "SELECT COUNT(*) FROM pim_products WHERE sku LIKE %s";
// 					$total_product_count = $wpdb->get_var($wpdb->prepare($count_query, '%' . $wpdb->esc_like($search) . '%'));
// 			}
// 	} else {
// 			// If no search term, run your existing logic for sub_brand_category_id, filters, etc.
// 			if ($sub_brand_category_id) {
// 					$results = $wpdb->get_results($wpdb->prepare(
// 							"SELECT p.* FROM pim_products p
// 							INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 							WHERE tp.term_id = %d LIMIT %d OFFSET %d",
// 							intval($sub_brand_category_id), $limit, $offset), ARRAY_A);

// 					$total_product_count = $wpdb->get_var($wpdb->prepare(
// 							"SELECT COUNT(p.id) FROM pim_products p
// 							INNER JOIN pim_taxonomies_products tp ON p.id = tp.product_id
// 							WHERE tp.term_id = %d", intval($sub_brand_category_id)));
// 			} else {
// 					// Handle other filters or default cases






// 			}
// 	}

// 	// Check if each product item is a favorite
// 	if (!empty($results)) {
// 			foreach ($results as &$product_item) {
// 					$favorite_count = $wpdb->get_var($wpdb->prepare(
// 							"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = %d",
// 							$user_id, $product_item['id'], $product_item['brand_id']
// 					));
// 					$product_item['is_favorite'] = $favorite_count > 0;
// 					$product_item['main_image'] = $this->get_media_url($product_item['main_image'], 'thumbnail');
// 			}
// 	}

// 	wp_send_json_success([
// 			'product_items' => $results,
// 			'total_product_count' => $total_product_count,
// 	]);
// }



	// function prod_toggle_favorite_item() {
	// 	check_ajax_referer('prod_toggle_favorite_item', '_wpnonce');

	// 	$item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
	// 	$is_favorite = isset($_POST['is_favorite']) ? intval($_POST['is_favorite']) : 0;

	// global $wpdb;

	// $user_id = get_current_user_id();
	// $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
	// $is_favorite = isset($_POST['is_favorite']) ? intval($_POST['is_favorite']) : 0;

	// if ($item_id && $user_id) {
	// 	if ($is_favorite) {
	// 		// Add to favorites
	// 		$wpdb->insert('bui_pods_favorites', [
	// 			'user_id' => $user_id,
	// 			'item_id' => $item_id,
	// 			'item_type' => 'product',
	// 			'brand_id' => 1,
	// 		]);
	// 	} else {
	// 		// Remove from favorites
	// 		$wpdb->delete('bui_pods_favorites', [
	// 			'user_id' => $user_id,
	// 			'item_id' => $item_id,
	// 			'item_type' => 'product',
	// 			'brand_id' => 1
	// 		]);
	// 	}

	// 	wp_send_json_success();
	// } else {
	// 	wp_send_json_error('Invalid item or user.');
	// }
	// }


	public function get_related_media_items() {
		check_ajax_referer('get_related_media_items');

		$prod_id = (int) $_POST['prod_id'];
		$assignment_id = (int) $_POST['assignment_id'];

		if(! $prod_id || ! $assignment_id) {
			wp_send_json_error(['message' => 'Product id or Asset id not found.']);
			die();
		}

		global $wpdb;
		$user_id = get_current_user_id();

	// 	$results = $wpdb->get_results($wpdb->prepare(
	// 		"SELECT * FROM pim_media WHERE associated_item_id = %d AND media_assignment_id = %d",
	// 		$prod_id,
	// 		$assignment_id
	// ));

	$results = $wpdb->get_results($wpdb->prepare(
		"SELECT * FROM pim_media WHERE associated_item_id = %d",
		$prod_id,
));

	foreach ($results as &$media_item) {
		// $favorite_count = $wpdb->get_var($wpdb->prepare(
		// 	"SELECT COUNT(*) FROM bui_pods_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'media' AND brand_id = 1",
		// 	$user_id, $media_item->id
		// ));
		$favorite_count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'media' AND brand_id = %d",
			$user_id, $media_item->id, $media_item->brand_id
		));

		$media_item->is_favorite = $favorite_count > 0;

		$media_item->thumb_url = $this->get_media_url($media_item->thumb_url, 'thumbnail');;
	}


	wp_send_json_success([
		'media_items' => $results,
	]);

	}


	// function fetch_media_items() {
	// 	check_ajax_referer('fetch_media_items');

	// 	global $wpdb;
	// 	$user_id = get_current_user_id();

	// 	$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 28;
	// 	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	// 	$offset = ($page - 1) * $limit;
	// 	$media_type = isset($_POST['media_type']) ? sanitize_text_field($_POST['media_type']) : '';

	// 	$result = $this->fetch_media_from_db($limit, $offset, $media_type);

	// 	// Check if each media item is a favorite
	// 	foreach ($result['media_items'] as &$media_item) {
	// 		$favorite_count = $wpdb->get_var($wpdb->prepare(
	// 			"SELECT COUNT(*) FROM bui_pods_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'media' AND brand_id = 1",
	// 			$user_id, $media_item['id']
	// 		));
	// 		$media_item['is_favorite'] = $favorite_count > 0;
	// 		$media_item['thumb_url'] = $this->get_media_url($media_item['thumb_url'], 'thumbnail');;
	// 		// $media_item['source_url'] = $favorite_count > 0;

	// 	}

	// 	// Send response back to the client
	// 	wp_send_json_success([
	// 		'media_items' => $result['media_items'],
	// 		'total_media_count' => $result['total_media_count'],
	// 		// 'media_url_prefix' => "https://d31il057o05hvr.cloudfront.net/raw-folder/",
	// 	]);
	// }


	// function fetch_media_from_db($limit = 28, $offset = 0, $media_type = 'all') {
	// 	global $wpdb;
	// 	$table_name = 'pim_media';
	// 	$media_assignments_table = 'pim_media_assignments';

	// 	if ($media_type === 'all') {
	// 			$total_media_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	// 			$media_items = $wpdb->get_results($wpdb->prepare(
	// 					"SELECT * FROM $table_name LIMIT %d OFFSET %d",
	// 					$limit, $offset
	// 			), ARRAY_A);
	// 	} else {
	// 			// Get IDs of the media assignments matching the selected type
	// 			$assignment_ids = $wpdb->get_col($wpdb->prepare(
	// 					"SELECT id FROM $media_assignments_table WHERE file_type = %s",
	// 					$media_type
	// 			));

	// 			if (empty($assignment_ids)) {
	// 					return [
	// 							'media_items' => [],
	// 							'total_media_count' => 0,
	// 					];
	// 			}

	// 			$ids_placeholders = implode(',', array_fill(0, count($assignment_ids), '%d'));

	// 			$total_media_count = $wpdb->get_var($wpdb->prepare(
	// 					"SELECT COUNT(*) FROM $table_name WHERE media_assignment_id IN ($ids_placeholders)",
	// 					...$assignment_ids
	// 			));

	// 			$media_items = $wpdb->get_results($wpdb->prepare(
	// 					"SELECT * FROM $table_name WHERE media_assignment_id IN ($ids_placeholders) LIMIT %d OFFSET %d",
	// 					...array_merge($assignment_ids, [$limit, $offset])
	// 			), ARRAY_A);
	// 	}

	// 	return [
	// 			'media_items' => $media_items,
	// 			'total_media_count' => $total_media_count,
	// 	];
	// }

	function fetch_media_items() {
    check_ajax_referer('fetch_media_items');

    global $wpdb;
    $user_id = get_current_user_id();

    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 28;
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $offset = ($page - 1) * $limit;
    $media_type = isset($_POST['media_type']) ? sanitize_text_field($_POST['media_type']) : '';
    $media_group_id = isset($_POST['media_group_id']) ? intval($_POST['media_group_id']) : null;
    $media_assignment_id = isset($_POST['media_assignment_id']) ? intval($_POST['media_assignment_id']) : null;

		$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

		// $exact_search = isset($_POST['exact_search']) ? $_POST['exact_search'] : 0;

    // Fetch media from the database with media_type and media_group_id filtering
    $result = $this->fetch_media_from_db($limit, $offset, $media_type, $search, $media_assignment_id, $media_group_id);

    // Check if each media item is a favorite
    foreach ($result['media_items'] as &$media_item) {
				$favorite_count = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'media' AND brand_id = %d",
					$user_id, $media_item['id'], $media_item['brand_id']
			));
        $media_item['is_favorite'] = $favorite_count > 0;
        $media_item['thumb_url'] = $this->get_media_url($media_item['thumb_url'], 'thumbnail');
    }

    // Send response back to the client
    wp_send_json_success([
        'media_items' => $result['media_items'],
        'total_media_count' => $result['total_media_count'],
    ]);
}

function fetch_media_from_db($limit = 28, $offset = 0, $media_type = 'all', $search = '', $media_assignment_id = null, $media_group_id = null) {
    global $wpdb;
    $table_name = 'pim_media';
    $media_assignments_table = 'pim_media_assignments';

    if ($media_group_id) {

			if (!empty($search)) {

				$searched_data = $this->get_matching_media_items($search, $limit, $offset, $media_group_id, $media_assignment_id);

				$media_items = $searched_data['media_items'];
				$total_media_count = $searched_data['total_media_count'];

				return [
					'media_items' => $media_items,
					'total_media_count' => $total_media_count,
			];

			} else {

				// Get all media_assignment_ids for the provided media_group_id
				$assignment_ids = $wpdb->get_col($wpdb->prepare(
						"SELECT id FROM $media_assignments_table WHERE media_group_id = %d",
						$media_group_id
				));

				if (empty($assignment_ids)) {
						return [
								'media_items' => [],
								'total_media_count' => 0,
						];
				}

				if($media_assignment_id) {

					$ids_placeholders = '%d';
					$assignment_ids = [$media_assignment_id];

				} else {
					// Prepare placeholders and fetch media items for the assignment IDs
					$ids_placeholders = implode(',', array_fill(0, count($assignment_ids), '%d'));
				}

				$total_media_count = $wpdb->get_var($wpdb->prepare(
						"SELECT COUNT(*) FROM $table_name WHERE media_assignment_id IN ($ids_placeholders)",
						...$assignment_ids
				));

				$media_items = $wpdb->get_results($wpdb->prepare(
						"SELECT * FROM $table_name WHERE media_assignment_id IN ($ids_placeholders) LIMIT %d OFFSET %d",
						...array_merge($assignment_ids, [$limit, $offset])
				), ARRAY_A);

				return [
						'media_items' => $media_items,
						'total_media_count' => $total_media_count,
				];
			}

    }

    // Default behavior (no media_group_id)
    if ($media_type === 'all') {
// if search characters exist
if (!empty($search)) {

	$searched_data = $this->get_matching_media_items($search, $limit, $offset);

	$media_items = $searched_data['media_items'];
	$total_media_count = $searched_data['total_media_count'];


} else {
        $total_media_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $media_items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name LIMIT %d OFFSET %d",
            $limit, $offset
        ), ARRAY_A);
			}
    } else {


			if (!empty($search)) {

				$searched_data = $this->get_matching_media_items($search, $limit, $offset, null, null, $media_type);

				$media_items = $searched_data['media_items'];
				$total_media_count = $searched_data['total_media_count'];


			} else {

				// Get IDs of the media assignments matching the selected type
					$assignment_ids = $wpdb->get_col($wpdb->prepare(
							"SELECT id FROM $media_assignments_table WHERE file_type = %s",
							$media_type
					));

					if (empty($assignment_ids)) {
				// die("OK");

							return [
									'media_items' => [],
									'total_media_count' => 0,
							];
					}

					$ids_placeholders = implode(',', array_fill(0, count($assignment_ids), '%d'));

					$total_media_count = $wpdb->get_var($wpdb->prepare(
							"SELECT COUNT(*) FROM $table_name WHERE media_assignment_id IN ($ids_placeholders)",
							...$assignment_ids
					));

					$media_items = $wpdb->get_results($wpdb->prepare(
							"SELECT * FROM $table_name WHERE media_assignment_id IN ($ids_placeholders) LIMIT %d OFFSET %d",
							...array_merge($assignment_ids, [$limit, $offset])
					), ARRAY_A);
						}
		}





    return [
        'media_items' => $media_items,
        'total_media_count' => $total_media_count,
    ];
}

public function get_matching_media_items($search, $limit, $offset, $media_group_id = null, $media_assignment_id = null, $media_type = null) {
					// if ($exact_search) {
					global $wpdb;
					$table_name = 'pim_media';
    $media_assignments_table = 'pim_media_assignments';

					if ($media_group_id) {

							// Get all media_assignment_ids for the provided media_group_id
							$assignment_ids = $wpdb->get_col($wpdb->prepare(
									"SELECT id FROM $media_assignments_table WHERE media_group_id = %d",
									$media_group_id
							));

							if (empty($assignment_ids)) {
									return [
											'media_items' => [],
											'total_media_count' => 0,
									];
							}

							if($media_assignment_id) {

								$ids_placeholders = '%d';
								$assignment_ids = [$media_assignment_id];

							} else {
								// Prepare placeholders and fetch media items for the assignment IDs
								$ids_placeholders = implode(',', array_fill(0, count($assignment_ids), '%d'));
							}

							$total_media_count = $wpdb->get_var($wpdb->prepare(
									"SELECT COUNT(*) FROM $table_name WHERE media_assignment_id IN ($ids_placeholders) AND title LIKE %s",
									...array_merge($assignment_ids, ['%' . $wpdb->esc_like($search) . '%'])
							));

							$media_items = $wpdb->get_results($wpdb->prepare(
									"SELECT * FROM $table_name WHERE media_assignment_id IN ($ids_placeholders) AND title LIKE %s LIMIT %d OFFSET %d",
									...array_merge($assignment_ids, ['%' . $wpdb->esc_like($search) . '%',$limit, $offset])
							), ARRAY_A);

					} else {

				
									// Partial search using 'LIKE'
									$query = "SELECT * FROM pim_media WHERE title LIKE %s LIMIT %d OFFSET %d";
									$media_items = $wpdb->get_results($wpdb->prepare($query, '%' . $wpdb->esc_like($search) . '%', $limit, $offset), ARRAY_A);
						
									$count_query = "SELECT COUNT(*) FROM pim_media WHERE title LIKE %s";
									$total_media_count = $wpdb->get_var($wpdb->prepare($count_query, '%' . $wpdb->esc_like($search) . '%'));
					
					}

	
	return [
		'media_items' => $media_items,
		'total_media_count' => $total_media_count,
];

}


	function toggle_favorite_item() {
    check_ajax_referer('toggle_favorite_item', '_wpnonce');

    global $wpdb;

		$allowed_types = ['media', 'product'];

    $user_id = get_current_user_id();
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $is_favorite = isset($_POST['is_favorite']) ? intval($_POST['is_favorite']) : 0;
    $item_type = isset($_POST['item_type']) ? sanitize_text_field($_POST['item_type']) : '';


    if ($item_id && $user_id && in_array($item_type, $allowed_types)) {
        if ($is_favorite) {
            // Add to favorites
						$wpdb->insert('pim_favorites', [
							'user_id' => $user_id,
							'item_id' => $item_id,
							'item_type' => $item_type,
							'brand_id' => 1,
					]);
            // $wpdb->insert('bui_pods_favorites', [
            //     'user_id' => $user_id,
            //     'item_id' => $item_id,
            //     'item_type' => $item_type,
            //     'brand_id' => 1,
						// 		'is_new' => 1,
						// 		'new_id' => $item_id
            // ]);
        } else {
            // Remove from favorites
						$wpdb->delete('pim_favorites', [
							    'user_id' => $user_id,
							    'item_id' => $item_id,
							    'item_type' => $item_type,
							    'brand_id' => 1,
							]);
            // $wpdb->delete('bui_pods_favorites', [
            //     'user_id' => $user_id,
            //     'item_id' => $item_id,
            //     'item_type' => $item_type,
            //     'brand_id' => 1,
						// 		'is_new' => 1,
						// 		'new_id' => $item_id
            // ]);
        }

        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid item, user, or item type.');
    }
}

function get_users_name( $user_id ) {

	if( empty($user_id) ) return null;

	$user_data = get_userdata($user_id);

	return $user_data->user_email;

}

function get_its_brand($id) {

	if(empty($id)) return null;

	return $this->get_var_from_table('pim_brands', 'name', (int) $id);

}

function get_assignment_name_of_asset($media_assignment_id) {

	if(empty($media_assignment_id)) return null;

	return $this->get_var_from_table('pim_media_assignments', 'assignment_name', (int) $media_assignment_id);

}

function get_assignment_type_of_asset($media_assignment_id) {

	if(empty($media_assignment_id)) return null;

	return $this->get_var_from_table('pim_media_assignments', 'file_type', (int) $media_assignment_id);

}

function update_asset_data() {

	check_ajax_referer('update_asset_data');

	if(empty($_POST['id'])) wp_send_json_error(['message' => 'Asset id not found.']);

	$asset_id = (int) sanitize_text_field($_POST['id']);

	if(!empty($_POST['title']) &&  !empty($_POST['status'])) {

		$data = [
			'title' => sanitize_text_field($_POST['title']),
			'media_status' => sanitize_text_field($_POST['status']),
			'media_note' => sanitize_text_field($_POST['note'])
		];
		$result = $this->update_row_of_table('pim_media', $data, ['id' => $asset_id]);

		if($result) {
			wp_send_json_success(['message' => 'Asset updated successfully']);
		} else {
			wp_send_json_error(['message' => 'Error Updating Asset']);
		}

	} else {

		wp_send_json_error(['message' => 'Title or Status not found.']);

	}
}
	// function toggle_favorite_item() {
	// check_ajax_referer('toggle_favorite_item');

	// global $wpdb;

	// $user_id = get_current_user_id();
	// $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
	// $is_favorite = isset($_POST['is_favorite']) ? intval($_POST['is_favorite']) : 0;

	// if ($item_id && $user_id) {
	// 	if ($is_favorite) {
	// 		// Add to favorites
	// 		$wpdb->insert('bui_pods_favorites', [
	// 			'user_id' => $user_id,
	// 			'item_id' => $item_id,
	// 			'item_type' => 'media',
	// 			'brand_id' => 1,
	// 		]);
	// 	} else {
	// 		// Remove from favorites
	// 		$wpdb->delete('bui_pods_favorites', [
	// 			'user_id' => $user_id,
	// 			'item_id' => $item_id,
	// 			'item_type' => 'media',
	// 			'brand_id' => 1
	// 		]);
	// 	}

	// 	wp_send_json_success();
	// } else {
	// 	wp_send_json_error('Invalid item or user.');
	// }
	// }

	public function get_all_cols_in_one_to_many($from_table, $key_name, $foreign_key) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $from_table WHERE $key_name = %d", intval($foreign_key)));
	}

	public function get_all_rows_and_cols_from_table($table_name) {
		global $wpdb;


		// return  $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name"));
		return  $wpdb->get_results("SELECT * FROM $table_name");
	}

	// public function get_all_rows_and_cols_from_table($table_name) {
	// 	global $wpdb;

	// 	// return  $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name"));
	// 	return $wpdb->get_results("SELECT * FROM $table_name WHERE brand_id = 87 AND is_new = 1");
	// }

	// public function get_all_favs($table_name) {
	// 	global $wpdb;

	// 	// return  $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name"));
	// 	$results = $wpdb->get_results("SELECT * FROM $table_name WHERE (brand_id = 87 OR brand_id = 1) AND is_new = 1");

	// 	foreach($results as &$res) {
	// 		if($res->item_type == 'media') {
	// 			$media_item = $this->get_full_row_from_table('pim_media', $res->new_id);
	// 			$res->main_image = $this->get_media_url($media_item->thumb_url, 'thumbnail');
	// 		} elseif(($res->item_type == 'product')) {
	// 			$product = $this->get_full_row_from_table('pim_products', $res->new_id);
	// 			$res->main_image = $this->get_media_url($product->main_image, 'thumbnail');
	// 		}
	// 	}

	// 	return $results;
	// }

	public function mark_product_complete() {
		check_ajax_referer('mark_product_complete', '_wpnonce');

		$prod_id = sanitize_text_field($_POST['product_id']);
		$is_complete = sanitize_text_field($_POST['is_complete']);

		if(!$prod_id) {
			wp_send_json_error(['message' => 'Prod Id not found.']);
		}

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare("
			UPDATE pim_products
			SET is_completed = %d
			WHERE id = %d
		", $is_complete, $prod_id));

		if($result) {

			wp_send_json_success(['message' => 'updated', 'is_complete' => $is_complete]);

		} else {

			wp_send_json_error(['message' => 'Cannot update is_complete status']);

		}

	}

	public function assign_product_parent() {
		check_ajax_referer('assign_product_parent', '_wpnonce');

		$prod_id = sanitize_text_field($_POST['product_id']);
		$parent_id = sanitize_text_field($_POST['parent_id']);

		if(!$prod_id) {
			wp_send_json_error(['message' => 'Prod Id not found.', 'success' => false]);
		}

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare("
			UPDATE pim_products
			SET parent_id = %d
			WHERE id = %d
		", $parent_id, $prod_id));

		if($result) {
			wp_send_json_success(['message' => 'assigned product parent', 'success' => true]);
		}

			wp_send_json_error(['message' => 'Cannot assign a parent to this product.', 'success' => false]);

	}

	public function sync_field_to_syndication() {
		check_ajax_referer('sync_field_to_syndication', '_wpnonce');

		$field_id = sanitize_text_field($_POST['field_id']);

		if(!$field_id) {
			wp_send_json_error(['message' => 'Field is already in sync with syndication.']);
		}

		$body = $this->prepare_request_body($field_id, 'CREATE OR UPDATE');

		$result = $this->send_field_sync_request_to_syndication($body);

		if($result['done']) {
			wp_send_json_success(['message' => 'SYNC DONE', 'result' => $result]);
		}

		// $this->dump($result);

			wp_send_json_error(['message' => 'SYNC FAILED']);

	}

	public function get_seller_apps_names($seller_apps) {
		// global $wpdb;
		$seller_apps_names = [];
		foreach($seller_apps as $s_app_id) {
		foreach($this->seller_apps_names as $s_app_data) {
			if($s_app_id == $s_app_data->id) {
				$seller_apps_names[] = $s_app_data->name;
			}
		}

			// $seller_apps_names[] = $wpdb->get_var("SELECT `name` from `pim_seller_apps` WHERE id = $s_app_id");
		}
		return $seller_apps_names;
	}

	public function load_seller_apps() {
		global $wpdb;
		$this->seller_apps_names = $wpdb->get_results("SELECT * from `pim_seller_apps`");
	}

	public function remove_parent() {
		check_ajax_referer('remove_parent', '_wpnonce');

		$prod_id = sanitize_text_field($_POST['product_id']);
		$parent_id = sanitize_text_field($_POST['parent_id']);

		if(!$prod_id) {
			wp_send_json_error(['message' => 'Prod Id not found.', 'success' => false]);
		}


		if(!$parent_id) {
			wp_send_json_error(['message' => 'Parent not found.']);
		}

		global $wpdb;

			$result = $wpdb->query($wpdb->prepare("
				UPDATE pim_products
				SET parent_id = 0
				WHERE id = %d
			", $prod_id));


		if($result) {
			wp_send_json_success(['message' => 'Removed product parent', 'success' => true]);
		}

			wp_send_json_error(['message' => 'Cannot Remove product parent.', 'success' => false]);

	}

	public function prepare_request_body($field_id, $action) {

		$field_row = $this->get_full_row_from_table('pim_field_metas', $field_id);
		$field_row = json_decode(json_encode($field_row), TRUE);

		$field_config = json_decode($field_row['CONFIG'], TRUE);

		$seller_apps_names = $this->get_seller_apps_names($field_config['seller_apps']);

		return array_merge($field_row, [
			'seller_apps' => json_encode($seller_apps_names),
			'action' => $action
		]);

		// $this->send_field_sync_request_to_syndication(array_merge($data, [
					// 	'id' => $result['id'],
					// 	'seller_apps' => json_encode($seller_apps_names),
					// 	'action' => 'CREATE'
					// ]));

	}

	function get_all_favs() {
		check_ajax_referer('get_all_favs', '_wpnonce');

		$user_id = get_current_user_id();

		$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 28;
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$offset = ($page - 1) * $limit;

		$item_type = isset($_POST['item_type']) ? sanitize_text_field($_POST['item_type']) : '';
		$media_type = isset($_POST['media_type']) ? sanitize_text_field($_POST['media_type']) : '';

		$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';


		global $wpdb;


		if ($item_type == '0' || $item_type === 'all') {

			$product_favs = $wpdb->get_results(
				$wpdb->prepare("
						SELECT p.*, f.item_type
						FROM pim_favorites f
						INNER JOIN pim_products p ON f.item_id = p.id
						WHERE f.user_id = %d AND f.item_type = 'product' AND p.title LIKE %s
						LIMIT %d OFFSET %d
				", $user_id, '%' . $wpdb->esc_like($search) . '%', $limit, $offset)
		);

		$fav_prods_count = $wpdb->get_var(
			$wpdb->prepare("
					SELECT COUNT(*)
					FROM pim_favorites f
					INNER JOIN pim_products p ON f.item_id = p.id
					WHERE f.user_id = %d AND f.item_type = 'product' AND p.title LIKE %s
			", $user_id, '%' . $wpdb->esc_like($search) . '%')
	);

	// 	$count_1 =  $wpdb->get_results(
	// 		$wpdb->prepare("
	// 				SELECT p.*, f.item_type
	// 				FROM pim_favorites f
	// 				INNER JOIN pim_products p ON f.item_id = p.id
	// 				WHERE f.user_id = %d AND f.item_type = 'product' AND p.title LIKE %s
	// 				LIMIT %d OFFSET %d
	// 		", $user_id, '%' . $wpdb->esc_like($search) . '%', $limit, $offset)
	// );

		$media_favs = $wpdb->get_results(
			$wpdb->prepare("
					SELECT m.*, f.item_type
					FROM pim_favorites f
					INNER JOIN pim_media m ON f.item_id = m.id
					WHERE f.user_id = %d AND f.item_type = 'media' AND m.title LIKE %s
					LIMIT %d OFFSET %d
			", $user_id, '%' . $wpdb->esc_like($search) . '%', $limit, $offset)
	);

	$fav_media_count = $wpdb->get_var(
		$wpdb->prepare("
				SELECT COUNT(*)
				FROM pim_favorites f
				INNER JOIN pim_media m ON f.item_id = m.id
				WHERE f.user_id = %d AND f.item_type = 'media' AND m.title LIKE %s
		", $user_id, '%' . $wpdb->esc_like($search) . '%')
);

	$results = array_merge($product_favs, $media_favs);
	$total_fav_items_count = $fav_prods_count + $fav_media_count;

	} elseif($item_type == 'product') {

		$results = $wpdb->get_results(
			$wpdb->prepare("
					SELECT p.*, f.item_type
					FROM pim_favorites f
					INNER JOIN pim_products p ON f.item_id = p.id
					WHERE f.user_id = %d AND f.item_type = 'product' AND p.title LIKE %s
					LIMIT %d OFFSET %d
			", $user_id, '%' . $wpdb->esc_like($search) . '%', $limit, $offset)
	);

	$total_fav_items_count = $wpdb->get_var(
		$wpdb->prepare("
				SELECT COUNT(*)
				FROM pim_favorites f
				INNER JOIN pim_products p ON f.item_id = p.id
				WHERE f.user_id = %d AND f.item_type = 'product' AND p.title LIKE %s
		", $user_id, '%' . $wpdb->esc_like($search) . '%')
);

	} elseif($item_type == 'media') {

		$results = $wpdb->get_results(
			$wpdb->prepare("
					SELECT m.*, f.item_type
					FROM pim_favorites f
					INNER JOIN pim_media m ON f.item_id = m.id
					WHERE f.user_id = %d AND f.item_type = 'media' AND m.title LIKE %s
					LIMIT %d OFFSET %d
			", $user_id, '%' . $wpdb->esc_like($search) . '%', $limit, $offset)
	);

	$total_fav_items_count = $wpdb->get_var(
		$wpdb->prepare("
				SELECT COUNT(*)
				FROM pim_favorites f
				INNER JOIN pim_media m ON f.item_id = m.id
				WHERE f.user_id = %d AND f.item_type = 'media' AND m.title LIKE %s
		", $user_id, '%' . $wpdb->esc_like($search) . '%')
);

	}

		// if(empty($media_type) || $media_type == 'all') {






			// $results = $this->fetch_favs_from_db($limit, $offset, $item_type);

			// $results = $wpdb->get_results("SELECT * FROM bui_pods_favorites WHERE (brand_id = 87 OR brand_id = 1) AND is_new = 1");

			if (!empty($results)) {

				// $allowed_types = ['media', 'product'];
			// Check if each product item is a favorite
			foreach($results as &$res) {

				// if(in_array($res->item_type, $allowed_types)) {


					// $table_name = $res->item_type == 'product' ? 'pim_products' : 'pim_media';

					// $item = $this->get_full_row_from_table($table_name, $res->item_id);
					// global $wpdb;



						// $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table_name` WHERE id = %d",  $res->item_id));




					// $res->title = $item->title;
					// $res->new_id = $item->id;

					// if($item_type == 'product') {
					// 	$res->main_image = $this->get_media_url($res->main_image, 'thumbnail');
					// 	// die();
					// } elseif($item_type == 'media') {
					// 	$res->main_image = $this->get_media_url($res->thumb_url, 'thumbnail');
					// }

					$res->main_image = $this->get_media_url($res->thumb_url ? $res->thumb_url : $res->main_image, 'thumbnail');


					$favorite_count = $wpdb->get_var($wpdb->prepare(
						"SELECT COUNT(*) FROM pim_favorites WHERE user_id = %d AND item_id = %d AND item_type = %s AND brand_id = %d",
						$user_id, $res->id, $res->item_type, $res->brand_id
					));
					$res->is_favorite = $favorite_count > 0;
					// $res->main_image = $this->get_media_url($res->main_image, 'thumbnail');

				// }

				// if($res->item_type == 'media') {

				// 	$media_item = $this->get_full_row_from_table('pim_media', $res->new_id);
				// 	$res->main_image = $this->get_media_url($media_item->thumb_url, 'thumbnail');

				// 	$favorite_count = $wpdb->get_var($wpdb->prepare(
				// 		"SELECT COUNT(*) FROM bui_pods_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'media' AND brand_id = 1 OR brand_id = 87",
				// 		$user_id, $media_item->id
				// 	));
				// 	$res->is_favorite = $favorite_count > 0;
				// 	// $res->main_image = $this->get_media_url($res->main_image, 'thumbnail');

				// } elseif(($res->item_type == 'product')) {

				// 	$product = $this->get_full_row_from_table('pim_products', $res->new_id);
				// 	$res->main_image = $this->get_media_url($product->main_image, 'thumbnail');

				// 	$favorite_count = $wpdb->get_var($wpdb->prepare(
				// 		"SELECT COUNT(*) FROM bui_pods_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'product' AND brand_id = 1 OR brand_id = 87",
				// 		$user_id, $product->id
				// 	));
				// 	$res->is_favorite = $favorite_count > 0;

				// }

			}
		// }

		}


// 	function test_odd($item) {
//    return $item->
//   }

// $a1=array(1,3,2,3,4);
// print_r();


	// if($item_type === 'media' && $media_type !== 'all' && !empty($media_type)) {
	// 	$media_data = $this->fetch_media_from_db($limit, $offset, $media_type);

	// 	foreach ($media_data['media_items'] as &$media_item) {
	// 		// $favorite_count = $wpdb->get_var($wpdb->prepare(
	// 		// 	"SELECT COUNT(*) FROM bui_pods_favorites WHERE user_id = %d AND item_id = %d AND item_type = 'media' AND brand_id = 1",
	// 		// 	$user_id, $media_item['id']
	// 		// ));
	// 		// $media_item['is_favorite'] = $favorite_count > 0;
	// 		$media_item['main_image'] = $this->get_media_url($media_item['thumb_url'], 'thumbnail');;
	// 		// $media_item['source_url'] = $favorite_count > 0;

	// 	}

	// 	wp_send_json_success([
	// 		'fav_items' => $media_data['media_items'],
	// 		'total_fav_items_count' => count($media_data['media_items']),
	// ]);

	// die();
	// 	// $results['results'] = array_filter($item,"filter_wrt_media_type");
	// }

		wp_send_json_success([
				'fav_items' => $results,
				'total_fav_items_count' => $total_fav_items_count,
				// 'media_favs' => $media_favs ,
				// 'product_favs' => $product_favs
		]);
	}


	function fetch_favs_from_db($limit = 28, $offset = 0, $item_type = 'all') {
		global $wpdb;

		$table_name = 'pim_favorites';

		if ($item_type == '0' || $item_type === 'all') {

			$results = $wpdb->get_results("SELECT * FROM $table_name LIMIT $limit OFFSET $offset");

	} elseif($item_type == 'product' || $item_type == 'media') {

		$results = $wpdb->get_results("SELECT * FROM $table_name WHERE item_type = '$item_type' LIMIT $limit OFFSET $offset");

	}

		// 0 for "All"
		// if ($item_type == '0' || $item_type === 'all') {

		// 		$results = $wpdb->get_results("SELECT * FROM $table_name WHERE (brand_id = 87 OR brand_id = 1) AND is_new = 1 LIMIT $limit OFFSET $offset");

		// } elseif($item_type === 'product') {

		// 	$results = $wpdb->get_results("SELECT * FROM $table_name WHERE (brand_id = 87 OR brand_id = 1) AND is_new = 1 AND item_type = 'product' LIMIT $limit OFFSET $offset");

		// } elseif($item_type === 'media') {

		// 	$results = $wpdb->get_results("SELECT * FROM $table_name WHERE (brand_id = 87 OR brand_id = 1) AND is_new = 1 AND item_type = 'media' LIMIT $limit OFFSET $offset");

		// }

		return [
				'results' => $results,
				'total_media_count' => count($results),
		];
	}

	public function get_specific_cols_from_table($table_name, $columns, $record_id) {
		global $wpdb;
		isset($columns) && is_array($columns) ? $columns = implode(',', $columns) : '';
		isset($record_id) ? intval($record_id) : '';

		// return  $wpdb->get_row($wpdb->prepare("SELECT $columns FROM $table_name"));
		return  $wpdb->get_row("SELECT $columns FROM $table_name");

	}


	public function get_specific_rows_from_table($table_name, $key, $key_value) {
		global $wpdb;

		$placeholder = gettype($key) == 'integer' ? '%s' : (gettype($key) == 'string' ? '%d' : null);

		if($placeholder === null) return null;
		// return  $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE $key = '$key_value'"));
		return  $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE $key = $placeholder", $key_value));

	}

	// public function get_specific_row_from_table($table_name, $key, $key_value) {
	// 	global $wpdb;

	// 	return  $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE $key = '$key_value'"));

	// }
	public function get_specific_row_from_table($table_name, $key, $key_value) {
    global $wpdb;

    $placeholder = is_int($key_value) ? '%d' : '%s';

    $query = $wpdb->prepare("SELECT * FROM `$table_name` WHERE `$key` = $placeholder", $key_value);

    return $wpdb->get_row($query);
}


	public function get_full_row_from_table($table_name, $record_id) {
		global $wpdb;
		isset($record_id) ? intval($record_id) : '';

		return  $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table_name` WHERE id = %d", $record_id));
	}

	public function get_media_record($record_id) {
		global $wpdb;
		isset($record_id) ? intval($record_id) : '';

		$result =  $wpdb->get_row($wpdb->prepare("SELECT * FROM pim_media WHERE id = %d", $record_id));

		if($result) {
			$result->thumb_url = $this->get_media_url($result->thumb_url, 'thumbnail');
			$result->source_url = $this->media_url_prefix . $result->source_url;
		}


		return $result;
	}

	public function get_specific_cols_from_row_of_table($table_name, $columns, $record_id) {

		global $wpdb;
		isset($columns) && is_array($columns) ? $columns = implode(',', $columns) : '';
		isset($record_id) ? intval($record_id) : '';

		return  $wpdb->get_row($wpdb->prepare("SELECT $columns FROM $table_name WHERE id = %d", $record_id));
	}

	public function get_var_from_table($table_name, $columns, $record_id) {
		global $wpdb;
		isset($columns) && is_array($columns) ? $columns = implode(',', $columns) : '';
		isset($record_id) ? intval($record_id) : '';

		return  $wpdb->get_var($wpdb->prepare("SELECT $columns FROM $table_name WHERE id = %d", $record_id));
	}

	public function get_var_like($name) {
		global $wpdb;
		return $wpdb->get_var("SHOW TABLES LIKE '$name'");
	}

	public function ajax_delete_media() {
		check_ajax_referer('delete_media_nonce', '_wpnonce');

    $media_id = intval($_POST['media_id']);

    if (!$media_id) {
        wp_send_json_error(['message' => 'Invalid media ID']);
    }

		$result = $this->delete_row_from_table($media_id, 'pim_media');

		if($result) {
			wp_send_json_success(['message' => 'Media deleted successfully']);
		} else {
			wp_send_json_error(['message' => 'Error deleting media']);
		}
	}

	public function ajax_refresh_media_html() {
		check_ajax_referer('refresh_media_html_nonce', '_wpnonce');

		$prod_id = intval($_POST['product_id']);

		if(! $prod_id) {
			wp_send_json_error(['message' => 'Invalid product ID']);
		}

		ob_start();

		$this->display_media_fields($prod_id);

		$media_html = ob_get_clean();

    wp_send_json_success(['message' => 'Media refreshed successfully', "media_html" => $media_html]);
	}

	public function ajax_get_product_information() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error(['message' => 'Product ID not provided.']);
    }

    $product_id = intval($_POST['product_id']);
		// $stm = SettingsManager::GI();


    // Generate the field groups structure
    $field_groups_structure = $this->generate_field_groups_structure($product_id)['field_groups'];


    // Start output buffering to capture the HTML output
    ob_start();
// $this->dump($this->generate_field_groups_structure($product_id));
    // Generate the tabs HTML
    ?>
<ul class="tabs">
  <?php $this->generate_html_tabs_for_each_table_meta($field_groups_structure); ?>
</ul>

<?php

				// Get the captured HTML
				$product_specs_html = ob_get_clean();



 ob_start();

 $this->generate_html_fields_for_each_table_meta($product_id, $field_groups_structure);

$tabs_content_html = ob_get_clean();



    wp_send_json_success(['product_specs_html' => $product_specs_html, 'tabs_content_html' => $tabs_content_html]);
}

public function ajax_get_latest_revisions() {
	global $wpdb;
	$product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : '';

	if (empty($product_id)) {
			wp_send_json_error(['message' => 'No product ID provided']);
	}

	$revisions = $wpdb->get_results($wpdb->prepare("
			SELECT * FROM pim_revisions WHERE related_item_id = %d ORDER BY date_time DESC
	", $product_id), ARRAY_A);

	if (!$revisions) {
			wp_send_json_error(['message' => 'No revisions found']);
	}

	wp_send_json_success(['revisions' => $revisions]);
}

public function ajax_restore_revision() {
	global $wpdb;

	if (!isset($_POST['revision_id'])) {
			wp_send_json_error(['message' => 'No revision ID provided']);
	}

	$revision_id = sanitize_text_field($_POST['revision_id']);
	$revision = $wpdb->get_row($wpdb->prepare("SELECT * FROM pim_revisions WHERE id = %d", $revision_id), ARRAY_A);

	if (!$revision) {
			wp_send_json_error(['message' => 'Revision not found']);
	}

	$previous_values = json_decode($revision['previous_value'], true);
	$related_item_id = $revision['related_item_id'];
	$field_group_name = $previous_values['field_group_name'];
	$fields = $previous_values['fields'];

	$dynamic_table_name = 'pim_ar_' . $field_group_name;

	// Start a transaction
	$wpdb->query('START TRANSACTION');

	$existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$dynamic_table_name}` WHERE `product_id` = %d", $related_item_id), ARRAY_A);

	// $old_data = [];
	// foreach ($fields as $field_name => $field_data) {
	// 	$old_data[$field_name] = $existing_record[$field_name];
	// }
	$old_data = [];
	foreach ($fields as $field_id => $field_data) {
		$field_name = $field_data['field_name'];
		$old_data[$field_id] = $existing_record[$field_name];
	}

	$this->record_revision($old_data, $field_group_name, $related_item_id);

	// update (restore old one) data/values in dynamic table
	$update_data = [];
	foreach ($fields as $field_id => $field_data) {
		$field_name = $field_data['field_name'];
		$field_names[] = $field_name;
		$update_data[$field_name] = $field_data['previous_value'];
	}

	$result = $wpdb->update($dynamic_table_name, $update_data, ['product_id' => $related_item_id]);

	if ($result !== false) {
			$wpdb->query('COMMIT');
			wp_send_json_success(['message' => 'Revision restored successfully']);
	} else {
			$wpdb->query('ROLLBACK');
			wp_send_json_error(['message' => 'Failed to restore revision']);
	}
}

public function ajax_save_dynamic_table_data() {
	global $wpdb;
	// $stm = SettingsManager::GI();

	if (isset($_POST['dynamic_table_name']) && isset($_POST['product_id'])) {
			$dynamic_table_name = sanitize_text_field($_POST['dynamic_table_name']);
			$product_id = sanitize_text_field($_POST['product_id']);

			$table_meta_name = isset($_POST['table_meta_name']) && $_POST['table_meta_name'] ? sanitize_text_field($_POST['table_meta_name']) : str_replace('pim_ar_', '', $dynamic_table_name);

			$data = [];
			$revision_data = [];
			$default_values = [];
			$field_ids = [];

			// Get the field configurations for the current table
			$fields = $wpdb->get_results($wpdb->prepare("
					SELECT fm.id, fm.field_name, fm.user_defined_type, fm.config
					FROM pim_table_metas tm
					LEFT JOIN pim_field_metas fm ON tm.id = fm.table_meta_id
					WHERE tm.table_name = %s
			", $table_meta_name), ARRAY_A);

			foreach ($fields as $field) {

				$field_name = $field['field_name'];
				$field_id = $field['id'];
				$field_options = json_decode($field['config'], true)['field_options'] ?? [];
				$type = $field['user_defined_type'];
				$field_type_info = $this->get_mapped_type($field_options)[$type];

				// Set default values for each field
				$default_values[$field_id] = $field_type_info['default'];
				// $default_values[$field_name] = $field_type_info['default'];
				$field_ids[$field_name] = $field['id'];
			}

			foreach ($_POST as $key => $value) {
					if ($key !== 'dynamic_table_name' && $key !== 'action' && $key !== 'product_id') {
							// $value = sanitize_text_field($value);
							// $value = sanitize_textarea_field($value);
							$value = htmlspecialchars($value);

// $i = 0;
// while($i < 10) {
// 		stripslashes($value);
// 	$i++;
// }

// $value = json_decode('"' . $value . '"');

							// check field permissions
							if($this->ck_perm('fields', $field_ids[$key]) != 'EDIT') {
								continue;
							}

							// $query = $wpdb->prepare("SELECT id FROM pim_field_metas WHERE `field_name` = %s", $key);
							// $field_id = $wpdb->get_var($query);
							$f_id = $field_ids[$key];
							// $revision_data[$f_id] = empty($value) ? $default_values[$f_id] : $value;
							$data[$key] = empty($value) ? $default_values[$f_id] : $value;
							// $data[$key] = empty($value) ? $default_values[$key] : $value;



							// $data[$key] = empty($value) ? null : $value;
					}
			}

			// Start a transaction
			$wpdb->query('START TRANSACTION');

			$table_meta_name = str_replace('pim_ar_', '', $dynamic_table_name);

			// Check if the record with this product_id already exists
			$existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$dynamic_table_name}` WHERE `product_id` = %d", $product_id), ARRAY_A);

			foreach($existing_record as $field_name => $field_value) {
				$default_keys = ['product_id', 'id', 'created_at', 'updated_at'];
				if(in_array($field_name, $default_keys)) continue;
				$table_meta_id = $wpdb->get_var($wpdb->prepare("SELECT id from `pim_table_metas` WHERE table_name = %s", $table_meta_name));
				$field_id = $wpdb->get_var($wpdb->prepare("SELECT id from `pim_field_metas` WHERE field_name = %s AND table_meta_id = %d", $field_name, $table_meta_id));
				// $field_id = $wpdb->get_var($wpdb->prepare("SELECT id from `pim_field_metas` WHERE field_name = %s AND table_name = %s", $table_meta_name));
				if($field_id) {
					$revision_data[$field_id] = $field_value;
				}
			}


			// $old_data = [];
			// $table_metas = [];
			// foreach ($existing_record as $field_name => $field_data) {
			// 	$field_id = $wpdb->get_var("SELECT id, table_meta_id FROM pim_field_metas WHERE field_name = $field_name");
			// 	$table_metas[] = $field_id['table_meta_id'];
			// 	if()
			// 	$old_data[$field_id] = $existing_record[$field_name];
			// }

			// no matter we have to update or insert new data to an empty field,  we will record revision
			$this->record_revision($existing_record, $table_meta_name, $product_id, $revision_data);

			// set dirty col value in db (pim_table_metas) to 1
			$this->update_dirty_att_of_product($product_id);


			// if(sizeof($data)  > 0) {

				// if ($existing_record) {

				// 		// Update existing record in dynamic table
				// 		$where = ['product_id' => $product_id];
				// 		$result = $wpdb->update($dynamic_table_name, $data, $where);
				// } else {
				// 		// Insert new record
				// 		$data['product_id'] = $product_id; // Ensure product_id is included in the insert data
				// 		$result = $wpdb->insert($dynamic_table_name, $data);
				// }
				if (!empty($data)) {  // Check if there's any data to update
					if ($existing_record) {
							// Update existing record in dynamic table
							$where = ['product_id' => $product_id];
							$result = $wpdb->update($dynamic_table_name, $data, $where);
					} else {
							// Insert new record
							$data['product_id'] = $product_id; // Ensure product_id is included in the insert data
							$result = $wpdb->insert($dynamic_table_name, $data);
					}
			} else {
					// Handle case where there is no data to update
					wp_send_json_error(['message' => 'No fields to update']);
					return;
			}



			// } else {
			// 	wp_send_json_success(['message' => 'No Fields to update']);
			// 	die();
			// }



			// Commit or rollback the transaction
			if ($result !== false) {
					$wpdb->query('COMMIT');
					wp_send_json_success(['message' => 'Permitted Data saved successfully']);
			} else {
					$wpdb->query('ROLLBACK');
					wp_send_json_error(['message' => 'Failed to save data']);
			}
	} else {
			wp_send_json_error(['message' => 'Dynamic Table name or product ID not provided']);
	}
}
// public function ajax_save_dynamic_table_data() {
// 	global $wpdb;

// 	if (isset($_POST['dynamic_table_name']) && isset($_POST['product_id'])) {
// 		$dynamic_table_name = sanitize_text_field($_POST['dynamic_table_name']);
// 		$product_id = sanitize_text_field($_POST['product_id']);

// 		$table_meta_name = isset($_POST['table_meta_name']) && $_POST['table_meta_name'] ? sanitize_text_field($_POST['table_meta_name']) : str_replace('pim_ar_', '', $dynamic_table_name);

// 		$data = [];
// 		$default_values = [];
// 		$field_ids = [];

// 		// Get the field configurations for the current table
// 		$fields = $wpdb->get_results($wpdb->prepare("
// 				SELECT fm.id, fm.field_name, fm.user_defined_type, fm.config
// 				FROM pim_table_metas tm
// 				LEFT JOIN pim_field_metas fm ON tm.id = fm.table_meta_id
// 				WHERE tm.table_name = %s
// 		", $table_meta_name), ARRAY_A);

// 		foreach ($fields as $field) {
// 			$field_name = $field['field_name'];
// 			$field_options = json_decode($field['config'], true)['field_options'] ?? [];
// 			$type = $field['user_defined_type'];
// 			$field_type_info = $this->get_mapped_type($field_options)[$type];

// 			// Set default values for each field
// 			$default_values[$field_name] = $field_type_info['default'];
// 			$field_ids[$field_name] = $field['id'];
// 		}

// 		foreach ($_POST as $key => $value) {
// 			if ($key !== 'dynamic_table_name' && $key !== 'action' && $key !== 'product_id') {
// 				// Use sanitize_textarea_field to allow quotes and special characters
// 				$value = sanitize_textarea_field($value);

// 				// Check field permissions
// 				if($this->ck_perm('fields', $field_ids[$key]) != 'EDIT') {
// 					continue;
// 				}

// 				$data[$key] = empty($value) ? $default_values[$key] : $value;
// 			}
// 		}

// 		// Start a transaction
// 		$wpdb->query('START TRANSACTION');

// 		// Check if the record with this product_id already exists
// 		$existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$dynamic_table_name}` WHERE `product_id` = %d", $product_id), ARRAY_A);

// 		// Record revision
// 		$this->record_revision($existing_record, $table_meta_name, $product_id, $data);

// 		// Set dirty column value in the database (pim_table_metas) to 1
// 		$this->update_dirty_att_of_product($product_id);

// 		// Insert or update the record
// 		if ($existing_record) {
// 			$where = ['product_id' => $product_id];
// 			$result = $wpdb->update($dynamic_table_name, $data, $where);
// 		} else {
// 			$data['product_id'] = $product_id;
// 			$result = $wpdb->insert($dynamic_table_name, $data);
// 		}

// 		// Commit or rollback the transaction
// 		if ($result !== false) {
// 			$wpdb->query('COMMIT');
// 			wp_send_json_success(['message' => 'Permitted Data saved successfully']);
// 		} else {
// 			$wpdb->query('ROLLBACK');
// 			wp_send_json_error(['message' => 'Failed to save data']);
// 		}
// 	} else {
// 		wp_send_json_error(['message' => 'Dynamic Table name or product ID not provided']);
// 	}
// }
// public function ajax_save_dynamic_table_data() {
// 	global $wpdb;

// 	if (isset($_POST['dynamic_table_name']) && isset($_POST['product_id'])) {
// 			$dynamic_table_name = sanitize_text_field($_POST['dynamic_table_name']);
// 			$product_id = sanitize_text_field($_POST['product_id']);

// 			$table_meta_name = isset($_POST['table_meta_name']) && $_POST['table_meta_name'] ? sanitize_text_field($_POST['table_meta_name']) : str_replace('pim_ar_', '', $dynamic_table_name);

// 			$data = [];
// 			$default_values = [];
// 			$field_ids = [];

// 			// Get the field configurations for the current table
// 			$fields = $wpdb->get_results($wpdb->prepare("
// 					SELECT fm.id, fm.field_name, fm.user_defined_type, fm.config
// 					FROM pim_table_metas tm
// 					LEFT JOIN pim_field_metas fm ON tm.id = fm.table_meta_id
// 					WHERE tm.table_name = %s
// 			", $table_meta_name), ARRAY_A);

// 			foreach ($fields as $field) {
// 					$field_name = $field['field_name'];
// 					$field_options = json_decode($field['config'], true)['field_options'] ?? [];
// 					$type = $field['user_defined_type'];
// 					$field_type_info = $this->get_mapped_type($field_options)[$type];

// 					// Set default values for each field
// 					$default_values[$field_name] = $field_type_info['default'];
// 					$field_ids[$field_name] = $field['id'];
// 			}

// 			foreach ($_POST as $key => $value) {
// 					if ($key !== 'dynamic_table_name' && $key !== 'action' && $key !== 'product_id') {
// 							// Sanitize textarea field which includes handling of single and double quotes
// 							$value = sanitize_textarea_field($value);

// 							// Check field permissions
// 							if ($this->ck_perm('fields', $field_ids[$key]) != 'EDIT') {
// 									continue;
// 							}

// 							// Escape single quotes to prevent JSON errors
// 							$escaped_value = str_replace("'", "\'", $value);

// 							// Apply default value if the field is empty
// 							$data[$key] = empty($escaped_value) ? $default_values[$key] : $escaped_value;
// 					}
// 			}

// 			// Start a transaction
// 			$wpdb->query('START TRANSACTION');

// 			// Check if the record with this product_id already exists
// 			$existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$dynamic_table_name}` WHERE `product_id` = %d", $product_id), ARRAY_A);

// 			// Record revision
// 			$this->record_revision($existing_record, $table_meta_name, $product_id, $data);

// 			// Set dirty col value in db (pim_table_metas) to 1
// 			$this->update_dirty_att_of_product($product_id);

// 			if ($existing_record) {
// 					// Update existing record
// 					$where = ['product_id' => $product_id];
// 					$result = $wpdb->update($dynamic_table_name, $data, $where);
// 			} else {
// 					// Insert new record
// 					$data['product_id'] = $product_id;
// 					$result = $wpdb->insert($dynamic_table_name, $data);
// 			}

// 			// Commit or rollback the transaction
// 			if ($result !== false) {
// 					$wpdb->query('COMMIT');
// 					wp_send_json_success(['message' => 'Permitted Data saved successfully']);
// 			} else {
// 					$wpdb->query('ROLLBACK');
// 					wp_send_json_error(['message' => 'Failed to save data']);
// 			}
// 	} else {
// 			wp_send_json_error(['message' => 'Dynamic Table name or product ID not provided']);
// 	}
// }
// public function ajax_save_dynamic_table_data() {
// 	global $wpdb;

// 	if (isset($_POST['dynamic_table_name']) && isset($_POST['product_id'])) {
// 			$dynamic_table_name = sanitize_text_field($_POST['dynamic_table_name']);
// 			$product_id = sanitize_text_field($_POST['product_id']);

// 			$table_meta_name = isset($_POST['table_meta_name']) && $_POST['table_meta_name'] ? sanitize_text_field($_POST['table_meta_name']) : str_replace('pim_ar_', '', $dynamic_table_name);

// 			$data = [];
// 			$default_values = [];
// 			$field_ids = [];

// 			// Get the field configurations for the current table
// 			$fields = $wpdb->get_results($wpdb->prepare("
// 					SELECT fm.id, fm.field_name, fm.user_defined_type, fm.config
// 					FROM pim_table_metas tm
// 					LEFT JOIN pim_field_metas fm ON tm.id = fm.table_meta_id
// 					WHERE tm.table_name = %s
// 			", $table_meta_name), ARRAY_A);

// 			foreach ($fields as $field) {
// 					$field_name = $field['field_name'];
// 					$field_options = json_decode($field['config'], true)['field_options'] ?? [];
// 					$type = $field['user_defined_type'];
// 					$field_type_info = $this->get_mapped_type($field_options)[$type];

// 					// Set default values for each field
// 					$default_values[$field_name] = $field_type_info['default'];
// 					$field_ids[$field_name] = $field['id'];
// 			}

// 			foreach ($_POST as $key => $value) {
// 					if ($key !== 'dynamic_table_name' && $key !== 'action' && $key !== 'product_id') {
// 							// Sanitize textarea field which includes handling of single and double quotes
// 							// Use wp_unslash() to remove any unnecessary slashes before saving
// 							$value = wp_unslash($value);
// 							$value = sanitize_textarea_field($value);

// 							// Check field permissions
// 							if ($this->ck_perm('fields', $field_ids[$key]) != 'EDIT') {
// 									continue;
// 							}

// 							// Apply default value if the field is empty
// 							$data[$key] = empty($value) ? $default_values[$key] : $value;
// 					}
// 			}

// 			// Start a transaction
// 			$wpdb->query('START TRANSACTION');

// 			// Check if the record with this product_id already exists
// 			$existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$dynamic_table_name}` WHERE `product_id` = %d", $product_id), ARRAY_A);

// 			// Record revision
// 			$this->record_revision($existing_record, $table_meta_name, $product_id, $data);

// 			// Set dirty col value in db (pim_table_metas) to 1
// 			$this->update_dirty_att_of_product($product_id);

// 			if ($existing_record) {
// 					// Update existing record
// 					$where = ['product_id' => $product_id];
// 					$result = $wpdb->update($dynamic_table_name, $data, $where);
// 			} else {
// 					// Insert new record
// 					$data['product_id'] = $product_id;
// 					$result = $wpdb->insert($dynamic_table_name, $data);
// 			}

// 			// Commit or rollback the transaction
// 			if ($result !== false) {
// 					$wpdb->query('COMMIT');
// 					wp_send_json_success(['message' => 'Permitted Data saved successfully']);
// 			} else {
// 					$wpdb->query('ROLLBACK');
// 					wp_send_json_error(['message' => 'Failed to save data']);
// 			}
// 	} else {
// 			wp_send_json_error(['message' => 'Dynamic Table name or product ID not provided']);
// 	}
// }



public function record_revision($existing_record, $table_meta_name, $product_id, $newData = null, $related_item_type = "PRODUCT") {
	global $wpdb;
error_log("preparing revisons");
	// Prepare previous values
	$previous_values = [
		"fields" => [],
		"field_group_name" => '',
		"restored" => false
	];
// 	foreach (($newData ? $newData : $existing_record) as $field_name => $value) {
// 		// used PHP heredoc
// 		$val = <<<STR
// $existing_record[$field_name]
// STR;
// 					$previous_values["fields"][$field_name] = [
// 							// 'previous_value' => json_encode(htmlspecialchars($existing_record[$field_name]))
// 							// 'previous_value' => json_encode(htmlspecialchars($val))
// 							'previous_value' => $val,
// 							'field_name' => $field_name
// 					];

// 					$previous_values["field_group_name"] = $table_meta_name;
// 	}
foreach (($newData ? $newData : $existing_record) as $field_id => $value) {
	// used PHP heredoc
	$val = <<<STR
$existing_record[$field_id]
STR;

$newVal = <<<STR
$newData[$field_id]
STR;

$val = empty($val) ? $newVal : $val;

				$previous_values["fields"][$field_id] = [
						// 'previous_value' => json_encode(htmlspecialchars($existing_record[$field_name]))
						// 'previous_value' => json_encode(htmlspecialchars($val))
						'previous_value' => $val,
						'field_name' => $wpdb->get_var("SELECT field_name FROM pim_field_metas WHERE id = $field_id")
				];

				$previous_values["field_group_name"] = $table_meta_name;
}


// $enc_value = json_encode($previous_values);

	// Save the revision if there are any changes
	if (!empty($previous_values)) {
			$wpdb->insert('pim_revisions', [
					'related_item_id' => $product_id,
					'related_item_type' => $related_item_type,
					// 'related_item_type' => $related_item_type ?? 'PRODUCT',
					'date_time' => current_time('mysql'),
					// 'previous_value' => stripslashes(json_encode($previous_values)),
					// 'previous_value' => wp_json_encode($previous_values),
					'previous_value' => json_encode($previous_values),

			]);
			error_log("revion added");
	}

	// $this->update_dirty_att_of_product($product_id);
// global $wpdb;
// 	$result = $wpdb->query($wpdb->prepare(
// 		"UPDATE `pim_products` SET `is_dirty` = %d WHERE `id` = %d", 1, $product_id
// ));

}

public function update_dirty_att_of_product($product_id) {
	global $wpdb;

	// Ensure product_id is properly sanitized and cast to integer
	$product_id = (int) $product_id;

	// Log product id
	error_log("product id: " . $product_id);

	// Run the query and log the result
	$result = $wpdb->query($wpdb->prepare(
			"UPDATE pim_products SET is_dirty = %d WHERE id = %d", 1, $product_id
	));

	// Log query result
	error_log("Query result: " . var_export($result, true));

	// Log any database errors
	if (false === $result) {
			error_log("DB error: " . $wpdb->last_error);
	}

	// Log confirmation message
	error_log("Update dirty flag complete");
}


// public function update_dirty_att_of_product($product_id) {
// 	global $wpdb;
// 	// setting is  dirty to true
// 	$product_id = (int) $product_id;
// 	error_log("product id: ".$product_id);
// 	// $wpdb->query("UPDATE pim_products SET is_dirty = 1 WHERE id = $product_id");
// 	$result = $wpdb->query($wpdb->prepare("UPDATE pim_products SET is_dirty = %d WHERE id = %d", 1, $product_id));
// 	error_log( $result );
// 	error_log( "___________________________________abhi9" );
// }

	public function get_mapped_type($field_options) {
    // Map user-defined types to SQL types and define default values
    return [
        'text' => ['type' => 'VARCHAR(255)', 'default' => ""],
        'number' => ['type' => 'INT', 'default' => null],
        'textarea' => ['type' => 'TEXT', 'default' => ""],
        // 'currency' => ['type' => 'VARCHAR(255)', 'default' => ""],
        'select' => ['type' => 'ENUM(' . implode(',', array_map(function($item) { return "'$item'"; }, $field_options)) . ')', 'default' => null],
        'multi_select' => ['type' => 'SET(' . implode(',', array_map(function($item) { return "'$item'"; }, $field_options)) . ')', 'default' => ""],
        'range' => ['type' => 'INT', 'default' => ""],
        'radio' => ['type' => 'VARCHAR(255)', 'default' => null],
        'checkbox' => ['type' => 'VARCHAR(255)', 'default' => null],
				'products' => ['type' => 'VARCHAR(255)', 'default' => null],
				'dimension' => ['type' => 'INT', 'default' => null],
				'currency' => ['type' => 'INT', 'default' => null],
				'weight' => ['type' => 'INT', 'default' => null],
				'volume' => ['type' => 'INT', 'default' => null],
				'repeater' => ['type' => 'VARCHAR(255)', 'default' => null],

        // 'checkbox' => ['type' => 'ENUM(' . implode(',', array_map(function($item) { return "'$item'"; }, $field_options)) . ')', 'default' => null]
        // 'radio' => ['type' => 'ENUM(' . implode(',', array_map(function($item) { return "'$item'"; }, $field_options)) . ')', 'default' => null],
        // 'checkbox' => ['type' => 'BOOLEAN', 'default' => '0']
    ];
}

	public function get_pim_product_type_revisions($related_item_id) {
    global $wpdb;
    $revisions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM pim_revisions WHERE related_item_id = %d AND related_item_type = 'PRODUCT' ORDER BY date_time DESC",
        $related_item_id
    ), ARRAY_A);
    return $revisions;
	}


	public function check_if_column_exists($field_name, $dynamic_table_name) {
		global $wpdb;
		return $wpdb->get_var(
			$wpdb->prepare("SHOW COLUMNS FROM `$dynamic_table_name` LIKE %s", $field_name)
		);
	}

	public function delete_column_from_table($field_name, $dynamic_table_name) {
		global $wpdb;
		return $wpdb->query("ALTER TABLE `$dynamic_table_name` DROP COLUMN `$field_name`");
	}

	public function add_column_to_table($field_name, $dynamic_table_name, $field_sql_type) {
		global $wpdb;
		return $wpdb->query("ALTER TABLE `$dynamic_table_name` ADD `$field_name` $field_sql_type");
	}

	public function create_new_dynamic_table_with_new_field($field_name, $dynamic_table_name, $field_sql_type) {
		global $wpdb;
		return $wpdb->query("CREATE TABLE `$dynamic_table_name` (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id INT UNSIGNED,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`$field_name` $field_sql_type NULL,
			PRIMARY KEY (id)
		)");
	}

	public function rename_column_of_table($current_field_name, $new_field_name, $col_type, $dynamic_table_name) {
		global $wpdb;
		return $wpdb->query(
			// $wpdb->prepare(
					"ALTER TABLE `$dynamic_table_name` CHANGE `$current_field_name` `$new_field_name` $col_type"
			// )
	);
	}

	public function delete_row_from_table($row_id, $table_name) {
		global $wpdb;
		return $wpdb->delete($table_name, array('id' => $row_id));
	}



	public function update_row_of_table($table_name, $data, $conditions) {
    global $wpdb;
		$result = $wpdb->update($table_name, $data, $conditions);
    return $result;
}


	public function display_taxonomies_and_terms_tree($tax_id) {
    global $wpdb;

    // Step 1: Retrieve Taxonomies and Taxonomy Terms from the Database
    // $taxonomies = $wpdb->get_results("
    //     SELECT id, name, slug, `desc`, brand_id
    //     FROM pim_taxonomies
    // ", ARRAY_A);

		$taxonomy_terms = $wpdb->get_results($wpdb->prepare("
		SELECT id, tax_id, parent_id, name, slug, level, icon
		FROM pim_taxonomy_terms
		WHERE tax_id = %d
		", $tax_id), ARRAY_A);

    // Step 2: Build the Hierarchical Structure
    function buildCategoryTree(array $elements, $parentId = 0) {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = buildCategoryTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    $categoryTree = buildCategoryTree($taxonomy_terms);

    // Step 3: Display the Hierarchical Structure
    function renderCategoryTree($categoryTree) {
        echo '<ul>';
        foreach ($categoryTree as $category) {
            echo '<li>' . $category['name'];
            if (!empty($category['children'])) {
                renderCategoryTree($category['children']);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    renderCategoryTree($categoryTree);
}


	public function insert_into_table($table_name, $data) {
    global $wpdb;

    // Validate inputs
    if (empty($table_name) || !is_array($data) || empty($data)) {
        return false; // Invalid input
    }

    // Insert data into the table
    $result = $wpdb->insert(
        $table_name,
        $data
    );

    // Return the result of the insert operation
    return [
			'done' => $result !== false,
			'id' => $wpdb->insert_id
		];
	}

	public function create_new_dynamic_table_or_add_new_column($field_name, $field_sql_type, $dynamic_table_name) {
		// check if it exists/true
    if (isset($field_sql_type)) {
			if ($this->check_if_dynamic_table_exists($dynamic_table_name)) {
					// Add new field to the existing dynamic table
					$this->add_column_to_table($field_name, $dynamic_table_name, $field_sql_type);
			} else {
					// Create new dynamic table with the new field
					$this->create_new_dynamic_table_with_new_field($field_name, $dynamic_table_name, $field_sql_type);
			}
		} else {
				echo "Invalid field type.";
		}
	}

	public function handle_field_meta_create_or_update_in_db($request, $table_meta_id, $dynamic_table_name) {
		// Get data from HTML form
    $title = sanitize_text_field($request['title']);
    $field_name = $this->create_slug($request['title']);
    $user_defined_type = sanitize_text_field(trim($request['user_defined_type']));
    $is_erp = sanitize_text_field($request['is_erp']);
		$is_system_defined = sanitize_text_field($request['is_system_defined']);
    $completion_weightage = sanitize_text_field($request['completion_weightage']);
		$field_options = array_map('sanitize_text_field', $request['field_options']);
		$option_default_value = $field_options[$request['option_default_value']];
		$default_value = trim($request['default_value']) ? sanitize_text_field($request['default_value']) : sanitize_text_field($option_default_value);
		$unit = sanitize_text_field($request['unit_select']);
		$range = [sanitize_text_field($request['range_min']), sanitize_text_field($request['range_max'])];


		// $selected_products = array_map('sanitize_text_field',$request['products_select']);
		$selected_products = [];


		if(!$default_value) {
			if($user_defined_type == 'number') {
				$default_value = null;
			} else {
				$default_value = '';
			}
		}

    $placeholder = sanitize_text_field($request['placeholder']);
    $display_order = sanitize_text_field($request['display_order']);
    // $field_options = sanitize_text_field($request['field_options']);
		// echo "D";
		$seller_apps = isset($request['seller_apps']) && is_array($request['seller_apps']) ? array_map('sanitize_text_field', $request['seller_apps']) : '';

    $config = json_encode(array(
        // 'field_value' => $field_value,
        'placeholder' => $placeholder,
        'field_options' => $field_options,
				'seller_apps' => $seller_apps,
				'default_value' => $default_value,
				'unit' => $unit,
				'range' => $range,
				'selected_products' => $selected_products,
				'completion_weightage' => empty($completion_weightage) ? 0 : $completion_weightage ,
    ));

		$seller_apps_names = $this->get_seller_apps_names($seller_apps);


		// update field
    if (isset($request['field_id']) && $request['field_id']) {
			// get field_id
			$field_id = intval($request['field_id']);

				// Assuming you get the new field name from POST data
				$new_field_name = $field_name;
				// Fetch the previous(current) field name & user_defined_type

				$field_meta = $this->get_specific_cols_from_row_of_table('pim_field_metas', ['field_name', 'user_defined_type'], $field_id);
				// if got some data
				if ($field_meta) {
						// get previous(current) data of this field (name, type)
						$current_field_name = $field_meta->field_name;
						$defined_type = $field_meta->user_defined_type;
						// now map the type (HTML to SQL)
						$type = $this->get_mapped_type($field_options)[$defined_type]['type'];

						$this->rename_column_of_table($current_field_name, $new_field_name, $type, $dynamic_table_name);

					}

					$data =  array(
						'field_name' => $field_name,
						'title' => $title,
						'display_order' => !$display_order ? 0 : $display_order,
						// cannnot update field type once field is created
						'user_defined_type' => $user_defined_type,
						'is_erp' => !$is_erp ? 0 : $is_erp,
						'is_system_defined' => $is_system_defined,
						'CONFIG' => $config,
					);

					$conditions = array('id' => $field_id);

					$result = $this->update_row_of_table('pim_field_metas', $data, $conditions);

					if($result) {

						$body = $this->prepare_request_body($field_id, 'UPDATE');

						$this->send_field_sync_request_to_syndication($body);

						// $this->send_field_sync_request_to_syndication(array_merge($data, [
						// 	'id' => $field_id,
						// 	'seller_apps' => json_encode($seller_apps_names),
						// 	'action' => 'UPDATE'
						// ]));

					}



        echo '<div class="success-message">Field updated successfully!</div>';
    } else {


			if($this->same_field_already_exists_in_table($table_meta_id, $field_name)) return;


				$data = array(
					'table_meta_id' => $table_meta_id,
          'field_name' => $field_name,
          'title' => $title,
          'user_defined_type' => $user_defined_type,
          'is_erp' => !$is_erp ? 0 : $is_erp,
					'is_system_defined' => $is_system_defined,
					'display_order' => !$display_order ? 0 : $display_order,
          'CONFIG' => $config,
				);

				$result = $this->insert_into_table('pim_field_metas', $data);

				if($result['done']) {

					$body = $this->prepare_request_body($result['id'], 'CREATE');

						$this->send_field_sync_request_to_syndication($body);

					// $this->send_field_sync_request_to_syndication(array_merge($data, [
					// 	'id' => $result['id'],
					// 	'seller_apps' => json_encode($seller_apps_names),
					// 	'action' => 'CREATE'
					// ]));

				}





        echo '<div class="success-message">Field added successfully!</div>';
    }




		// get the HTML to SQL type (check if user defined type can be mapped to sql) OR (decide sql column type using user defined type for html fields)
		$field_sql_type = $this->get_mapped_type($field_options)[$user_defined_type]['type'];
		// Creating new Dynamic table OR Update the column of existing one
		$this->create_new_dynamic_table_or_add_new_column($field_name, $field_sql_type, $dynamic_table_name);
	}


	public function same_field_already_exists_in_table($table_meta_id, $field_name) {
		global $wpdb;

		$check_if_same_name_field_already_exists = $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_field_metas WHERE field_name = %s AND table_meta_id = %d", $field_name, $table_meta_id));

		return sizeof($check_if_same_name_field_already_exists) > 0 ? TRUE : FALSE;
		// $check_if_same_name_field_already_exists = $this->get_specific_rows_from_table('pim_field_metas', 'field_name', $key_value);
	}


	public function update_taxonomy_in_db($request) {
		// Sanitize and validate input
		$tax_id = sanitize_text_field($request['tax_id']);
    $brand_id = sanitize_text_field($request['brand_id']);
    $name = sanitize_text_field($request['name']);
    $slug = $this->create_slug($request['name']);
    $desc = sanitize_textarea_field($request['desc']);
    $is_system_defined = sanitize_text_field($request['is_system_defined']);

		$data = array(
			'brand_id' => $brand_id,
			'name' => $name,
			'slug' => $slug,
			'desc' => $desc,
			'is_system_defined' => $is_system_defined,
		);

		$conditions = array('id' => $tax_id);

		$this->update_row_of_table('pim_taxonomies', $data, $conditions);
	}

	public function create_media_group_in_db($request) {
		// Sanitize and validate input
    $brand_id = sanitize_text_field($request['brand_id']);
    $group_name = sanitize_text_field($request['group_name']);
    $slug = $this->create_slug($request['group_name']);
    $display_order = sanitize_textarea_field($request['display_order']);
    $is_class_specific = sanitize_text_field($request['is_class_specific']);

		$data = array(
			'brand_id' => $brand_id,
			'group_name' => $group_name,
			'slug' => $slug,
			'display_order' => !$display_order ? 0 : $display_order,
			'is_class_specific' => !$is_class_specific ? 0 : $is_class_specific,
		);

		$this->insert_into_table('pim_media_groups', $data);
	}

	public function update_media_group_in_db($request) {
		// Sanitize and validate input
		$m_group_id = sanitize_text_field($request['m_group_id']);
    $brand_id = sanitize_text_field($request['brand_id']);
    $group_name = sanitize_text_field($request['group_name']);
    $slug = $this->create_slug($request['group_name']);
    $display_order = sanitize_textarea_field($request['display_order']);
    $is_class_specific = sanitize_text_field($request['is_class_specific']);

		$data = array(
			'brand_id' => $brand_id,
			'group_name' => $group_name,
			'slug' => $slug,
			'display_order' => $display_order,
			'is_class_specific' => $is_class_specific,
		);

		$conditions = array('id' => $m_group_id);

		$this->update_row_of_table('pim_media_groups', $data, $conditions);
	}


	public function create_media_assignment_in_db($request, $media_group_id) {
		// Sanitize and validate input
    $media_group_id = sanitize_text_field($media_group_id);
    $assignment_name = sanitize_text_field($request['assignment_name']);
    $slug = $this->create_slug($request['assignment_name']);
    $file_type = sanitize_text_field($request['file_type']);
    $display_order = sanitize_textarea_field($request['display_order']);

		$data = array(
			'media_group_id' => (int) $media_group_id,
			'assignment_name' => $assignment_name,
			'slug' => $slug,
			'file_type' => $file_type,
			'display_order' => !$display_order ? 0 : $display_order,
		);

		$this->insert_into_table('pim_media_assignments', $data);
	}

	public function update_media_assignment_in_db($request) {
		// Sanitize and validate input
    $assignment_name = sanitize_text_field($request['assignment_name']);
    $slug = $this->create_slug($request['assignment_name']);
    $file_type = sanitize_text_field($request['file_type']);
    $display_order = sanitize_textarea_field($request['display_order']);

		$data = array(
			'assignment_name' => $assignment_name,
			'slug' => $slug,
			// 'file_type' => $file_type,
			'display_order' => $display_order,
		);

		$conditions = array('id' => $request['m_assignment_id']);

		$this->update_row_of_table('pim_media_assignments', $data, $conditions);
	}


	public function create_table_meta_in_db($request) {
		// Sanitize and validate input
    $brand_id = sanitize_text_field($request['brand_id']);
    $title = sanitize_text_field($request['title']);
    $desc = sanitize_textarea_field($request['desc']);
    $is_class_specific = false;
    $display_order = sanitize_text_field($request['display_order']);
    // $ui_color = sanitize_text_field($request['ui_color']);
    $background_color = sanitize_text_field($request['background_color']);
    $text_color = sanitize_text_field($request['text_color']);
    $add_info = sanitize_textarea_field($request['add_info']);

		$data = array(
			'brand_id' => $brand_id,
			'title' => $title,
			'table_name' => $this->create_slug($title),
			'desc' => $desc,
			'is_class_specific' => !$is_class_specific ? 0 : $display_order,
			'display_order' => !$display_order ? 0 : $display_order,
			'CONFIG' => json_encode(array(
					// 'ui_color' => $ui_color,
					'background_color' => $background_color,
					'text_color' => $text_color,
					'add_info' => $add_info
			)),
		);

		$this->insert_into_table('pim_table_metas', $data);
	}

	public function update_table_meta_in_db($request) {
		// Sanitize and validate input
    $brand_id = sanitize_text_field($request['brand_id']);
    $title = sanitize_text_field($request['title']);
    $desc = sanitize_textarea_field($request['desc']);
    $is_class_specific = false;
    $display_order = sanitize_text_field($request['display_order']);
    // $ui_color = sanitize_text_field($request['ui_color']);
    $background_color = sanitize_text_field($request['background_color']);
    $text_color = sanitize_text_field($request['text_color']);
    $add_info = sanitize_textarea_field($request['add_info']);

		$data = array(
			// 'brand_id' => $brand_id,
			// 'title' => $title,
			// 'table_name' => $this->create_slug($title),
			'desc' => $desc,
			'is_class_specific' => !$is_class_specific ? 0 : $display_order,
			'display_order' => !$display_order ? 0 : $display_order,
			'CONFIG' => json_encode(array(
					// 'ui_color' => $ui_color,
					'background_color' => $background_color,
					'text_color' => $text_color,
					'add_info' => $add_info
			)),
		);

		$conditions = array('id' => $request['table_update_id']);

		$this->update_row_of_table('pim_table_metas', $data, $conditions);
	}

	public function create_slug($str) {
		return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', trim($str)));
	}

	public function create_taxonomy_in_db($request) {
		// Sanitize and validate input
    $brand_id = sanitize_text_field($request['brand_id']);
    $name = sanitize_text_field($request['name']) ? sanitize_text_field($request['name']) : NULL;
    $slug = $this->create_slug($name) ? $this->create_slug($name) : NULL;
    $desc = sanitize_textarea_field($request['desc']);
    $is_system_defined = sanitize_text_field($request['is_system_defined']);

		$data = array(
			'brand_id' => $brand_id,
			'name' => $name,
			'slug' => $slug,
			'desc' => $desc,
			'is_system_defined' => $is_system_defined, // default 0 set in DB
		);

		$this->insert_into_table('pim_taxonomies', $data);
	}

	public function create_taxonomy_term_in_db($request, $tax_id) {
		// Sanitize and validate input
    $name = sanitize_text_field($request['name']);
    $slug = $this->create_slug($request['name']);
    $parent_id = sanitize_textarea_field($request['parent_id']);
    $icon = sanitize_textarea_field($request['icon']);

		$data = array(
			'tax_id' => (int) $tax_id,
			'name' => $name,
			'slug' => $slug,
			'parent_id' => $parent_id === '' ? null : $parent_id,
			'level' => null,
			'icon' => $icon === '' ? null : $parent_id,
		);

		$this->insert_into_table('pim_taxonomy_terms', $data);
	}

	public function update_taxonomy_term_in_db($request) {
		// Sanitize and validate input
		$term_id = sanitize_text_field($request['term_id']);
    $name = sanitize_text_field($request['name']);
    $slug = $this->create_slug($request['name']);
    $parent_id = sanitize_textarea_field($request['parent_id']);
    $icon = sanitize_textarea_field($request['icon']);

		$data = array(
			'name' => $name,
			'slug' => $slug,
			'parent_id' => !$parent_id ? null : $parent_id,
			'icon' => !$icon ? null : $icon,
		);

		$conditions = array('id' => $term_id);

		// a term cannot be parent of its own
		if($parent_id == $term_id) return;

		$this->update_row_of_table('pim_taxonomy_terms', $data, $conditions);
	}

	// create new seller app
	public function create_new_seller_app($request) {
		// Sanitize and validate input
    // $brand_id = sanitize_text_field($request['brand_id']);
    $name = sanitize_text_field($request['seller_name']) ? sanitize_text_field($request['seller_name']) : NULL;
    $slug = $this->create_slug($name) ? $this->create_slug($name) : NULL;
    $desc = sanitize_textarea_field($request['desc']);

		$data = array(
			// 'brand_id' => $brand_id,
			'name' => $name,
			'slug' => $slug,
			'description' => $desc,
			// 'is_system_defined' => $is_system_defined, // default 0 set in DB
		);

		$this->insert_into_table('pim_seller_apps', $data);
	}

	// Update seller app
public function update_seller_app($request) {
	// Sanitize and validate input
	$seller_app_id = intval($request['update_seller_app_id']);
	$name = sanitize_text_field($request['seller_name']) ? sanitize_text_field($request['seller_name']) : NULL;
	$slug = $this->create_slug($name) ? $this->create_slug($name) : NULL;
	$desc = sanitize_textarea_field($request['desc']);

	// Prepare the data for update
	$data = array(
			'name' => $name,
			'slug' => $slug,
			'description' => $desc,
	);

	$conditions = array('id' => $seller_app_id);

	// Perform the update in the database
	$this->update_row_of_table('pim_seller_apps', $data, $conditions);
	// $this->update_row_of_table($seller_app_id, 'pim_seller_apps', $data);
}


	//delete seller app
	public function delete_seller_app_from_db($seller_app_id, $table_name) {
    $seller_app_id = intval($seller_app_id);

		$this->delete_row_from_table($seller_app_id, 'pim_seller_apps');
	}

	public function handle_field_delete_in_db($delete_field_id, $dynamic_table_name) {
    $delete_field_id = intval($delete_field_id);
		// Assuming $delete_field_id is the ID of the field metadata to be deleted
		// Get field_name
		$field_meta = $this->get_specific_cols_from_row_of_table('pim_field_metas', 'field_name', $delete_field_id);

		if ($field_meta) {
			$field_name = $field_meta->field_name;

			// Check if the column with the name "$field_name" exists in the dynamic table
			if ($this->check_if_column_exists($field_name, $dynamic_table_name)) {
					// Delete the column from the dynamic table
					$this->delete_column_from_table($field_name, $dynamic_table_name);

					// Delete the record/field data from pim_field_metas table
					$this->delete_row_from_table($delete_field_id, 'pim_field_metas');

					echo '<div class="success-message">Field deleted successfully!</div>';
			}
		}
	}

	public function redirect_to_same_page() {
		unset($_POST);
		header("Location: " . $_SERVER['REQUEST_URI']);
	}


	public function return_if_empty_or_not_found($val, $msg = null) {
		// If no tables found, exit
		if (empty($val)) {
			echo $msg ?? 'Not Found.';
			exit;
		}
	}

	public function check_if_dynamic_table_exists($dynamic_table_name) {
		return $this->get_var_like($dynamic_table_name) === $dynamic_table_name;
	}

	public function get_pim_dynamic_table_name($table_meta) {
		$brand_id = $table_meta->brand_id;
		$table_name = $table_meta->table_name;

		// Retrieve brand_code from pim_brands table using $brand_id as forign key
		$brand_code = $this->get_var_from_table('pim_brands', 'code', $brand_id);

		return "pim_{$brand_code}_{$table_name}";
	}


public function generate_html_fields_for_each_table_meta($product_id, $field_groups_structure) {

$this->products = $this->get_all_rows_and_cols_from_table('pim_products');
	// foreach ($field_groups_structure['field_groups'] as $group) {
		foreach ($field_groups_structure as $index => $group) {
			$activeClass = $group['display_order'] === '0' ? 'active' : '';
			echo '<div class="tab-content ' . $activeClass . '" id="tab-' . $group['id'] . '">';
			$this->get_html_fields_form($group['fields'], $group, $product_id, $index);
			echo '</div>';
	}
}

function order_field_groups_structure_by_display_order($field_groups_structure) {
	// Loop through each table meta in the field groups structure

	// Order the feild metas themselves by display_order
	foreach ($field_groups_structure as &$table_meta) {
			// Order the field metas within each table meta by display_order
			usort($table_meta['fields'], function($a, $b) {

					return $a['display_order'] <=> $b['display_order'];
			});
	}

	// Order the table metas themselves by display_order
	usort($field_groups_structure, function($a, $b) {

			return $a['display_order'] <=> $b['display_order'];
	});

	return $field_groups_structure;
}
// public function order_field_groups_structure_by_display_order($field_groups_structure) {
// 	$this->dump($field_groups_structure);
	// Sort the field groups (table_metas) by their display_order
	// usort($field_groups_structure['field_groups'], function($a, $b) {
	// 		return $a['display_order'] - $b['display_order'];
	// });

	// Sort the fields (field_metas) within each field group by their display_order
	// foreach ($field_groups_structure['field_groups'] as &$field_group) {
	// 		if (isset($field_group['fields']) && is_array($field_group['fields'])) {
	// 				usort($field_group['fields'], function($a, $b) {
	// 						return $a['display_order'] - $b['display_order'];
	// 				});
	// 		}
	// }

// 	return $field_groups_structure;
// }

public function ck_perm($item_type, $item_id) {
	return $this->permissionsEnum[$this->find_permission_for_current_item($item_type, $item_id)];
}

public function check_page_permission($page_slug) {

	$page = $this->get_specific_row_from_table('pim_pages', 'slug', $page_slug);

	if(isset($page->id)) {
		return $this->ck_perm('pages', $page->id);
	}
}

public function page_not_permitted() {
	get_header("blank-subheader");
			echo '<div style="min-height: 40vh;" class="d-flex justify-content-center align-items-center">';
			echo '<h1 class="text-center fw-bold" style="font-size: 35px;">Page Not Permitted</h1>';
			echo '</div>';
			get_footer();
			die();
						return;
}

public function item_not_found() {
	get_header("blank-subheader");
			echo '<div style="min-height: 40vh;" class="d-flex justify-content-center align-items-center">';
			echo '<h1 class="text-center fw-bold" style="font-size: 35px;">Item Not Found</h1>';
			echo '</div>';
			get_footer();
			die();
			return;
}


	public function generate_html_tabs_for_each_table_meta($field_groups_structure) {
		// Retrieve the field groups from the $field_groups_structure
    // $field_groups = $field_groups_structure['field_groups'];
    $field_groups = $field_groups_structure;
// $this->dump($field_groups_structure[0]);
		// Sort tables by display_order
    // usort($tables, function($a, $b) {
		// 	return $a->display_order - $b->display_order;
		// });
		// Generate tabs
		foreach ($field_groups as $index => $group) {
			// $this->dump($this->ck_perm('tables', $group['id']));
			// if($this->ck_perm('tables', $group['id']) == 'VIEW' || $this->ck_perm('tables', $group['id']) == 'EDIT') {
				$this->get_tab_button_html($index, $group);
			// } else {
				// continue;
			// }
		}
	}

	public function get_tab_button_html($index, $group) {
		$activeClass = $index === 0 ? 'active' : '';
		$border = 'style="border: 2px solid ' . ($group['color'] ? $group['color'] : 'black') . ';"';
		$bg_color = $group['background_color'] ? $group['background_color'] : '#000000';
		$text_color = $group['text_color'] ? $group['text_color'] : '#ffffff';
		echo '<li class="tab ' . $activeClass . " info-tab-$index" . '"' . " data-tab-num='$index'" . ' >';
		echo '<a style="background-color:' . $bg_color . '; color:' . $text_color . ';' . '"  href="#tab-' . $group['id'] . '">' . $group['name'] . '</a>';
		echo '</li>';
	}

	public function get_html_fields_form($fields, $table, $product_id, $index) {

    $all_seller_apps = $this->get_all_rows_and_cols_from_table('pim_seller_apps');
    $table_slug = $table['slug'];

    if (!empty($fields)) {
        echo '<form id="product_detail_form" class="arrow_prod_detail_form" data-table-meta-name="' . esc_attr($table_slug) . '">';

        echo '<div class="d-flex  justify-content-between gap-2 align-items-center mt-5 mb-4">';

        echo "<div class='d-flex  justify-content-between gap-2 align-items-center ar-user-selected-tab'>";
        $this->get_tab_button_html($index, $table);
        echo "</div>";

        echo "<div class='d-flex justify-content-between gap-2 align-items-center'>";
        echo "<div class='filter_seller_apps_div' style='position: relative;'>";
        echo "<label class='me-2'>Filter by Seller Apps</label>";
        echo "<select data-purpose='filter_seller_apps' id='filter_seller_apps_for_$table_slug'>";
        echo "<option value='0' selected>All</option>";
        foreach($all_seller_apps as $s_app) {
            echo "<option value='$s_app->id'>$s_app->name</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='edit_div' style='position: relative;'>
                <div class='loader-parent' style='height: 41px !important;'>
                    <div class='loader hidden'></div>
                </div>
                <button type='button' id='edit_btn' class='edit_btn'>Edit</button>
                <div id='cancel_or_save_div' class='cancel_or_save_div'>
                    <button type='button' id='cancel_btn' class='cancel_btn'>Cancel</button>
                    <button type='button' id='save_btn' class='save_btn'>Save</button>
                </div>
              </div>";
        echo "</div>";

        echo '</div>';

        // Render fields in a two-column format
        echo '<table class="table" style="table-layout:fixed;">';
        $field_count = count($fields);

        for ($i = 0; $i < $field_count; $i += 2) {
            echo '<tr>';

            // Render first column (current field)
            $this->get_html_field($fields[$i], $product_id);

            // Render second column if it exists
            if (isset($fields[$i + 1])) {
                $this->get_html_field($fields[$i + 1], $product_id);
            } else {
                // Empty cell if there's no second field in this row
                // echo '<td></td>';
                // echo '<td></td>';
                // echo '<td></td>';
            }

            echo '</tr>';
        }
        echo '</table>';

        echo '</form>';
    } else {
        echo '<p>No fields found for this table.</p>';
    }
}

public function get_html_field($field, $product_id) {
	$field_id = $field['id'];
    $field_name = $field['slug'];
    $field_label = $field['name'];
    $field_type = $field['field_type'];
    $placeholder = $field['placeholder'];
    $field_value = isset($field['value']) ? $field['value'] : '';
    $seller_apps = implode(',', $field['seller_apps']);

    $is_erp_or_pim = $field['is_erp'] ? 'ERP' : 'PIM';

		if(!empty($field['unit'])) {
			echo "
			<style>
					td.unit-" . $field['unit'] . "::after {
				content: '" . $field['unit'] . "';
				}
			</style>
		";
		}



		// if($this->ck_perm('fields', $field['id']) == 'VIEW' || $this->ck_perm('fields', $field['id']) == 'EDIT') {
			// Render field in a table column format
			echo '<td style="width: 5%;" data-seller-apps="' . $seller_apps . '">';
			echo "<span class='pim_span'>" . $is_erp_or_pim . "</span>";
			echo '<th data-seller-apps="' . $seller_apps . '" style="width: 20%;">' . esc_html($field_label) . '</th>';
			echo '<td ' . (!empty($field['unit']) ? 'class="unit-input-td unit-' . $field["unit"] . ' position-relative"' : '') . ' data-seller-apps="' . $seller_apps . '" >';
			$this->get_html_field_structure($field_name, $field_label, $field_type, $placeholder, $field_value, $field, $field_id);
			echo '</td>';
			echo '</td>';
		// } else {
			// continue;
		// }

}



// public function get_html_fields_form($fields, $table, $product_id, $index) {

// 	$all_seller_apps = $this->get_all_rows_and_cols_from_table('pim_seller_apps');

// 	$table_slug = $table['slug'];
// 	// $dynamic_table_name = $this->get_pim_dynamic_table_name($table);
// 	if (!empty($fields)) {
// 			echo '<form id="product_detail_form" class="arrow_prod_detail_form" data-table-meta-name="' . esc_attr($table_slug) . '">';

// 			echo '<div class="d-flex  justify-content-between gap-2 align-items-center mt-5 mb-4">';

// echo "<div class='d-flex  justify-content-between gap-2 align-items-center ar-user-selected-tab'>";

// $this->get_tab_button_html($index, $table);
// // echo '<li class="tab ' . $activeClass . " info-tab-$index" . '"' . " data-tab-num='$index'" . ' >';
// // 			echo '<a style="background-color:' . $bg_color . '; color:' . $text_color . ';' . '"  href="#tab-' . $group['id'] . '">' . $group['name'] . '</a>';
// // 			echo '</li>';

// 	// echo "<li class='tab'><a style='border: 2px solid #de1717; ' href='#tab-16'>Packaging Specifications</a></li>";
// echo "</div>";

// echo "<div class='d-flex justify-content-between gap-2 align-items-center'>";
// 				echo "<div class='filter_seller_apps_div' style='position: relative;'>";
// 				echo "<label class='me-2'>Filter by Seller Apps</label>";
// 				echo "<select data-purpose='filter_seller_apps' id='filter_seller_apps_for_$table_slug'>";
// 				echo "<option value='0' selected>All</option>";
// 					foreach($all_seller_apps as $s_app) {
// 						echo "<option value='$s_app->id'>$s_app->name</option>";
// 					}
// 				echo "</select>";
// 				echo "</div>";

// 				echo "<div class='edit_div' style='position: relative;'>
// 					<div class='loader-parent' style='height: 41px !important;'>
// 						<div class='loader hidden'></div>
// 					</div>
// 							<button type='button' id='edit_btn' class='edit_btn'>Edit</button>
// 							<div id='cancel_or_save_div' class='cancel_or_save_div'>
// 									<button type='button' id='cancel_btn' class='cancel_btn'>Cancel</button>
// 									<button type='button' id='save_btn' class='save_btn'>Save</button>
// 							</div>
// 				</div>";
// echo "</div>";

// 			echo '</div>';


// 			echo '<table class="table" style="table-layout:fixed;">';
// 			foreach ($fields as $field) {
// 					$this->get_html_field($field, $product_id);
// 			}
// 			echo '</table>';
// 			echo '</form>';
// 	} else {
// 			echo '<p>No fields found for this table.</p>';
// 	}
// }


// public function get_html_field($field, $product_id) {
// 	$field_name = $field['slug'];
// 	$field_label = $field['name']; // Adjust based on the actual field label
// 	$field_type = $field['field_type'];
// 	$placeholder = $field['placeholder'];
// 	$field_value = isset($field['value']) ? $field['value'] : '';
// 	// $seller_apps = ;
// 	$seller_apps = implode(',', $field['seller_apps']);

// 	$is_erp_or_pim = $field->is_erp ? 'ERP' : 'PIM';

// 	// Generate field HTML
// 	echo '<tr data-seller-apps="' . $seller_apps . '">';
// 	echo "<td class='brand__span pim_span'>" . $is_erp_or_pim . "</td>";
// 	// echo "<span class='pim_span'>" . $is_erp_or_pim . "</span>";
// 	echo '<th>' . esc_html($field_label) . '</th>';
// 	echo '<td>';
// 	$this->get_html_field_structure($field_name, $field_label, $field_type, $placeholder, $field_value, $field);
// 	echo '</td>';
// 	echo '</tr>';
// }


public function get_html_field_structure($field_name, $field_label, $field_type, $placeholder, $field_value, $field, $field_id) {

	$unit_input_style = !empty($field['unit']) ? ' style="max-width:85%;" ' : '';

	$disabled = 'disabled' . $unit_input_style;

	$pim_field_identity = 'pim-data-field';

	if($this->ck_perm('fields', $field_id) == 'EDIT') {
		$pim_field_identity .= ' pim-data-permitted';
	}


	switch ($field_type) {
			case 'text':
					echo '<input ' . $pim_field_identity . ' type="text" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" placeholder="' . esc_attr($placeholder) . '"' . $disabled . '>';
					break;
			case 'currency':
			case 'dimension':
				case 'volume':
					case 'weight':
					echo '<input ' . $pim_field_identity . ' type="text" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" placeholder="' . esc_attr($placeholder) . '"' . $disabled . '>';
					break;

			case 'number':
					echo '<input ' . $pim_field_identity . ' type="number" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" placeholder="' . esc_attr($placeholder) . '"' . $disabled . '>';
					break;
			case 'textarea':
					echo '<textarea ' . $pim_field_identity . ' name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '" placeholder="' . esc_attr($placeholder) . '"' . $disabled . '>' . esc_textarea($field_value) . '</textarea>';
					break;
			case 'boolean':
					echo '<input ' . $pim_field_identity . ' type="checkbox" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '" ' . checked($field_value, 1, false) . '' . $disabled . '>';
					break;
			case 'select':
					echo '<select ' . $pim_field_identity . ' name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '"' . $disabled . '>';
					if (trim($placeholder) !== '') {
							echo '<option value="" selected disabled>' . esc_html($placeholder) . '</option>';
					} else {
						echo '<option value="" selected disabled>' . 'Select an option' . '</option>';
					}
					foreach ($field['field_options'] as $option) {
							echo '<option value="' . esc_attr($option) . '" ' . selected($field_value, $option, false) . '>' . esc_html($option) . '</option>';
					}
					echo '</select>';
					break;
			case 'multi_select':
					echo '<select ' . $pim_field_identity . ' name="' . esc_attr($field_name) . '[]" id="' . esc_attr($field_name) . '" multiple ' . $disabled . '>';
					if (trim($placeholder) !== '') {
							echo '<option value="" selected disabled>' . esc_html($placeholder) . '</option>';
					} else {
						echo '<option value="" selected disabled>' . 'Select an option' . '</option>';
					}
					foreach ($field['field_options'] as $option) {
							echo '<option value="' . esc_attr($option) . '" ' . (in_array($option, explode(',', $field_value)) ? 'selected' : '') . '>' . esc_html($option) . '</option>';
					}
					echo '</select>';
					break;
			case 'range':
				$range_style = ' style="width:50% !important;" ';

				echo "
				<input class='range_main_input' name='$field_name' type='hidden'  $pim_field_identity $disabled value='". json_encode($field_value) ."' >
				";

				echo '<input class="range_min" ' . $range_style  . $pim_field_identity . ' type="number" name="' . esc_attr($field_name) . '_min" id="' . esc_attr($field_name) . '_min" value="' . esc_attr($field_value[0]) . '" placeholder="' . esc_attr($placeholder) . '"' . $disabled . '>';

					echo '<input class="range_max" ' . $range_style . $pim_field_identity . ' type="number" name="' . esc_attr($field_name) . '_max" id="' . esc_attr($field_name) . '_max" value="' . esc_attr($field_value[1]) . '" placeholder="' . esc_attr($placeholder) . '"' . $disabled . '>';

					break;
			case 'radio':
				echo "
				<input class='pim_radio_main_input' name='$field_name' type='hidden'  $pim_field_identity $disabled value='". $field_value ."' >
				";

					foreach ($field['field_options'] as $option) {
							echo '<label><span>';
							echo '<input class="pim_inner_radio_input" ' . $pim_field_identity . ' type="radio" name="' . esc_attr($field_name) . '" value="' . esc_attr($option) . '" ' . checked($field_value, $option, false) . ' ' . $disabled . '>';
							echo esc_html($option);
							echo '</span></label>';
					}
					break;
			case 'checkbox':


			echo "
			<input class='pim_checkbox_main_input' name='$field_name' type='hidden'  $pim_field_identity $disabled value='". json_encode($field_value) ."'  >
			";

					foreach ($field['field_options'] as $option) {
							echo '<label><span>';
							echo '<input class="pim_inner_checkbox_input" ' . $pim_field_identity . ' type="checkbox" name="' . esc_attr($field_name) . '[]" value="' . esc_attr($option) . '" ' . (in_array($option, $field_value) ? 'checked' : '') . ' ' . $disabled . '>';
							echo esc_html($option);
							echo '</span></label>';
					}
					break;

		// 			case 'products':
    //         // Assuming $products is an array of products fetched from the database in the format [ 'sku' => 'Product Name' ]
    //         $products = $this->get_all_rows_and_cols_from_table('pim_products');

		// 				// Hidden main input to store JSON value of selected products
    // echo '<input type="hidden"' . $pim_field_identity . ' class="pim_products_select_MAIN_input" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '_main" value="' . esc_attr(json_encode($field_value)) . '"  data-db-value="' . esc_attr(json_encode($field_value)) . '" />';

    //         echo '<select ' . $pim_field_identity . ' name="' . esc_attr($field_name) . '[]" id="products_select_' . esc_attr($field_name) . '" multiple="multiple" class="pim_products_select_INNER_input" ' . $disabled . '>';

    //         // If placeholder exists, show it as the first option
    //         // if (trim($placeholder) !== '') {
    //         //     echo '<option value="" disabled>' . esc_html($placeholder) . '</option>';
    //         // }

    //         // Loop through each product and add it as an option
    //         foreach ($products as $i => $product) {
		// 					$sku = $product->sku;
		// 					$product_name = $product->title;
    //             echo '<option value="' . esc_attr($sku) . '" ' . (in_array($sku, $field_value) ? 'selected' : '') . '>';
    //             echo esc_html($product_name) . ' (' . esc_html($sku) . ')';
    //             echo '</option>';
    //         }

    //         echo '</select>';

		// // 				echo '
		// // 				<script>
		// // 				console.log("prod_____P #products_select_' . esc_attr($field_name) . '");
		// // 				jQuery("#products_select_' . esc_attr($field_name) . '").select2({
    // //   placeholder: "Select Products...",
    // //   multiple: true,
    // //   // allowClear: true
    // // });
		// // 				</script>';
    //         break;
		case 'products':
			// Fetch products from the database
			// if (!is_array($this->products)) {
			// 		$products = []; // Ensure $products is an array
			// }

			// Ensure $field_value is an array
			// $field_value = isset($field_value) ? (array) $field_value : [];

			// Hidden main input to store JSON value of selected products
			echo '<input type="hidden"' . $pim_field_identity . ' class="pim_products_select_MAIN_input" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '_main" value="' . esc_attr(json_encode($field_value)) . '" data-db-value="' . esc_attr(json_encode($field_value)) . '" />';

			// Select field
			echo '<select ' . $pim_field_identity . ' name="' . esc_attr($field_name) . '[]" id="products_select_' . esc_attr($field_name) . '" multiple="multiple" class="pim_products_select_INNER_input" ' . $disabled . '>';

			// Loop through each product
			foreach ($this->products as $i => $product) {
					$sku = isset($product->sku) ? $product->sku : '';
					$product_name = isset($product->title) ? $product->title : '';

					if (!empty($sku)) {
							echo '<option value="' . esc_attr($sku) . '" ' . (in_array($sku, $field_value) ? 'selected' : '') . '>';
							echo esc_html($product_name) . ' (' . esc_html($sku) . ')';
							echo '</option>';
					}
			}

			echo '</select>';

			// Uncomment this if you want to initialize Select2 via JavaScript
			// echo '
			// <script>
			// jQuery("#products_select_' . esc_attr($field_name) . '").select2({
			//   placeholder: "Select Products...",
			//   multiple: true,
			// });
			// </script>';
			break;


						case 'repeater':
							echo '<div class="repeater-wrapper" id="repeater_' . esc_attr($field_name) . '">';

							// Get existing values from the database or provide an empty field
							if (!empty($field_value)) {
									foreach ($field_value as $index => $repeater_value) {
											echo $this->get_repeater_input($field_name, $index, $repeater_value, $pim_field_identity);
									}
							} else {
									// Empty row when no value exists
									echo $this->get_repeater_input($field_name, 0, '', $pim_field_identity);
							}

							echo '</div>';

							// Hidden field that will store the JSON-encoded repeater values
							echo '<input ' . $pim_field_identity . ' type="hidden" class="repeater-main-input" name="' . esc_attr($field_name) . '" value="' . esc_attr(json_encode($field_value)) . '" data-db-value="' . esc_attr(json_encode($field_value)) . '">';

							// Button to add new repeater row
							echo '<button '. $pim_field_identity .' ' . $disabled . ' type="button" class="add-repeater-row" data-field-name="' . esc_attr($field_name) . '">+</button>';
							break;

			default:
					echo 'Unsupported field type';
	}
}

public function get_repeater_input($field_name, $index, $value, $pim_field_identity) {
	$disabled = 'disabled';
	ob_start(); ?>

<div class="repeater-row" data-index="<?php echo esc_attr($index); ?>">
  <input <?= ' ' . $pim_field_identity . ' ' . $disabled . ' ' ?> type="text"
    name="<?php echo esc_attr($field_name . '[' . $index . ']'); ?>" value="<?php echo esc_attr($value); ?>"
    placeholder="Enter value" class="repeater-input">
  <button <?= ' ' . $pim_field_identity . ' ' . $disabled . ' ' ?> type="button" class="remove-repeater-row">-</button>
</div>

<?php
	return ob_get_clean();
}

public function get_media_groups_and_assignments($brand_id = 1) {
	global $wpdb;

	$query = "
			SELECT
					mg.id as group_id, mg.group_name, mg.slug as group_slug, mg.is_class_specific, mg.display_order as group_display_order,
					ma.id as assignment_id, ma.assignment_name, ma.slug as assignment_slug, ma.file_type, ma.display_order as assignment_display_order
			FROM pim_media_groups mg
			LEFT JOIN pim_media_assignments ma ON mg.id = ma.media_group_id
			WHERE mg.brand_id = %d
	";

	return $wpdb->get_results($wpdb->prepare($query, $brand_id));
}


// public function generate_media_groups_and_assignments_structure($data) {
// 	$media_groups = [];

// 	foreach ($data as $row) {
// 		if (!isset($media_groups[$row->group_id])) {
// 			$media_groups[$row->group_id] = [
// 				'id' => $row->group_id,
// 				'group_name' => $row->group_name,
// 				'group_slug' => $row->group_slug,
// 				'is_class_specific' => $row->is_class_specific,
// 				'display_order' => $row->group_display_order,
// 				'assignments' => []
// 			];
// 		}

// 		if ($row->assignment_id) {
// 			$media_groups[$row->group_id]['assignments'][] = [
// 				'id' => $row->assignment_id,
// 				'assignment_name' => $row->assignment_name,
// 				'assignment_slug' => $row->assignment_slug,
// 				'file_type' => $row->file_type,
// 				'display_order' => $row->assignment_display_order
// 			];
// 		}
// 	}

// 	// Custom sort function for display_order
// 	if (!empty($media_groups)) {
// 		usort($media_groups, function($a, $b) {
// 			return $a['display_order'] - $b['display_order'];
// 		});

// 		foreach ($media_groups as &$group) {
// 			if (!empty($group)) {
// 				usort($group['assignments'], function($a, $b) {
// 					return $a['display_order'] - $b['display_order'];
// 				});
// 			}
// 		}
// 	}

// 	// Calculate completion_percentage for each product
// 	$completion_percentages = [];

// 	// foreach ($field_metas as $product_id => $fields) {
// 	// 	$total_weightage = 0;
// 	// 	$achieved_weightage = 0;

// 	// 	foreach ($fields as $field) {
// 	// 		$weightage = $field->completion_weightage ?? 0; // Default weightage is 0
// 	// 		$field_value = $field->value;

// 	// 		if ($weightage > 0) {
// 	// 			$total_weightage += $weightage;

// 	// 			// Check if field has a value (not empty or null)
// 	// 			if (!empty($field_value) && $field_value !== null) {
// 	// 				$achieved_weightage += $weightage;
// 	// 			}
// 	// 		}
// 	// 	}

// 	// 	// Calculate completion percentage
// 	// 	$completion_percentage = ($total_weightage > 0) ? ($achieved_weightage / $total_weightage) * 100 : 0;
// 	// 	$completion_percentages[$product_id] = $completion_percentage;
// 	// }

// 	// Assign the calculated completion_percentage to each media group if applicable
// 	foreach ($media_groups as &$group) {
// 		foreach ($group['assignments'] as &$assignment) {
// 			$product_id = $assignment['id']; // Assuming assignment id is the product_id
// 			$assignment['completion_percentage'] = $completion_percentages[$product_id] ?? 0;
// 		}
// 	}

// 	// return [
// 	// 	'media_groups' => $media_groups,
// 	// 	'completion_percentage' => $completion_percentage
// 	// ];
// 	return $media_groups;
// }


// WORKING FUNC ***####
public function generate_media_groups_and_assignments_structure($data) {
	$media_groups = [];

	foreach ($data as $row) {
			if (!isset($media_groups[$row->group_id])) {
					$media_groups[$row->group_id] = [
							'id' => $row->group_id,
							'group_name' => $row->group_name,
							'group_slug' => $row->group_slug,
							'is_class_specific' => $row->is_class_specific,
							'display_order' => $row->group_display_order,
							'assignments' => []
					];
			}

			if ($row->assignment_id) {
					$media_groups[$row->group_id]['assignments'][] = [
							'id' => $row->assignment_id,
							'assignment_name' => $row->assignment_name,
							'assignment_slug' => $row->assignment_slug,
							'file_type' => $row->file_type,
							'display_order' => $row->assignment_display_order
					];
			}
	}

	if(! empty($media_groups)) {
		// Custom sort function for display_order
		usort($media_groups, function($a, $b) {
			return $a['display_order'] - $b['display_order'];
		});

		foreach ($media_groups as &$group) {
			if(! empty($group)) {
				usort($group['assignments'], function($a, $b) {
						return $a['display_order'] - $b['display_order'];
				});
			}
		}

	}



	return $media_groups;
}


// $this->media_base_url = "https://d31il057o05hvr.cloudfront.net/raw-folder/";

	/**
	 * This function resizes the image on fly and serves them from cloudfront
	 */
	public function get_media_url($media_key, $size = "full", $width=0, $height=0){
		/**
		 * Media_Key includes the offset path from base url, without leading slash '/'
		 * Width and Height takes precedence over Size
		 */
		if(empty($media_key)){
			return "";
		}
		$fit = "inside";
		$params = array(
			"bucket" => "com.altprod.arrow.raw-data",
			"key" => 'raw-folder/'.$media_key,
		);

		if($width == 0 || $height == 0){
			switch ($size){
				case "thumbnail":
					$width = 177;
					$height = 146;
				break;
				case "medium":
					$width = 360;
					$height = 292;
				break;
				case "preview":
					$width = 806;
					$height = 600;
				break;
			};

		}
		$params["edits"] = array(
			"resize" => array(
				"width" => $width ,
				"height" => $height,
				"fit" => $fit
			)
		);

		$encoded_params = json_encode($params, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

	  	return "https://d2cc3fex3dydkw.cloudfront.net/".base64_encode($encoded_params);
	}


public function get_all_media_records_for_current_prod($current_product_id) {
	global $wpdb;
	$query = $wpdb->prepare(
		"SELECT * FROM pim_media WHERE associated_item_id = %d",
		$current_product_id
	);
	$all_media = $wpdb->get_results($query, ARRAY_A);
	return $all_media;
}

// function generate_media_html($media_groups) {
// 	echo '<div class="media-groups-tabs">';
// 	echo '<ul class="tabs">';
// 	foreach ($media_groups as $group_id => $group) {
// 			echo '<li><a href="#group-' . esc_attr($group_id) . '">' . esc_html($group['group_name']) . '</a></li>';
// 	}
// 	echo '</ul>';

// 	foreach ($media_groups as $group_id => $group) {
// 			echo '<div id="group-' . esc_attr($group_id) . '" class="tab-content">';
// 			echo '<form id="product_detail_form_' . esc_attr($group_id) . '" class="arrow_prod_detail_form" data-table-meta-name="' . esc_attr($group['group_slug']) . '">';
// 			echo '<div class="edit_div" style="position: relative;">';
// 			echo '<div class="loader-parent" style="height: 41px !important;">';
// 			echo '<div class="loader hidden"></div>';
// 			echo '</div>';
// 			echo '<button type="button" id="edit_btn" class="edit_btn">Edit</button>';
// 			echo '<div id="cancel_or_save_div" class="cancel_or_save_div" style="display: none;">';
// 			echo '<button type="button" id="cancel_btn" class="cancel_btn">Cancel</button>';
// 			echo '<button type="button" id="save_btn" class="save_btn">Save</button>';
// 			echo '</div>';
// 			echo '</div>';
// 			echo '<table class="table"><tbody>';
// 			foreach ($group['assignments'] as $assignment) {
// 				echo '<tr>';
// 					echo '<th style="background-color: #F4474C; color: white;">' . esc_html($assignment['assignment_name']) . ' - ' . esc_html($assignment['file_type']) . '</th>';
// 					echo '<td>';
// 						$this->get_media_field_unassigned($assignment);
// 					echo '</td>';
// 				echo '</tr>';
// 			}
// 			echo '</tbody></table>';
// 			echo '</form>';
// 			echo '</div>';
// 	}

// 	echo '</div>';
// }
public function generate_media_html($media_groups, $current_product_id) {

	// Fetch all media records at once
	$all_media = $this->get_all_media_records_for_current_prod($current_product_id);

	echo '<div class="media-groups-tabs">';

echo '<div class="media-tabs-div">';
	echo '<ul class="tabs">';
		$this->generate_media_tabs($media_groups);
	echo '</ul>';
	echo '</div>';

	echo '<div class="media-tabs-contents-div" >';
	$this->generate_media_fields_table($all_media, $media_groups, $current_product_id );
	echo '</div>';

	echo '</div>';
}

public function generate_media_tabs($media_groups) {


  foreach ($media_groups as $group_id => $group) {
		echo '<li><a href="#group-' . esc_attr($group_id) . '">' . esc_html($group['group_name']) . '</a></li>';
}

}

public function generate_media_fields_table($all_media, $media_groups, $current_product_id ) {
	foreach ($media_groups as $group_id => $group) {
		echo '<div id="group-' . esc_attr($group_id) . '" class="tab-content">';
		echo '<div id="product_detail_form_' . esc_attr($group_id) . '" class="arrow_prod_detail_form" data-table-meta-name="' . esc_attr($group['group_slug']) . '">';

		echo '<li style="width: 243.75px; list-style: none; margin-bottom:15px;"><a style="    text-decoration: none;
    padding: 10px 15px;
    color: #fff;
    background-color: #707070;
    font-size: 15px;
    display: block;
    transition: background-color 0.3s;
    text-align: center;" href="#group-' . esc_attr($group_id) . '">' . esc_html($group['group_name']) . '</a></li>';


		echo '<table class="table"><tbody>';

		// foreach ($group['assignments'] as $assignment) {
		// 		$assigned_media = $this->filter_assigned_media($assignment['id'], $all_media);
		// 		echo '<tr>';
		// 		echo '<th style="background-color: #F4474C; color: white;">' . esc_html($assignment['assignment_name']) . ' - ' . esc_html($assignment['file_type']) . '</th>';
		// 		echo '<td>';

		// 		if ($assigned_media) {
		// 				$this->get_media_field_assigned($assignment['file_type'], $assigned_media);
		// 				$this->get_add_media_HTML($assignment['file_type'], $assignment['id']);
		// 		} else {
		// 				$this->get_media_field_unassigned($assignment, $current_product_id);
		// 		}

		// 		echo '</td>';
		// 		echo '</tr>';
		// }

		for($i = 0; $i < sizeof($group['assignments']); $i += 2) {
			echo '<tr>';

			$assignment = $group['assignments'][$i];
			$this->get_half_media_row($assignment, $all_media, $current_product_id);

			if($group['assignments'][$i+1]) {

				$assignment = $group['assignments'][$i+1];
				$this->get_half_media_row($assignment, $all_media, $current_product_id);

			} else {


			}
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
		echo '</div>';
}
}

public function get_half_media_row($assignment, $all_media, $current_product_id) {
	$assigned_media = $this->filter_assigned_media($assignment['id'], $all_media);
	echo '<td style="text-align: center;width: 3.5%; padding:0 12px !important;font-size:13px !important;vertical-align: baseline;">PIM</td>';
					echo '<th style="width:15%;vertical-align: baseline;line-height: 1.5;padding: 10px 14px;" style="background-color: #F4474C; color: white;">' . esc_html($assignment['assignment_name']) .  '</th>';
					// ' - ' . esc_html($assignment['file_type']) .
					echo '<td style="width:30%;vertical-align: baseline;">';

					if ($assigned_media) {
							$this->get_media_field_assigned($assignment['file_type'], $assigned_media);
							$this->get_add_media_HTML($assignment['file_type'], $assignment['id']);
					} else {
							$this->get_media_field_unassigned($assignment, $current_product_id);
					}

					echo '</td>';
}

public function get_add_media_HTML($file_type, $id){
	echo '

		<div class="add-new-media-div">
 <button type="button" class="media-add-btn">
	<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
</svg>
 </button>
	<form  class="single-media add-new-media-form" style="display:none; position: relative;" data-file-type="' . $file_type . '"   data-assignment-id="' . $id . '">
	<div class="loader-parent" style="height: 41px !important;">
						<div class="loader hidden"></div>
						</div>
		' .
			$this->get_add_media_form($file_type)
		. '
		<div class="cancel_or_save_new_media">
				<button type="button" class="media-terminate-button">
					<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
</svg>

				</button>
				<button type="button" class="media-save-btn">
					<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
</svg>

				</button>
		</div>
 </form>
</div>

		';
}
// public function get_add_media_form($assignment_file_type) {
// 	$form_html = '';

// 	switch ($assignment_file_type) {
// 			case 'image':
// 					$form_html .= "<div class='media-assigned add-media-form-main-div'>";
// 					$form_html .= "
// 							<div class='media-edit-field add-media-form-inner-div'>
// 									<input type='hidden' name='media_type' value='file'/>
// 									<label for='title'>Title</label>
// 									<input id='title' type='text' name='title' required/>
// 									<label for='upload-file'>Upload File</label>
// 									<input id='upload-file' type='file' name='file' required/>
// 									<br>
// 							</div>
// 					";
// 					$form_html .= "</div>";
// 					$form_html .= "<hr>";
// 					break;

// 					case 'doc':
// 						$form_html .= "<div class='media-assigned add-media-form-main-div'>";
// 						$form_html .= "
// 								<div class='media-edit-field add-media-form-inner-div'>
// 										<input type='hidden' name='media_type' value='file'/>
// 										<label for='title'>Title</label>
// 										<input id='title' type='text' name='title' required/>
// 										<label for='upload-file'>Upload File</label>
// 										<input id='upload-file' type='file' name='file' required/>
// 										<br>
// 								</div>
// 						";
// 						$form_html .= "</div>";
// 						$form_html .= "<hr>";
// 						break;

// 			case 'video':
// 					$form_html .= "<div class='media-assigned add-media-form-main-div'>";
// 					$form_html .= "
// 							<div class='media-edit-field add-media-form-inner-div'>
// 									<input type='hidden' name='media_type' value='file'/>
// 									<label for='title'>Title</label>
// 									<input id='title' type='text' name='title' required/>
// 									<label for='upload-file'>Upload File</label>
// 									<input id='upload-file' type='file' name='file' required/>
// 									<br>
// 									<label for='upload-thumb'>Upload Thumbnail</label>
// 									<input id='upload-thumb' type='file' name='thumbnail'/>
// 							</div>
// 					";
// 					$form_html .= "</div>";
// 					$form_html .= "<hr>";
// 					break;

// 			// Other cases go here

// 			default:
// 					$form_html .= "MEDIA TYPE NOT SUPPORTED.";
// 					break;
// 	}

// 	return $form_html;
// }

public function get_add_media_form($assignment_file_type) {
	switch ($assignment_file_type) {
		case 'image':
				return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='file'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='upload-image'>Upload Image</label>
						<input id='upload-image' type='file' name='file' accept='image/*' required/>
						<br>
					</div>
				</div>
				<hr>";
				break;

		case 'video':
			 return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='file'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='upload-file'>Upload File</label>
						<input id='upload-file' type='file' name='file' accept='video/mp4,video/x-m4v,video/*' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*'/>
					</div>
				</div>
				<hr>";
				break;
		case 'audio':
			return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='file'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='upload-file'>Upload File</label>
						<input id='upload-file' type='file' name='file' accept='.mp3,audio/*' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>

				</div>
				<hr>";
				break;
		case 'zip':
			return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='file'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='upload-file'>Upload File</label>
						<input id='upload-file' type='file' name='file' accept='.zip,.rar,.7zip' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>

				</div>
				<hr>";
				break;
		case 'doc':
			return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='file'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='upload-file'>Upload File</label>
						<input id='upload-file' type='file' name='file' accept='.doc, .docx,.xlsx,.xls, .csv' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>

				</div>
				<hr>";
				break;
		case 'pdf':
				 return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='file'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='upload-file'>Upload File</label>
						<input id='upload-file' type='file' name='file' accept='.pdf' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>

				</div>
				<hr>";
				break;

		case 'youtube_url':
			return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
					<input type='hidden' name='media_type' value='youtube_url'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='enter-link'>Enter Link</label>
						<input id='enter-link' type='text' name='url' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>

				</div>
				<hr>";
				break;
		case 'vimeo_url':
				return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='vimeo_url'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='enter-link'>Enter Link</label>
						<input id='enter-link' type='text' name='url' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>
				</div>
				<hr>";
				break;
		case 'video_url':
			return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='video_url'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='enter-link'>Enter Link</label>
						<input id='enter-link' type='text' name='url' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>
				</div>
				<hr>";
				break;
		case 'link':
			return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='link'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='enter-link'>Enter Link</label>
						<input id='enter-link' type='text' name='url' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>
			</div>
			<hr>";
			break;

			case 'image_url':
				return "<div class='media-assigned add-media-form-main-div'>
					<div class=' media-edit-field add-media-form-inner-div' >
						<input type='hidden' name='media_type' value='image_url'/>
						<label for='title'>Title</label>
						<input id='title' type='text' name='title' required/>
						<label for='enter-link'>Enter Link</label>
						<input id='enter-link' type='text' name='url' required/>
						<br>
						<label for='upload-thumb'>Upload Thumbnail</label>
						<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
					</div>
			</div>
			<hr>";
			break;

			default:
		echo "MEDIA TYPE NOT SUPPORTED.";
}
}

private function filter_assigned_media($assignment_id, $all_media) {
	$filtered_media = array();

	foreach ($all_media as $media) {
			if ($media['media_assignment_id'] == $assignment_id) {
					$filtered_media[] = $media;
			}
	}

	return $filtered_media;
}

public function get_media_field_assigned($assignment_file_type, $media) {
	foreach ($media as $media_item) {
		$src_url = $media_item['source_url'];
		$src_url = $this->media_url_prefix . $media_item['source_url'];

		$width = esc_attr($media_item['resolution_x']) . 'px !important';
		$height = esc_attr($media_item['resolution_y']) . 'px !important';

		$media_url = $media_item['thumb_url'];
		$media_url = $this->get_media_url($media_url, 'thumbnail');

		$file_ext = $media_item['file_extension'];
		$media_title = $media_item['title'];

		// $upload_date = esc_html($media_item['uploader']);
		echo "<div style='position: relative;' id='single-media' class='single-media assigned-single-media' data-assignment-id='" . $media_item['media_assignment_id'] . "' data-media-id='" . $media_item['id'] . "' data-associated-item-id='" . $media_item['associated_item_id'] . "' " . "data-file-type='" .$assignment_file_type . "' >";

		echo '
						<div class="media_edit_div assigned" style="position: absolute;right: -2.5px;top: -7px;">
						<div class="loader-parent" style="height: 41px !important;">
						<div class="loader hidden"></div>
						</div>
						<div id="media_cancel_or_save_div" class="media_cancel_or_save_div">
						<button type="button" id="media_delete_btn" class="media_delete_btn">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
</svg>
</button>
						</div>
						</div>
					';
					/*
					<div class="media_edit_div assigned" style="position: absolute; right: 5px;">
						<div class="loader-parent" style="height: 41px !important;">
						<div class="loader hidden"></div>
						</div>
						<button type="button" id="media_edit_btn" class="media_edit_btn">Edit</button>
						<div id="media_cancel_or_save_div" class="media_cancel_or_save_div" style="display: none;">
						<button type="button" id="media_delete_btn" class="media_delete_btn">Delete</button>
						<button type="button" id="media_cancel_btn" class="media_cancel_btn">Cancel</button>
						<button type="button" id="media_save_btn" class="media_save_btn">Save</button>
						</div>
						</div>
					*/

		echo "<h3 style='
		color: white;
    font-size: 18px;
    background: #707070;
    max-width: fit-content;
    padding: 5px 15px;
		'>$media_title</h3>";
			switch ($assignment_file_type) {
					case 'image':
							echo "<div class='media-assigned'>";
							echo "<img src='" . $media_url . "' alt='Assignment Image' >";
							// echo "<p>Upload Date: " . $upload_date . "</p>";
							echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Image (.$file_ext)</h4>";
							echo "<p><a target='_blank' href='" . $media_url . "' download>Download Image</a></p>";

							echo "</div>";
							echo "<hr>";
							break;

					case 'video':
						echo "<div class='media-assigned'>";
							if (!empty($media_item['thumb_url'])) {
									echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
									echo "<img src='" . $media_url . "' alt='Thumbnail' >";
							}
							echo "<br>";
							// echo "<p>File: <a href='" . $src_url . "' download>" . esc_html($media_item['file_extension']) . "</a></p>";
							// echo "<p>Upload Date: " . $upload_date . "</p>";
							echo "
							<video width='300' height='240' controls>
							<source src='$src_url' >
							Your browser does not support the video tag.
							</video>
							";
							echo "<h4 style='margin-top:15px; font-weight: bold; color: black;'>Source File (.$file_ext)</h4>";
							echo "<p><a target='_blank' href='" . $src_url . "' download>Download Video</a></p>";
							// echo "
							// 	<div class='media-edit-field' style='display: none;'>
							// 		<input type='hidden' name='media_type' value='file'/>
							// 		<label for='title'>Title</label>
							// 		<input id='title' type='text' name='title' required/>
							// 		<label for='upload-file'>Upload File</label>
							// 		<input id='upload-file' type='file' name='file' required/>
							// 		<br>
							// 		<label for='upload-thumb'>Upload Thumbnail</label>
							// 		<input id='upload-thumb' type='file' name='thumbnail'/>
							// 	</div>
							// ";
							echo "</div>";
							echo "<hr>";
							break;
					case 'audio':
					case 'zip':
					case 'doc':
					case 'pdf':
							echo "<div class='media-assigned'>";
							if (!empty($media_item['thumb_url'])) {
									echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
									echo "<img src='" . $media_url . "' alt='Thumbnail' >";
							}
							echo "<br>";
							// echo "<p>File: <a href='" . $src_url . "' download>" . esc_html($media_item['file_extension']) . "</a></p>";
							// echo "<p>Upload Date: " . $upload_date . "</p>";
							echo "<h4 style='margin-top:15px; font-weight: bold; color: black;'>Source File (.$file_ext)</h4>";
							echo "<p><a target='_blank' href='" . $src_url . "' download>Download File</a></p>";
							// echo "
							// 	<div class='media-edit-field' style='display: none;'>
							// 		<input type='hidden' name='media_type' value='file'/>
							// 		<label for='title'>Title</label>
							// 		<input id='title' type='text' name='title' required/>
							// 		<label for='upload-file'>Upload File</label>
							// 		<input id='upload-file' type='file' name='file' required/>
							// 		<br>
							// 		<label for='upload-thumb'>Upload Thumbnail</label>
							// 		<input id='upload-thumb' type='file' name='thumbnail'/>
							// 	</div>
							// ";
							echo "</div>";
							echo "<hr>";
							break;

					case 'youtube_url':
							echo "<div class='media-assigned'>";
							if (!empty($media_item['thumb_url'])) {
								echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
								echo "<img src='" . $media_url . "' alt='Thumbnail' >";
							}
							// echo "<a href='" . $src_url . "' target='_blank'>" . esc_html($media_item['source_url']) . "</a>";
							echo "<h4 style='margin-top:15px; font-weight: bold; color: black;'>Yoututbe URL</h4>";
							echo '<iframe width="300" height="300"
							src="' . $media_item['source_url'] . '">
							</iframe>';
							// echo "<p>Upload Date: " . $upload_date . "</p>";
							// echo "<p><a target='_blank' href='" . $media_item['source_url'] . "' download>Download File</a></p>";
							// echo "
							// 	<div class='media-edit-field' style='display: none;'>
							// 	<input type='hidden' name='media_type' value='youtube_url'/>
							// 		<label for='title'>Title</label>
							// 		<input id='title' type='text' name='title' required/>
							// 		<label for='enter-link'>Enter Link</label>
							// 		<input id='enter-link' type='text' name='url' required/>
							// 		<br>
							// 		<label for='upload-thumb'>Upload Thumbnail</label>
							// 		<input id='upload-thumb' type='file' name='thumbnail'/>
							// 	</div>
							// ";
							echo "</div>";
							echo "<hr>";
							break;
					case 'vimeo_url':
							echo "<div class='media-assigned'>";
							if (!empty($media_item['thumb_url'])) {
								echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
								echo "<img src='" . $media_url . "' alt='Thumbnail' >";
							}
							// echo "<a href='" . $src_url . "' target='_blank'>" . esc_html($media_item['source_url']) . "</a>";
							echo "<h4 style='margin-top:15px; font-weight: bold; color: black;'>Vimeo URL</h4>";
							echo '<iframe width="300" height="300" src="' . $media_item['source_url'] . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>';
							// echo "<p>Upload Date: " . $upload_date . "</p>";
							// echo "<p><a target='_blank' href='" . $media_item['source_url'] . "' download>Download File</a></p>";
							// echo "
							// 	<div class='media-edit-field' style='display: none;'>
							// 		<input type='hidden' name='media_type' value='vimeo_url'/>
							// 		<label for='title'>Title</label>
							// 		<input id='title' type='text' name='title' required/>
							// 		<label for='enter-link'>Enter Link</label>
							// 		<input id='enter-link' type='text' name='url' required/>
							// 		<br>
							// 		<label for='upload-thumb'>Upload Thumbnail</label>
							// 		<input id='upload-thumb' type='file' name='thumbnail'/>
							// 	</div>
							// ";
							echo "</div>";
							echo "<hr>";
							break;
					case 'video_url':
						echo "<div class='media-assigned'>";
						if (!empty($media_item['thumb_url'])) {
							echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
							echo "<img src='" . $media_url . "' alt='Thumbnail' >";
						}
							// echo "<a href='" . $src_url . "' target='_blank'>" . esc_html($media_item['source_url']) . "</a>";
						echo "<h4 style='margin-top:15px; font-weight: bold; color: black;'>Video URL</h4>";
						echo '<video width="320" height="240" controls>
						<source src="' . $media_item['source_url'] . '" type="video/mp4">
						<source src="' . $media_item['source_url'] . '" type="video/ogg">
						Your browser does not support the video tag.
					</video>';
							// echo "<p>Upload Date: " . $upload_date . "</p>";
							// echo "<p><a target='_blank' href='" . $media_item['source_url'] . "' download>Download File</a></p>";
							// echo "
							// 	<div class='media-edit-field' style='display: none;'>
							// 		<input type='hidden' name='media_type' value='video_url'/>
							// 		<label for='title'>Title</label>
							// 		<input id='title' type='text' name='title' required/>
							// 		<label for='enter-link'>Enter Link</label>
							// 		<input id='enter-link' type='text' name='url' required/>
							// 		<br>
							// 		<label for='upload-thumb'>Upload Thumbnail</label>
							// 		<input id='upload-thumb' type='file' name='thumbnail'/>
							// 	</div>
							// ";
							echo "</div>";
							echo "<hr>";
							break;
					case 'link':
						echo "<div class='media-assigned'>";
						if (!empty($media_item['thumb_url'])) {
							echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
							echo "<img src='" . $media_url . "' alt='Thumbnail' >";
						}
						echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Link</h4>";
						// echo "<img src='" . $media_url . "' alt='Assignment Link' >";
						// echo "<p>Upload Date: " . $upload_date . "</p>";
						echo "<p><a target='_blank' href='" . $media_item['source_url'] . "' download>Open Link in new tab</a></p>";
						// echo "
						// 		<div class='media-edit-field' style='display: none;'>
						// 			<input type='hidden' name='media_type' value='link'/>
						// 			<label for='title'>Title</label>
						// 			<input id='title' type='text' name='title' required/>
						// 			<label for='enter-link'>Enter Link</label>
						// 			<input id='enter-link' type='text' name='url' required/>
						// 			<br>
						// 			<label for='upload-thumb'>Upload Thumbnail</label>
						// 			<input id='upload-thumb' type='file' name='thumbnail'/>
						// 		</div>
						// 	";
						echo "</div>";
						echo "<hr>";
						break;

						case 'image_url':
							echo "<div class='media-assigned'>";
							if (!empty($media_item['thumb_url'])) {
							echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Thumbnail</h4>";
							echo "<img src='" . $media_url . "' alt='Thumbnail' >";
						}
						echo "<h4 style='margin-top:15px; font-weight: bold;  color: black;'>Image (URL based)</h4>";
						echo "<img src='" . $media_item['source_url'] . "' height='200' width='200' alt='Image from URL' >";
						// echo "<p>Upload Date: " . $upload_date . "</p>";
						echo "<p><a target='_blank' href='" . $media_item['source_url'] . "' download>Download Image</a></p>";
						// echo "
						// 		<div class='media-edit-field' style='display: none;'>
						// 			<input type='hidden' name='media_type' value='image_url'/>
						// 			<label for='title'>Title</label>
						// 			<input id='title' type='text' name='title' required/>
						// 			<label for='enter-link'>Enter Link</label>
						// 			<input id='enter-link' type='text' name='url' required/>
						// 			<br>
						// 			<label for='upload-thumb'>Upload Thumbnail</label>
						// 			<input id='upload-thumb' type='file' name='thumbnail'/>
						// 		</div>
						// 	";
						echo "</div>";
						echo "<hr>";
						break;

						// case 'audio_url':
					// default:
					// 		echo "<div class='media-assigned'>";
					// 		echo "<input disabled type='text' value='" . esc_attr($media_item['source_url']) . "'>";
					// 		// echo "<p>Upload Date: " . $upload_date . "</p>";
					// 		echo "</div>";
					// 		break;
			}

			echo "</div>";

		}
}


public function get_media_field_unassigned($assignment, $aasoc_item_id) {
	echo "<form style='position: relative;' id='single-media' class='single-media un-assigned-single-media' data-assignment-id='" . $assignment['id'] . "'  data-file-type='" . $assignment['file_type'] . "' data-associated-item-id='" . $aasoc_item_id . "' >";

		echo '
						<div class="media_edit_div un_assigned" style="position: absolute; right: -2.5px;">
						<div class="loader-parent" style="height: 41px !important;">
						<div class="loader hidden"></div>
						</div>
						<button type="button" id="media_edit_btn" class="media_edit_btn">
						<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
</svg>


						</button>
						<div id="media_cancel_or_save_div" class="media_cancel_or_save_div" style="display: none;">
						<button type="button" id="media_cancel_btn" class="media_cancel_btn">
							<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
</svg>

						</button>
						<button type="button" id="media_save_btn" class="media_save_btn">
								<svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
</svg>
						</button>
						</div>
						</div>
					';

	$disabled = 'disabled';
			switch ($assignment['file_type']) {
					case 'image':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='image' value='image'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='upload-image'>Upload Image</label>
									<input id='upload-image' type='file' name='file' accept='image/*' required/>
								</div>
							";
							break;
					case 'video':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='video' value='file'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='upload-file'>Upload File</label>
									<input id='upload-file' type='file' name='file' accept='video/mp4,video/x-m4v,video/*' required/>
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					// case 'audio':
					// 		echo "<input $disabled data-file-type='" . esc_attr($assignment['file_type']) . "' type='file' id='" . esc_attr($assignment['assignment_slug']) . "' name='" . esc_attr($assignment['assignment_slug']) . "'>";
					// 		break;
					case 'link':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='link' value='link'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='enter-link'>Enter Link</label>
									<input id='enter-link' type='text' name='url' required/>
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					case 'youtube_url':
						echo "
								<div class='media-edit-field' style='display: none;'>
								<input type='hidden' name='media_type' data-exact-media-type='youtube_url' value='youtube_url'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='enter-link'>Enter Link</label>
									<input id='enter-link' type='text' name='url' required/>
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					case 'vimeo_url':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='vimeo_url' value='vimeo_url'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='enter-link'>Enter Link</label>
									<input id='enter-link' type='text' name='url' required/>
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					// case 'audio_url':
					case 'video_url':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='video_url' value='video_url'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='enter-link'>Enter Link</label>
									<input id='enter-link' type='text' name='url' required/>
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					case 'image_url':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='image_url' value='image_url'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='enter-link'>Enter Link</label>
									<input id='enter-link' type='text' name='url' required/>
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
									</div>
									";
									break;
							// echo "<input $disabled data-file-type='" . esc_attr($assignment['file_type']) . "' type='url' id='" . esc_attr($assignment['assignment_slug']) . "' name='" . esc_attr($assignment['assignment_slug']) . "'>";
					case 'audio':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='" . $assignment['file_type'] . "' value='file'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='upload-file'>Upload File</label>
									<input id='upload-file' type='file' name='file' required accept='.mp3, audio/*' />
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					case 'zip':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='" . $assignment['file_type'] . "' value='file'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='upload-file'>Upload File</label>
									<input id='upload-file' type='file' name='file' required accept='.zip, .rar, .7zip' />
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					case 'doc':
						echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='" . $assignment['file_type'] . "' value='file'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='upload-file'>Upload File</label>
									<input id='upload-file' type='file' name='file' required accept='.doc, .docx,.xlsx,.xls, .csv' />
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
					case 'pdf':
							echo "
								<div class='media-edit-field' style='display: none;'>
									<input type='hidden' name='media_type' data-exact-media-type='" . $assignment['file_type'] . "' value='file'/>
									<label for='title'>Title</label>
									<input id='title' type='text' name='title' required/>
									<label for='upload-file'>Upload File</label>
									<input id='upload-file' type='file' name='file' required accept='.pdf' />
									<br>
									<label for='upload-thumb'>Upload Thumbnail</label>
									<input id='upload-thumb' type='file' name='thumbnail' accept='image/*' />
								</div>
							";
							break;
							// echo "<input $disabled data-file-type='" . esc_attr($assignment['file_type']) . "' type='file' id='" . esc_attr($assignment['assignment_slug']) . "' name='" . esc_attr($assignment['assignment_slug']) . "'>";
					// default:
					// 		echo "<input $disabled data-file-type='" . esc_attr($assignment['file_type']) . "' type='text' id='" . esc_attr($assignment['assignment_slug']) . "' name='" . esc_attr($assignment['assignment_slug']) . "'>";
					// 		break;
			}

			echo "</form>";
}

public function localize_media_edit_script($args){
	/**
	 * Keywords fetcher, Form validation and duplication Handling
	 */
		wp_localize_script( 'mam-ar-asset-form', 'mamAFObj', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			// 'nonce'  =>  wp_create_nonce("mam_asset_update"),
			// 'action_keywords' => 'ajax_get_keywords',
			// 'action_duplicate_checker' => 'ajax_check_ar_duplicate_media_assignment',// rewrite ajax
			// 'action_get_dest_file_names' => 'ajax_action_get_dest_file_names',
			// 'action_add_asset' => 'ajax_add_ar_asset',/// rewrite ajax
			// 'media_type' => $args['media_type'],
			// 'img_key' => $args['img_key'],
			// 'vid_key' => $args['vid_key'],
			// 'drawing_key' => $args['drawing_key'],
			// 'artwork_key' => $args['artwork_key'],
			// 'manual_key' => $args['manual_key'],
			'media_structure' => $args['media_structure']

			)
		);
		// wp_enqueue_script( 'media-edit-from-prod-page' );
		wp_enqueue_script( 'mam-ar-asset-form' );
}

public function display_media_fields($prod_id) {
	$data = $this->get_media_groups_and_assignments();
	$media_groups = $this->generate_media_groups_and_assignments_structure($data);
	$this->generate_media_html($media_groups, $prod_id);
}

// public function generate_field_groups_structure($product_id) {
// 	global $wpdb;

// 	// Fetch table metas and corresponding field metas
// 	$results = $wpdb->get_results($wpdb->prepare("
// 			SELECT
// 					tm.id as table_id,
// 					tm.title as table_title,
// 					tm.table_name,
// 					tm.display_order as table_display_order,
// 					tm.brand_id,
// 					tm.is_class_specific,
// 					tm.CONFIG as table_config,
// 					GROUP_CONCAT(fm.id ORDER BY fm.display_order) as field_ids,
// 					GROUP_CONCAT(fm.title ORDER BY fm.display_order) as field_titles,
// 					GROUP_CONCAT(fm.field_name ORDER BY fm.display_order) as field_names,
// 					GROUP_CONCAT(fm.user_defined_type ORDER BY fm.display_order) as field_types,
// 					GROUP_CONCAT(fm.CONFIG ORDER BY fm.display_order) as field_configs,
// 					GROUP_CONCAT(fm.is_erp ORDER BY fm.display_order) as field_is_erp,
// 					b.code as brand_code
// 			FROM
// 					pim_table_metas tm
// 			LEFT JOIN
// 					pim_field_metas fm
// 			ON
// 					tm.id = fm.table_meta_id
// 			LEFT JOIN
// 					pim_brands b
// 			ON
// 					tm.brand_id = b.id
// 			GROUP BY
// 					tm.id
// 			ORDER BY
// 					tm.display_order",
// 			$product_id), ARRAY_A);

// 			$this->dump($results);
// 	// Initialize the structure
// 	$structure = ['field_groups' => []];

// 	// Process results
// 	foreach ($results as $row) {
// 			// Start a new table group
// 			$table_config = json_decode($row['table_config'], true);
// 			$current_table = [
// 					'id' => $row['table_id'],
// 					'name' => $row['table_title'],
// 					'slug' => $row['table_name'],
// 					'display_order' => $row['table_display_order'],
// 					'brand_id' => $row['brand_id'],
// 					'color' => !empty($table_config['ui_color']) ? $table_config['ui_color'] : '#000000',
// 					'is_class_specific' => $row['is_class_specific'],
// 					'fields' => []
// 			];

// 			// Determine the dynamic table name
// 			$dynamic_table_name = "pim_{$row['brand_code']}_{$row['table_name']}";

// 			// Fetch data from the dynamic table
// 			$dynamic_data = $wpdb->get_row($wpdb->prepare("
// 					SELECT *
// 					FROM `{$dynamic_table_name}`
// 					WHERE product_id = %d",
// 					$product_id), ARRAY_A);

// 			// Process each field in the group
// 			if (!empty($row['field_ids'])) {
// 					$field_ids = explode(',', $row['field_ids']);
// 					$field_titles = explode(',', $row['field_titles']);
// 					$field_names = explode(',', $row['field_names']);
// 					$field_types = explode(',', $row['field_types']);
// 					$field_configs = explode('},{', trim($row['field_configs'], '{}'));
// 					$field_is_erp = explode(',', $row['field_is_erp']);

// 					foreach ($field_ids as $index => $field_id) {
// 							$field_config = json_decode("{" . $field_configs[$index] . "}", true);
// 							$field_data = [
// 									'id' => $field_id,
// 									'name' => $field_titles[$index],
// 									'slug' => $field_names[$index],
// 									'table_meta_id' => $row['table_id'],
// 									'field_type' => $field_types[$index],
// 									'field_options' => $field_config['field_options'] ?? [],
// 									'placeholder' => $field_config['placeholder'] ?? '',
// 									'display_order' => $index, // Assume display order from index
// 									'is_erp' => $field_is_erp[$index],
// 									'value' => $dynamic_data[$field_names[$index]] ?? null
// 							];

// 							$current_table['fields'][] = $field_data;
// 					}
// 			}

// 			// Add the current table to the structure
// 			$structure['field_groups'][] = $current_table;

// 		}

// 		$this->dump($structure);
// 	// return $structure;
// }

public function get_fields_and_tables_from_db($product_id) {
	global $wpdb;

	$results = $wpdb->get_results("
	SELECT
			tm.id as table_id,
			tm.title as table_title,
			tm.table_name,
			tm.display_order as table_display_order,
			tm.brand_id,
			tm.is_class_specific,
			tm.CONFIG as table_config,
			fm.id as field_id,
			fm.title as field_title,
			fm.field_name,
			fm.display_order as field_display_order,
			fm.user_defined_type as field_type,
			fm.CONFIG as field_config,
			fm.is_erp as field_is_erp,
			b.code as brand_code
	FROM
			pim_table_metas tm
	LEFT JOIN
			pim_field_metas fm
	ON
			tm.id = fm.table_meta_id
	LEFT JOIN
			pim_brands b
	ON
			tm.brand_id = b.id
	WHERE
			fm.id IS NOT NULL", ARRAY_A);
	// $results = $wpdb->get_results($wpdb->prepare("
	// SELECT
	// 		tm.id as table_id,
	// 		tm.title as table_title,
	// 		tm.table_name,
	// 		tm.display_order as table_display_order,
	// 		tm.brand_id,
	// 		tm.is_class_specific,
	// 		tm.CONFIG as table_config,
	// 		fm.id as field_id,
	// 		fm.title as field_title,
	// 		fm.field_name,
	// 		fm.display_order as field_display_order,
	// 		fm.user_defined_type as field_type,
	// 		fm.CONFIG as field_config,
	// 		fm.is_erp as field_is_erp,
	// 		b.code as brand_code
	// FROM
	// 		pim_table_metas tm
	// LEFT JOIN
	// 		pim_field_metas fm
	// ON
	// 		tm.id = fm.table_meta_id
	// LEFT JOIN
	// 		pim_brands b
	// ON
	// 		tm.brand_id = b.id
	// WHERE
	// 		fm.id IS NOT NULL",
	// $product_id), ARRAY_A);

	return $results;
}

public function generate_field_groups_structure($product_id) {
	if (empty($product_id)) return;


	$results = $this->get_fields_and_tables_from_db($product_id);

	// Initialize the structure
	$structure = ['field_groups' => []];

	global $wpdb;

	// Variables for completion percentage calculation
	$total_weightage = 0;
	$achieved_weightage = 0;

	// Process results
	foreach ($results as $row) {

		// check tables permissions
		if($this->ck_perm('tables', $row['table_id']) == 'HIDE') {
			continue;
		}

			// Start a new table group if it does not exist
			if (!isset($structure['field_groups'][$row['table_id']])) {
					$table_config = json_decode($row['table_config'], true);
					$structure['field_groups'][$row['table_id']] = [
							'id' => $row['table_id'],
							'name' => $row['table_title'],
							'slug' => $row['table_name'],
							'display_order' => $row['table_display_order'],
							'brand_id' => $row['brand_id'],
							'color' => !empty($table_config['ui_color']) ? $table_config['ui_color'] : '#000000',
							'is_class_specific' => $row['is_class_specific'],
							'background_color' => !empty($table_config['background_color']) ? $table_config['background_color'] : '#000000',
							'text_color' => !empty($table_config['text_color']) ? $table_config['text_color'] : '#ffffff',
							'fields' => []
					];
			}


			if($this->ck_perm('fields', $row['field_id']) == 'HIDE') {
				continue;
			}

			// Determine the dynamic table name
			$dynamic_table_name = "pim_{$row['brand_code']}_{$row['table_name']}";

			// Fetch data from the dynamic table
			$dynamic_data = $wpdb->get_row($wpdb->prepare("
					SELECT *
					FROM `{$dynamic_table_name}`
					WHERE product_id = %d",
					$product_id), ARRAY_A);

			// Process the field configuration
			$field_config = json_decode($row['field_config'], true);

			// Field value
			$field_value = isset($dynamic_data[$row['field_name']]) ? $dynamic_data[$row['field_name']] : $field_config['default_value'];

			$field_value = stripslashes(htmlspecialchars_decode($field_value));

if($row['field_type'] == 'range') {
	if($this->isJson($field_value) ) {
		$field_value = json_decode($field_value);
		// if not value exists in dynamic table then show default value
		if(!trim($field_value[0]) && !trim($field_value[1])) {
			$field_value = $field_config['range'];
		}
	} else {
		$field_value = $field_config['range'];
	}
}

if($row['field_type'] == 'checkbox') {
		if($this->isJson($field_value)) {
			$field_value = json_decode($field_value);
			if(empty($field_value)) {
				$field_value = [$field_config['default_value']];
			}
		} else {
		$field_value = [$field_config['default_value']];
	}
}

if($row['field_type'] == 'products') {
	if($field_value) {
		$field_value = json_decode($field_value);
	} else {
		$field_value = [];
	}
}

if($row['field_type'] == 'repeater') {
	// $field_value = json_decode($field_value);
	if($field_value) {
		$field_value = json_decode($field_value);
	} else {
		$field_value = [];
	}
}
// 	if($this->isJson($field_value)) {
// 		$field_value = json_decode($field_value);
// 		if(empty($field_value)) {
// 			$field_value = [$field_config['default_value']];
// 		}
// 	} else {
// 	$field_value = [$field_config['default_value']];
// }


// if($row['field_type'] == 'radio') {
	// if($this->isJson($field_value) ) {
		// $field_value = json_decode($field_value);
		// if not value exists in dynamic table then show default value
		// if(!trim($field_value[0]) && !trim($field_value[1])) {
		// 	$field_value = $field_config['range'];
		// }
	// } else {
	// if(!$field_value) {
	// 	$field_value = $field_config['range'];
	// }
	// }
// }
// if(sizeof($field_value) == 2) {
	// }

			// if()
			// if($this->isJson($field_value)) {

				// if(!$field_value) {
				// 	$field_value = $field_config['range'];
				// }

			// 	if(json_last_error() ) {
			// 		echo json_last_error_msg();
			// 	}

			// echo 'b2 => ' . $row['field_title'];
			// echo "asdasdasd";

		// } else {
		// 		echo 'A1 => '. $row['field_title'];
		// 		$field_value = json_decode($field_value);
		// 	}
			// $field_value = stripslashes(htmlspecialchars_decode($field_value));
			// $field_range = $field_config['range'];

			// is_array($field_range) ? json_encode($field_range) :  json_validate($field_range) ? json_decode($field_range) : $field_range;

			// if(is_array($field_value)) {

			// 	$field_value = json_encode($field_value);

			// } elseif(json_validate($field_value)) {

			// 	$field_value = json_decode($field_value);

			// }

			// Calculate total weightage and achieved weightage
			// $completion_weightage = isset($field_config['completion_weightage']) ? (float)$field_config['completion_weightage'] : 0;

			// if ($completion_weightage > 0) {
			// 	$total_weightage += $completion_weightage;
			// 	// echo 'C => ' . $completion_weightage;
			// 	// echo 'T => ' . $total_weightage;

			// 		// Check if the field has a value
			// 		if (!empty($field_value) && $field_value !== null) {
			// 				$achieved_weightage += $completion_weightage;
			// 				// echo 'A => ' . $achieved_weightage;
			// 		}
			// }




			// Add the field to the current table group
			$structure['field_groups'][$row['table_id']]['fields'][] = [
					'id' => $row['field_id'],
					'name' => $row['field_title'],
					'slug' => $row['field_name'],
					'table_meta_id' => $row['table_id'],
					'field_type' => $row['field_type'],
					'field_options' => isset($field_config['field_options']) ? $field_config['field_options'] : [],
					'placeholder' => isset($field_config['placeholder']) ? $field_config['placeholder'] : '',
					// 'display_order' => count($structure['field_groups'][$row['table_id']]['fields']),
					'display_order' => $row['field_display_order'],
					"completion_weightage" => isset($field_config['completion_weightage']) ? $field_config['completion_weightage'] : '',
					"seller_apps"=> isset($field_config['seller_apps']) ? $field_config['seller_apps'] : [],
					'is_erp' => $row['field_is_erp'],
					'unit' => $field_config['unit'],
					'selected_products' => $field_config['selected_products'],
					'value' => $field_value,
					// 'range' => $field_config['range']
			];

	}

	// Calculate the completion percentage
	// $completion_percentage = ($total_weightage > 0) ? ($achieved_weightage / 100) * 100 : 0;

	// $structure['completion_percentage'] = $completion_percentage;

	// $this->current_product_completion_weightage = $completion_percentage;

	$completion_percentage = $this->calculate_product_completion_percentage($product_id);

	// update class variable also with same value
	// $completion_percentage = ($total_weightage > 0) ? ($achieved_weightage / $total_weightage) * 100 : 0;
	// echo 'F => ' . $completion_percentage;

	// echo $completion_percentage . ' ' . 'this is original completion percentage';
		// $current_product = $this->get_full_row_from_table('pim_products', $product_id);
		// $completion_percentage = $current_product->is_completed ? 100 : $completion_percentage;

		// Attach completion_percentage to the structure
		// $completion_percentage = 40;
		// $structure['field_groups'] = array_values($structure['field_groups']);

		// Re-index and return the structure
	// return $structure;
	// return array_values($structure['field_groups']);
	return [
		'field_groups' => array_values($structure['field_groups']),
		'completion_percentage' => $completion_percentage
	];

}

public function isJson($str) {
	$json = json_decode($str);
	return $json && $str != $json;
}

public function calculate_product_completion_percentage($product_id) {
	if (empty($product_id)) return;

	$results = $this->get_fields_and_tables_from_db($product_id);

	// Variables for completion percentage calculation
	$total_weightage = 0;
	$achieved_weightage = 0;

	global $wpdb;
	// Process results
	foreach ($results as $row) {

			// Determine the dynamic table name
			$dynamic_table_name = "pim_{$row['brand_code']}_{$row['table_name']}";

			// Fetch data from the dynamic table
			$dynamic_data = $wpdb->get_row($wpdb->prepare("
					SELECT *
					FROM `{$dynamic_table_name}`
					WHERE product_id = %d",
					$product_id), ARRAY_A);

			// Process the field configuration
			$field_config = json_decode($row['field_config'], true);

			// Field value
			$field_value = isset($dynamic_data[$row['field_name']]) ? $dynamic_data[$row['field_name']] : null;

			// Calculate total weightage and achieved weightage
			$completion_weightage = isset($field_config['completion_weightage']) ? (float)$field_config['completion_weightage'] : 0;

			if ($completion_weightage > 0) {
				$total_weightage += $completion_weightage;
				// echo 'C => ' . $completion_weightage;
				// echo 'T => ' . $total_weightage;

					// Check if the field has a value
					if (!empty($field_value) && $field_value !== null) {
							$achieved_weightage += $completion_weightage;
							// echo 'A => ' . $achieved_weightage;
					}
			}
	}

	// Calculate the completion percentage

	// $completion_percentage = ($total_weightage > 0) ? ($achieved_weightage / $total_weightage) * 100 : 0;
	$completion_percentage = ($total_weightage > 0) ? ($achieved_weightage / 100) * 100 : 0;
	// $completion_percentage = 60;

	$this->current_product_completion_weightage = $completion_percentage;

	return $completion_percentage;

}


public function get_ar_sidebar_filters() {
	// return $wpdb->get_results($wpdb->prepare("SELECT * FROM pim_taxonomy_terms WHERE tax_id = 1"));
	return $this->get_specific_rows_from_table('pim_taxonomy_terms', 'tax_id', 1);
}


// WORKING FUNC ****************************************************
// public function generate_field_groups_structure($product_id) {

// 	if(empty($product_id)) return;

// 		global $wpdb;

// 		// Fetch table metas and corresponding field metas
// 		$results = $wpdb->get_results($wpdb->prepare("
// 				SELECT
// 						tm.id as table_id,
// 						tm.title as table_title,
// 						tm.table_name,
// 						tm.display_order as table_display_order,
// 						tm.brand_id,
// 						tm.is_class_specific,
// 						tm.CONFIG as table_config,
// 						fm.id as field_id,
// 						fm.title as field_title,
// 						fm.field_name,
// 						fm.user_defined_type as field_type,
// 						fm.CONFIG as field_config,
// 						fm.is_erp as field_is_erp,
// 						b.code as brand_code
// 				FROM
// 						pim_table_metas tm
// 				LEFT JOIN
// 						pim_field_metas fm
// 				ON
// 						tm.id = fm.table_meta_id
// 				LEFT JOIN
// 						pim_brands b
// 				ON
// 						tm.brand_id = b.id
// 				WHERE
// 						fm.id IS NOT NULL",
// 				$product_id), ARRAY_A);

// 				//
// 				// ORDER BY
// 				// tm.display_order, fm.display_order

// 		// Initialize the structure
// 		$structure = ['field_groups' => []];

// 		// Process results
// 		foreach ($results as $row) {
// 				// Start a new table group if it does not exist
// 				if (!isset($structure['field_groups'][$row['table_id']])) {
// 						$table_config = json_decode($row['table_config'], true);
// 						$structure['field_groups'][$row['table_id']] = [
// 								'id' => $row['table_id'],
// 								'name' => $row['table_title'],
// 								'slug' => $row['table_name'],
// 								'display_order' => $row['table_display_order'],
// 								'brand_id' => $row['brand_id'],
// 								'color' => !empty($table_config['ui_color']) ? $table_config['ui_color'] : '#000000',
// 								'is_class_specific' => $row['is_class_specific'],
// 								'fields' => []
// 						];
// 				}

// 				// Determine the dynamic table name
// 				$dynamic_table_name = "pim_{$row['brand_code']}_{$row['table_name']}";

// 				// Fetch data from the dynamic table
// 				$dynamic_data = $wpdb->get_row($wpdb->prepare("
// 						SELECT *
// 						FROM `{$dynamic_table_name}`
// 						WHERE product_id = %d",
// 						$product_id), ARRAY_A);

// 				// $this->dump($dynamic_table_name);

// 				// Process the field configuration
// 				$field_config = json_decode($row['field_config'], true);

// 				// Add the field to the current table group
// 				$structure['field_groups'][$row['table_id']]['fields'][] = [
// 						'id' => $row['field_id'],
// 						'name' => $row['field_title'],
// 						'slug' => $row['field_name'],
// 						'table_meta_id' => $row['table_id'],
// 						'field_type' => $row['field_type'],
// 						'field_options' => isset($field_config['field_options']) ? $field_config['field_options'] : [],
// 						'placeholder' => isset($field_config['placeholder']) ? $field_config['placeholder'] : '',
// 						'display_order' => count($structure['field_groups'][$row['table_id']]['fields']),
// 						'is_erp' => $row['field_is_erp'],
// 						'value' => isset($dynamic_data[$row['field_name']]) ? $dynamic_data[$row['field_name']] : null
// 				];
// 		}
// 		// $this->dump($structure['field_groups']);
// 		// Re-index and return the structure
// 		return array_values($structure['field_groups']);
// 		// return $structure['field_groups'];

// }


public function get_sku_folder_name($sku = ""){
	if(empty($sku)){
		return false;
	}
	$folder = str_replace("/","-",$sku);
	$folder = str_replace(".","",$folder);
	return $folder;
}

public function get_product_folder_path($prod_id = 0){
	if($prod_id == 0){
		return false;
	}

	// new way
	$sku =  $this->get_var_from_table('pim_products', 'sku', $prod_id);
	$brand_id  = $this->get_var_from_table('pim_products', 'brand_id', $prod_id);
	$brand  = $this->get_full_row_from_table('pim_brands', $brand_id);

	// old way
	if(empty($sku) && empty($brand)) {
		$sku = get_post_meta($prod_id, "sku", true);
		$brand  = get_post_meta( $prod_id , "brand", true );
	}

	$brand_root = $brand->code;

	$folder_name = $this->get_sku_folder_name($sku);
	$path = $brand_root."/".$folder_name."/";
	return $path;
}

public function get_sub_brands_categories() {
	// global $wpdb;
	// id of sub_brand_category Taxonomy is 10 (used as a foreign key (as tax_id) in pim_taxonomy_terms)
	return $this->get_specific_rows_from_table('pim_taxonomy_terms', 'tax_id', 10);

	// $cat = $wpdb->get_row($wpdb->prepare('SELECT * from pim_taxonomy_terms WHERE id = 10'));
	// return $this->get_all_cols_in_one_to_many('pim_taxonomies_products', 'term_id', $cat->id);
}

public function get_media_groups() {
	// global $wpdb;
	// id of sub_brand_category Taxonomy is 10 (used as a foreign key (as tax_id) in pim_taxonomy_terms)
	return $this->get_all_rows_and_cols_from_table('pim_media_groups');

	// $cat = $wpdb->get_row($wpdb->prepare('SELECT * from pim_taxonomy_terms WHERE id = 10'));
	// return $this->get_all_cols_in_one_to_many('pim_taxonomies_products', 'term_id', $cat->id);
}

public function get_current_sub_brands_category() {

	if( (get_query_var( 'pg'  ) == "products") && (get_query_var( 'ptype'  ) == "category") ) {

		$cat_slug = get_query_var( 'cat_slug'  );

		// global $wpdb;

		return $this->get_specific_rows_from_table('pim_taxonomy_terms', 'slug', $cat_slug)[0];
		// return $wpdb->get_row($wpdb->prepare("SELECT * from pim_taxonomy_terms WHERE slug = %s", $cat_slug));

	}

	return null;

}

public function get_current_media_group() {

	if( (get_query_var( 'pg'  ) == "mam") && (get_query_var( 'ptype'  ) == "media_type") ) {

		$media_type = get_query_var( 'media_type'  );

		// global $wpdb;

		return $this->get_specific_rows_from_table('pim_media_groups', 'slug', $media_type)[0];
		// return $wpdb->get_row($wpdb->prepare("SELECT * from pim_taxonomy_terms WHERE slug = %s", $cat_slug));

	}

	return null;

}

public function get_current_brand() {

	$query_brand_str = get_query_var('brand');

	if( $this->is_this_main_page() && !$query_brand_str ) $query_brand_str = 'kvi';

	$brand = $this->get_specific_rows_from_table('pim_brands', 'name', $query_brand_str)[0];

	if($brand) return $brand;

	return null;
	// return 'kvi';
}

public function setup_products_filters(){
	if(empty($this->products_filters)){

		$current_brand = $this->get_current_brand()->name;

		$this->products_filters = new ProductsFilters($current_brand);
	}
	$this->products_filters->display_filters();
}

public function setup_mam_filters(){
	if(empty($this->mam_filters)){
		$this->mam_filters = new MAMFilters();
	}
	$this->mam_filters->display_filters();
}


public function get_current_brand_name() {

	return $this->get_current_brand()->name;

}

function is_this_main_page() {

	return is_front_page();

}


public function send_field_sync_request_to_syndication($body) {

	global $wpdb;

	$field_id = (int) $body['id'];

	if($field_id) {
		$wpdb->query("UPDATE `pim_field_metas` SET is_sync = 0 WHERE id = $field_id ");
	}


	$url = 'https://syndicate.arrowpim.com/api/add_fields';

	$token = 'jMhHXghnYBTpJpacDARvBA0xoud4MENPv6jzqQqFb2izKvbHFmjOtRDgI1dezlzL';

	$args = array(
    'headers' => array(
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json'
        ),
   'body' => json_encode($body)
	);

	// $this->dump($args);
	$request = wp_remote_post($url, $args);

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( "ERROR FOR : send_field_sync_request_to_syndication() ");
		error_log( print_r( $request, true ) );
		return ['request' => $request];
	}

	$response = wp_remote_retrieve_body( $request );
	error_log( "RESPONSE FOR : $response ");

	if($field_id) {
		$wpdb->query("UPDATE `pim_field_metas` SET is_sync = 1 WHERE id = $field_id ");
	}

	return [
		'done' => true,
		'data' => ['response' => $response],
		'field_id' => $field_id
	] ;

}

// public function generate_field_groups_structure($product_id) {
// 	global $wpdb;

// 	// Fetch table metas and corresponding field metas
// 	$results = $wpdb->get_results($wpdb->prepare("
// 			SELECT
// 					tm.id as table_id,
// 					tm.title as table_title,
// 					tm.table_name,
// 					tm.display_order as table_display_order,
// 					tm.brand_id,
// 					tm.is_class_specific,
// 					tm.CONFIG as table_config,
// 					fm.id as field_id,
// 					fm.title as field_title,
// 					fm.field_name,
// 					fm.table_meta_id,
// 					fm.user_defined_type,
// 					fm.CONFIG as field_config,
// 					fm.display_order as field_display_order,
// 					fm.is_erp,
// 					b.code as brand_code
// 			FROM
// 					pim_table_metas tm
// 			LEFT JOIN
// 					pim_field_metas fm
// 			ON
// 					tm.id = fm.table_meta_id
// 			LEFT JOIN
// 					pim_brands b
// 			ON
// 					tm.brand_id = b.id
// 			ORDER BY
// 					tm.display_order,
// 					fm.display_order",
// 			$product_id), ARRAY_A);

// 	// Initialize the structure
// 	$structure = ['field_groups' => []];

// 	// Process results
// 	$current_table_id = null;
// 	$current_table = null;

// 	foreach ($results as $row) {
// 			if ($current_table_id !== $row['table_id']) {
// 					// Save the current table if exists
// 					if ($current_table !== null) {
// 							$structure['field_groups'][] = $current_table;
// 					}

// 					// Start a new table group
// 					$table_config = json_decode($row['table_config'], true);
// 					$current_table = [
// 							'id' => $row['table_id'],
// 							'name' => $row['table_title'],
// 							'slug' => $row['table_name'],
// 							'display_order' => $row['table_display_order'],
// 							'brand_id' => $row['brand_id'],
// 							'color' => !$table_config['ui_color'] ? '#000000' : $table_config['ui_color'],
// 							'is_class_specific' => $row['is_class_specific'],
// 							'fields' => []
// 					];
// 					$current_table_id = $row['table_id'];

// 					// Determine the dynamic table name
// 					$dynamic_table_name = "pim_{$row['brand_code']}_{$row['table_name']}";

// 					// Fetch data from the dynamic table
// 					$dynamic_data = $wpdb->get_row($wpdb->prepare("
// 							SELECT *
// 							FROM `{$dynamic_table_name}`
// 							WHERE product_id = %d",
// 							$product_id), ARRAY_A);
// 			}

// 			// Add field if it exists
// 			if ($row['field_id'] !== null) {
// 					$field_config = json_decode($row['field_config'], true);
// 					$field_data = [
// 							'id' => $row['field_id'],
// 							'name' => $row['field_title'],
// 							'slug' => $row['field_name'],
// 							'table_meta_id' => $row['table_meta_id'],
// 							'field_type' => $row['user_defined_type'],
// 							'field_options' => $field_config['field_options'],
// 							'placeholder' => $field_config['placeholder'],
// 							'display_order' => !$row['field_display_order'] ? 0 : $row['field_display_order'],
// 							'is_erp' => $row['is_erp']
// 					];

// 					// Check if dynamic table column matches field name and add "value" key
// 					if (isset($dynamic_data[$row['field_name']])) {
// 							$field_data['value'] = $dynamic_data[$row['field_name']];
// 					} else {
// 							$field_data['value'] = null;
// 					}

// 					$current_table['fields'][] = $field_data;
// 			}
// 	}

// 	// Add the last table group
// 	if ($current_table !== null) {
// 			$structure['field_groups'][] = $current_table;
// 	}
// 	// Convert the structure array to a JSON string
// 	// $json_string = json_encode($structure);
// 	// $this->field_groups_data = json_decode($json_string, true);
// 	// return $json_string;

// 	// return $this->field_groups_data = $structure;

// 	return $structure;
// }


}

//  Table metas and field metas
/*
{
    "field_groups": [
        {
            "id": "15",
            "name": "Table color test",
            "slug": "table-color-test",
            "display_order": "0",
            "brand_id": "1",
            "color": "#00f094",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "75",
                    "name": "Field 1",
                    "slug": "field-1",
                    "table_meta_id": "15",
                    "field_type": "number",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "enter number",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "11",
            "name": "Table 1",
            "slug": "table-1",
            "display_order": "1",
            "brand_id": "1",
            "color": "magenta",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "66",
                    "name": "field 1 table 1",
                    "slug": "field-1-table-1",
                    "table_meta_id": "11",
                    "field_type": "number",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "field 1 placeholder",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "6",
            "name": "abctest",
            "slug": "abctest",
            "display_order": "2",
            "brand_id": "1",
            "color": "green",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "64",
                    "name": "field 1 table 2",
                    "slug": "field-1-table-2",
                    "table_meta_id": "6",
                    "field_type": "text",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                },
                {
                    "id": "65",
                    "name": "field 2 table 2",
                    "slug": "field-2-table-2",
                    "table_meta_id": "6",
                    "field_type": "textarea",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "enter data",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "12",
            "name": "table 3",
            "slug": "table-3",
            "display_order": "3",
            "brand_id": "1",
            "color": "green",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "67",
                    "name": "table 3 field 1",
                    "slug": "table-3-field-1",
                    "table_meta_id": "12",
                    "field_type": "textarea",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "table 3 field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "13",
            "name": "table 4",
            "slug": "table-4",
            "display_order": "4",
            "brand_id": "1",
            "color": "red",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "68",
                    "name": "table 4 field 1",
                    "slug": "table-4-field-1",
                    "table_meta_id": "13",
                    "field_type": "text",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "table 4 field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "14",
            "name": "table 5",
            "slug": "table-5",
            "display_order": "5",
            "brand_id": "1",
            "color": "blue",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "69",
                    "name": "table 5 field 1",
                    "slug": "table-5-field-1",
                    "table_meta_id": "14",
                    "field_type": "number",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "table 5 field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "7",
            "name": "Table test 6",
            "slug": "table-test-6",
            "display_order": "6",
            "brand_id": "1",
            "color": "cyan",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "70",
                    "name": "field 1",
                    "slug": "field-1",
                    "table_meta_id": "7",
                    "field_type": "textarea",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "8",
            "name": "MK1",
            "slug": "mk1",
            "display_order": "7",
            "brand_id": "1",
            "color": "white",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "71",
                    "name": "field 1",
                    "slug": "field-1",
                    "table_meta_id": "8",
                    "field_type": "text",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "9",
            "name": "mk2",
            "slug": "mk2",
            "display_order": "8",
            "brand_id": "1",
            "color": "red",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "72",
                    "name": "field 1",
                    "slug": "field-1",
                    "table_meta_id": "9",
                    "field_type": "text",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        },
        {
            "id": "10",
            "name": "test 365",
            "slug": "test-365",
            "display_order": "9",
            "brand_id": "1",
            "color": "gray",
            "is_class_specific": "0",
            "fields": [
                {
                    "id": "61",
                    "name": "first field",
                    "slug": "first-field",
                    "table_meta_id": "10",
                    "field_type": "number",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": "2500"
                },
                {
                    "id": "62",
                    "name": "sbcddd",
                    "slug": "sbcddd",
                    "table_meta_id": "10",
                    "field_type": "text",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "dwq2d",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": "wefwerf"
                },
                {
                    "id": "73",
                    "name": "field 1",
                    "slug": "field-1",
                    "table_meta_id": "10",
                    "field_type": "number",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "field 1",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": "0"
                },
                {
                    "id": "74",
                    "name": "field 44",
                    "slug": "field-44",
                    "table_meta_id": "10",
                    "field_type": "number",
                    "field_options": [
                        ""
                    ],
                    "placeholder": "f4",
                    "display_order": 0,
                    "is_erp": "0",
                    "value": null
                }
            ]
        }
    ]
}
*/



// media groups and assignments
// array(4) {
//   [0]=>
//   array(6) {
//     ["id"]=>
//     string(1) "1"
//     ["group_name"]=>
//     string(14) "Media Group 01"
//     ["group_slug"]=>
//     string(14) "media-group-01"
//     ["is_class_specific"]=>
//     string(1) "0"
//     ["display_order"]=>
//     string(1) "0"
//     ["assignments"]=>
//     array(1) {
//       [0]=>
//       array(5) {
//         ["id"]=>
//         string(1) "6"
//         ["assignment_name"]=>
//         string(6) "File 1"
//         ["assignment_slug"]=>
//         string(6) "file-1"
//         ["file_type"]=>
//         string(5) "image"
//         ["display_order"]=>
//         string(1) "0"
//       }
//     }
//   }
//   [1]=>
//   array(6) {
//     ["id"]=>
//     string(1) "2"
//     ["group_name"]=>
//     string(14) "Media Group 02"
//     ["group_slug"]=>
//     string(14) "media-group-02"
//     ["is_class_specific"]=>
//     string(1) "0"
//     ["display_order"]=>
//     string(1) "0"
//     ["assignments"]=>
//     array(0) {
//     }
//   }
//   [2]=>
//   array(6) {
//     ["id"]=>
//     string(1) "3"
//     ["group_name"]=>
//     string(14) "Media Group 03"
//     ["group_slug"]=>
//     string(14) "media-group-03"
//     ["is_class_specific"]=>
//     string(1) "0"
//     ["display_order"]=>
//     string(1) "0"
//     ["assignments"]=>
//     array(0) {
//     }
//   }
//   [3]=>
//   array(6) {
//     ["id"]=>
//     string(1) "7"
//     ["group_name"]=>
//     string(14) "Media Group 04"
//     ["group_slug"]=>
//     string(14) "media-group-04"
//     ["is_class_specific"]=>
//     string(1) "0"
//     ["display_order"]=>
//     string(1) "0"
//     ["assignments"]=>
//     array(1) {
//       [0]=>
//       array(5) {
//         ["id"]=>
//         string(1) "5"
//         ["assignment_name"]=>
//         string(12) "Assignment 1"
//         ["assignment_slug"]=>
//         string(12) "assignment-1"
//         ["file_type"]=>
//         string(5) "audio"
//         ["display_order"]=>
//         string(1) "0"
//       }
//     }
//   }
// }