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
				<label for="share_cc_emails">Email address</label>
				<input type="email" class="form-control" id="share_cc_emails" aria-describedby="ccEmailHelp" placeholder="Enter email">
				<small id="ccEmailHelp" class="form-text text-muted">Enter Comma separated values in case of multiple Email addresses.</small>
			</div>
      <div class="form-group">
				<label for="share_queue_name">Queue name</label>
				<input type="text" class="form-control" id="queue_name" aria-describedby="ccEmailHelp" placeholder="Enter queue name">
			</div>
      <div class="form-group">
				<label for="share_queue_name">Queue duration</label>
        <select class="custom-select" id="queue_duration">
          <option value="48 hours">48 hours</option>
          <option value="2 weeks">2 weeks</option>
        </select>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancel-share" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-share">Process Queue and Share</button>
      </div>
    </div>
  </div>
</div>
