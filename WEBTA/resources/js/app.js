import './bootstrap';
import $ from 'jquery'; // Pastikan mengimpor jQuery


// Mengimpor Tabler
import '@tabler/core/dist/css/tabler.min.css';
import '@tabler/core/dist/js/tabler.min.js';

$('#usersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ url('/admin/users') }}", // Ganti dengan URL yang sesuai
        data: function(d) {
            d.uuid = $('#searchUUID').val();
            d.name = $('#searchName').val();
            d.email = $('#searchEmail').val();
            d.role = $('#searchRole').val();
        }
    },
    columns: [
        { data: 'uuid' },
        { data: 'usn' },
        { data: 'name' },
        { data: 'email' },
        { data: 'role' },
        { data: 'created_at' },
        {
            data: 'uuid',
            orderable: false,
            searchable: false,
            render: function(data) {
                return `<button class="btn btn-sm btn-danger delete-btn" data-uuid="${data}">Delete</button>`;
            }
        }
    ]
});
