<?php
function displayBorrowModal() {
    ?>
    <div id="borrowModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-[1000] opacity-0 transition-opacity duration-300">
        <div class="bg-white w-[95%] max-w-[600px] rounded-xl shadow-lg transform -translate-y-5 transition-transform duration-300">
            <!-- Modal Header -->
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-gray-800 text-xl font-medium">Borrow Equipment</h2>
                <button class="bg-transparent border-none text-2xl text-gray-500 hover:text-gray-800 cursor-pointer flex items-center justify-center p-1" onclick="closeBorrowModal()">
                    <i class='bx bx-x'></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5">
                <form id="borrowForm" action="process_borrow.php" method="POST">
                    <!-- Equipment Preview -->
                    <div class="flex gap-4 p-4 bg-gray-50 rounded-lg mb-5">
                        <img src="" id="previewImage" alt="Equipment Image" class="w-[100px] h-[100px] object-cover rounded-lg">
                        <div class="flex flex-col justify-center">
                            <h3 id="previewName" class="text-gray-800 mb-1"></h3>
                            <p id="previewId" class="text-gray-500 text-sm"></p>
                        </div>
                    </div>

                    <!-- Borrow Form -->
                    <div class="flex flex-col gap-4">
                        <input type="hidden" name="equipment_id" id="equipmentId">
                        
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="borrowDate" class="block font-medium text-gray-800 mb-2">Borrow Date</label>
                                <input type="date" id="borrowDate" name="borrow_date" required 
                                    class="w-full p-2.5 border border-gray-200 rounded-lg text-base">
                            </div>
                            <div class="flex-1">
                                <label for="returnDate" class="block font-medium text-gray-800 mb-2">Return Date</label>
                                <input type="date" id="returnDate" name="return_date" required 
                                    class="w-full p-2.5 border border-gray-200 rounded-lg text-base">
                            </div>
                        </div>

                        <div>
                            <label for="unit" class="block font-medium text-gray-800 mb-2">Unit Number</label>
                            <select name="unit" id="unit" required 
                                class="w-full p-2.5 border border-gray-200 rounded-lg text-base">
                            </select>
                        </div>

                        <div>
                            <label for="purpose" class="block font-medium text-gray-800 mb-2">Purpose of Borrowing</label>
                            <textarea id="purpose" name="purpose" required 
                                class="w-full p-2.5 border border-gray-200 rounded-lg text-base resize-y"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="p-5 border-t border-gray-200 flex justify-end gap-3">
                <button onclick="closeBorrowModal()" 
                    class="px-5 py-2.5 bg-gray-50 text-gray-800 rounded-lg text-base hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <button onclick="submitBorrowForm()" 
                    class="px-5 py-2.5 bg-[#b90303] hover:bg-[#a00202] text-white rounded-lg text-base transition-colors">
                    Submit Request
                </button>
            </div>
        </div>
    </div>

    <script>
    function showBorrowModal(equipmentId) {
        // Show loading state
        document.getElementById('borrowModal').style.display = 'flex';
        document.getElementById('previewName').textContent = 'Loading...';
        
        // Fetch equipment details
        fetch('get_equipment.php?id=' + equipmentId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Update modal with equipment details
                document.getElementById('equipmentId').value = data.id;
                document.getElementById('previewImage').src = data.image_path;
                document.getElementById('previewName').textContent = data.name;
                document.getElementById('previewId').textContent = 'ID: ' + data.id;
                
                // Set date constraints
                const today = new Date().toISOString().split('T')[0];
                const maxDate = new Date();
                maxDate.setDate(maxDate.getDate() + data.max_borrow_days);
                
                const borrowDateInput = document.getElementById('borrowDate');
                const returnDateInput = document.getElementById('returnDate');
                
                borrowDateInput.min = today;
                borrowDateInput.max = maxDate.toISOString().split('T')[0];
                returnDateInput.min = today;
                returnDateInput.max = maxDate.toISOString().split('T')[0];
                
                // Populate unit numbers
                const unitSelect = document.getElementById('unit');
                unitSelect.innerHTML = ''; // Clear previous options
                data.units.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit;
                    option.textContent = unit;
                    unitSelect.appendChild(option);
                });
                
                // Show modal with animation
                setTimeout(() => {
                    const modal = document.getElementById('borrowModal');
                    modal.classList.add('opacity-100');
                    modal.querySelector('.transform').classList.remove('-translate-y-5');
                }, 10);
            })
            .catch(error => {
                alert('Error loading equipment details: ' + error.message);
                closeBorrowModal();
            });
    }

    function closeBorrowModal() {
        const modal = document.getElementById('borrowModal');
        modal.classList.remove('opacity-100');
        modal.querySelector('.transform').classList.add('-translate-y-5');
        setTimeout(() => {
            modal.style.display = 'none';
            // Reset form
            document.getElementById('borrowForm').reset();
        }, 300);
    }

    function submitBorrowForm() {
        const form = document.getElementById('borrowForm');
        
        // Basic validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Additional validation
        const borrowDate = new Date(document.getElementById('borrowDate').value);
        const returnDate = new Date(document.getElementById('returnDate').value);
        
        if (returnDate < borrowDate) {
            alert('Return date cannot be earlier than borrow date');
            return;
        }

        // Submit form via AJAX
        const formData = new FormData(form);
        fetch('process_borrow.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                // Refresh the page or update the UI dynamically
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while processing the request.');
        });
    }

    document.getElementById('borrowDate').addEventListener('change', function() {
        const returnDateInput = document.getElementById('returnDate');
        returnDateInput.min = this.value;
    });
    </script>
    <?php
}
?>