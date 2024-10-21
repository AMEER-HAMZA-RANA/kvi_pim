<div class="modal fade" id="assetShareModal" tabindex="-1" role="dialog" aria-labelledby="assetShareModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assetShareModalLongTitle">Share</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	 		<div id="share-alert" class="alert alert-primary alert-success hidden" role="alert"></div>
			<div class="form-group">
				<label for="share_email">Email address</label>
				<input type="email" class="form-control" id="share_email" placeholder="Enter email address to share">
			</div>
			<div class="form-group">
				<label for="share_cc_emails">CC</label>
				<input type="email" class="form-control" id="share_cc_emails" aria-describedby="ccEmailHelp" placeholder="Enter email">
				<small id="ccEmailHelp" class="form-text text-muted">Enter Comma separated values in case of multiple CC Email addresses.</small>
			</div>
			<div class="form-group">
				<label for="share_note">Note</label>
				<textarea class="form-control" id="share_note" rows="3"></textarea>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancel-share" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-share">Share Now</button>
      </div>
    </div>
  </div>
</div>
