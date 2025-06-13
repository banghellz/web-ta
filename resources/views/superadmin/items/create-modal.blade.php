{{-- resources/views/superadmin/items/create-modal.blade.php --}}
<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">
                    <i class="ti ti-plus me-2"></i>Add New Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addItemForm" method="POST" action="{{ route('superadmin.items.store') }}" accept-charset="UTF-8">
                @csrf
                <div class="modal-body">
                    <!-- Alert container for form messages -->
                    <div id="formAlert" class="alert alert-dismissible d-none" role="alert">
                        <div class="alert-message"></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="row">
                        <!-- EPC Field -->
                        <div class="col-md-6 mb-3">
                            <label for="epc" class="form-label required">
                                <i class="ti ti-qrcode me-1"></i>EPC Code
                            </label>
                            <input type="text" class="form-control" id="epc" name="epc"
                                placeholder="Enter EPC code" required autocomplete="off" spellcheck="false"
                                maxlength="255">
                            <div class="invalid-feedback"></div>
                            <small class="form-hint">Unique identifier for the item</small>
                        </div>

                        <!-- Item Name Field -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang" class="form-label required">
                                <i class="ti ti-package me-1"></i>Item Name
                            </label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                                placeholder="Enter item name" required autocomplete="off" spellcheck="false"
                                maxlength="255">
                            <div class="invalid-feedback"></div>
                            <small class="form-hint">Descriptive name for the item</small>
                        </div>
                    </div>

                    <!-- Debug Information (Remove in production) -->
                    <div class="row" id="debugInfo" style="display: none;">
                        <div class="col-12">
                            <div class="card bg-warning">
                                <div class="card-body py-2">
                                    <small><strong>Debug Info:</strong></small>
                                    <div id="debugContent"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-info-circle text-info me-2"></i>
                                        <small class="text-muted">
                                            The item will be automatically set as available when created.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        <i class="ti ti-plus me-1"></i>Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Enable debug mode (set to false in production)
        const DEBUG_MODE = true;

        // Enhanced toast function using unified system
        function showModalToast(message, type = 'info', title = null) {
            console.log('Modal toast request:', {
                type,
                message,
                title
            });

            // Try multiple toast methods in order of preference
            if (window.UnifiedToastSystem) {
                window.UnifiedToastSystem.show(type, message, title);
            } else if (typeof window.showNotificationToast === 'function') {
                window.showNotificationToast(message, type);
            } else if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                // Fallback: trigger the toast manually
                console.log('Using manual toast fallback');
                const toast = $('#notificationToast');
                const toastIcon = $('#toastIcon');
                const toastMessage = $('#toastMessage');
                const toastTitle = $('#toastTitle');

                if (toast.length && toastIcon.length && toastMessage.length) {
                    // Set content
                    if (title) toastTitle.text(title);
                    toastMessage.text(message);

                    // Set icon based on type
                    const icons = {
                        'success': 'ti-check',
                        'error': 'ti-alert-circle',
                        'warning': 'ti-alert-triangle',
                        'info': 'ti-info-circle'
                    };

                    toastIcon.attr('class', `ti ${icons[type] || icons.info} me-2`);

                    // Add color class based on type
                    toast.removeClass('text-success text-danger text-warning text-info');
                    if (type !== 'info') {
                        toast.addClass(`text-${type === 'error' ? 'danger' : type}`);
                    }

                    // Show toast using Bootstrap
                    const bsToast = new bootstrap.Toast(toast[0], {
                        autohide: true,
                        delay: 5000
                    });
                    bsToast.show();
                } else {
                    // Ultimate fallback
                    alert(`${type.toUpperCase()}: ${message}`);
                }
            }
        }

        // Reset form when modal is opened
        $('#addItemModal').on('show.bs.modal', function() {
            resetForm();
            if (DEBUG_MODE) {
                $('#debugInfo').show();
            }
        });

        // Handle form submission
        $('#addItemForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Real-time validation and debugging
        $('#epc, #nama_barang').on('input', function() {
            clearFieldError($(this));

            if (DEBUG_MODE) {
                updateDebugInfo();
            }
        });

        // Debug function to show current values
        function updateDebugInfo() {
            const epcVal = $('#epc').val();
            const namaVal = $('#nama_barang').val();

            $('#debugContent').html(`
                <div><strong>EPC Raw:</strong> "${epcVal}"</div>
                <div><strong>EPC Length:</strong> ${epcVal.length}</div>
                <div><strong>EPC Chars:</strong> ${Array.from(epcVal).map(c => c.charCodeAt(0)).join(', ')}</div>
                <div><strong>Nama Raw:</strong> "${namaVal}"</div>
                <div><strong>Nama Length:</strong> ${namaVal.length}</div>
                <div><strong>Nama Chars:</strong> ${Array.from(namaVal).map(c => c.charCodeAt(0)).join(', ')}</div>
            `);
        }

        /**
         * Submit the add item form
         */
        function submitForm() {
            const form = $('#addItemForm');
            const submitBtn = $('#submitBtn');
            const spinner = submitBtn.find('.spinner-border');

            // Get raw values
            const epcRaw = $('#epc').val();
            const namaRaw = $('#nama_barang').val();

            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('EPC Raw Value:', epcRaw);
            console.log('EPC Length:', epcRaw.length);
            console.log('EPC Char Codes:', Array.from(epcRaw).map(c => c.charCodeAt(0)));
            console.log('Nama Raw Value:', namaRaw);
            console.log('Nama Length:', namaRaw.length);
            console.log('Nama Char Codes:', Array.from(namaRaw).map(c => c.charCodeAt(0)));

            // Clean the values properly
            const epcClean = epcRaw.trim().replace(/[^\w\-\.]/g, '');
            const namaClean = namaRaw.trim().replace(/\s+/g, ' ');

            console.log('EPC Clean:', epcClean);
            console.log('Nama Clean:', namaClean);

            // Validate cleaned values
            if (!epcClean) {
                showAlert('danger', 'EPC code is required');
                return;
            }

            if (!namaClean) {
                showAlert('danger', 'Item name is required');
                return;
            }

            // Update the form with clean values
            $('#epc').val(epcClean);
            $('#nama_barang').val(namaClean);

            // Create FormData manually to ensure proper encoding
            const formData = new FormData();
            formData.append('_token', $('input[name="_token"]').val());
            formData.append('epc', epcClean);
            formData.append('nama_barang', namaClean);

            // Log what we're sending
            console.log('=== SENDING DATA ===');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': "' + pair[1] + '"');
            }

            // Clear previous errors
            clearAllErrors();

            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            submitBtn.find('i.ti-plus').addClass('d-none');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                },
                success: function(response) {
                    console.log('=== SUCCESS RESPONSE ===');
                    console.log('Response:', response);

                    if (response.success) {
                        // Clean message from any HTML entities
                        const cleanMessage = $('<div>').html(response.message ||
                            'Item added successfully!').text();

                        // Show success message in modal
                        showAlert('success', cleanMessage);

                        // Reset form
                        form[0].reset();

                        // Close modal after short delay
                        setTimeout(function() {
                            $('#addItemModal').modal('hide');

                            // Refresh DataTable if it exists
                            if (typeof window.itemsDataTable !== 'undefined') {
                                window.itemsDataTable.ajax.reload(null, false);
                            }

                            // Show toast notification with unified system
                            showModalToast(cleanMessage, 'success', 'Item Added');

                            // Refresh notifications
                            if (typeof window.refreshNotifications === 'function') {
                                window.refreshNotifications();
                            }
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    console.log('=== ERROR RESPONSE ===');
                    console.log('Status:', xhr.status);
                    console.log('Response:', xhr.responseJSON);

                    const response = xhr.responseJSON;

                    if (xhr.status === 422 && response.errors) {
                        // Validation errors
                        displayValidationErrors(response.errors);
                        showAlert('danger', response.message || 'Please fix the errors below.');
                    } else {
                        // Other errors
                        const errorMessage = response.message ||
                            'An error occurred while adding the item.';
                        showAlert('danger', errorMessage);

                        // Also show as toast for better visibility
                        showModalToast(errorMessage, 'error', 'Error');
                    }
                },
                complete: function() {
                    // Reset loading state
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                    submitBtn.find('i.ti-plus').removeClass('d-none');
                }
            });
        }

        /**
         * Display validation errors
         */
        function displayValidationErrors(errors) {
            $.each(errors, function(field, messages) {
                const input = $(`#${field}`);
                const feedback = input.siblings('.invalid-feedback');

                input.addClass('is-invalid');
                feedback.text(messages[0]);
            });
        }

        /**
         * Clear field error
         */
        function clearFieldError(input) {
            input.removeClass('is-invalid');
            input.siblings('.invalid-feedback').text('');
        }

        /**
         * Clear all form errors
         */
        function clearAllErrors() {
            $('#addItemForm .form-control').removeClass('is-invalid');
            $('#addItemForm .invalid-feedback').text('');
            $('#formAlert').addClass('d-none');
        }

        /**
         * Show alert message in modal
         */
        function showAlert(type, message) {
            const alert = $('#formAlert');
            const alertMessage = alert.find('.alert-message');

            alert.removeClass('alert-success alert-danger alert-warning alert-info')
                .addClass(`alert-${type}`)
                .removeClass('d-none');

            // Decode HTML entities if present
            const cleanMessage = $('<div>').html(message).text();
            alertMessage.text(cleanMessage);

            // Auto hide success messages
            if (type === 'success') {
                setTimeout(function() {
                    alert.addClass('d-none');
                }, 3000);
            }
        }

        /**
         * Reset form to initial state
         */
        function resetForm() {
            const form = $('#addItemForm');
            form[0].reset();
            clearAllErrors();

            // Clear debug info
            $('#debugContent').empty();
        }

        // Make functions available globally
        window.submitForm = submitForm;
        window.showAlert = showAlert;
        window.clearAllErrors = clearAllErrors;
        window.resetForm = resetForm;
    });

    // Global function to open the modal
    window.openAddItemModal = function() {
        $('#addItemModal').modal('show');
    };
</script>

<style>
    .required::after {
        content: " *";
        color: var(--tblr-danger);
    }

    .form-hint {
        color: var(--tblr-secondary);
    }

    .modal-header {
        background-color: var(--tblr-bg-surface-secondary);
        border-bottom: 1px solid var(--tblr-border-color);
    }

    .modal-footer {
        background-color: var(--tblr-bg-surface-secondary);
        border-top: 1px solid var(--tblr-border-color);
    }

    #addItemForm .form-control:focus {
        border-color: var(--tblr-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.25);
    }

    .card.bg-light {
        background-color: var(--tblr-bg-surface-tertiary) !important;
    }

    /* Ensure proper text input handling */
    #addItemForm input[type="text"] {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        direction: ltr;
        text-align: left;
        unicode-bidi: normal;
    }

    /* Alert styling in modal */
    #formAlert {
        border-radius: var(--tblr-border-radius);
        margin-bottom: 1rem;
    }

    #formAlert.alert-success {
        background-color: var(--tblr-success-lt);
        border-color: var(--tblr-success);
        color: var(--tblr-success-fg);
    }

    #formAlert.alert-danger {
        background-color: var(--tblr-danger-lt);
        border-color: var(--tblr-danger);
        color: var(--tblr-danger-fg);
    }
</style>
