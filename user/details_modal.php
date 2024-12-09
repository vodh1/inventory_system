<?php
function displayDetailsModal() {
?>
<div class="fixed inset-0 bg-black/50 hidden justify-center items-center z-[1000] opacity-0 transition-all duration-300 ease-in-out" 
     id="detailsModal">
    <div class="bg-white w-[95%] max-w-[800px] rounded-xl shadow-lg transform -translate-y-5 transition-transform duration-300 ease-in-out max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="p-5 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-gray-800 text-xl font-medium">Equipment Details</h2>
            <button class="text-gray-500 hover:text-gray-700 text-2xl p-1 flex items-center justify-center transition-colors duration-300"
                    onclick="closeDetailsModal()">
                <i class='bx bx-x'></i>
            </button>
        </div>
        
        <div class="p-5">
            <!-- Equipment Preview -->
            <div class="flex gap-5 p-5 bg-gray-50 rounded-xl mb-6 sm:flex-row flex-col sm:items-start items-center">
                <img src="" alt="Equipment" id="modalEquipmentImage"
                     class="w-[120px] h-[120px] object-cover rounded-lg sm:w-[120px] sm:h-[120px] w-[150px] h-[150px]">
                <div class="flex flex-col justify-center sm:text-left text-center">
                    <h3 class="text-gray-800 text-2xl mb-2" id="modalEquipmentName"></h3>
                    <p class="text-sm text-gray-600" id="modalEquipmentId"></p>
                </div>
            </div>

            <!-- Availability Status -->
            <div class="mb-8">
                <h4 class="text-gray-800 text-lg font-medium mb-4">Availability Status</h4>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex justify-around mb-5 pb-5 border-b border-gray-200 sm:flex-row flex-col gap-5">
                        <div class="flex sm:flex-col flex-row sm:items-center justify-between items-center gap-2 sm:w-auto w-full sm:p-0 p-5 sm:bg-transparent bg-gray-50 rounded-lg">
                            <span class="text-green-600 sm:text-3xl text-2xl font-semibold" id="availableCount">0</span>
                            <span class="text-gray-500 text-sm font-medium">Available</span>
                        </div>
                        <div class="flex sm:flex-col flex-row sm:items-center justify-between items-center gap-2 sm:w-auto w-full sm:p-0 p-5 sm:bg-transparent bg-gray-50 rounded-lg">
                            <span class="text-blue-600 sm:text-3xl text-2xl font-semibold" id="borrowedCount">0</span>
                            <span class="text-gray-500 text-sm font-medium">In Use</span>
                        </div>
                        <div class="flex sm:flex-col flex-row sm:items-center justify-between items-center gap-2 sm:w-auto w-full sm:p-0 p-5 sm:bg-transparent bg-gray-50 rounded-lg">
                            <span class="text-orange-500 sm:text-3xl text-2xl font-semibold" id="pendingCount">0</span>
                            <span class="text-gray-500 text-sm font-medium">Pending Requests</span>
                        </div>
                    </div>
                    <p class="text-center text-gray-700 font-medium text-base" id="totalUnits">Total Units: 0</p>
                </div>
            </div>

            <!-- Unit Details Table -->
            <div class="mb-8">
                <h4 class="text-gray-800 text-lg font-medium mb-4">Unit Details</h4>
                <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 text-left text-gray-700 font-medium text-sm">Unit Number</th>
                                <th class="p-4 text-left text-gray-700 font-medium text-sm">Status</th>
                                <th class="p-4 text-left text-gray-700 font-medium text-sm">Current/Last Borrower</th>
                                <th class="p-4 text-left text-gray-700 font-medium text-sm">Date Borrowed</th>
                                <th class="p-4 text-left text-gray-700 font-medium text-sm">Expected Return</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailsBody">
                            <!-- Units will be populated here via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Equipment Guidelines -->
            <div>
                <h4 class="text-gray-800 text-lg font-medium mb-4">Equipment Guidelines</h4>
                <ul class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-gray-700 text-sm">
                        <i class='bx bx-check-circle text-blue-600 text-xl flex-shrink-0'></i>
                        Return equipment in the same condition
                    </li>
                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-gray-700 text-sm">
                        <i class='bx bx-check-circle text-blue-600 text-xl flex-shrink-0'></i>
                        Report any issues immediately
                    </li>
                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-gray-700 text-sm">
                        <i class='bx bx-check-circle text-blue-600 text-xl flex-shrink-0'></i>
                        Follow proper operating procedures
                    </li>
                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-gray-700 text-sm">
                        <i class='bx bx-check-circle text-blue-600 text-xl flex-shrink-0'></i>
                        Clean after each use
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function showDetailsModal(equipmentId) {
    const modal = document.getElementById('detailsModal');
    modal.style.display = 'flex';
    
    fetch(`../user/get_equipment_details.php?id=${equipmentId}`)
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
            document.getElementById('modalEquipmentName').textContent = data.equipment.name;
            document.getElementById('modalEquipmentId').textContent = `ID: ${data.equipment.id}`;
            document.getElementById('modalEquipmentImage').src = data.equipment.image_path;
            
            document.getElementById('availableCount').textContent = data.counts.available;
            document.getElementById('borrowedCount').textContent = data.counts.borrowed;
            document.getElementById('pendingCount').textContent = data.counts.pending;
            document.getElementById('totalUnits').textContent = 
                `Total Units: ${data.counts.available + data.counts.borrowed + data.counts.maintenance + data.counts.pending}`;
            
            const tbody = document.getElementById('unitDetailsBody');
            tbody.innerHTML = '';
            
            data.units.forEach(unit => {
                const statusClasses = {
                    'available': 'bg-green-100 text-green-600',
                    'borrowed': 'bg-red-100 text-red-600',
                    'maintenance': 'bg-yellow-100 text-yellow-600',
                    'pending': 'bg-orange-100 text-orange-600'
                };
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="p-4 text-gray-500 text-sm border-b border-gray-200">${unit.unit_code}</td>
                    <td class="p-4 text-gray-500 text-sm border-b border-gray-200">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClasses[unit.status.toLowerCase()]}">
                            ${unit.status}
                        </span>
                    </td>
                    <td class="p-4 text-gray-500 text-sm border-b border-gray-200">${unit.borrower || '-'}</td>
                    <td class="p-4 text-gray-500 text-sm border-b border-gray-200">${unit.date_borrowed || '-'}</td>
                    <td class="p-4 text-gray-500 text-sm border-b border-gray-200">${unit.expected_return || '-'}</td>
                `;
                tbody.appendChild(tr);
            });
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                modal.querySelector('.bg-white').classList.remove('-translate-y-5');
            }, 10);
        })
        .catch(error => {
            console.error('Error fetching equipment details:', error);
            alert('Error loading equipment details. Please try again.');
        });
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.bg-white').classList.add('-translate-y-5');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}
</script>
<?php
}
?>