<?php
// $sm = StateManager::GI();
$brand_slug = 'kvi';
$notif_manager = new NotificationsManager();
$notifications = $notif_manager->load_unread_notifications();
$unread_notifications_count = $notif_manager->get_unread_notifications_count();

?>
<!-- Quick Links Bar -->
<nav id="quick-links-menu" class="quicklinks-menu navbar navbar-icon-top navbar-expand-lg navbar-dark bg-dark p-0">
  <div class="container">
    <div class="row w-100">
      <div class="col-md-6">
      </div>
      <div class="col-md-6 text-right">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="nav-content collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
          <ul class="navbar-nav ">
            <?php if(current_user_can( 'administrator' )):?>
            <li class="nav-item">
              <a href="/api-monitoring" class="nav-link">API Monitoring</a>
            </li>
            <li class="nav-item">
              <!-- <a href="/settings/db" class="nav-link">DB Setup</a> -->

              <style>
              .dropdown-toggle::after {

                content: none;

              }
              </style>
              <!-- Example split danger button -->
              <?php
							$stm = SettingsManager::GI();
							if($stm->check_page_permission('admin-settings') != 'HIDE'): ?>


              <div class="btn-group">
                <a href="/settings/db" class="nav-link">Admin Settings</a>
                <button type="button" style="background: none; border:0; outline: none; box-shadow: none;"
                  class="btn btn-secondary dropdown-toggle dropdown-toggle-split p-0 m-0" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <span class="">
                    <svg style="width:10px;stroke:darkgray;" xmlns="http://www.w3.org/2000/svg" fill="none"
                      viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>

                  </span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="/settings/db/tables" class="dropdown-item">Tables</a></li>
                  <li><a href="/settings/db/taxonomies" class="dropdown-item">Taxonomies</a></li>
                  <li><a href="/settings/db/media" class="dropdown-item">Media</a></li>
                  <li><a href="/settings/db/seller-apps" class="dropdown-item">Seller Apps</a></li>
                  <li><a href="/permissions-management" class="dropdown-item">Permissions Managment</a></li>
                </ul>
              </div>

              <?php endif; ?>


              <!-- <ul>
                <li><a href="/settings/db/tables" class="nav-link">Tables</a></li>
                <li><a href="/settings/db/taxonomies" class="nav-link">Taxonomies</a></li>
                <li><a href="/settings/db/media" class="nav-link">Media</a></li>
                <li><a href="/settings/db/seller-apps" class="nav-link">Seller Apps</a></li>
              </ul> -->
            </li>
            <?php endif;?>


            <!-- arrow permission check -->
            <?php
							$stm = SettingsManager::GI();
							if($stm->check_page_permission('report-issue-request') != 'HIDE'): ?>


            <li class="nav-item">
              <a href="#" class="nav-link" data-toggle="modal" data-target="#supportFormModal"><i class="fa fa-ticket"
                  aria-hidden="true"></i> Report Issue/Request</a>
            </li>


            <?php endif; ?>
            <!-- ---- -->

            <li class="nav-item">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bell">
                  <?php if ($unread_notifications_count != 0 ){
										?>
                  <span class="badge badge-info"><?php echo $unread_notifications_count;?></span>
                  <?php
									}
									?>
                </i>
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <?php
								if(count($notifications) >0 ){
									foreach($notifications as $notification){
										?>
                <a class="dropdown-item" href="<?php echo $notification->notif_link;?>"
                  data-id="<?php echo $notification->notif_id;?>"><?php echo $notification->message;?></a>
                <?php
									}
								}
								else{
									?>
                No New Notifications
                <?php
								}
								?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo home_url('/').$brand_slug."/notifications/";?>">View all
                  notifications.</a>
                <a class="dropdown-item"
                  href="<?php echo home_url('/').$brand_slug."/archived-notifications/";?>">Archived notifications.</a>
              </div>
            </li>
            <?php
						//if(current_user_can( 'mc_edit' ) || current_user_can( 'shopvac_mc_edit' )){?>

            <!-- arrow permission check -->
            <?php
							$stm = SettingsManager::GI();
							if($stm->check_page_permission('catalogue') != 'HIDE'): ?>


            <li class="nav-item">
              <a href="/arrow/catalog/?ctype=pb" class="nav-link">Catalog</a>
            </li>

            <?php endif; ?>
            <!-- ---- -->

            <!-- arrow permission check -->
            <?php
							$stm = SettingsManager::GI();
							if($stm->check_page_permission('importer') != 'HIDE'): ?>


            <li class="nav-item">
              <a class="nav-link" href="<?php echo home_url('/importer') ;?>">
                <i class="fa fa-cloud-download" aria-hidden="true"></i> Import
              </a>
            </li>


            <?php endif; ?>
            <!-- ---- -->

            <?php
						//}
						?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo wp_logout_url( home_url('/') );?>">
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>

    </div>
  </div>

</nav>



<?php // require( locate_template( 'template-parts/support-form-modal.php', false, false ) );?>