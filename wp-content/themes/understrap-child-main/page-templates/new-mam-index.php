<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header("mam-index");
$container = get_theme_mod('understrap_container_type');

?>

<style>
.delete-bulk-assets,
.save-bulk-to-queue {
  display: none;
}

.media-top-bar {
  display: flex;
  justify-content: space-between;
  max-width: 970px;
  margin: auto;
  margin-bottom: 50px;


}

.mam-container {
  padding: 20px;
  max-width: 1000px;
  margin: auto;
  max-width: 1300px;
  /* min-width: 1200px; */
  margin-bottom: 100px;
}

.media-items-count {
  text-align: right;
  margin-bottom: 10px;
}

.media-items-grid {
  display: flex;
  flex-wrap: wrap;
  column-gap: 20px;
  row-gap: 35px;
  margin: auto;
  justify-content: center;
}

.media-item {
  flex: 1 1 30%;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  margin: 0;
  max-width: 145px;
  border: 1px solid black;
  position: relative;
}

.media-items-grid .media-item .bulk-queue-assets {
  position: absolute;
  right: 13px;
  top: 5px;
  width: 20px;
  height: 20px;
  display: none;
  z-index: 1;
}

.media-item a {
  display: flex;
  max-width: 155px;
  margin: auto !important;
  position: relative;
  min-height: 120px;
}

.media-item img {
  /* max-width: 100%;
  height: auto; */
  height: 151px;
  width: 151px;
  object-fit: cover;
  margin: auto;
}

.product-id {
  text-align: center;
  /* margin-top: 5px; */
  font-weight: bold;
  position: absolute;
  left: 50%;
  bottom: -20px;
  transform: translateX(-50%);
  width: 100%;
}

/*
.pagination-container {
  display: flex;
  justify-content: center;
  margin-top: 60px;
  gap: 50px;
  font-size: 15px;
}

.pagination a {
  margin: 0 5px;
  text-decoration: none;
  padding: 5px 10px;
  background-color: #ddd;
  color: #333;
  border-radius: 5px;
}

.pagination a:hover {
  background-color: #999;
  color: white;
} */

.hidden {
  display: none;
}

.loader {
  position: static;
  inset: 50%;
  width: 100px;
  height: 100px;
  margin: 50px auto;
  /* position: absolute;
  inset: 50%; */
  /* right: 50%;
  left: 50%;
  top: 50%;
  bottom: 50%; */
  /* width: 100px;
  height: 100px; */
}


/* fav */

/* fav */

.favorite_selection {
  position: absolute;
  bottom: 7px;
  right: 7px;
  -webkit-transition: all .5s ease;
  -o-transition: all .5s ease;
  -moz-transition: all .5s ease;
  transition: all .5s ease;
  background-color: #839cc4;
  color: #fff;
  padding: 2px 10px;
  border-radius: 3px;
  cursor: pointer;
}

.favorite_selection .favorite-icon {
  background-color: #000;
  width: 19px;
  height: 19px;
  line-height: 13px;
  text-align: center;
  border-radius: 100%;
}

.favorite_selection .favorite-icon .fa {
  font-size: 12px;
  position: absolute;

}

.fa {
  display: inline-block;
  font: normal normal normal 14px / 1 FontAwesome;
  font-size: inherit;
  text-rendering: auto;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;

  position: static;
  top: 26%;
  right: 35%;
}

.fa-heart:before {
  content: "\f004";
}

.favorite_selection i:hover {
  color: #da1f19;
  background-color: #fff;

}

.favorite_selection i:hover {
  color: #ff6319;
}

.favorite_selection:hover .favorite-icon {
  color: #ff6319;
  background-color: #fff;

}

.favorite_selection.active .favorite-icon .fa {
  color: #da1f19;
}

.favorite_selection.active .favorite-icon {
  background-color: #fff;
}

/* .fav_mark {
  position: absolute;
  bottom: 17px;
  right: -7px;
}

.fav_mark * {
  cursor: pointer;
}

.fav_mark input[type="checkbox"] {
  display: none;
}


.fav_mark input[type="checkbox"]+label {
  position: relative;
  padding-left: 35px;
  display: inline-block;
  font-size: 16px;
}

.fav_mark input[type="checkbox"]+label:before {
  content: "\1F5A4";
  top: -11px;
  left: -8px;
  border: 1px solid transparent;
  padding: 10px;
  border-radius: 3px;
  display: block;
  position: absolute;
  transition: .5s ease;
}



.fav_mark input[type="checkbox"]:checked+label:before {
  border: 1px solid transparent;
  background-color: transparent;
}


.fav_mark input[type="checkbox"]:checked+label:after {
  content: '\1F49B';
  font-size: 18px;
  position: absolute;
  top: -1px;
  left: 1px;
  color: gold;
  transition: .5s ease;
} */

.spinner_aj0A {
  transform-origin: center;
  animation: spinner_KYSC .50s infinite linear
}

@keyframes spinner_KYSC {
  100% {
    transform: rotate(360deg)
  }
}


/* media Items Count */

.list-items {
  list-style: none;
  margin: -45px 0 32px auto;
  max-width: fit-content;
}

.list-items li {
  display: inline-block;
  font-weight: 700;
  font-size: 16px;
  color: #707070;
  padding: 0 15px;
  cursor: pointer;
}

.list-items li:not(:last-of-type) {
  border-right: 2px solid #707070;
}

.list-items li:hover,
.list-items li.active {
  color: #ffffff;
  background-color: #707070;
}

.media-item:has(.favorite_selection.active) {
  border: 2px solid red;
}

.pg-heading {
  font-weight: 700;
  font-size: 18px;
  color: #707070;
}

nav:has(.pagination) {
  display: flex;
  justify-content: center;
  margin-top: 80px;
}

.total-media-found {
  text-align: center;
  font-size: 20px;
}

.product-items-count {
  text-align: right;
  margin-bottom: 10px;
  margin-left: auto;
}

#media-type-select {
  display: inline-block;
  width: 159px;
  text-align: center;
  font-size: 14px;
  height: 30px;
  color: #707070;
  font-weight: 400;
}
</style>

<h3 class="current-cat text-center pg-heading">Media</h3>

<div class="loader">
  <svg class="hidden" width="60" height="60" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path
      d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
      class="spinner_aj0A" />
  </svg>
</div>
<div class="mam-container hidden">
  <div class="media-top-bar">
    <!-- <select id="media-type-select">
      <option value="all">All</option>
      <option value="image">Image</option>
      <option value="video">Video</option>
      <option value="audio">Audio</option>
      <option value="vimeo_url">Vimeo URL</option>
      <option value="audio_url">Audio URL</option>
      <option value="image_url">Image URL</option>
      <option value="youtube_url">YouTube URL</option>
      <option value="video_url">Video URL</option>
      <option value="link">Link</option>
      <option value="zip">Zip</option>
      <option value="doc">Doc</option>
      <option value="pdf">PDF</option>
    </select> -->

    <!-- Radio buttons to select the number of media items to display -->
    <!-- <div class="media-items-count">
      <label><input type="radio" name="items_count" value="28" checked> 28</label>
      <label><input type="radio" name="items_count" value="56"> 56</label>
      <label><input type="radio" name="items_count" value="84"> 84</label>
    </div> -->
    <ul class="list-items product-items-count">
      <li class="active" data-val="28">28</li>
      <li data-val="56">56</li>
      <li data-val="84">84</li>
    </ul>
  </div>

  <!-- Media items display area -->
  <!-- <div class="media-items-grid" id="media-items-grid"> -->
  <!-- Media items will be loaded here via AJAX -->
  <!-- </div> -->
  <div class=" item-grid">
    <div id="media-items-grid" class="media-items-grid row" data-selection-mode="0" id="items_container">

    </div>

  </div>

  <!-- Pagination and total media count -->
  <div class="pagination-container">
    <!-- <div class="pagination"> -->
    <!-- Pagination links will be here -->
    <!-- </div> -->
    <nav aria-label="Page navigation example">
      <ul class="pagination">
        <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item"><a class="page-link" href="#">Next</a></li> -->
      </ul>
    </nav>

    <div class="total-media-found">
      Total Found: <span id="total-media-count" class="fw-bold">0</span>
    </div>
  </div>
</div>


<?php get_footer();
?>

<script>
// 	<div class="fav_mark">
//     <input type="checkbox" id="heart-${item.id}" class="favorite-checkbox" data-item-id="${item.id}" ${item.is_favorite ? 'checked' : ''}/>
//     <label for="heart-${item.id}">

//     </label>
// </div>
document.addEventListener('DOMContentLoaded', function() {

  function activateCount(target) {
    [...target.parentElement.children].forEach(el => el.classList.remove('active'))

    target.classList.add('active');
  }


  function loadMediaItems(limit, page, mediaType, target = null) {
    console.log(document.querySelector('input#media-search-input')?.value);
    return fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: new URLSearchParams({
          limit,
          page,
          media_type: mediaType, // Include media type in the request
          search: document.querySelector('input#media-search-input')?.value,
          // exact_search: document.querySelector('#filter_sku_exact').checked ? 1 : 0,
          action: 'fetch_media_items',
          _wpnonce: '<?php echo wp_create_nonce('fetch_media_items'); ?>'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const mediaItemsGrid = document.getElementById('media-items-grid');
          const paginationContainer = document.querySelector('.pagination');
          const totalMediaCount = document.getElementById('total-media-count');

          // Clear the current grid
          mediaItemsGrid.innerHTML = '';
          console.log(data)
          // Populate new media items
          data.data.media_items?.forEach(function(item) {

            const mediaItemHtml = `
  <label class="media-item px-0" for="checkbox${item.id}">
  <input type="checkbox" name="bulk-queue-assets[]" id="checkbox${item.id}" value="${item.id}" class="bulk-queue-assets">
    <a href="/arrow/mam/view/${item.id}">
      <img src="${item.thumb_url}" alt="${item.title}" onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png';">
            <div class="product-id">${item.title.length > 21 ? item.title.substr(0, 19) + '...' : item.title}</div>
    </a>

		<div class="favorite_selection d-flex ${item.is_favorite ? 'active' : ''}" data-id="${item.id}">

												<div class="favorite-icon">
													<i class="fa fa-heart" aria-hidden="true"></i>
												</div>

											</div>

  </label>
`;

            mediaItemsGrid.innerHTML += mediaItemHtml;
          });

          // Update total media count
          totalMediaCount.textContent = data.data.total_media_count;

          // Update pagination
          const totalPages = Math.ceil(data.data.total_media_count / limit);
          const maxPagesToShow = 3;
          let startPage = Math.max(1, page - Math.floor(maxPagesToShow / 2));
          let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

          if (endPage - startPage < maxPagesToShow - 1) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
          }

          paginationContainer.innerHTML = '';

          // Previous button
          // if (page > 1) {
          //   paginationContainer.innerHTML +=
          //     `<a href="#" data-page="${page - 1}" class="pagination-link">Previous</a>`;
          // }

          // // Page number links
          // for (let i = startPage; i <= endPage; i++) {
          //   const activeClass = i == page ? 'active' : '';
          //   const style = i == page ? 'style="background: black; color: white;"' : '';
          //   paginationContainer.innerHTML +=
          //     `<a href="#" data-page="${i}" class="pagination-link ${activeClass}" ${style}>${i}</a>`;
          // }

          // // Next button
          // if (page < totalPages) {
          //   paginationContainer.innerHTML +=
          //     `<a href="#" data-page="${page + 1}" class="pagination-link">Next</a>`;
          // }
          // Previous button
          if (page > 1) {
            paginationContainer.innerHTML +=
              `<li class="page-item"><a href="#" data-page="${page - 1}" class="page-link">Previous</a></li>`;
          }

          // Page number links
          for (let i = startPage; i <= endPage; i++) {
            const activeClass = i == page ? 'active' : '';
            const style = i == page ? 'style="background: #727272; color: white;"' : '';
            paginationContainer.innerHTML +=
              `<li class="page-item"><a href="#" data-page="${i}" class="page-link ${activeClass}" ${style}>${i}</a></li>`;
          }

          // Next button
          if (page < totalPages) {
            paginationContainer.innerHTML +=
              `<li class="page-item"><a href="#" data-page="${page + 1}" class="page-link">Next</a></li>`;
          }


          // activate count
          if (target) activateCount(target)

          return true
        }
      })
      .catch(error => {
        console.error('Error:', error);
      })
      .finally(() => {
        document.querySelector('.mam-container').classList.remove('hidden')
        document.querySelector('.loader').classList.add('hidden')
      });
  }

  // Initial load with default limit, page 1, and media type
  loadMediaItems(28, 1, document.getElementById('media-type-select')?.value);

  // Handle media type change
  document.getElementById('media-type-select')?.addEventListener('change', function() {
    const mediaType = this.value;
    const limit = document.querySelector('.product-items-count li.active').dataset.val;
    loadMediaItems(limit, 1, mediaType, this);

    document.querySelector('.pg-heading').textContent = [...this.children].find(el => el.value == mediaType)
      .textContent
    document.querySelector('.pg-heading').style.textTransform = 'capitalize'
  });

  // search field
  const searchField = document.querySelector('input#media-search-input')
  // console.log(searchField)
  // searchField.addEventListener("click", e => {
  //   console.log("EX 2")

  // })
  searchField?.addEventListener("keypress", e => {
    console.log("EX")
    if (e.key === "Enter") {
      // Cancel the default action, if needed
      e.preventDefault();

      const searchChars = document.querySelector('input.mam_search_field').value.trim()

      // if (searchChars.length > 3 == false) return;

      document.querySelector('.search-input-loader').classList.remove('hidden')

      const limit = document.querySelector('.product-items-count li.active').dataset.val

      const mediaType = document.getElementById('media-type-select').value;


      loadMediaItems(limit, 1, mediaType).then(data => {
        document.querySelector('.search-input-loader').classList.add('hidden')
      });
      // Trigger the button element with a click
      // document.getElementById("myBtn").click();
    }
  })

  // Handle radio button change
  // document.querySelectorAll('input[name="items_count"]').forEach(function(radio) {
  //   radio.addEventListener('change', function() {
  //     const limit = this.value;
  //     const mediaType = document.getElementById('media-type-select').value;
  //     loadMediaItems(limit, 1, mediaType);
  //   });
  // });
  let bulk_selected_ids = [];
  let checkboxes = [];
  let saveBulk = document.getElementsByClassName("save-bulk-to-queue")[0];
  let deleteBulk = document.getElementsByClassName("delete-bulk-assets")[0];
  let selectedCount = 0;
  document.addEventListener('click', function(e) {

    if (e.target.closest('.product-items-count li')) {
      // activateCount(e.target)
      const limit = e.target.dataset.val
      const mediaType = document.getElementById('media-type-select').value;
      loadMediaItems(limit, 1, mediaType, e.target);
    }



    // Handle pagination click
    if (e.target.closest('.page-item')) {
      e.preventDefault();
      // const page = parseInt(e.target.dataset.page);
      // const limit = document.querySelector('.product-items-count li.active').dataset.val;
      // const mediaType = document.getElementById('media-type-select').value;
      // loadMediaItems(limit, page, mediaType);
      const target = e.target.closest('.page-item').querySelector('.page-link')
      const page = parseInt(target.dataset.page);
      const limit = document.querySelector('.product-items-count li.active').dataset.val;
      const mediaType = document.getElementById('media-type-select').value;
      loadMediaItems(limit, page, mediaType);
    }

    // fav
    if (e.target.closest('.favorite_selection')) {
      // const checkbox = e.target;
      // const itemId = checkbox.dataset.itemId;
      // const isChecked = checkbox.checked;
      const favMarkDiv = e.target.closest('.favorite_selection');
      const itemId = favMarkDiv.dataset.id;
      const isChecked = favMarkDiv.classList.contains('active');

      favMarkDiv.style.pointerEvents = "none"

      fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
          method: 'POST',
          body: new URLSearchParams({
            item_id: itemId,
            item_type: 'media',
            is_favorite: !isChecked ? 1 : 0,
            action: 'toggle_favorite_item',
            _wpnonce: '<?php echo wp_create_nonce('toggle_favorite_item'); ?>'
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log('Favorite status updated.');
            !isChecked ? favMarkDiv.classList.add('active') : favMarkDiv.classList.remove('active');
          } else {
            console.error('Failed to update favorite status.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
        })
        .finally(() => {
          favMarkDiv.style.pointerEvents = "auto"

        });;
    }


    // Bulk selelct option
    if (e.target.closest('.enable-bulk-to-queue')) {
      let assestsBult = document.getElementsByClassName("bulk-queue-assets");
      let MediaAnchorTag = document.querySelectorAll('.media-item a');

      if (saveBulk.style.display === "none" || saveBulk.style.display === "") {
        saveBulk.style.display = "block";
      } else {
        saveBulk.style.display = "none";
      }
      if (deleteBulk.style.display === "none" || deleteBulk.style.display === "") {
        deleteBulk.style.display = "block";
      } else {
        deleteBulk.style.display = "none";
      }
      for (let i = 0; i < assestsBult.length; i++) {
        if (assestsBult[i].style.display === "none" || assestsBult[i].style.display === "") {
          assestsBult[i].style.display = "block";

        } else {
          assestsBult[i].style.display = "none";
        }
        assestsBult[i].checked = false;
      }
      for (let j = 0; j < MediaAnchorTag.length; j++) {
        if (MediaAnchorTag[j].style.pointerEvents === "auto" || MediaAnchorTag[j].style.pointerEvents === "") {
          MediaAnchorTag[j].style.pointerEvents = "none";
        } else {
          MediaAnchorTag[j].style.pointerEvents = "auto";
        }
      }
    }

    //Check Bulk select Limit
    if (e.target.closest('.bulk-queue-assets')) {
      checkboxes = document.querySelectorAll('input[type="checkbox"]');
      // console.log(checkboxes);

      // Count the already checked checkboxes and push them to bulk_selected_ids
      checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
          selectedCount++;
          if (!bulk_selected_ids.includes(checkbox.value)) {
            bulk_selected_ids.push(checkbox.value);
          }
        }
      });

      // Add event listener to handle checkbox changes
      checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
          if (this.checked) {
            // console.log("checked");
            selectedCount++;
            if (bulk_selected_ids.length > 11) {
              this.checked = false;
              alert('You have reached the maximum selection.');
              selectedCount--;
            } else {
              if (!bulk_selected_ids.includes(this.value)) {
                bulk_selected_ids.push(this.value);
              }
            }
          } else {
            // console.log("unchecked");
            selectedCount--;
            let index = bulk_selected_ids.indexOf(this.value);
            if (index > -1) {
              bulk_selected_ids.splice(index, 1);
              selectedCount--;
            }
          }
          console.log('Updated selectedCount:', selectedCount);
          console.log('Updated bulk_selected_ids:', bulk_selected_ids);
        });
      });
    }
    // bulk save ajax request
    if (e.target.closest('.save-bulk-to-queue')) {
      let length = bulk_selected_ids.length;
      if (length > 0) {
        saveBulk.style.display = "none";
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: new URLSearchParams({
              asset_ids: bulk_selected_ids,
              action: 'ar_bulk_save_queue',
              _wpnonce: '<?php echo wp_create_nonce('ar_download_queue_nonce'); ?>'
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.data.message);
              checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                  checkbox.checked = false;
                }
              });
              selectedCount = 0;
              length = 0;
              bulk_selected_ids = [];
            } else {
              alert(data.data.message);
              checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                  checkbox.checked = false;
                }
              });
              selectedCount = 0;
              length = 0;
              bulk_selected_ids = [];
            }
          })
          .catch(error => {
            console.error('Error:', error);
          })
          .finally(() => {
            //

          });
      } else {
        alert("Assets not selected.");
      }
    };

    //bulk delete ajax request
    if (e.target.closest('.delete-bulk-assets')) {
      let length = bulk_selected_ids.length;
      if (length > 0) {
        deleteBulk.style.display = "none";
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: new URLSearchParams({
              asset_ids: bulk_selected_ids,
              action: 'ar_bulk_delete_queue',
              _wpnonce: '<?php echo wp_create_nonce('ar_delete_queue_nonce'); ?>'
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.data.message);
              checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                  checkbox.checked = false;
                }
              });
              selectedCount = 0;
              length = 0;
              bulk_selected_ids = [];
            } else {
              alert(data.data.message);
              checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                  checkbox.checked = false;
                }
              });
              selectedCount = 0;
              length = 0;
              bulk_selected_ids = [];
            }
          })
          .catch(error => {
            console.error('Error:', error);
          })
          .finally(() => {
            //

          });
      } else {
        alert("Assets not selected.");
      }
    };


  });




});
</script>