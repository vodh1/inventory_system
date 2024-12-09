$(document).ready(function() {
    var table = $('#borrowRequestsTable').DataTable({
        "lengthChange": false, // Disable the "Show entries" dropdown
        "pageLength": 5,
        "order": [], // Disable initial sorting
        "columnDefs": [
            {
                "targets": 9, // Index of the Category column (zero-indexed)
                "visible": true
            },
            {
                "targets": '_all',
                "orderable": true
            }
        ],
        "stateSave": true, // Save table state across page reloads
        "stateDuration": 60 * 60 * 24, // Save state for 24 hours
        "dom": 'lrtip', // Custom layout: l - length changing, r - processing, t - table, i - information, p - pagination
        "language": {
            "emptyTable": "No borrow requests found",
            "zeroRecords": "No matching borrow requests"
        },
        "drawCallback": function() {
            // Style pagination numbers
            $('.paginate_button').addClass('text-red-800');
            $('.paginate_button.current').addClass('bg-red-800 text-white font-semibold');
            $('.paginate_button.disabled').addClass('text-gray-400');
        }
    });

    // Preserve column order and visibility
    table.on('column-reorder', function(e, settings, details) {
        table.state.save();
    });

    // Handle global search from top navigation
    $('#custom-search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Handle category filter from top navigation
    $('#category-filter').on('change', function() {
        var selectedCategory = $(this).val();
        
        // If 'choose' or empty, reset the filter
        if (selectedCategory === 'choose' || selectedCategory === '') {
            table.column(9).search('').draw();
        } else {
            // Apply category filter
            table.column(9).search(selectedCategory).draw();
        }
    });

    // Handle approve button click
    $('#borrowRequestsTable').on('click', '.approve-btn', function() {
        var requestId = $(this).data('request-id');
        $.ajax({
            url: '../admin/request.php',
            type: 'POST',
            dataType: 'json',
            data: { approve_borrow_request: true, request_id: requestId },
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload(); // Reload the page to refresh the table
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('An error occurred while processing the request.');
            }
        });
    });

    // Handle reject button click
    $('#borrowRequestsTable').on('click', '.reject-btn', function() {
        var requestId = $(this).data('request-id');
        if (confirm('Are you sure you want to reject this request?')) {
            $.ajax({
                url: '../admin/request.php',
                type: 'POST',
                dataType: 'json',
                data: { reject_borrow_request: true, request_id: requestId },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to refresh the table
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('An error occurred while processing the request.');
                }
            });
        }
    });

    // Mobile menu toggle
    $('#menuToggle').on('click', function() {
        $('.sidebar').toggleClass('-translate-x-full');
    });

    // Responsive adjustments
    function handleResize() {
        if (window.innerWidth <= 768) {
            $('.sidebar').addClass('-translate-x-full');
            $('#menuToggle').removeClass('hidden');
            $('.container').removeClass('ml-64 w-[calc(100%-16rem)]').addClass('ml-0 w-full');
        } else {
            $('.sidebar').removeClass('-translate-x-full');
            $('#menuToggle').addClass('hidden');
            $('.container').addClass('ml-64 w-[calc(100%-16rem)]').removeClass('ml-0 w-full');
        }
    }

    // Initial check and event listener
    handleResize();
    window.addEventListener('resize', handleResize);
});