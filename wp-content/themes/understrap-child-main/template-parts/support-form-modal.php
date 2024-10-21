<?php
$sm = StateManager::GI();
$sm->setup_support_form_handler();
?>
<div class="modal fade" id="supportFormModal" tabindex="-1" role="dialog" aria-labelledby="supportFormModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="supportFormModalLongTitle">Submit an Issue/Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	 		<div id="support-alert" class="alert alert-primary alert-success hidden" role="alert"></div>
			<div class="form-group">
				<input type="text" class="form-control" id="support_name" placeholder="Your Name" required>
			</div>
			<div class="form-group">
				<input type="email" class="form-control" id="support_email" placeholder="Your email" required>
			</div>
			<div class="form-group">
				<textarea name="support_message" class="form-control" id="support_message" rows="6"  placeholder="Your Message" required></textarea>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancel-support" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-submit-support">Submit Message</button>
      </div>
    </div>
  </div>
</div>
