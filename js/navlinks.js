$(document).ready(function() {
    // Handle sidebar link clicks
    $('#sidebar a[href]').on('click', function(e) {
        // Don't prevent default - allow normal navigation
        const url = $(this).attr('href');
        
        // Skip if it's just a "#" link
        if (url === '#') {
            e.preventDefault();
            return;
        }

        // Remove active class from all links
        $('#sidebar .active-nav-item').removeClass('active-nav-item bg-white/20 translate-x-2');
        
        // Add active class to clicked link's parent div
        $(this).closest('div').addClass('active-nav-item bg-white/20 translate-x-2');
    });

    // Toggle sidebar on mobile
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('-translate-x-full');
    });

    // Close sidebar when clicking outside on mobile
    $(document).on('click', function(e) {
        if (window.innerWidth < 768) {  // Only on mobile
            if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
                $('#sidebar').addClass('-translate-x-full');
            }
        }
    });

    // Initialize active state based on current URL
    const currentPath = window.location.pathname;
    $('#sidebar a[href]').each(function() {
        const href = $(this).attr('href');
        if (currentPath.endsWith(href)) {
            $(this).closest('div').addClass('active-nav-item bg-white/20 translate-x-2');
        }
    });

    // Add hover effect for non-active items
    $('#sidebar a[href]').parent('div').hover(
        function() {
            if (!$(this).hasClass('active-nav-item')) {
                $(this).addClass('hover:bg-white/10 hover:translate-x-2');
            }
        },
        function() {
            if (!$(this).hasClass('active-nav-item')) {
                $(this).removeClass('hover:bg-white/10 hover:translate-x-2');
            }
        }
    );
});