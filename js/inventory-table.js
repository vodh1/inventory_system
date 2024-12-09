document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-inventory');
    const categoryFilter = document.getElementById('category-filter');
    const sortBy = document.getElementById('sort-by');
    const table = document.getElementById('table-equipment');
    const tbody = table.querySelector('tbody');

    // Populate category filter
    const categories = new Set();
    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        const category = row.children[3].textContent.trim();
        categories.add(category);
    });
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category;
        option.textContent = category;
        categoryFilter.appendChild(option);
    });

    // Search functionality
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        const rows = tbody.querySelectorAll('tr');

        rows.forEach(row => {
            const name = row.children[2].textContent.toLowerCase();
            const category = row.children[3].textContent.toLowerCase();
            const description = row.children[4].textContent.toLowerCase();

            const matchesSearch = name.includes(searchTerm) || 
                                description.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;

            row.style.display = matchesSearch && matchesCategory ? '' : 'none';
        });
    }

    // Sorting functionality
    function sortTable(column) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const sortOrder = sortBy.value;

        rows.sort((a, b) => {
            let aValue, bValue;

            switch(sortOrder) {
                case 'name':
                    aValue = a.children[2].textContent.toLowerCase();
                    bValue = b.children[2].textContent.toLowerCase();
                    break;
                case 'category':
                    aValue = a.children[3].textContent.toLowerCase();
                    bValue = b.children[3].textContent.toLowerCase();
                    break;
                case 'units':
                    aValue = parseInt(a.children[5].textContent);
                    bValue = parseInt(b.children[5].textContent);
                    break;
                case 'updated':
                    aValue = new Date(a.children[7].textContent);
                    bValue = new Date(b.children[7].textContent);
                    break;
                default:
                    return 0;
            }

            if (aValue < bValue) return -1;
            if (aValue > bValue) return 1;
            return 0;
        });

        // Clear and re-append rows
        rows.forEach(row => tbody.appendChild(row));
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
    sortBy.addEventListener('change', () => sortTable(sortBy.value));

    // Initialize tooltips for low stock items
    const lowStockItems = document.querySelectorAll('[data-low-stock="true"]');
    lowStockItems.forEach(item => {
        tippy(item, {
            content: 'Low stock!',
            theme: 'red'
        });
    });

    // Modal elements
    const addModal = document.getElementById('add-equipment-modal');
    const editModal = document.getElementById('edit-equipment-modal');
    const addButton = document.getElementById('add-equipment');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    // Form elements
    const addForm = document.getElementById('add-equipment-form');
    const editForm = document.getElementById('edit-equipment-form');
    
    // Image upload elements
    const addImageInput = document.getElementById('equipment-image');
    const editImageInput = document.getElementById('edit-equipment-image');
    const addImagePreview = document.getElementById('preview-image');
    const editImagePreview = document.getElementById('edit-preview-image');
    const addUploadTrigger = document.getElementById('upload-trigger');
    const editUploadTrigger = document.getElementById('edit-upload-trigger');
    
    // Modal Control Functions
    function openModal(modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset forms when closing
        if (modal === addModal) {
            addForm.reset();
            addImagePreview.classList.add('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        } else if (modal === editModal) {
            editForm.reset();
            editImagePreview.classList.add('hidden');
            document.getElementById('edit-upload-placeholder').classList.remove('hidden');
        }
    }
    
    // Event Listeners for Modal Controls
    addButton.addEventListener('click', () => openModal(addModal));
    
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('#add-equipment-modal, #edit-equipment-modal');
            closeModal(modal);
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === addModal || e.target === editModal) {
            closeModal(e.target);
        }
    });
    
    // Image Upload Preview Functions
    function handleImageUpload(input, preview, placeholder) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Image Upload Event Listeners
    addUploadTrigger.addEventListener('click', () => addImageInput.click());
    editUploadTrigger.addEventListener('click', () => editImageInput.click());
    
    addImageInput.addEventListener('change', () => {
        handleImageUpload(
            addImageInput,
            addImagePreview,
            document.getElementById('upload-placeholder')
        );
    });
    
    editImageInput.addEventListener('change', () => {
        handleImageUpload(
            editImageInput,
            editImagePreview,
            document.getElementById('edit-upload-placeholder')
        );
    });
    
    // Form Submission Handlers
    addForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const formData = new FormData(addForm);
            
            const response = await fetch('/inventory/add_equipment.php', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Refresh the table or add new row
                    location.reload();
                } else {
                    alert(result.message || 'Failed to add equipment');
                }
            } else {
                throw new Error('Network response was not ok');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding equipment');
        }
    });
    
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const formData = new FormData(editForm);
            
            const response = await fetch('/inventory/update_equipment.php', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Refresh the table
                    location.reload();
                } else {
                    alert(result.message || 'Failed to update equipment');
                }
            } else {
                throw new Error('Network response was not ok');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating equipment');
        }
    });
    
    // Edit Button Click Handler
    document.querySelectorAll('.edit-equipment').forEach(button => {
        button.addEventListener('click', async () => {
            const equipmentId = button.dataset.id;
            
            try {
                const response = await fetch(`/inventory/get_equipment.php?id=${equipmentId}`);
                if (response.ok) {
                    const equipment = await response.json();
                    
                    // Populate edit form
                    document.getElementById('edit-equipment-id').value = equipment.id;
                    document.getElementById('edit-equipment-name').value = equipment.name;
                    document.getElementById('edit-equipment-category').value = equipment.category_id;
                    document.getElementById('edit-equipment-description').value = equipment.description;
                    document.getElementById('edit-equipment-units').value = equipment.total_units;
                    document.getElementById('edit-equipment-available').value = equipment.available_units;
                    
                    // Show image preview if exists
                    if (equipment.image_path) {
                        editImagePreview.src = equipment.image_path;
                        editImagePreview.classList.remove('hidden');
                        document.getElementById('edit-upload-placeholder').classList.add('hidden');
                    }
                    
                    openModal(editModal);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load equipment details');
            }
        });
    });
    
    // Delete Button Click Handler
    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', async () => {
            const equipmentId = button.dataset.id;
            const equipmentName = button.dataset.name;
            
            if (confirm(`Are you sure you want to delete ${equipmentName}?`)) {
                try {
                    const response = await fetch('/inventory/delete_equipment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: equipmentId })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            // Remove the row from the table
                            button.closest('tr').remove();
                        } else {
                            alert(result.message || 'Failed to delete equipment');
                        }
                    } else {
                        throw new Error('Network response was not ok');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while deleting equipment');
                }
            }
        });
    });
});
