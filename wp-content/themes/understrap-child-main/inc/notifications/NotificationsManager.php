<?php
defined( 'ABSPATH' ) || exit;
// echo get_theme_file_path() ;
require_once( get_theme_file_path() ."/inc/notifications/Notification.php");
	/**
	 * Notifications Manager Class
	 */

	class NotificationsManager{
		public $notifications;
		public $unread_notifications;
		public $max_notifications;
		public $notifs_pods;

		public function __construct($load_ajax = true){
			//setup ajax handlers
			if($load_ajax){
				// add_action( "wp_ajax_ajax_downlaod_action", array($this, "ajax_downlaod_action"));
				add_action( "wp_ajax_ajax_delete_action", array($this, "ajax_delete_action"));
				add_action( "wp_ajax_ajax_mark_all_read_action", array($this, "ajax_mark_all_read_action"));
				add_action( "wp_ajax_ajax_delete_archived_notif", array($this, "ajax_delete_archived_notif"));
			}
		}

		public function load_JS(){
			wp_localize_script( 'notifications-handler', 'NotifObj', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'  =>  wp_create_nonce("notifs_nonce"),
				// "downlaod_action" => "ajax_downlaod_action",
				"delete_action" => "ajax_delete_action",
				"mark_all_read_action" => "ajax_mark_all_read_action",
				"delete_archived_notif_action" => "ajax_delete_archived_notif"
				)
			);
			wp_enqueue_script( 'notifications-handler' );
		}

		public function get_current_notification(){
			$notification_id = get_query_var( "notif_id" );
			//read notification
			update_post_meta($notification_id, "is_read", true);

			$notification = new Notification($notification_id);
			return $notification;
		}

		public function create_notification($item_id, $item, $shortmessage, $message, $email_notify = false){
			$notification = new Notification();
			$notification->item = $item;
			$notification->item_id = $item_id;
			$notification->user_id = 1;
			$notification->message = $shortmessage;
			$notification->detailed_message = $message;
			$notification->is_read = false;
			$notification->save($email_notify);
		}

		public function load_unread_notifications($limit=10){

			$notifications = array();
			//current user id
			// $sm = StateManager::GI();
			// $user_id = $sm->user_id;

			// $params = array(
			// 	'limit' => $limit,
			// 	'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
			// 	'where' => "user_id.id =".$user_id." and d.is_read = false and is_archived = false"
			// );
			// $notifs_pods = pods( 'notification', $params );
			// if( false != $notifs_pods && $notifs_pods->total_found()>0 ){
			// 	while ( $notifs_pods->fetch() ) {
			// 		$notification = new Notification($notifs_pods->field('id'));
			// 		$notifications[] = $notification;
			// 	}
			// }
			return $notifications;
		}

		public function load_latest_notifications($limit=10){

			$this->notifications = array();
			//current user id
			// $sm = StateManager::GI();
			// $user_id = $sm->user_id;

			// $params = array(
			// 	'limit' => $limit,
			// 	'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
			// 	'orderby' => 'post_date desc',
			// 	'where' => "user_id.id =".$user_id." and d.is_archived = false"
			// );
			// $this->notifs_pods = pods( 'notification', $params );
			// if( false != $this->notifs_pods && $this->notifs_pods->total_found()>0 ){
			// 	while ( $this->notifs_pods->fetch() ) {
			// 		$notification = new Notification($this->notifs_pods->field('id'));
			// 		$this->notifications[] = $notification;
			// 	}
			// }
			return $this->notifications;
		}


		public function get_unread_notifications_count(){
			//current user id
			$count = 0;
			// $sm = StateManager::GI();
			// $user_id = $sm->user_id;

			// $params = array(
			// 	'limit' => -1,
			// 	'where' => "user_id.id =".$user_id." and is_read = false and is_archived = false"
			// );
			// $mypod = pods( 'notification', $params );
			// if( false != $mypod && $mypod->total_found()>0 ){
			// 	$count = $mypod->total_found();
			// }
			// $this->unread_notifications = $count;

			return $count;
		}

		public function download_action(){
			// // check_ajax_referer( 'notifs_nonce', 'nonce' );
			// header('Content-Type: application/csv');
			// header('Content-Disposition: attachment; filename="'."notifications.csv".'";');

			// // open the "output" stream
			// // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
			// $f = fopen('php://output', 'w');
			// $params = array(
			// 	'limit' => -1,
			// 	'where' => "user_id.id = ".get_current_user_id( )." and is_archived = false",
			// );
			// $notif_pods = pods( 'notification', $params );
			// if( false != $notif_pods && $notif_pods->total_found()>0 ){
			// 	// echo $notif_pods->total_found()." -- ";
			// 	$data = array();
			// 	$header[] = array("notification_id","object","item_id","brief","description","is_read","date");
			// 	// $csv = "notification_id,object,item_id,brief,description,is_read,date \n";//Column headers
			// 	while ( $notif_pods->fetch() ) {
			// 		$data[] = array(
			// 			$notif_pods->field('id'),
			// 			$notif_pods->field('object'),
			// 			$notif_pods->field('item_id'),
			// 			$notif_pods->field('message'),
			// 			$notif_pods->field('detailed_message'),
			// 			$notif_pods->field('is_read'),
			// 			$notif_pods->field('post_date')
			// 		);

			// 	}
			// }
			// foreach ($header as $fields) {
			// 	fputcsv($f, $fields);
			// }
			// foreach ($data as $fields) {
			// 	fputcsv($f, $fields);
			// }
			// die();

		}

		public function ajax_delete_action(){
			check_ajax_referer( 'notifs_nonce', 'nonce' );

			global $wpdb;

			$podsrel = $wpdb->prefix . 'podsrel';
			$notif_pods_id = 22966;
			$user_field_id = 22978;
			$user_id = get_current_user_id(  );
			$sql = "update {$wpdb->base_prefix}pods_notification n
			inner join {$podsrel} rel on
				n.id = rel.item_id
			set n.is_read = true, is_archived = true
			where rel.related_item_id = {$user_id} and rel.pod_id = {$notif_pods_id} and rel.field_id = {$user_field_id}";
			$wpdb->query($wpdb->prepare($sql));

			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(array('message' => "done", 'code' => 1)));

		}

		public function ajax_mark_all_read_action(){
			check_ajax_referer( 'notifs_nonce', 'nonce' );


			global $wpdb;

			$podsrel = $wpdb->prefix . 'podsrel';
			$notif_pods_id = 22966;
			$user_field_id = 22978;
			$user_id = get_current_user_id(  );
			$sql = "update {$wpdb->base_prefix}pods_notification n
			inner join {$podsrel} rel on
				n.id = rel.item_id
			set n.is_read = true
			where rel.related_item_id = {$user_id} and rel.pod_id = {$notif_pods_id} and rel.field_id = {$user_field_id}";
			$wpdb->query($wpdb->prepare($sql));

			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(array('message' => "done", 'code' => 1)));

		}


		public function ajax_delete_archived_notif(){
			check_ajax_referer( 'notifs_nonce', 'nonce' );
			$file=$_POST['asset'];
			if(unlink(ABSPATH.$file)){
				echo 'true';
			}else{
				echo 'false';
			}
			die();
		}
	}