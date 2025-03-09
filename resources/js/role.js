/**
 * Page User List
 */

'use strict';

// Datatable (jquery)
$(function () {
  // Variable declaration for table
  var dt_role_table = $('.datatables-roles'),
    offCanvasForm = $('#offcanvasAddUser');

  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const dt_buttons = [
    {
      extend: 'collection',
      className: 'btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light',
      text: '<i class="ti ti-upload me-2 ti-xs"></i>Export',
      buttons: [
        {
          extend: 'print',
          title: 'Roles',
          text: '<i class="ti ti-printer me-2" ></i>Print',
          className: 'dropdown-item',
          exportOptions: {
            columns: [1, 2, 3, 4, 5],
            // prevent avatar to be print
            format: {
              body: function (inner, coldex, rowdex) {
                if (inner.length <= 0) return inner;
                var el = $.parseHTML(inner);
                var result = '';
                $.each(el, function (index, item) {
                  if (item.classList !== undefined && item.classList.contains('role-name')) {
                    result = result + item.lastChild.firstChild.textContent;
                  } else if (item.innerText === undefined) {
                    result = result + item.textContent;
                  } else result = result + item.innerText;
                });
                return result;
              }
            }
          },
          customize: function (win) {
            //customize print view for dark
            $(win.document.body)
              .css('color', config.colors.headingColor)
              .css('border-color', config.colors.borderColor)
              .css('background-color', config.colors.body);
            $(win.document.body)
              .find('table')
              .addClass('compact')
              .css('color', 'inherit')
              .css('border-color', 'inherit')
              .css('background-color', 'inherit');
          }
        },
        {
          extend: 'csv',
          title: 'Roles',
          text: '<i class="ti ti-file-text me-2" ></i>Csv',
          className: 'dropdown-item',
          exportOptions: {
            columns: [1, 2, 3, 4, 5],
            // prevent avatar to be display
            format: {
              body: function (inner, coldex, rowdex) {
                if (inner.length <= 0) return inner;
                var el = $.parseHTML(inner);
                var result = '';
                $.each(el, function (index, item) {
                  if (item.classList !== undefined && item.classList.contains('role-name')) {
                    result = result + item.lastChild.firstChild.textContent;
                  } else if (item.innerText === undefined) {
                    result = result + item.textContent;
                  } else result = result + item.innerText;
                });
                return result;
              }
            }
          }
        },
        {
          extend: 'excel',
          title: 'Roles',
          text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
          className: 'dropdown-item',
          exportOptions: {
            columns: [1, 2, 3, 4, 5],
            // prevent avatar to be display
            format: {
              body: function (inner, coldex, rowdex) {
                if (inner.length <= 0) return inner;
                var el = $.parseHTML(inner);
                var result = '';
                $.each(el, function (index, item) {
                  if (item.classList !== undefined && item.classList.contains('role-name')) {
                    result = result + item.lastChild.firstChild.textContent;
                  } else if (item.innerText === undefined) {
                    result = result + item.textContent;
                  } else result = result + item.innerText;
                });
                return result;
              }
            }
          }
        },
        {
          extend: 'pdf',
          title: 'Roles',
          text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
          className: 'dropdown-item',
          exportOptions: {
            columns: [1, 2, 3, 4, 5],
            // prevent avatar to be display
            format: {
              body: function (inner, coldex, rowdex) {
                if (inner.length <= 0) return inner;
                var el = $.parseHTML(inner);
                var result = '';
                $.each(el, function (index, item) {
                  if (item.classList !== undefined && item.classList.contains('role-name')) {
                    result = result + item.lastChild.firstChild.textContent;
                  } else if (item.innerText === undefined) {
                    result = result + item.textContent;
                  } else result = result + item.innerText;
                });
                return result;
              }
            }
          }
        },
        {
          extend: 'copy',
          title: 'Roles',
          text: '<i class="ti ti-copy me-2" ></i>Copy',
          className: 'dropdown-item',
          exportOptions: {
            columns: [1, 2, 3, 4, 5],
            // prevent avatar to be display
            format: {
              body: function (inner, coldex, rowdex) {
                if (inner.length <= 0) return inner;
                var el = $.parseHTML(inner);
                var result = '';
                $.each(el, function (index, item) {
                  if (item.classList !== undefined && item.classList.contains('role-name')) {
                    result = result + item.lastChild.firstChild.textContent;
                  } else if (item.innerText === undefined) {
                    result = result + item.textContent;
                  } else result = result + item.innerText;
                });
                return result;
              }
            }
          }
        }
      ]
    }
  ];

  if (canCreateRole) {
    dt_buttons.push({
      text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Role</span>',
      className: 'add-new btn btn-primary waves-effect waves-light'
    });
  }

  // Users datatable
  if (dt_role_table.length) {
    var dt_role = dt_role_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + 'roles/data'
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'id' },
        { data: 'name' },
        { data: 'created_at' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          searchable: false,
          orderable: false,
          targets: 1,
          render: function (data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            return (
              '<div class="d-flex align-items-center gap-50">' +
              (canUpdateRole
                ? `<a href="javascript:;" class="edit-record" data-id="${full['id']}"><i class="ti ti-edit"></i></a>`
                : '') +
              (canDeleteRole
                ? `<a href="javascript:;" class="delete-record" data-id="${full['id']}"><i class="ti ti-trash"></i></a>`
                : '') +
              '</div>'
            );
          }
        }
      ],
      order: [[2, 'desc']],
      dom:
        '<"row"' +
        '<"col-md-2"<"ms-n2"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"fB>>' +
        '>t' +
        '<"row"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      lengthMenu: [10, 25, 50, 100],
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Role',
        info: 'Displaying _START_ to _END_ of _TOTAL_ entries',
        paginate: {
          next: '<i class="ti ti-chevron-right ti-sm"></i>',
          previous: '<i class="ti ti-chevron-left ti-sm"></i>'
        }
      },
      // Buttons with Dropdown
      buttons: dt_buttons,
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
  }

  // Delete Record
  $(document).on('click', '.delete-record', function () {
    var role_id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // sweetalert for confirmation of delete
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        // delete the data
        $.ajax({
          type: 'DELETE',
          url: `${baseUrl}roles/${role_id}`,
          success: function () {
            dt_role.draw();
          },
          error: function (error) {
            console.log(error);
          }
        });

        // success sweetalert
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'The role has been deleted!',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Cancelled',
          text: 'The User is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // edit record
  $(document).on('click', '.edit-record', function () {
    var role_id = $(this).data('id');

    window.location.href = `${baseUrl}roles/${role_id}/edit`;
  });

  // changing the title
  $('.add-new').on('click', function () {
    window.location.href = `${baseUrl}roles/create`;
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);

  if ($('.toast-error').length > 0) {
    const toastAnimationError = document.querySelector('.toast-error');
    let toastAnimation;
    toastAnimation = new bootstrap.Toast(toastAnimationError);
    toastAnimation.show();
  }
});
