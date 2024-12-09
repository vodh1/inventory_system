document.addEventListener('DOMContentLoaded', function() {
    // Get current page URL
    const currentPage = window.location.pathname;
    
    // Get all navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Function to update active state
    function updateActiveState() {
        navLinks.forEach(link => {
            // Remove any existing active states
            link.classList.remove('bg-white/10');
            link.classList.add('opacity-80');
            
            // Get the href attribute
            const href = link.getAttribute('href');
            
            // Check if this is the current page
            if (currentPage.includes(href)) {
                // Add active state
                link.classList.add('bg-white/10');
                link.classList.remove('opacity-80');
            }
        });
    }
    
    // Initial update
    updateActiveState();
    
    // Add click handlers
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active state from all links
            navLinks.forEach(l => {
                l.classList.remove('bg-white/10');
                l.classList.add('opacity-80');
            });
            
            // Add active state to clicked link
            this.classList.add('bg-white/10');
            this.classList.remove('opacity-80');
        });
    });
});
