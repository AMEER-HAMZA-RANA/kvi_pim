<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header("mam-index");
$container = get_theme_mod('arrow_container_type');


global $wp_roles;
$roles = $wp_roles->roles;

// // print it to the screen
// echo '<pre>' . print_r( $roles, true ) . '</pre>';


?>

<style>
#new-permissions-management {
  padding: 40px 0 100px 0;
}

#new-permissions-management h1 {
  font-size: 35px;
}

#new-permissions-management .selection-row select,
#new-permissions-management .selection-row .go-button {
  width: 100%;
  height: 100%;
  padding: 10px;
  font-size: 15px;
}

button.view-for-all,
button.reset-radios,
button.edit-for-all,
button.hide-for-all {
  font-size: 13px;
}

.hidden {
  display: none !important;
}
</style>

<section id="new-permissions-management">
  <div class="container mx-auto">
    <div class="row">
      <div class="col-12 mb-5">
        <h1>Manage Permissions</h1>
      </div>
      <div class="col-12">
        <div class="row selection-row">
          <div class="col-md-4">

            <select name="user_role" id="user_role_select">
              <option value="0" selected disabled>Select Role</option>
              <?php foreach($roles as $role_key => $role_data): ?>
              <option value="<?php echo esc_attr($role_key); ?>">
                <?php echo esc_html($role_data['name']); ?>
              </option>
              <?php endforeach; ?>
            </select>

          </div>
          <div class="col-md-4">

            <select name="item_type" id="item_type_select">
              <option value="0" selected disabled>Select Item Type</option>
              <option value="fields">Fields</option>
              <option value="tables">Tables</option>
              <option value="pages">Pages</option>
            </select>

          </div>
          <div class="col-md-4">

            <button id="go-button" class="btn btn-lg bg-success go-button text-white">
              Go
            </button>

          </div>
        </div>
      </div>
      <div class="col-12 mt-5">
        <div class="row">
          <div class="col-12 permissions-selection-container hidden">
            <div class="d-flex justify-content-between align-items-center gap-3 my-3">

              <div class="field-table-select-div hidden">
                <select id="field-table-select">
                  <option value="0" selected disabled>Select Fields Table</option>
                  <option value="all">All</option>

                </select>
              </div>

              <div id="controls-buttons" class="d-flex justify-content-end gap-3 my-3 ml-auto ">

              </div>
            </div>
            <table id="permissionsTable" class="table table-bordered">
              <thead>
                <tr>
                  <!-- <th>Item ID</th> -->
                  <th>Item Name</th>
                  <th>Permission</th>
                  <!-- <th>Item ID</th> -->
                  <th>Item Name</th>
                  <th>Permission</th>
                  <!-- <th>Item ID</th> -->
                  <th>Item Name</th>
                  <th>Permission</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            <button id="permissions-submit-button"
              class="btn btn-lg permissions-submit-button bg-success text-white ms-auto d-block mt-3 px-4 py-2">
              Submit
            </button>
          </div>
        </div>
      </div>
      <div class="col-12">
      </div>
    </div>

  </div>
</section>


<style>
#permissionsTable .radio-cell {
  width: 10%;
  align-items: center;
}

#permissionsTable .id-cell {
  width: 6%;
}

#permissionsTable .name-cell {
  width: 18%;
}

#permissionsTable {}

#permissionsTable td {
  padding: 5px 10px;
}

#permissionsTable th {
  font-size: 16px;
}

.permissions-selection-container .permissions-submit-button {
  font-size: 16px;
}

.radio-cell-main {
  display: flex;
  gap: 12px;
}

.radio-cell-main svg {
  width: 16px;
  height: 16px;
  stroke: black;
}

#field-table-select {
  width: 100%;
  height: 100%;
  padding: 10px;
  font-size: 15px;
}

td:nth-child(odd) {
  background: #E4E4E3;
  color: black;
}

tr {
  border-color: black;

}
</style>



<script>
document.addEventListener('DOMContentLoaded', () => {

  const selectedRole = document.querySelector('#user_role_select')
  const selectedItemType = document.querySelector('#item_type_select')
  const goButton = document.querySelector('#go-button')
  const selectionDiv = document.querySelector('.selection-row')
  const editForAllButton = document.querySelector('.edit-for-all')
  const viewForAllButton = document.querySelector('.view-for-all')

  let userConfig = {}
  let goFlag = false

  function resetSelectionForm() {
    selectedItemType.value = 0
    selectedRole.value = 0
    userConfig = {}
  }

  function presentPermissions(data) {
    const selectParentDiv = document.querySelector('.field-table-select-div')
    if (userConfig.itemType == 'fields') {
      selectParentDiv.classList.remove('hidden')

      const tableOptionsAlreadyExist = [...selectParentDiv.querySelector('select').children].some(option => option
        .value != '0' && option.value != 'all')
      console.log('tableOptionsAlreadyExist ', tableOptionsAlreadyExist);
      if (!tableOptionsAlreadyExist) {
        insertFieldTableSelect(data.table_metas)
      }
    } else {
      selectParentDiv.classList.add('hidden')
    }

    enableControlButtons()

    generateTable(data.results)
  }


  function enableControlButtons() {
    const buttonsHTML = `
			<button class="btn btn-sm view-for-all bg-success text-white px-3">
                  Select view for all
                </button>
                <button
                  class="btn btn-sm edit-for-all bg-success text-white px-3 ${userConfig.itemType == 'pages' ? 'hidden' : ''}">
                  Select Edit for all
                </button>
                <button class="btn btn-sm hide-for-all bg-success text-white px-3">
                  Hide for All
                </button>
                <button class="btn btn-sm reset-radios bg-success text-white px-3">
                  Reset to initial <small>(loaded from Database)</small>
                </button>
			`

    document.querySelector('#controls-buttons').innerHTML = buttonsHTML
  }

  function saveAllRadiosInitialStateLocally() {
    const radioGroups = document.querySelectorAll('input[type="radio"]');

    radioGroups.forEach(radio => {
      if (radio.checked) {
        initialState[radio.name] = radio.value;
      }
    });
  }

  function revertAllRadiosToInitialState() {
    const radioGroups = document.querySelectorAll('input[type="radio"]');

    radioGroups.forEach(radio => {
      // Re-check the initially checked radio button
      if (initialState[radio.name] === radio.value) {
        radio.checked = true;
      } else {
        radio.checked = false;
      }
    });
  }

  // let initialRadiosState = []
  let initialState = {};

  function generateTable(data) {
    const table = document.querySelector(".permissions-selection-container");
    const tableBody = table.querySelector("tbody");

    tableBody.innerHTML = ''

    let row;

    data?.forEach((item, index) => {
      if (index % 3 === 0) {
        // Create a new row every two items
        row = document.createElement("tr");
        tableBody.appendChild(row);
      }

      // Create item id cell
      // const idCell = document.createElement("td");
      // idCell.classList.add('id-cell')
      // if (item.table_meta_id) {
      //   idCell.dataset.tableMetaId = item.table_meta_id
      // }
      // idCell.textContent = item.id;
      // row.appendChild(idCell);

      // Create item name cell
      const nameCell = document.createElement("td");
      nameCell.classList.add('name-cell')
      if (item.table_meta_id) {
        nameCell.dataset.tableMetaId = item.table_meta_id
      }
      nameCell.textContent = item.title;
      row.appendChild(nameCell);

      // Create radio buttons cell
      const radioCell = document.createElement("td");
      radioCell.classList.add('radio-cell')
      if (item.table_meta_id) {
        radioCell.dataset.tableMetaId = item.table_meta_id
      }
      radioCell.innerHTML = `
			<div class="radio-cell-main">
                    <div class="form-check form-check-inline d-inline-flex gap-1 p-0 m-0">
                        <input class="" id="view_${item.id}" type="radio" name="${item.id}" value="4" ${item.permission == 4 && 'checked'}>
												<label id="label_for_view_${item.id}" for="view_${item.id}">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
</svg>

												</label>
                    </div>
                    <div class=" form-check form-check-inline d-inline-flex gap-1 p-0 m-0 ${userConfig.itemType == 'pages' ? 'hidden' : ''}">
                        <input class="" id="edit_${item.id}" type="radio" name="${item.id}" value="5" ${item.permission == 5 && 'checked'}>
												<label id="label_for_edit_${item.id}" for="edit_${item.id}">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
</svg>

												</label>
                    </div>
										<div class="form-check form-check-inline d-inline-flex gap-1 p-0 m-0 ">
                        <input class="" id="hide_${item.id}" type="radio" name="${item.id}" value="0" ${item.permission == 0 && 'checked'}>
												<label id="label_for_hide_${item.id}" for="hide_${item.id}">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
</svg>

												</label>
                    </div>
					</div>
                `;
      row.appendChild(radioCell);

      // initialRadiosState.push({
      //   itemId: item.id,
      //   itemPermission: item.permission
      // })
      saveAllRadiosInitialStateLocally()



    });

    table.classList.remove('hidden')

    enableToolTips()
  }

  function enableToolTips() {
    const radios = [...document.querySelectorAll('#permissionsTable input[type="radio"]')].map(el => el
      .nextElementSibling.id).forEach(
      id => {
        const selector = `#${id}`
        const currentRadioValue = document.querySelector(selector).previousElementSibling.value
        tippy(selector, {
          content: currentRadioValue == '0' ? 'Hide' : currentRadioValue == '5' ? 'Edit' :
            currentRadioValue == '4' ? 'View' : '',
        });
      })
  }

  function insertFieldTableSelect(table_metas) {
    console.log('TM ', table_metas)

    const selectParentDiv = document.querySelector('.field-table-select-div')

    let optionsArr = table_metas?.map(tm => `<option value="${tm.id}">${tm.title}</option>`)
    console.log(optionsArr);
    optionsArr.forEach(option => selectParentDiv
      .querySelector('select').insertAdjacentHTML('beforeend', option))

    // select.

  }

  function loadPermissions(userConfig) {

    selectionDiv.style.pointerEvents = 'none'

    return fetch('<?= admin_url('admin-ajax.php') ?>', {
        method: 'POST',
        body: new URLSearchParams({
          action: 'loadPermissions',
          ...userConfig,
          _wpnonce: '<?= wp_create_nonce('loadPermissions') ?>'
        })
      })
      .then(res => res.json())
      .then(data => {

        if (data.success) {

          console.log("Successfully loaded")
          console.log('DATA => ', data)
          presentPermissions(data.data)

          return true

        } else {

          console.log("FAiled to load.", data)

        }

      })
      .catch(err => {
        console.log("ERROR => ", err)
      })
      .finally(() => {
        selectionDiv.style.pointerEvents = 'auto'
        goFlag = true
        // resetSelectionForm()
      })
  }

  function savePermissions(dataToSubmit) {
    console.log('submitting')

    const body = new URLSearchParams({
      action: 'submitPermissionsData',
      data: JSON.stringify(dataToSubmit),
      item_type: userConfig.itemType,
      role_key: userConfig.role,
      _wpnonce: '<?= wp_create_nonce('submitPermissionsData') ?>'
    })
    console.log('body => ', body)

    fetch('<?= admin_url('admin-ajax.php') ?>', {
        method: 'POST',
        body
      })
      .then(res => res.json())
      .then(data => {

        if (data.success) {

          console.log("Successfully saved")
          // console.log('DATA => ', data.data.results)
          // console.log('DATA => ', data.data.data_saved)
          alert("Successfully saved")


        } else {

          console.log("FAiled to save.", data)

        }

      })
      .catch(err => {
        console.log("ERROR => ", err)
      })
      .finally(() => {})

  }

  document.addEventListener('click', e => {

    if (e.target.closest('#permissions-submit-button')) {

      const dataToSubmit = [...document.querySelectorAll('input[type="radio"]')].filter(radio => radio.checked)
        .map(radio => {
          return {
            id: radio.name,
            permission: radio.value
          }
        });

      if (dataToSubmit.length) {
        savePermissions(dataToSubmit)
      }
      console.log('dataToSubmit => ', dataToSubmit)

    }

    if (e.target.closest('#go-button')) {

      console.log(userConfig)


      if (userConfig.role?.trim() && userConfig.itemType?.trim()) {

        if (goFlag) {

          if (confirm("Are you sure ? Data will refresh.")) {

            loadPermissions(userConfig).then(() => {
              document.querySelector('#field-table-select').value = '0'
            })


          }

        } else {

          loadPermissions(userConfig).then(() => {
            document.querySelector('#field-table-select').value = '0'
          })

        }


      } else {

        alert("Please select Role and Item Type")

      }

    }


    // view for all button
    if (e.target.closest('.view-for-all')) {

      const table = document.querySelector(".permissions-selection-container");
      const tableBody = table.querySelector("tbody");

      const radios = tableBody.querySelectorAll('input[type="radio"][value="4"]')
      radios.forEach(radio => radio.checked = true)

    }

    // view for all button
    if (e.target.closest('.hide-for-all')) {

      const table = document.querySelector(".permissions-selection-container");
      const tableBody = table.querySelector("tbody");

      const radios = tableBody.querySelectorAll('input[type="radio"][value="0"]')
      radios.forEach(radio => radio.checked = true)

    }

    // reset button
    if (e.target.closest('.reset-radios')) {

      const table = document.querySelector(".permissions-selection-container");
      const tableBody = table.querySelector("tbody");

      const radios = tableBody.querySelectorAll('input[type="radio"]')
      // radios.forEach(radio => radio.checked = true)
      // console.log('initialRadiosState => ', initialRadiosState)

      revertAllRadiosToInitialState();
      // });
      // radios.forEach(r => {
      //   initialRadiosState.forEach(s => {
      //     if (r.value == s.itemPermission) {
      //       r.checked = true
      //     } else {
      //       r.checked = false
      //       r.removeAttribute('checked')
      //     }
      //   })

      // for (let i = 0; i < radios.length; i++) {
      //   let r = radios[i]
      //   for (let j = 0; j < initialRadiosState.length; j++) {
      //     let s = initialRadiosState[j]
      //     console.log(radios, radios[i], initialRadiosState, initialRadiosState[j])
      //     console.log(r.value, s.itemPermission, r.value == s.itemPermission)
      //     if (r.value == s.itemPermission) {
      //       r.checked = true
      //       break;
      //     } else {
      //       r.checked = false
      //       r.removeAttribute('checked')

      //     }

      //   }
      // }

      // })


    }

    // edit for all button
    if (e.target.closest('.edit-for-all')) {

      const table = document.querySelector(".permissions-selection-container");
      const tableBody = table.querySelector("tbody");

      const radios = tableBody.querySelectorAll('input[type="radio"][value="5"]')
      radios.forEach(radio => radio.checked = true)

    }

  })

  document.addEventListener('change', e => {

    if (e.target.closest('#user_role_select')) {

      userConfig.role = e.target.value

    }

    if (e.target.closest('#item_type_select')) {

      userConfig.itemType = e.target.value

    }

    // fields table select
    if (e.target.closest('#field-table-select')) {
      const select = e.target.closest('#field-table-select')
      // console.log(select.value);
      const allTableCells = document.querySelectorAll('#permissionsTable td')

      if (select.value == 'all') {
        allTableCells.forEach(td => {
          // console.log(td.dataset.tableMetaId, ' = ', select.value)
          td.classList.remove('hidden')
        })
      } else {
        allTableCells.forEach(td => {
          // console.log(td.dataset.tableMetaId, ' = ', select.value)
          td.dataset.tableMetaId == select.value ? td.classList.remove('hidden') : td
            .classList.add('hidden')
        })
      }
    }


  })
})
</script>

<!-- Tippy.js -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
// With the above scripts loaded, you can call `tippy()` with a CSS
// selector and a `content` prop:
</script>
<?php

get_footer();









?>
