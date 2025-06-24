<!-- resources/views/superadmin/rfid/index.blade.php -->
<x-layouts.admin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-nfc me-2 text-primary"></i>{{ $title ?? 'RFID Tags Management' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Manage RFID tags and their assignments' }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <span class="d-none d-sm-inline">
                            <button id="reload-rfid" class="btn btn-primary">
                                <i class="ti ti-refresh me-1"></i>
                                Refresh
                            </button>
                        </span>
                        <button class="btn btn-success" id="btn-add-rfid">
                            <i class="ti ti-plus me-1"></i>
                            Add New RFID Tag
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RFID Tags Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Total Tags -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <i class="ti ti-nfc"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-tags">{{ number_format($totalTags ?? 0) }}</span>
                                                Tags
                                            </div>
                                            <div class="text-muted">
                                                Total Tags
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Available Tags -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="ti ti-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span
                                                    id="available-tags">{{ number_format($availableTags ?? 0) }}</span>
                                                Tags
                                            </div>
                                            <div class="text-muted">
                                                Available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Used Tags -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-yellow text-white avatar">
                                                <i class="ti ti-user"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="used-tags">{{ number_format($usedTags ?? 0) }}</span>
                                                Tags
                                            </div>
                                            <div class="text-muted">
                                                Used
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RFID Tags Table -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">RFID Tags List</h3>
                    <div class="card-actions">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-filter"></i>
                                    </span>
                                    <select id="status-filter" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="available">Available</option>
                                        <option value="used">Used</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="rfidTable" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>RFID UID</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Created At</th>
                                    <th width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will fill this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding/Editing RFID Tags -->
    <div class="modal modal-blur fade" id="modal-rfid-form" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add New RFID Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rfid-form">
                        <input type="hidden" id="rfid-id">
                        <div class="mb-3" id="rfid-uid-group">
                            <label class="form-label required">RFID UID</label>
                            <input type="text" class="form-control" id="rfid-uid-input" required
                                placeholder="Enter RFID UID">
                            <div class="invalid-feedback" id="rfid-uid-error"></div>
                            <small class="form-hint">Unique identifier for the RFID tag</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tag Name/Notes</label>
                            <input type="text" class="form-control" id="rfid-notes"
                                placeholder="Optional name or notes">
                            <div class="invalid-feedback" id="rfid-notes-error"></div>
                            <small class="form-hint">Optional description for the tag</small>
                        </div>
                        <div class="mb-3" id="rfid-status-group">
                            <label class="form-label required">Status</label>
                            <select class="form-select" id="rfid-status" required>
                                <option value="Available">Available</option>
                                <option value="Used">Used</option>
                            </select>
                            <div class="invalid-feedback" id="rfid-status-error"></div>
                            <small class="form-hint">Current status of the RFID tag</small>
                        </div>
                        <div class="mb-3" id="assign-to-group" style="display: none;">
                            <label class="form-label">Assign To User</label>
                            <select class="form-select" id="assign-to-user">
                                <option value="">Select a user to assign (optional)</option>
                            </select>
                            <div class="invalid-feedback" id="assign-to-error"></div>
                            <small class="form-hint">Assign this RFID tag to a specific user</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ms-auto" id="btn-save-rfid">
                        <i class="ti ti-device-floppy me-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal modal-blur fade" id="modal-delete-rfid" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete RFID Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-alert-triangle text-danger" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Are you sure?</h3>
                        <p>Are you sure you want to delete the RFID tag <span id="rfid-to-delete"
                                class="fw-bold"></span>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger ms-auto" id="btn-confirm-delete">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Release RFID Confirmation -->
    <div class="modal modal-blur fade" id="modal-release-rfid" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Release RFID Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-user-minus text-warning" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Release RFID Tag?</h3>
                        <p>Are you sure you want to release the RFID tag <span id="rfid-to-release"
                                class="fw-bold"></span> from the user?</p>
                        <p class="text-warning">This will make the tag available for assignment to other users.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning ms-auto" id="btn-confirm-release">
                        <i class="ti ti-user-minus me-1"></i>Release
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .dropdown-menu-actions {
                min-width: 140px;
            }

            .dropdown-item:hover {
                background-color: var(--tblr-hover-bg);
            }

            .dropdown-item.text-danger:hover {
                background-color: var(--tblr-red-lt);
                color: var(--tblr-red) !important;
            }

            .dropdown-item.text-warning:hover {
                background-color: var(--tblr-yellow-lt);
                color: var(--tblr-yellow) !important;
            }

            .btn-actions {
                border: none;
                background: transparent;
                color: var(--tblr-body-color);
                font-size: 1.2rem;
                padding: 0.375rem 0.5rem;
                border-radius: var(--tblr-border-radius);
                transition: all 0.15s ease-in-out;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-actions:hover {
                background-color: var(--tblr-hover-bg);
                color: var(--tblr-primary);
            }

            /* Pastikan kolom actions benar-benar center */
            #rfidTable thead th:last-child,
            #rfidTable tbody td:last-child {
                text-align: center !important;
                vertical-align: middle !important;
            }

            /* Force center alignment untuk DataTables */
            .dataTables_wrapper .text-center {
                text-align: center !important;
            }

            /* Loading spinner for modal */
            .modal-loading {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 200px;
            }

            .spinner-border-sm {
                width: 1rem;
                height: 1rem;
                border-width: 0.1em;
            }

            .rotating {
                animation: rotate 1s linear infinite;
            }

            @keyframes rotate {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(function() {
                const csrfToken = "{{ csrf_token() }}";
                let rfidToDelete = null;
                let rfidToRelease = null;

                // Initialize DataTable with custom styling
                const table = $('#rfidTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/admin/rfid-tags/data",
                        type: 'GET',
                        dataSrc: function(json) {
                            // Update statistics if provided in response
                            updateStats(json.stats || {});
                            return json.data;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'rfid_uid',
                            name: 'uid',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'assigned_to',
                            name: 'assigned_to',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center align-middle',
                            width: '80px',
                            render: function(data, type, row) {
                                return data;
                            }
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ], // Order by created_at descending (updated index)
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search RFID tags...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading RFID tags...",
                        emptyTable: "No RFID tags found",
                        info: "Showing _START_ to _END_ of _TOTAL_ tags",
                        infoEmpty: "Showing 0 to 0 of 0 tags",
                        infoFiltered: "(filtered from _MAX_ total tags)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 25,
                    responsive: true
                });

                // Handle refresh button
                $('#reload-rfid').on('click', function() {
                    const $btn = $(this);
                    const originalHtml = $btn.html();

                    // Show loading state
                    $btn.prop('disabled', true).html(
                        '<i class="ti ti-loader-2 me-1 rotating"></i>Refreshing...');

                    table.ajax.reload(function() {
                        // Use unified toast system
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.success('Data refreshed successfully!');
                        }

                        // Reset button state
                        setTimeout(() => {
                            $btn.prop('disabled', false).html(originalHtml);
                        }, 500);
                    });
                });

                // Status filter
                $('#status-filter').on('change', function() {
                    const selectedStatus = $(this).val();
                    table.column(2).search(selectedStatus).draw(); // Updated index
                });

                function loadUsers() {
                    console.log('Loading users for assignment...');

                    // Tampilkan loading di select
                    const select = $('#assign-to-user');
                    select.empty().append('<option value="">Loading users...</option>');

                    $.ajax({
                        url: "/superadmin/rfid-tags/available-users", // Pastikan route ini ada
                        type: 'GET',
                        success: function(response) {
                            console.log('Users loaded successfully:', response);

                            if (response.success && response.data) {
                                select.empty().append(
                                    '<option value="">Select a user to assign (optional)</option>');

                                response.data.forEach(function(user) {
                                    // Tampilkan info apakah user sudah punya RFID
                                    const hasRfidText = user.has_rfid ? ' (Has RFID)' :
                                        ' (No RFID)';
                                    const optionText = `${user.name} - ${user.nim}${hasRfidText}`;

                                    select.append(
                                        `<option value="${user.id}" data-has-rfid="${user.has_rfid}">${optionText}</option>`
                                    );
                                });

                                console.log(`Loaded ${response.data.length} users`);
                            } else {
                                select.empty().append('<option value="">No users available</option>');
                                console.error('Invalid response format:', response);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to load users:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                error: error
                            });

                            select.empty().append('<option value="">Failed to load users</option>');

                            // Tampilkan error message
                            if (window.UnifiedToastSystem) {
                                let errorMessage = 'Failed to load users for assignment';

                                if (xhr.status === 404) {
                                    errorMessage =
                                        'User assignment feature not available (route not found)';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Server error while loading users';
                                }

                                window.UnifiedToastSystem.error(errorMessage);
                            }
                        }
                    });
                }

                // Open the add RFID modal
                $('#btn-add-rfid').on('click', function() {
                    resetForm();
                    $('#modal-title').text('Add New RFID Tag');
                    $('#rfid-status').val('Available'); // Set default status
                    $('#rfid-uid-group').show();
                    $('#rfid-status-group').show();
                    $('#assign-to-group').hide();
                    $('#modal-rfid-form').modal('show');
                    // Focus on the first input field
                    setTimeout(function() {
                        $('#rfid-uid-input').focus();
                    }, 500);
                });

                // PERBAIKAN: Fungsi edit RFID yang diperbaiki
                $(document).on('click', '.btn-edit', function() {
                    const id = $(this).data('id');
                    resetForm();

                    // Show loading state
                    $('#modal-title').text('Loading...');
                    $('#modal-rfid-form').modal('show');

                    // Load users dulu sebelum load RFID details
                    console.log('Starting edit process for RFID ID:', id);
                    loadUsers();

                    // Fetch RFID details
                    $.ajax({
                        url: `/superadmin/rfid-tags/${id}/edit`,
                        type: 'GET',
                        success: function(response) {
                            console.log('RFID details loaded:', response);

                            if (response.success && response.data) {
                                const rfid = response.data;
                                $('#rfid-id').val(rfid.id);
                                $('#rfid-notes').val(rfid.name || '');

                                // Hide fields yang tidak perlu di edit
                                $('#rfid-uid-group').hide();
                                $('#rfid-status-group').hide();
                                $('#assign-to-group').show();

                                // Set assigned user jika ada
                                if (rfid.assigned_user_id) {
                                    // Tunggu sebentar untuk memastikan users sudah di-load
                                    setTimeout(function() {
                                        $('#assign-to-user').val(rfid.assigned_user_id);
                                        console.log('Set assigned user to:', rfid
                                            .assigned_user_id);
                                    }, 500);
                                }

                                $('#modal-title').text('Edit RFID Tag');

                                // Focus pada field pertama
                                setTimeout(function() {
                                    $('#rfid-notes').focus();
                                }, 100);
                            } else {
                                console.error('Invalid RFID response format:', response);
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.error('Invalid response format');
                                }
                                $('#modal-rfid-form').modal('hide');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Edit error:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                error: error
                            });

                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error('Failed to load RFID tag details');
                            }
                            $('#modal-rfid-form').modal('hide');
                        }
                    });
                });

                // Event handler untuk monitoring perubahan di select user
                $('#assign-to-user').on('change', function() {
                    const selectedUserId = $(this).val();
                    const selectedOption = $(this).find('option:selected');
                    const hasRfid = selectedOption.data('has-rfid');

                    console.log('User selection changed:', {
                        userId: selectedUserId,
                        hasRfid: hasRfid
                    });

                    // Bisa tambahkan warning jika user sudah punya RFID
                    if (selectedUserId && hasRfid) {
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.warning('This user already has an RFID tag assigned');
                        }
                    }
                });

                // Save RFID tag
                $('#btn-save-rfid').on('click', function() {
                    const button = $(this);
                    const id = $('#rfid-id').val();
                    const isEdit = id !== '';
                    const url = isEdit ? `/admin/rfid-tags/${id}` : '/admin/rfid-tags';
                    const method = isEdit ? 'PUT' : 'POST';

                    let formData;

                    if (isEdit) {
                        // For edit: only name and assignment
                        formData = {
                            name: $('#rfid-notes').val().trim(),
                            assigned_user_id: $('#assign-to-user').val() || null
                        };
                    } else {
                        // For create: validate required fields
                        const rfidUid = $('#rfid-uid-input').val().trim();
                        const status = $('#rfid-status').val();

                        if (!rfidUid) {
                            $('#rfid-uid-input').addClass('is-invalid');
                            $('#rfid-uid-error').text('RFID UID is required');
                            $('#rfid-uid-input').focus();
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.warning('Please fill in the RFID UID field');
                            }
                            return;
                        }

                        if (!status) {
                            $('#rfid-status').addClass('is-invalid');
                            $('#rfid-status-error').text('Status is required');
                            $('#rfid-status').focus();
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.warning('Please select a status');
                            }
                            return;
                        }

                        formData = {
                            uid: rfidUid,
                            status: status,
                            notes: $('#rfid-notes').val().trim()
                        };
                    }

                    // Reset validation errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    // Show loading state
                    setButtonLoading(button, true);

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            setButtonLoading(button, false);
                            if (response.success) {
                                $('#modal-rfid-form').modal('hide');
                                table.ajax.reload();

                                // Use unified toast system for success message
                                if (window.UnifiedToastSystem) {
                                    const successMessage = response.message || (isEdit ?
                                        'RFID tag updated successfully!' :
                                        'RFID tag created successfully!'
                                    );
                                    window.UnifiedToastSystem.success(successMessage);
                                }
                            } else {
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.error(response.message ||
                                        'Operation failed');
                                }
                            }
                        },
                        error: function(xhr) {
                            setButtonLoading(button, false);
                            console.error('Save error:', xhr);

                            if (xhr.status === 422) {
                                const response = xhr.responseJSON;
                                if (response && response.errors) {
                                    // Handle validation errors
                                    if (response.errors.uid) {
                                        $('#rfid-uid-input').addClass('is-invalid');
                                        $('#rfid-uid-error').text(response.errors.uid[0]);
                                    }
                                    if (response.errors.name) {
                                        $('#rfid-notes').addClass('is-invalid');
                                        $('#rfid-notes-error').text(response.errors.name[0]);
                                    }
                                    if (response.errors.notes) {
                                        $('#rfid-notes').addClass('is-invalid');
                                        $('#rfid-notes-error').text(response.errors.notes[0]);
                                    }
                                    if (response.errors.status) {
                                        $('#rfid-status').addClass('is-invalid');
                                        $('#rfid-status-error').text(response.errors.status[0]);
                                    }
                                    if (response.errors.assigned_user_id) {
                                        $('#assign-to-user').addClass('is-invalid');
                                        $('#assign-to-error').text(response.errors.assigned_user_id[
                                            0]);
                                    }

                                    // Show validation error toast
                                    if (window.UnifiedToastSystem) {
                                        window.UnifiedToastSystem.warning(
                                            'Please check the form for validation errors');
                                    }
                                } else {
                                    if (window.UnifiedToastSystem) {
                                        window.UnifiedToastSystem.error(response.message ||
                                            'Validation failed');
                                    }
                                }
                            } else {
                                const response = xhr.responseJSON;
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.error(response?.message ||
                                        'Failed to save RFID tag. Please try again.');
                                }
                            }
                        }
                    });
                });

                // Handle delete RFID click
                $(document).on('click', '.delete-rfid', function(e) {
                    e.preventDefault();

                    const rfidId = $(this).data('rfid-id');
                    const rfidUid = $(this).data('rfid-uid');

                    rfidToDelete = {
                        id: rfidId,
                        uid: rfidUid
                    };

                    $('#rfid-to-delete').text(rfidUid);
                    $('#modal-delete-rfid').modal('show');
                });

                // Handle release RFID click
                $(document).on('click', '.release-rfid', function(e) {
                    e.preventDefault();

                    const rfidId = $(this).data('rfid-id');
                    const rfidUid = $(this).data('rfid-uid');

                    rfidToRelease = {
                        id: rfidId,
                        uid: rfidUid
                    };

                    $('#rfid-to-release').text(rfidUid);
                    $('#modal-release-rfid').modal('show');
                });

                // Handle confirm delete
                $('#btn-confirm-delete').on('click', function() {
                    if (!rfidToDelete) return;

                    const button = $(this);
                    setButtonLoading(button, true);

                    // Use AJAX instead of form submission for better error handling
                    $.ajax({
                        url: `/admin/rfid-tags/${rfidToDelete.id}`,
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            setButtonLoading(button, false);

                            if (response.success) {
                                $('#modal-delete-rfid').modal('hide');
                                table.ajax.reload();

                                // Use unified toast system for success
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.success(response.message ||
                                        'RFID tag deleted successfully!');
                                }
                            } else {
                                // Use unified toast system for error
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.error(response.message ||
                                        'Failed to delete RFID tag');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            setButtonLoading(button, false);

                            let errorMessage = 'Failed to delete RFID tag. Please try again.';
                            let toastType = 'error';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;

                                // Check if it's the "assigned to user" error (status 400)
                                if (xhr.status === 400 && xhr.responseJSON.message.includes(
                                        'assigned to a user')) {
                                    toastType = 'warning';
                                    errorMessage =
                                        'Cannot delete RFID tag that is still assigned to a user. Please release it first.';
                                }
                            }

                            // Use unified toast system with appropriate type
                            if (window.UnifiedToastSystem) {
                                if (toastType === 'warning') {
                                    window.UnifiedToastSystem.warning(errorMessage);
                                } else {
                                    window.UnifiedToastSystem.error(errorMessage);
                                }
                            }

                            // Close modal only if it's not the "assigned to user" error
                            if (xhr.status !== 400) {
                                $('#modal-delete-rfid').modal('hide');
                            }
                        }
                    });
                });

                // Handle confirm release
                $('#btn-confirm-release').on('click', function() {
                    if (!rfidToRelease) return;

                    const button = $(this);
                    setButtonLoading(button, true);

                    $.ajax({
                        url: `/admin/rfid-tags/release/${rfidToRelease.id}`,
                        type: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            setButtonLoading(button, false);

                            if (response.success) {
                                $('#modal-release-rfid').modal('hide');
                                table.ajax.reload();

                                // Use unified toast system for success
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.success(response.message ||
                                        'RFID tag released successfully!');
                                }
                            } else {
                                // Use unified toast system for error
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.error(response.message ||
                                        'Failed to release RFID tag');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            setButtonLoading(button, false);
                            console.error('Error releasing RFID tag:', error);

                            let errorMessage = 'Failed to release RFID tag. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            // Use unified toast system for error
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error(errorMessage);
                            }
                        }
                    });
                });

                // Clear variables when modals are hidden
                $('#modal-delete-rfid').on('hidden.bs.modal', function() {
                    rfidToDelete = null;
                    // Reset delete button state
                    const deleteBtn = $('#btn-confirm-delete');
                    if (deleteBtn.data('original-html')) {
                        deleteBtn.prop('disabled', false).html(deleteBtn.data('original-html'));
                    }
                });

                $('#modal-release-rfid').on('hidden.bs.modal', function() {
                    rfidToRelease = null;
                    // Reset release button state
                    const releaseBtn = $('#btn-confirm-release');
                    if (releaseBtn.data('original-html')) {
                        releaseBtn.prop('disabled', false).html(releaseBtn.data('original-html'));
                    }
                });

                $('#modal-rfid-form').on('hidden.bs.modal', function() {
                    resetForm();
                });

                // Helper function to update the statistics
                function updateStats(stats) {
                    if (stats.total !== undefined) {
                        $('#total-tags').text(stats.total.toLocaleString());
                    }
                    if (stats.available !== undefined) {
                        $('#available-tags').text(stats.available.toLocaleString());
                    }
                    if (stats.used !== undefined) {
                        $('#used-tags').text(stats.used.toLocaleString());
                    }
                }

                // Reset form function
                function resetForm() {
                    $('#rfid-form')[0].reset();
                    $('#rfid-id').val('');
                    $('#rfid-status').val('Available'); // Reset to default status
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    // Reset save button state
                    const saveBtn = $('#btn-save-rfid');
                    if (saveBtn.data('original-html')) {
                        saveBtn.prop('disabled', false).html(saveBtn.data('original-html'));
                    }
                }

                // Enhanced button loading state function
                function setButtonLoading(button, loading = true) {
                    if (loading) {
                        button.prop('disabled', true);
                        const originalHtml = button.html();
                        button.data('original-html', originalHtml);

                        // Different loading text based on button
                        let loadingText = 'Loading...';
                        if (button.attr('id') === 'btn-save-rfid') {
                            loadingText = 'Saving...';
                        } else if (button.attr('id') === 'btn-confirm-delete') {
                            loadingText = 'Deleting...';
                        } else if (button.attr('id') === 'btn-confirm-release') {
                            loadingText = 'Releasing...';
                        }

                        button.html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>' +
                            loadingText);
                    } else {
                        button.prop('disabled', false);
                        const originalHtml = button.data('original-html');
                        if (originalHtml) {
                            button.html(originalHtml);
                        }
                    }
                }

                // Real-time validation for RFID UID
                $('#rfid-uid-input').on('input', function() {
                    const rfidUid = $(this).val().trim();
                    if (rfidUid.length > 0) {
                        // Remove validation error on input
                        $(this).removeClass('is-invalid');
                        $('#rfid-uid-error').text('');

                        // Clean RFID UID - allow only alphanumeric, hyphens, and underscores
                        const cleanRfidUid = rfidUid.replace(/[^a-zA-Z0-9_-]/g, '');
                        if (cleanRfidUid !== rfidUid) {
                            $(this).val(cleanRfidUid);
                        }
                    }
                });

                // Handle form submission with Enter key
                $('#rfid-form').on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $('#btn-save-rfid').click();
                    }
                });

                // Initial load completed message
                console.log('RFID Tags management initialized successfully');
            });

            // Global refresh function for backward compatibility
            function refreshRfidTable() {
                if ($('#rfidTable').DataTable()) {
                    $('#rfidTable').DataTable().ajax.reload(null, false);
                }
            }
        </script>
    @endpush
</x-layouts.admin_layout>
