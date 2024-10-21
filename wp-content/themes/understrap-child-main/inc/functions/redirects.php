<?php
function authentication_redirect(){
	if(!is_page("shared-file") && !is_page("reset-password") && !is_page("password-updated") && !is_page("email-sent") && (get_query_var('pg')!='run-cron')){
		if(is_page("login") && is_user_logged_in(  )){
			// $sm = StateManager::GI();

			// $slug = get_post_meta($sm->current_user->user_brands[0], 'url_slug', true);

				wp_redirect(home_url("/"));
			// wp_redirect( home_url("/") );
			echo "PLEASE see in redirects.php";
			die();
		}

		if(!is_page("login") && !is_user_logged_in(  )){
			wp_redirect( home_url("/login") );
			die();
		}
	}

	if ( function_exists("SimpleLogger") ) {

		global $wp;
        // Most basic example: just add some information to the log
        SimpleLogger()->info("Page viewed: ".home_url( $wp->request ));

        // A bit more advanced: log events with different severities
        // SimpleLogger()->info("User admin edited page 'About our company'");
        // SimpleLogger()->warning("User 'Jessie' deleted user 'Kim'");
        // SimpleLogger()->debug("Ok, cron job is running!");

	}

}
add_action( "template_redirect", "authentication_redirect");

function hide_admin_bar( $show ) {
	return false;
	// if ( ! current_user_can( 'administrator' ) ) {
	// 	return false;
	// }

	// return $show;
}
add_filter( 'show_admin_bar', 'hide_admin_bar' );

/**
 * Block wp-admin access for non-admins
 */
function block_wp_admin() {
	if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'admin_init', 'block_wp_admin' );



//URL Rewrite
function kvi_rewrite_rule() {

	/**
	 * ---------Products |  Favorites-----------
	 * Rule to match:
	 * website.com/brand_slug/products/add/
	 * website.com/brand_slug/products/view/{p_id}
	 * website.com/brand_slug/products/view/{p_id}/{ter_id}/{ret_id}
	 * website.com/brand_slug/products/edit/{p_id}
	 * website.com/brand_slug/products/category/{cat_slug}
	 */
	add_rewrite_rule( '^(kvi)/products/add/?$', 'index.php?brand=$matches[1]&pg=products&ptype=add&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/products/view/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=products&ptype=view&p_id=$matches[2]&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/products/view/([^/]*)/([^/]*)/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=products&ptype=view&p_id=$matches[2]&ter_id=$matches[3]&ret_id=$matches[4]&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/products/edit/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=products&ptype=edit&p_id=$matches[2]&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/products/type/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=products&ptype=media_type&media_type=$matches[2]&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/products/category/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=products&ptype=category&cat_slug=$matches[2]&allow_brand=$matches[1]', 'top' );


	/**
	 * ---------Permissions-----------
	 * Rule to match:
	 * website.com/manage-permissions
	 */

	add_rewrite_rule( '^permissions-management/?$', 'index.php?brand=kvi&pg=permissions&ptype=management&allow_brand=any', 'top' );




	/**
	 * ---------Media Asset Manager |  Favorites  |  Products | Catalog-----------
	 * Rule to match:
	 * website.com/brand_slug/mam/
	 * website.com/brand_slug/favorites/ ???? --- are they mam or product specific or brand specific?
	 * website.com/kvi/new-mam/
	 */
	add_rewrite_rule( '^(kvi)/(mam|favorites|products|catalog)/?$', 'index.php?brand=$matches[1]&pg=$matches[2]&ptype=index&allow_brand=$matches[1]', 'top' );

	// add_rewrite_rule( '^(kvi)/(new-mam)/?$', 'index.php?brand=$matches[1]&pg=$matches[2]&ptype=index&allow_brand=$matches[1]', 'top' );


		// kvi
		// add_rewrite_rule( '^kvi/new-mam/view/33914/?$', 'index.php?brand=kvi&pg=new-mam&ptype=view&asset_id=33914&allow_brand=kvi', 'top' );

		// add_rewrite_rule( '^kvi/new-mam/view/([^/]*)/?$', 'index.php?brand=kvi&pg=new-mam&ptype=view&asset_id=$matches[1]&allow_brand=kvi', 'top' );

	/**
	 * ---------Media Asset Manager-----------
	 * Rules to match:
	 * website.com/brand_slug/mam/add
	 * website.com/brand_slug/mam/view/{asset_id}
	 * website.com/brand_slug/mam/edit/{asset_id}
	 * website.com/brand_slug/mam/type/{media_type}
	 */
	add_rewrite_rule( '^(kvi)/mam/add/?$', 'index.php?brand=$matches[1]&pg=mam&ptype=add&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/mam/view/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=mam&ptype=view&asset_id=$matches[2]&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/mam/edit/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=mam&ptype=edit&asset_id=$matches[2]&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/mam/type/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=mam&ptype=media_type&media_type=$matches[2]&allow_brand=$matches[1]', 'top' );



	/**
	 * ---------MAM Download Queue-----------
	 * Rules to match:
	 * website.com/download-queue/
	 * website.com/download-queue/view/{queue_id}
	 */
	add_rewrite_rule( '^download-queue/?$', 'index.php?brand=kvi&pg=download-queue&ptype=index&allow_brand=any', 'top' );
	add_rewrite_rule( '^download-queue/view/([^/]*)/?$', 'index.php?brand=kvi&pg=download-queue&ptype=view&queue_id=$matches[1]&allow_brand=any', 'top' );

	/**
	 * ---------Catalogue Manager-----------
	 * Rules to match:
	 * website.com/brand_slug/catalog/add
	 * website.com/brand_slug/catalog/view/{catalog_id}
	 */
	add_rewrite_rule( '^(kvi)/catalog/add/?$', 'index.php?brand=$matches[1]&pg=catalog&ptype=add&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/catalog/view/([^/]*)/?$', 'index.php?brand=$matches[1]&pg=catalog&ptype=view&catalog_id=$matches[2]&allow_brand=$matches[1]', 'top' );

	/**
	 * --------Process Cron------------------
	 * Rules to match
	 * website.com/run-cron/{cron_num}
	 */
	add_rewrite_rule( '^run-cron/([^/]*)/?$', 'index.php?brand=kvi&pg=run-cron&cron_id=$matches[1]&allow_brand=any', 'top' );

	/**
	 * ---------Retail Channels-----------
	 * Rules to match:
	 * website.com/brand_slug/retail-channels/
	 */
	add_rewrite_rule( '^(kvi)/retail-channels/?$', 'index.php?brand=$matches[1]&pg=retail-channels&ptype=index&allow_brand=$matches[1]', 'top' );

	/**
	 * --------- API Monitoring -----------
	 * Rule to match:
	 * website.com/apimonitoring/
	 */
	add_rewrite_rule( '^api-monitoring/?$', 'index.php?brand=kvi&pg=api-monitoring&ptype=index&allow_brand=any', 'top' );

	/**
	 * --------- Search SKU -----------
	 * Rule to match:
	 * website.com/search-results/
	 */
	add_rewrite_rule( '^search-results/?$', 'index.php?brand=kvi&pg=search-results&ptype=index&allow_brand=any', 'top' );

	/**
	 * ---------Sales Territories-----------
	 * Rule to match:
	 * website.com/sales_territories/
	 * website.com/sales_territories/view/{ter_id}
	 * website.com/sales_territories/view/{ter_id}/{ret_id}
	 */
	add_rewrite_rule( '^sales-territories/?$', 'index.php?brand=kvi&pg=sales-territories&ptype=index&allow_brand=any', 'top' );
	add_rewrite_rule( '^sales-territories/view/([^/]*)/?$', 'index.php?brand=kvi&ter_id=$matches[1]&pg=sales-territories&ptype=view&allow_brand=any', 'top' );
	add_rewrite_rule( '^sales-territories/view/([^/]*)/([^/]*)/?$', 'index.php?brand=kvi&ter_id=$matches[1]&ter_id=$matches[2]&pg=sales-territories&ptype=view&allow_brand=any', 'top' );

	/**
	 * ---------Sell Sheets-----------
	 * Rules to match:
	 * website.com/brand_slug/sell-sheet/{sell_sheet_id}
	 */
	add_rewrite_rule( '^(kvi)/sell-sheet/([^/]*)/?$', 'index.php?brand=$matches[1]&p_id=$matches[2]&pg=sell-sheet&ptype=view&allow_brand=$matches[1]', 'top' );


	/**
	 * ---------Notifications-----------
	 * Rules to match:
	 * website.com/brand_slug/notifications/
	 * * website.com/brand_slug/notifications/download/
	 * website.com/brand_slug/notifications/{notif_id}
	 *
	 */
	add_rewrite_rule( '^(kvi)/archived-notifications/?$', 'index.php?brand=$matches[1]&pg=archived-notifications&ptype=index&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/notifications/?$', 'index.php?brand=$matches[1]&pg=notifications&ptype=index&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/notifications/download/?$', 'index.php?brand=$matches[1]&pg=notifications&ptype=download&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^(kvi)/notifications/([^/]*)/?$', 'index.php?brand=$matches[1]&notif_id=$matches[2]&pg=notifications&ptype=view&allow_brand=$matches[1]', 'top' );
	add_rewrite_rule( '^shopvac/shopvac-importer/?$', 'index.php?brand=shopvac[1]&pg=shopvac-importer&ptype=index&allow_brand=$matches[1]', 'top' );

	/**
	 * --------OAuth Google Sheet Importer
	 */
	add_rewrite_rule( '^/gsheetoauth2/?$', 'index.php?pg=gsheetoauth2', 'top' );

	/**
	 * ---------Database Settings-----------
	 * Rule to match:
	 * website.com/settings/db/
	 * website.com/settings/db/tables/
	 * website.com/settings/db/tables/{tbl_id}
	 * website.com/settings/db/taxonomies
	 * website.com/settings/db/taxonomies/{tax_id}
	 * website.com/settings/db/media/
	 * website.com/settings/db/media/{assignment_id}
	 * website.com/settings/db/seller-apps/
	 *
	 */
	add_rewrite_rule( '^settings/db/?$', 'index.php?brand=kvi&pg=settings-db&allow_brand=any', 'top' );
	add_rewrite_rule( '^settings/db/tables/?$', 'index.php?brand=kvi&pg=settings-table&ptype=index&allow_brand=any', 'top' );
	add_rewrite_rule( '^settings/db/tables/([^/]*)/?$', 'index.php?brand=kvi&tbl_id=$matches[1]&pg=settings-table&ptype=view&allow_brand=any', 'top' );

	// taxonomies
	add_rewrite_rule( '^settings/db/taxonomies/?$', 'index.php?brand=kvi&pg=taxonomies&ptype=index&allow_brand=any', 'top' );
	add_rewrite_rule( '^settings/db/taxonomies/([^/]*)/?$', 'index.php?brand=kvi&tax_id=$matches[1]&pg=taxonomies&ptype=view&allow_brand=any', 'top' );

	// media
	add_rewrite_rule( '^settings/db/media/?$', 'index.php?brand=kvi&pg=media&ptype=index&allow_brand=any', 'top' );
	add_rewrite_rule( '^settings/db/media/([^/]*)/?$', 'index.php?brand=kvi&m_grp_id=$matches[1]&pg=media&ptype=view&allow_brand=any', 'top' );

	// SELLER APPS
	add_rewrite_rule( '^settings/db/seller-apps/?$', 'index.php?brand=kvi&pg=seller-apps&ptype=index&allow_brand=any', 'top' );


	/**
	 * Tests
	 */


}
add_action( 'init', 'kvi_rewrite_rule',9 );

function kvi_register_query_var( $vars ) {

	$vars[] = 'asset_id';
	$vars[] = 'p_id';
	$vars[] = 'ptype';
	$vars[] = 'pg';
	$vars[] = 'brand';
	$vars[] = 'category';
	$vars[] = 'cat_slug';
	$vars[] = 'ret_id';
	$vars[] = 'ter_id';
	$vars[] = 'sell_sheet_id';
	$vars[] = 'notif_id';
	$vars[] = 'queue_id';
	$vars[] = 'cron_id';
	$vars[] = 'allow_brand';
	$vars[] = 'catalog_id';
	$vars[] = 'tbl_id';
	$vars[] = 'tax_id';
	$vars[] = 'm_grp_id';
	// echo "<pre>";
	// print_r($vars);
	// exit;
    return $vars;
}
add_filter( 'query_vars', 'kvi_register_query_var' );


function kvi_url_rewrite_templates() {
	$stm = SettingsManager::GI();

	if(is_user_logged_in(  ) && !current_user_can('administrator')){


		// if ( get_query_var( 'allow_brand' ) && 'any' != get_query_var( 'allow_brand' )) {


		// 	if(!in_array($stm->current_brand->brand_id, $stm->current_user->user_brands)){
		// 		$slug = get_post_meta($stm->current_user->user_brands[0], 'url_slug', true);

		// 		wp_redirect(home_url("/"));
		// 		die();
		// 	}

		// }
		// else if ( get_query_var( 'allow_brand' ) && 'any' == get_query_var( 'allow_brand' )) {
			//do nothing
		// }
		// else{
		// 	global $post;
		// 	$brand = get_post_meta($post->ID,'brand', true);
		// 	if(!in_array($brand['ID'], $stm->current_user->user_brands)){

		// 		$mypod = pods( 'brands', intval($stm->current_user->user_brands[0]) );
		// 		$slug = $mypod->field('url_slug');
		// 		wp_redirect(home_url("/".$slug));
		// 	 die();
		// 	}
		// 	// die();

		// }
	}

	if ( get_query_var( 'brand'  )  && (get_query_var( 'ptype'  ) == "index") ) {

		/**
         * Load Products Index Page of Brand
         */
				// echo get_query_var( 'brand'  );
		if(get_query_var( 'pg'  ) == "products"){
			// var_dump(get_query_var( 'brand'  ));
			add_filter( 'template_include', function() {
				if(get_query_var( 'brand'  ) == "kvi") {

					$stm = SettingsManager::GI();

					if($stm->check_page_permission('products-index') == 'HIDE') {
						$stm->page_not_permitted();
					}

					return get_stylesheet_directory() . '/page-templates/kvi-home.php';
				} else {
					return get_stylesheet_directory() . '/page-templates/brand-index.php';
				}
			});
			return;
		}

        /**
         * Load brand's mam index page
         */
		if(get_query_var( 'pg'  ) == "mam"){

			if(get_query_var('brand') == 'kvi') {

				$stm = SettingsManager::GI();

					if($stm->check_page_permission('mam') == 'HIDE') {
						$stm->page_not_permitted();
					}

				add_filter( 'template_include', function() {
					return get_stylesheet_directory() . '/page-templates/new-mam-index.php';
				});
			} else {
				add_filter( 'template_include', function() {
					return get_stylesheet_directory() . '/page-templates/mam-index.php';
				});
			}

			return;
		}


		/**
         * Load kvi's / NEW mam index page
         */
		// if(get_query_var( 'pg'  ) == "new-mam"){
		// 	if ( (get_query_var( 'ptype'  ) == "index") ) {

		// 		add_filter( 'template_include', function() {
		// 			return get_stylesheet_directory() . '/page-templates/new-mam-index.php';
		// 		});

		// 	}
			// else if ( (get_query_var( 'ptype'  ) == "view") ) {
			// 	die("SECCESS");

			// 	add_filter( 'template_include', function() {
			// 		return get_stylesheet_directory() . '/page-templates/new-mam-asset-detail.php';
			// 	});

			// }
		// 	return;
		// }

        /**
         * Load Favorites Index Page of Brand
         */
		if(get_query_var( 'pg'  ) == "favorites"){
			add_filter( 'template_include', function() {

				if(get_query_var( 'brand'  ) == "kvi") {

					$stm = SettingsManager::GI();

					if($stm->check_page_permission('favorites') == 'HIDE') {
						$stm->page_not_permitted();
					}

					return get_stylesheet_directory() . '/page-templates/ar-favorites.php';

				} else {
					return get_stylesheet_directory() . '/page-templates/favorites.php';
				}

			});
			return;
        }

		/**
         * Load Catalog Index Page of Brand
         */
		if(get_query_var( 'pg'  ) == "catalog"){

			$stm = SettingsManager::GI();

					if($stm->check_page_permission('catalogue') == 'HIDE') {
						$stm->page_not_permitted();
					}

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/catalog-index.php';
			});
			return;
        }

        /**
         * Load Retail channels Index Page of Brand
         */
		if(get_query_var( 'pg'  ) == "retail-channels"){

			$stm = SettingsManager::GI();

					if($stm->check_page_permission('retail-channels') == 'HIDE') {
						$stm->page_not_permitted();
					}

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/ret-channels-index.php';
			});
			return;
		}

		/**
         * Load Search Results page of Brand
         */
		if(get_query_var( 'pg'  ) == "search-results"){

			$stm = SettingsManager::GI();

					if($stm->check_page_permission('products-index') == 'HIDE') {
						$stm->page_not_permitted();
					}

			if( get_query_var('brand') == 'kvi' ) {

				add_filter( 'template_include', function() {
					return get_stylesheet_directory() . '/page-templates/new-search-sku.php';
				});

			} else {

				add_filter( 'template_include', function() {
					return get_stylesheet_directory() . '/page-templates/search-sku.php';
				});

			}

			return;
		}

		/**
         * Load Notifications page
         */
		if(get_query_var( 'pg'  ) == "notifications"){
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/notifications.php';
			});
			return;
		}
		/**
         * Load Notifications page
         */
		if(get_query_var( 'pg'  ) == "archived-notifications"){
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/archived_notifications.php';
			});
			return;
		}
		/**
         * Load shopvac Import page
         */
		if(get_query_var( 'pg'  ) == "shopvac-importer"){
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/shopvac-importer.php';
			});
			return;
		}

	}

	/**
	 * Load product's category page
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "products") && (get_query_var( 'ptype'  ) == "category") ) {

		if ( get_query_var( 'brand'  ) == 'kvi') {

			$stm = SettingsManager::GI();

	if($stm->check_page_permission('products-index') == 'HIDE') {
		$stm->page_not_permitted();
}

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/ar-subBrand-category-index.php';
		});

		} else {

			add_filter( 'template_include', function() {
					return get_stylesheet_directory() . '/page-templates/brand-index.php';
			});

}

		return;
	}

	/**
	 * Load product's detail page
	 */
	// if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "products") && (get_query_var( 'ptype'  ) == "view") ) {
  //       add_filter( 'template_include', function() {
  //           return get_stylesheet_directory() . '/page-templates/product-information.php';
	// 	});
	// 	return;
	// }
	if ( get_query_var( 'brand' ) && get_query_var( 'pg' ) == "products" && get_query_var( 'ptype' ) == "view" ) {
    add_filter( 'template_include', function( $template ) {
        $brand = get_query_var( 'brand' );

        if ( $brand == 'kvi' ) {
            return get_stylesheet_directory() . '/page-templates/kvi-product-information.php';
        } else {
            return get_stylesheet_directory() . '/page-templates/product-information.php';
        }
    });
    return;
	}

	/**
	 * Load brand's mam add page
	 */
    if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "mam") && (get_query_var( 'ptype'  ) == "add") ) {

		if(! current_user_can('photo_edit') && ! current_user_can('shopvac_photo_edit')){
			return;
		}
        add_filter( 'template_include', function() {
					if(get_query_var( 'brand'  ) == "kvi")
            return get_stylesheet_directory() . '/page-templates/new-ar-asset.php';
					else
					return get_stylesheet_directory() . '/page-templates/new-asset.php';
		});
		return;
	}

	/**
	 * Load brand's mam view page
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "mam") && (get_query_var( 'ptype'  ) == "view") ) {
if(get_query_var( 'brand'  ) == "kvi"){

	add_filter( 'template_include', function() {
		return get_stylesheet_directory() . '/page-templates/new-mam-asset-detail.php';
	});
}
else{
	add_filter( 'template_include', function() {
		return get_stylesheet_directory() . '/page-templates/mam-asset-detail.php';
});
}

		return;
	}

	/**
	 * Load brand's mam edit page
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "mam") && (get_query_var( 'ptype'  ) == "edit") ) {
		if(! current_user_can('photo_edit') || ! current_user_can('shopvac_photo_edit')){
			return;
		}
        add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/page-templates/mam-asset-detail.php';
		});
		return;
	}

	/**
	 * Load brand's mam media_type page
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "mam") && (get_query_var( 'ptype'  ) == "media_type") ) {

		if(get_query_var( 'brand'  ) == 'kvi'){

			$stm = SettingsManager::GI();

	if($stm->check_page_permission('mam') == 'HIDE') {
		$stm->page_not_permitted();
}

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/new-ar-media-type-mam-index.php';
		});

		} else {

			add_filter( 'template_include', function() {
					return get_stylesheet_directory() . '/page-templates/mam-index.php';
			});

		}
		return;
	}

	/**
	 * Download Queue
	 */
	if ( (get_query_var( 'pg'  ) == "download-queue") ) {
		// Index Page(shows current queue) and single view page
		if(get_query_var( 'ptype'  ) == "index" || get_query_var( 'ptype'  ) == "view"){
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/mam-download-queue.php';
			});
			return;
		}
	}

	/**
	 * Sales Territories
	 */
	if ( (get_query_var( 'pg'  ) == "sales-territories") ) {

		$stm = SettingsManager::GI();

					if($stm->check_page_permission('sales-territories') == 'HIDE') {
						$stm->page_not_permitted();
					}

		// Index Page
		if(get_query_var( 'ptype'  ) == "index"){
			if(! current_user_can('sales_ter_read') || ! current_user_can('shopvac_sales_ter_read')){
				return;
			}
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/sales-territories.php';
			});
			return;
		}

		// Retailer View Page
		if(get_query_var( 'ter_id'  ) && get_query_var( 'ret_id'  ) && get_query_var( 'ptype'  ) == "view" ){
			if(! current_user_can('ret_channels_read') || ! current_user_can('shopvac_ret_channels_read')){
				return;
			}
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/sales-territories.php';
			});
			return;
		}

		// Territory View Page
		if(get_query_var( 'ter_id'  )  && get_query_var( 'ptype'  ) == "view" ){
			if(! current_user_can('sales_ter_read') || ! current_user_can('shopvac_sales_ter_read')){
				return;
			}
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/sales-territories.php';
			});
			return;
		}


	}

	/**
	 * API Monitoring
	 */
	if ( (get_query_var( 'pg'  ) == "api-monitoring") ) {

		// Index Page
		if(get_query_var( 'ptype'  ) == "index"){
			// if(! current_user_can('apimonitoring')){
			// 	return;
			// }
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/apimonitoring.php';
			});
			return;
		}

	}

	/**
	 * Sell Sheets
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "sell-sheet") && (get_query_var( 'ptype'  ) == "view") ) {
        add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/page-templates/sell-sheets.php';
		});
		return;
	}

	/**
	 * Catalogs
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "catalog")){

		/**
		 * Load Catalog's Add Page
		 */
		if(get_query_var( 'ptype'  ) == "add"){
			if(! current_user_can('catalog_edit') || ! current_user_can('shopvac_catalog_edit')){
				return;
			}
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/catalog-add.php';
			});
			return;
		}

		/**
		 * Load Catalog's View Page
		 */
		if(get_query_var( 'ptype'  ) == "view"){
			if(! current_user_can('catalog_edit') || ! current_user_can('shopvac_catalog_edit')){
				return;
			}
			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/catalog-view.php';
			});
			return;
		}

	}


	/**
	 * Load notifications detail page
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "notifications") && (get_query_var( 'ptype'  ) == "view") ) {
        add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/page-templates/notification-detail.php';
		});
		return;
	}

	/**
	 * Load notifications Download page
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "notifications") && (get_query_var( 'ptype'  ) == "download") ) {
        add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/page-templates/notification-download.php';
		});
		return;
	}


	/**
	 * Setup Cron Script
	 */
	if ( get_query_var( 'brand'  ) && (get_query_var( 'pg'  ) == "run-cron") && (get_query_var( 'brand'  ) == "kvi") ) {
        add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/page-templates/cron.php';
		});
		return;
	}

	/**
	 * Setup GSheet Importer
	 */
	if(get_query_var( 'pg'  ) == "gsheetoauth2"){
		add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/page-templates/gsheetsoauthcallback.php';
		});
		return;
	}


	/**
	 * Load Settings Index page
	 */
	if(get_query_var( 'pg'  ) == "settings-db"){

		$stm = SettingsManager::GI();

	if($stm->check_page_permission('admin-settings') == 'HIDE') {
		$stm->page_not_permitted();
}

		add_filter( 'template_include', function() {
			return get_stylesheet_directory() . '/page-templates/settings/db-home.php';
		});
		return;
	}

	/**
	 * Load taxonomies Index page
	 */
	if(get_query_var( 'pg'  ) == "taxonomies"){

		$stm = SettingsManager::GI();

		if($stm->check_page_permission('admin-settings') == 'HIDE') {
			$stm->page_not_permitted();
	}


		// Table list page
		if(get_query_var( 'ptype'  ) == "index"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/taxonomies.php';
			});
			return;
		}

		// Table list page
		if(get_query_var( 'ptype'  ) == "view"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/taxonomy-terms.php';
			});
			return;
		}
	}


	/**
	 * Load SELLER APPS Index page
	 */
	if(get_query_var( 'pg'  ) == "seller-apps"){

		$stm = SettingsManager::GI();

	if($stm->check_page_permission('admin-settings') == 'HIDE') {
		$stm->page_not_permitted();
}

		// Table list page
		if(get_query_var( 'ptype'  ) == "index"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/seller-apps.php';
			});
			return;
		}

	}


	/**
	 * Load media Index page
	 */
	if(get_query_var( 'pg'  ) == "media"){

		$stm = SettingsManager::GI();

	if($stm->check_page_permission('admin-settings') == 'HIDE') {
		$stm->page_not_permitted();
}


		// Table list page
		if(get_query_var( 'ptype'  ) == "index"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/media.php';
			});
			return;
		}

		// Table list page
		if(get_query_var( 'ptype'  ) == "view"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/media-assignments.php';
			});
			return;
		}
	}



		/**
	 * Load Settings > Table Index page
	 */
	if(get_query_var( 'pg'  ) == "settings-table" ){

		$stm = SettingsManager::GI();

	if($stm->check_page_permission('admin-settings') == 'HIDE') {
		$stm->page_not_permitted();
}

		// Table list page
		if(get_query_var( 'ptype'  ) == "index"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/db-tables-index.php';
			});
			return;
		}

		// Table list page
		if(get_query_var( 'ptype'  ) == "view"){

			add_filter( 'template_include', function() {
				return get_stylesheet_directory() . '/page-templates/settings/db-table-crud.php';
			});
			return;
		}

	}



	// Load Permissions Dashboard
	if( get_query_var( 'pg' ) == 'permissions' ) {

		if( get_query_var( 'ptype' ) == 'management' ) {

			$stm = SettingsManager::GI();

	if($stm->check_page_permission('permissions-management') == 'HIDE') {
		$stm->page_not_permitted();
}


		add_filter('template_include', function() {
		return get_stylesheet_directory() . '/page-templates/new-permissions-management.php';
	});

	return;

}

}


}
add_action( 'template_redirect', 'kvi_url_rewrite_templates' );