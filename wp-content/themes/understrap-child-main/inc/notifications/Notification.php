<?php
	/**
	 * Notification Class
	 * It stores the notification
	 */
	class Notification{
		public $notif_id;
		public $notif_link;
		public $item;
		public $item_id;
		public $link;
		public $message;
		public $detailed_message;
		public $is_read;
		public $brand;
		public $user_id;
		public $sku;
		public $date;

		public $item_link;

		public function __construct($notif_id = null){
			//setup ajax handlers

			if( ! is_null($notif_id)){
				$this->notif_id = $notif_id;
				$this->item = get_post_meta($this->notif_id, "object", true);
				$this->item_id = get_post_meta($this->notif_id, "item_id", true);
				$this->message = get_post_meta($this->notif_id, "message", true);
				$this->detailed_message = get_post_meta($this->notif_id, "detailed_message", true);
				$this->is_read = get_post_meta($this->notif_id, "is_read", true);


				if($this->item == "product"){
					$brand = get_post_meta(intval($this->item_id) , "brand", true);
					if(isset($brand['url_slug'])){
						$brand_slug = $brand['url_slug'];
						$this->brand = $brand['brand_name'];
					}
					else{
						$brand_slug = "";
						$this->brand = "";
					}
					$this->item_link = home_url('/'. $brand_slug.'/products/view/').$this->item_id;
					$this->sku = get_post_meta($this->item_id, "sku", true);
				}
				else{
					// $sm = StateManager::GI();
					// $brand_slug  = $sm->current_brand->slug;
					$brand_slug  = 'kvi';
				}
				$this->notif_link = home_url('/'. $brand_slug .'/notifications')."/".$this->notif_id;

				$this->date = get_the_date("", $this->notif_id);
			}
		}

		public function save($email_notify = false){

			$args = array(
				// 'role'    => 'administrator',
				'role__in' => [ 'administrator', 'marketing' ]
			);
			$users = get_users( $args );

			foreach ( $users as $user ) {
				$this->notif_id = wp_insert_post( $arr = array(
					'post_title' => $this->item_id,
					'post_type' => 'notification',
					'post_status' => 'publish'
				), true );
				if(!is_wp_error($this->notif_id)){
					update_post_meta($this->notif_id, "object", $this->item);
					update_post_meta($this->notif_id, "message", $this->message);
					update_post_meta($this->notif_id, "detailed_message", $this->detailed_message);
					update_post_meta($this->notif_id, "is_read", $this->is_read);
					update_post_meta($this->notif_id, "item_id", $this->item_id);
					update_post_meta($this->notif_id, "user_id", $user->ID);
					if($email_notify){
						$this->notify_via_email($user->user_email, $this->detailed_message);
					}

				}
			}

		}

		public function notify_via_email($to, $message){
			wp_mail( $to, "PIM API Notification", $message);
		}

	}