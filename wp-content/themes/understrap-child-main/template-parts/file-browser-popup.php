<?php

$args = array("media_type" => $default_media_type,
"img_key" => ("Images" == $default_media_type)?$default_media_assignment:"Other",
"vid_key" => ("Video" == $default_media_type)?$default_media_assignment:"Other",
"artwork_key" => ("Art Work" == $default_media_type)?$default_media_assignment:"Other",
 );
$sm->mam->localize_script($args);


?>
<div class="popup-mam-browser modal" id="file_browser_popup">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
		<div class="modal-title">
			<h4>Browse Directory</h4>
			<h5>Path: <span id="mam_path"><a href="javascript:void(0);" class="node-folder-icon" data-path=""></a></span></h5>
		</div>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body" id="mam_body">

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
			<input type="text" class="form-control" id="dir_name" name="dir_name" >
			<button type="button" class="btn btn-primary " style="width: 150px;" id="btn_create_mam_dir" data-path="">Create Folder</button>
        	<!-- <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> -->
	  </div>
	  <div class="my-2 text-center">
	  <button type="button" class="btn btn-primary " style="width: 150px;" id="btn_select_dir" data-path="">Select Current Directory</button>
	  </div>

    </div>
  </div>
</div>


<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#file_browser_popup">
  Open modal
</button> -->
