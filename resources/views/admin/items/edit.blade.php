<x-layouts.admin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">Edit item details and manage inventory</x-slot>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('admin.items.update', $item->id) }}" method="POST" class="card">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h3 class="card-title">Edit Item: {{ $item->nama_barang }}</h3>
                            <div class="card-actions">
                                <span class="badge bg-{{ $item->status_badge_class }}">
                                    {{ $item->status_text }}
                                </span>
                                @if ($item->isBorrowed())
                                    <button type="button" class="btn btn-sm btn-danger ms-2" id="mark-missing-btn"
                                        data-item-id="{{ $item->id }}" data-item-name="{{ $item->nama_barang }}">
                                        <i class="ti ti-alert-triangle me-1"></i>Mark as Missing
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Item Information Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">EPC Code</label>
                                        <input type="text" name="epc"
                                            class="form-control @error('epc') is-invalid @enderror"
                                            value="{{ old('epc', $item->epc) }}" placeholder="Enter EPC code" required>
                                        <div class="text-muted mt-1">Unique identifier for the item</div>
                                        @error('epc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Item Name</label>
                                        <input type="text" name="nama_barang"
                                            class="form-control @error('nama_barang') is-invalid @enderror"
                                            value="{{ old('nama_barang', $item->nama_barang) }}"
                                            placeholder="Enter item name" required>
                                        @error('nama_barang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Item Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror"
                                            required>
                                            <option value="available"
                                                {{ old('status', $item->status) == 'available' ? 'selected' : '' }}>
                                                Available
                                            </option>
                                            <option value="out_of_stock"
                                                {{ old('status', $item->status) == 'out_of_stock' ? 'selected' : '' }}>
                                                Out of Stock
                                            </option>
                                            @if ($item->status == 'borrowed')
                                                <option value="borrowed" selected>
                                                    Borrowed (Current Status)
                                                </option>
                                            @endif
                                            @if ($item->status == 'missing')
                                                <option value="missing" selected>
                                                    Missing (Current Status)
                                                </option>
                                            @endif
                                        </select>
                                        <div class="text-muted mt-1">Set item status</div>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Current Status Details</label>
                                        <div class="form-control-plaintext">
                                            <span
                                                class="badge bg-{{ $item->status_badge_class }} me-2">{{ $item->status_text }}</span>
                                            @if ($item->isBorrowed())
                                                <span class="text-warning">
                                                    <i class="ti ti-user me-1"></i>
                                                    Borrowed by: {{ $item->borrowerDetail->nama ?? 'Unknown User' }}
                                                </span>
                                            @elseif($item->isMissing())
                                                <span class="text-danger">
                                                    <i class="ti ti-alert-triangle me-1"></i>
                                                    Missing - Last borrowed by:
                                                    {{ $item->borrowerDetail->nama ?? 'Unknown User' }}
                                                </span>
                                            @elseif($item->isAvailable())
                                                <span class="text-success">
                                                    <i class="ti ti-check me-1"></i>
                                                    Available for borrowing
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="ti ti-x me-1"></i>
                                                    Out of stock
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Information if borrowed or missing -->
                            @if ($item->isBorrowed() || $item->isMissing())
                                <div class="mt-4">
                                    <h4 class="mb-3">
                                        @if ($item->isBorrowed())
                                            Current Borrower Information
                                        @else
                                            Last Borrower Information (Item Missing)
                                        @endif
                                    </h4>
                                    <div class="row">
                                        @if ($item->borrowerDetail)
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Borrower Name</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $item->borrowerDetail->nama ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">NIM</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $item->borrowerDetail->nim ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Program</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $item->borrowerDetail->prodi ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-warning">
                                                    <i class="ti ti-alert-triangle me-2"></i>
                                                    No borrower information available
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Item Information Display -->
                            <div class="mt-4">
                                <h4 class="mb-3">Item Information</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Created Date</label>
                                            <div class="form-control-plaintext">
                                                {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Last Updated</label>
                                            <div class="form-control-plaintext">
                                                {{ $item->updated_at ? $item->updated_at->format('d M Y, H:i') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Items
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-1">
                                    <i class="ti ti-refresh me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Mark as Missing Confirmation Modal --}}
    <div class="modal modal-blur fade" id="mark-missing-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark Item as Missing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Warning!</strong> This action will mark the item as missing. This means:
                        <ul class="mt-2 mb-0">
                            <li>The item will be flagged as missing in the system</li>
                            <li>The current borrower will remain responsible until the item is reclaimed</li>
                            <li>The borrower's coins will not be restored until the missing item is resolved</li>
                            <li>A missing tool record will be created for tracking</li>
                        </ul>
                    </div>
                    <p class="mb-0">
                        <strong>Item:</strong> <span id="missing-item-name"></span><br>
                        <strong>Current Borrower:</strong> {{ $item->borrowerDetail->nama ?? 'Unknown User' }}
                    </p>
                    <div class="mt-3">
                        <label class="form-label">Reason for marking as missing (optional):</label>
                        <textarea class="form-control" id="missing-reason" rows="3" placeholder="Enter additional details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-missing-btn">
                        <i class="ti ti-alert-triangle me-1"></i>Confirm Mark as Missing
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const csrf = "{{ csrf_token() }}";

                // Mark as Missing button click
                $('#mark-missing-btn').on('click', function() {
                    const itemId = $(this).data('item-id');
                    const itemName = $(this).data('item-name');

                    $('#missing-item-name').text(itemName);
                    $('#confirm-missing-btn').data('item-id', itemId);
                    $('#mark-missing-modal').modal('show');
                });

                // Confirm mark as missing
                $('#confirm-missing-btn').on('click', function() {
                    const itemId = $(this).data('item-id');
                    const reason = $('#missing-reason').val();
                    markItemAsMissing(itemId, reason);
                });

                // Form validation
                document.querySelector('form').addEventListener('submit', function(e) {
                    const epcInput = document.querySelector('input[name="epc"]');
                    const namaBarangInput = document.querySelector('input[name="nama_barang"]');
                    const statusSelect = document.querySelector('select[name="status"]');

                    // Basic validation
                    if (!epcInput.value.trim()) {
                        e.preventDefault();
                        showToast('EPC code is required', 'error');
                        epcInput.focus();
                        return;
                    }

                    if (!namaBarangInput.value.trim()) {
                        e.preventDefault();
                        showToast('Item name is required', 'error');
                        namaBarangInput.focus();
                        return;
                    }

                    if (!statusSelect.value) {
                        e.preventDefault();
                        showToast('Please select item status', 'error');
                        statusSelect.focus();
                        return;
                    }
                });

                // Functions
                function markItemAsMissing(itemId, reason) {
                    const $btn = $('#confirm-missing-btn');
                    const originalText = $btn.html();

                    $.ajax({
                        url: `/admin/missing-tools/mark-missing/${itemId}`,
                        method: 'POST',
                        data: {
                            reason: reason
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        beforeSend: function() {
                            $btn.prop('disabled', true).html('<i class="ti ti-loader"></i> Processing...');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#mark-missing-modal').modal('hide');
                                showToast(response.message, 'success');

                                // Redirect to items index after success
                                setTimeout(function() {
                                    window.location.href = "{{ route('admin.items.index') }}";
                                }, 2000);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast(response?.message || 'Failed to mark item as missing', 'error');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                }

                function showToast(message, type) {
                    // Create toast element
                    const toast = document.createElement('div');
                    toast.className =
                        `alert alert-${type === 'success' ? 'success' : 'warning'} alert-dismissible fade show`;
                    toast.style.position = 'fixed';
                    toast.style.top = '20px';
                    toast.style.right = '20px';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

                    document.body.appendChild(toast);

                    // Auto remove after 3 seconds
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 3000);
                }
            });
        </script>
    @endpush
</x-layouts.admin_layout>
