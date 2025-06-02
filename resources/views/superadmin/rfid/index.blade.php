<x-layouts.superadmin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h3 class="card-title">RFID Tags</h3>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary" id="btn-bulk-actions" disabled>
                                    <i class="ti ti-settings me-1"></i>Bulk Actions
                                </button>
                                <button class="btn btn-primary" id="btn-add-tag">
                                    <i class="ti ti-plus me-1"></i>Add New Tag
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Stats Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-success text-white avatar">
                                                        <i class="ti ti-check"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium" id="stat-available">0</div>
                                                    <div class="text-muted">Available</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-warning text-white avatar">
                                                        <i class="ti ti-user"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium" id="stat-used">0</div>
                                                    <div class="text-muted">Used</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-danger text-white avatar">
                                                        <i class="ti ti-alert-triangle"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium" id="stat-damaged">0</div>
                                                    <div class="text-muted">Damaged</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-primary text-white avatar">
                                                        <i class="ti ti-list"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium" id="stat-total">0</div>
                                                    <div class="text-muted">Total</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="rfid-tags-table">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" class="form-check-input" id="select-all">
                                            </th>
                                            <th>#</th>
                                            <th>Tag ID</th>
                                            <th>Name/Notes</th>
                                            <th>Status</th>
                                            <th>Assigned To</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding/Editing RFID Tags -->
    <div class="modal modal-blur fade" id="modal-tag-form" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add New RFID Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tag-form">
                        <input type="hidden" id="tag-id">
                        <div class="mb-3">
                            <label class="form-label required">Tag ID</label>
                            <input type="text" class="form-control" id="tag-id-input" required
                                placeholder="Enter RFID tag ID">
                            <div class="invalid-feedback" id="tag-id-error"></div>
                            <small class="form-hint">Unique identifier for the RFID tag</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name/Notes</label>
                            <input type="text" class="form-control" id="tag-name"
                                placeholder="Optional name or notes">
                            <div class="invalid-feedback" id="tag-name-error"></div>
                            <small class="form-hint">Optional description for the tag</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="tag-active" checked>
                                <span class="form-check-label">Active Status</span>
                            </label>
                            <small class="form-hint">Inactive tags are marked as damaged</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ms-auto" id="btn-save-tag">
                        <i class="ti ti-device-floppy me-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal modal-blur fade" id="modal-delete-tag" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <p>Are you sure you want to delete the RFID tag <span id="tag-to-delete"
                                class="fw-bold"></span>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger ms-auto" id="btn-confirm-delete">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Bulk Actions -->
    <div class="modal modal-blur fade" id="modal-bulk-actions" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Selected Tags: <span id="selected-count">0</span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select class="form-select" id="bulk-action">
                            <option value="">Select an action</option>
                            <option value="Available">Mark as Available</option>
                            <option value="Damaged">Mark as Damaged</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ms-auto" id="btn-apply-bulk">Apply</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let selectedRows = [];

                // Load stats on page load
                loadStats();

                // Initialize DataTable
                const table = $('#rfid-tags-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('superadmin.rfid-tags.data') }}",
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return '<input type="checkbox" class="form-check-input row-select" value="' +
                                    row.id + '">';
                            }
                        },
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'uid',
                            name: 'uid'
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            render: function(data) {
                                return data || '<span class="text-muted">No notes</span>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'assigned_to',
                            name: 'assigned_to'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [2, 'desc']
                    ],
                    drawCallback: function() {
                        updateRowSelection();
                        // Reload stats after table draw
                        loadStats();
                    }
                });

                // Load statistics from API
                function loadStats() {
                    $.ajax({
                        url: "{{ route('superadmin.rfid-tags.stats') }}",
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                const stats = response.data;
                                $('#stat-available').text(stats.available);
                                $('#stat-used').text(stats.used);
                                $('#stat-damaged').text(stats.damaged);
                                $('#stat-total').text(stats.total);
                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to load statistics');
                        }
                    });
                }

                // Handle select all checkbox
                $('#select-all').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('.row-select').prop('checked', isChecked);
                    updateSelectedRows();
                });

                // Handle individual row selection
                $(document).on('change', '.row-select', function() {
                    updateSelectedRows();
                    updateSelectAllState();
                });

                function updateSelectedRows() {
                    selectedRows = [];
                    $('.row-select:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    $('#btn-bulk-actions').prop('disabled', selectedRows.length === 0);
                    $('#selected-count').text(selectedRows.length);
                }

                function updateSelectAllState() {
                    const totalRows = $('.row-select').length;
                    const checkedRows = $('.row-select:checked').length;
                    $('#select-all').prop('indeterminate', checkedRows > 0 && checkedRows < totalRows);
                    $('#select-all').prop('checked', checkedRows === totalRows && totalRows > 0);
                }

                function updateRowSelection() {
                    updateSelectedRows();
                    updateSelectAllState();
                }

                // Open the add tag modal
                $('#btn-add-tag').on('click', function() {
                    resetForm();
                    $('#modal-title').text('Add New RFID Tag');
                    $('#modal-tag-form').modal('show');
                });

                // Open the edit tag modal
                $(document).on('click', '.btn-edit', function() {
                    const id = $(this).data('id');
                    resetForm();

                    // Fetch tag details
                    $.ajax({
                        url: `/superadmin/rfid-tags/${id}`,
                        type: 'GET',
                        success: function(response) {
                            const tag = response.data;
                            $('#tag-id').val(tag.id);
                            $('#tag-id-input').val(tag.tag_id);
                            $('#tag-name').val(tag.name);
                            $('#tag-active').prop('checked', tag.is_active);

                            $('#modal-title').text('Edit RFID Tag');
                            $('#modal-tag-form').modal('show');
                        },
                        error: function(xhr) {
                            showToast('error', 'Failed to load tag details');
                        }
                    });
                });

                // Save tag
                $('#btn-save-tag').on('click', function() {
                    const id = $('#tag-id').val();
                    const isEdit = id !== '';
                    const url = isEdit ? `/superadmin/rfid-tags/${id}` : '/superadmin/rfid-tags';
                    const method = isEdit ? 'PUT' : 'POST';

                    const formData = {
                        tag_id: $('#tag-id-input').val(),
                        name: $('#tag-name').val(),
                        is_active: $('#tag-active').is(':checked') ? 1 : 0
                    };

                    // Reset validation errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#modal-tag-form').modal('hide');
                            table.ajax.reload();
                            loadStats(); // Reload stats after save
                            showToast('success', response.message);
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            if (response.errors) {
                                if (response.errors.tag_id) {
                                    $('#tag-id-input').addClass('is-invalid');
                                    $('#tag-id-error').text(response.errors.tag_id[0]);
                                }
                                if (response.errors.name) {
                                    $('#tag-name').addClass('is-invalid');
                                    $('#tag-name-error').text(response.errors.name[0]);
                                }
                            } else {
                                showToast('error', response.message || 'Failed to save RFID tag');
                            }
                        }
                    });
                });

                // Open delete confirmation modal
                // Open delete confirmation modal
                $(document).on('click', '.btn-delete', function() {
                    const id = $(this).data('id');
                    const tagId = $(this).data('tag');

                    $('#tag-to-delete').text(tagId);
                    $('#btn-confirm-delete').data('id', id);
                    $('#modal-delete-tag').modal('show');
                });

                // Confirm delete
                $('#btn-confirm-delete').on('click', function() {
                    const id = $(this).data('id');

                    $.ajax({
                        url: `/superadmin/rfid-tags/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#modal-delete-tag').modal('hide');
                            table.ajax.reload();
                            loadStats(); // Reload stats after delete
                            showToast('success', response.message);
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast('error', response.message || 'Failed to delete RFID tag');
                        }
                    });
                });

                // Open bulk actions modal
                $('#btn-bulk-actions').on('click', function() {
                    if (selectedRows.length === 0) {
                        showToast('warning', 'Please select at least one tag');
                        return;
                    }
                    $('#selected-count').text(selectedRows.length);
                    $('#bulk-action').val('');
                    $('#modal-bulk-actions').modal('show');
                });

                // Apply bulk action
                $('#btn-apply-bulk').on('click', function() {
                    const action = $('#bulk-action').val();

                    if (!action) {
                        showToast('warning', 'Please select an action');
                        return;
                    }

                    if (selectedRows.length === 0) {
                        showToast('warning', 'No tags selected');
                        return;
                    }

                    // Map action to status
                    let status = action;

                    $.ajax({
                        url: '/superadmin/rfid-tags/bulk-update-status',
                        type: 'POST',
                        data: {
                            tag_ids: selectedRows,
                            status: status
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#modal-bulk-actions').modal('hide');
                            table.ajax.reload();
                            loadStats(); // Reload stats after bulk update
                            selectedRows = [];
                            $('#select-all').prop('checked', false);
                            $('#btn-bulk-actions').prop('disabled', true);
                            showToast('success', response.message);
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast('error', response.message || 'Failed to update tags');
                        }
                    });
                });

                // Reset form function
                function resetForm() {
                    $('#tag-form')[0].reset();
                    $('#tag-id').val('');
                    $('#tag-active').prop('checked', true);
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                }

                // Toast notification function
                function showToast(type, message) {
                    // Remove any existing toasts
                    $('.toast').remove();

                    const iconMap = {
                        'success': 'ti-check',
                        'error': 'ti-alert-circle',
                        'warning': 'ti-alert-triangle',
                        'info': 'ti-info-circle'
                    };

                    const colorMap = {
                        'success': 'success',
                        'error': 'danger',
                        'warning': 'warning',
                        'info': 'info'
                    };

                    const toast = `
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" 
                             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                            <div class="toast-header bg-${colorMap[type]} text-white">
                                <i class="ti ${iconMap[type]} me-2"></i>
                                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                            </div>
                            <div class="toast-body">
                                ${message}
                            </div>
                        </div>
                    `;

                    $('body').append(toast);

                    // Auto hide after 5 seconds
                    setTimeout(function() {
                        $('.toast').fadeOut(function() {
                            $(this).remove();
                        });
                    }, 5000);
                }

                // Handle modal close events to reset selections
                $('#modal-bulk-actions').on('hidden.bs.modal', function() {
                    $('#bulk-action').val('');
                });

                $('#modal-tag-form').on('hidden.bs.modal', function() {
                    resetForm();
                });

                // Auto-refresh stats every 30 seconds
                setInterval(function() {
                    loadStats();
                }, 30000);

                // Keyboard shortcuts
                $(document).on('keydown', function(e) {
                    // Ctrl/Cmd + N for new tag
                    if ((e.ctrlKey || e.metaKey) && e.which === 78) {
                        e.preventDefault();
                        $('#btn-add-tag').click();
                    }

                    // Escape to close modals
                    if (e.which === 27) {
                        $('.modal').modal('hide');
                    }
                });

                // Handle form submission with Enter key
                $('#tag-form').on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $('#btn-save-tag').click();
                    }
                });

                // Real-time validation for tag ID
                $('#tag-id-input').on('input', function() {
                    const tagId = $(this).val().trim();
                    if (tagId.length > 0) {
                        // Remove any non-alphanumeric characters except hyphens and underscores
                        const cleanTagId = tagId.replace(/[^a-zA-Z0-9_-]/g, '');
                        if (cleanTagId !== tagId) {
                            $(this).val(cleanTagId);
                        }
                    }
                });

                // Add loading states to buttons
                function setButtonLoading(button, loading = true) {
                    if (loading) {
                        button.prop('disabled', true);
                        const originalText = button.text();
                        button.data('original-text', originalText);
                        button.html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...');
                    } else {
                        button.prop('disabled', false);
                        button.html(button.data('original-text'));
                    }
                }

                // Override AJAX success/error handlers to include loading states
                const originalSaveClick = $('#btn-save-tag').get(0).onclick;
                $('#btn-save-tag').off('click').on('click', function() {
                    const button = $(this);
                    setButtonLoading(button, true);

                    // Call original save function but modify AJAX to handle loading state
                    const id = $('#tag-id').val();
                    const isEdit = id !== '';
                    const url = isEdit ? `/superadmin/rfid-tags/${id}` : '/superadmin/rfid-tags';
                    const method = isEdit ? 'PUT' : 'POST';

                    const formData = {
                        tag_id: $('#tag-id-input').val(),
                        name: $('#tag-name').val(),
                        is_active: $('#tag-active').is(':checked') ? 1 : 0
                    };

                    // Reset validation errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            setButtonLoading(button, false);
                            $('#modal-tag-form').modal('hide');
                            table.ajax.reload();
                            loadStats();
                            showToast('success', response.message);
                        },
                        error: function(xhr) {
                            setButtonLoading(button, false);
                            const response = xhr.responseJSON;
                            if (response.errors) {
                                if (response.errors.tag_id) {
                                    $('#tag-id-input').addClass('is-invalid');
                                    $('#tag-id-error').text(response.errors.tag_id[0]);
                                }
                                if (response.errors.name) {
                                    $('#tag-name').addClass('is-invalid');
                                    $('#tag-name-error').text(response.errors.name[0]);
                                }
                            } else {
                                showToast('error', response.message || 'Failed to save RFID tag');
                            }
                        }
                    });
                });

                // Add search functionality enhancement
                $('#rfid-tags-table_filter input').attr('placeholder', 'Search tags...');

                // Initial load completed message
                console.log('RFID Tags management initialized successfully');
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
