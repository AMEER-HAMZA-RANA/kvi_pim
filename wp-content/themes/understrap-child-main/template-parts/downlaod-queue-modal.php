<div class="modal fade" id="assetDownloadModal" tabindex="-1" role="dialog" aria-labelledby="assetShareModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assetShareModalLongTitle">Dowload Queue</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	 		<div id="download-alert" class="alert alert-primary alert-success hidden" role="alert"></div>
      <div class="form-group">
				<label for="share_queue_name">Queue name</label>
				<input type="text" class="form-control" id="d_queue_name" aria-describedby="ccEmailHelp" placeholder="Only numbers and letters are allowed">
        <small class="form-text text-muted">Do not enter </small>
			</div>
      <div class="form-group">
				<label for="share_queue_name">Queue duration</label>
        <select class="custom-select" id="d_queue_duration">
          <option value="48 hours">48 hours</option>
          <option value="2 weeks">2 weeks</option>
        </select>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancel-share" data-dismiss="modal">Cancel</button>
        <button type="button" id="btn-download" class="btn btn-primary">Download Queue</button>
      </div>
    </div>
  </div>
</div>
