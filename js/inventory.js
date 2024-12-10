$(document).ready(function () {
	// Initialize Bootstrap modals
	const addModal = new bootstrap.Modal(
		document.getElementById('addEquipmentModal')
	);
	const editModal = new bootstrap.Modal(
		document.getElementById('editEquipmentModal')
	);

	// Initialize DataTable
	var equipmentTable = $('#table-equipment').DataTable({
		columnDefs: [
			{
				className:
					'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
				targets: '_all'
			},
			{ orderable: false, targets: [1, 8] } // Disable sorting for image and actions columns
		],
		autoWidth: false,
		pageLength: 10,
		lengthChange: false,
		dom: 'frtip',
		language: {
			info: '<span class="text-gray-600">Showing</span> _START_ <span class="text-gray-600">to</span> _END_ <span class="text-gray-600">of</span> _TOTAL_ <span class="text-gray-600">entries</span>',
			search: '<span class="text-gray-600">Search:</span> _INPUT_',
			paginate: {
				previous: 'Previous',
				next: 'Next'
			},
			emptyTable:
				'<div class="text-center p-4 text-gray-500">No equipment found</div>',
			zeroRecords:
				'<div class="text-center p-4 text-gray-500">No matching records found</div>'
		},
		drawCallback: function (settings) {
			// Style the pagination controls
			$('.dataTables_paginate').addClass(
				'flex items-center justify-center gap-1 mt-4'
			);

			// Style the page numbers and navigation buttons
			$('.paginate_button').each(function () {
				$(this).addClass(
					'px-3 py-1 min-w-[40px] text-center transition-colors duration-200'
				);

				if ($(this).hasClass('previous') || $(this).hasClass('next')) {
					$(this).addClass(
						'bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-md'
					);
				} else {
					$(this).addClass('hover:bg-blue-50 rounded-md');
				}
			});

			// Style the current/active page
			$('.paginate_button.current').addClass(
				'!bg-blue-500 !text-white hover:!bg-blue-600'
			);

			// Style disabled buttons
			$('.paginate_button.disabled').addClass(
				'opacity-50 cursor-not-allowed hover:bg-gray-100'
			);

			// Style the length menu and search input
			$('.dataTables_filter input').addClass('rounded-md border-gray-200 ml-2');

			// Make info text responsive
			$('.dataTables_info').addClass(
				'text-sm text-gray-600 text-center md:text-left mt-4 md:mt-0'
			);

			// Add responsive wrapper
			if (!$('.dataTables_wrapper').hasClass('responsive-wrapper')) {
				$('.dataTables_wrapper').addClass('responsive-wrapper px-4');
			}
		},
		order: [[0, 'asc']], // Sort by first column (No.) by default
		drawCallback: function () {
			// Style pagination controls
			$('.paginate_button').addClass(
				'px-3 py-2 border border-gray-200 hover:bg-red-800 hover:text-white hover:border-red-800 rounded mx-1'
			);
			$('.paginate_button.current')
				.addClass('bg-red-800 text-white border-red-800')
				.removeClass('hover:bg-red-800');
			$('.paginate_button.disabled').addClass(
				'opacity-50 cursor-not-allowed hover:bg-transparent hover:text-gray-500'
			);

			// Add transition effects
			$('.paginate_button').addClass('transition-all duration-200');

			// Attach event listeners
			attachEventListeners();
		}
	});

	// Define the attachEventListeners function
	function attachEventListeners() {
		// Add Item button click handler
		$('#add-equipment')
			.off('click')
			.on('click', function (e) {
				e.preventDefault();
				$('#form-add-equipment')[0].reset(); // Reset form
				$('.is-invalid').removeClass('is-invalid');
				$('.invalid-feedback').empty();
				fetchCategories();
				addModal.show();
			});

		// Form submission handler for adding equipment
		$('#form-add-equipment')
			.off('submit')
			.on('submit', function (e) {
				e.preventDefault();
				var formData = new FormData(this);

				$.ajax({
					type: 'POST',
					url: '../inventory/add_equipment.php',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function (response) {
						if (response.status === 'success') {
							addModal.hide();
							showNotification(response.message, 'success');
							location.reload();
						} else {
							handleValidationErrors(response.errors, 'add');
						}
					},
					error: function () {
						showNotification('Failed to add equipment', 'danger');
					}
				});
			});

		// Form submission handler for updating equipment
		$('#form-edit-equipment')
			.off('submit')
			.on('submit', function (e) {
				e.preventDefault();
				console.log('submitting');
				updateEquipment();
			});

		// Event handler for edit button
		$(document)
			.off('click', '.edit-equipment')
			.on('click', '.edit-equipment', function (e) {
				e.preventDefault();
				var equipmentId = $(this).data('id');
				$('#form-edit-equipment')[0].reset();
				$('.is-invalid').removeClass('is-invalid');
				$('.invalid-feedback').empty();
				fetchEquipmentDetails(equipmentId);
			});

		// Event handler for delete button
		$(document)
			.off('click', '.deleteBtn')
			.on('click', '.deleteBtn', function (e) {
				e.preventDefault();
				var equipmentId = $(this).data('id');
				var equipmentName = $(this).data('name');

				if (
					confirm(
						'Are you sure you want to delete the equipment: ' +
							equipmentName +
							'?'
					)
				) {
					deleteEquipment(equipmentId);
				}
			});

		// Handle category filter change
		$('#category-filter')
			.off('change')
			.on('change', function () {
				var categoryId = $(this).val();
				var table = $('#table-equipment').DataTable();

				if (categoryId === '') {
					table.column(3).search('').draw(); // Clear the search if "All" is selected
				} else {
					var categoryName = $(this).find('option:selected').text();
					table.column(3).search(categoryName).draw(); // Search by category name
				}
			});

		// Custom search handler
		$('#custom-search')
			.off('keyup')
			.on('keyup', function () {
				equipmentTable.search(this.value).draw();
			});
	}

	// Function to fetch equipment details and populate the edit modal
	function fetchEquipmentDetails(equipmentId) {
		$.ajax({
			type: 'GET',
			url: '../inventory/get_equipment.php',
			data: { id: equipmentId },
			dataType: 'json',
			success: function (equipment) {
				populateEditModal(equipment);
				editModal.show();
			},
			error: function () {
				showNotification('Failed to fetch equipment details', 'danger');
			}
		});
	}

	// Function to populate the edit modal with equipment details
	function populateEditModal(equipment) {
		$('#edit-equipment-id').val(equipment.id);
		$('#edit-name').val(equipment.name);
		$('#edit-description').val(equipment.description);
		$('#edit-max_borrow_days').val(equipment.max_borrow_days);
		$('#edit-units').val(equipment.units.length); // Ensure units are populated

		// Populate categories dropdown
		fetchCategoriesForEdit(equipment.category_id);
	}

	// Function to fetch categories for the edit modal
	function fetchCategoriesForEdit(selectedCategoryId) {
		$.ajax({
			type: 'GET',
			url: '../inventory/fetch_category.php',
			dataType: 'json',
			success: function (categories) {
				var select = $('#edit-category');
				select.empty();
				select.append('<option value="">--Select--</option>');
				categories.forEach(function (category) {
					var selected = category.id == selectedCategoryId ? 'selected' : '';
					select.append(
						`<option value="${category.id}" ${selected}>${category.name}</option>`
					);
				});
			},
			error: function () {
				showNotification('Failed to fetch categories', 'danger');
			}
		});
	}

	// Function to update equipment details
	function updateEquipment() {
		var formData = new FormData($('#form-edit-equipment')[0]);

		$.ajax({
			type: 'POST',
			url: '../inventory/update_equipment.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success') {
					editModal.hide();
					showNotification(response.message, 'success');
					location.reload();
				} else {
					handleValidationErrors(response.errors, 'edit');
				}
			},
			error: function () {
				showNotification('Failed to update equipment', 'danger');
			}
		});
	}

	function fetchCategories() {
		$.ajax({
			type: 'GET',
			url: '../inventory/fetch_category.php',
			dataType: 'json',
			success: function (categories) {
				var select = $('#category');
				select.empty();
				select.append('<option value="">--Select--</option>');
				categories.forEach(function (category) {
					select.append(
						`<option value="${category.id}">${category.name}</option>`
					);
				});
			},
			error: function () {
				showNotification('Failed to fetch categories', 'danger');
			}
		});
	}

	function saveEquipment() {
		var formData = new FormData($('#form-add-equipment')[0]);
		$.ajax({
			type: 'POST',
			url: '../inventory/add_equipment.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success') {
					$('#addEquipmentModal').modal('hide');
					$('#form-add-equipment')[0].reset();
					showNotification(response.message, 'success');
					addEquipmentToTable(response.equipment); // Add the new equipment to the table
				} else {
					handleValidationErrors(response, 'add');
				}
			},
			error: function () {
				showNotification('Failed to save equipment', 'danger');
			}
		});
	}

	function handleValidationErrors(response, formType) {
		console.log(response);
		// Clear previous errors
		$('.is-invalid').removeClass('is-invalid');
		$('.invalid-feedback').empty();

		// Handle specific field errors
		const fields = [
			'name',
			'description',
			'category',
			'max_borrow_days',
			'units',
			'image'
		];
		const prefix = formType === 'edit' ? '#edit-' : '#';

		fields.forEach((field) => {
			if (response[field]) {
				$(prefix + field).addClass('is-invalid');
				$(prefix + field)
					.next('.invalid-feedback')
					.text(response[field]);
			}
		});
	}

	function deleteEquipment(equipmentId) {
		$.ajax({
			type: 'POST',
			url: '../inventory/delete_equipment.php',
			data: { equipment_id: equipmentId },
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success') {
					// Remove the deleted equipment row from the table
					removeEquipmentFromTable(equipmentId);
					alert(response.message);
				} else {
					alert(response.message);
				}
			},
			error: function () {
				alert('An error occurred while deleting the equipment.');
			}
		});
	}

	function addEquipmentToTable(equipment) {
		// Remove existing row if it exists (for updates)
		equipmentTable.rows().every(function () {
			if (this.data()[0] === equipment.id) {
				this.remove();
			}
		});

		// Add new row
		equipmentTable.row
			.add([
				equipmentTable.data().length + 1,
				'<img src="' +
					equipment.image_path +
					'" alt="' +
					equipment.name +
					'" class="w-16 h-16 object-cover rounded-lg border border-gray-200">',
				equipment.name,
				'<span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">' +
					equipment.category_name +
					'</span>',
				equipment.description,
				'<span class="font-medium ' +
					(parseInt(equipment.available_units) < 5
						? 'text-red-600'
						: 'text-green-600') +
					'">' +
					equipment.available_units +
					'</span>',
				equipment.total_units,
				equipment.created_at,
				`<div class="flex gap-2">
                <button class="px-3 py-1 text-blue-600 border border-blue-600 rounded hover:bg-blue-50 edit-equipment" 
                        data-id="${equipment.id}">
                    <span class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit
                    </span>
                </button>
                <button class="px-3 py-1 text-red-600 border border-red-600 rounded hover:bg-red-50 deleteBtn" 
                        data-id="${equipment.id}" 
                        data-name="${equipment.name}">
                    <span class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete
                    </span>
                </button>
            </div>`
			])
			.draw();

		// Update row numbers
		updateRowNumbers();
	}

	function updateRowNumbers() {
		equipmentTable.rows().every(function (index) {
			var data = this.data();
			data[0] = index + 1;
			this.data(data);
		});
		equipmentTable.draw(false);
	}

	function removeEquipmentFromTable(equipmentId) {
		equipmentTable.rows().every(function () {
			if ($(this.node()).find('.deleteBtn').data('id') === equipmentId) {
				this.remove();
			}
		});
		equipmentTable.draw();
		updateRowNumbers();
	}

	function showNotification(message, type) {
		// Create notification element if it doesn't exist
		if (!$('#notification').length) {
			$('body').append(`
                <div id="notification" class="alert alert-dismissible fade show fixed-top mx-auto mt-3" 
                     style="width: fit-content; display: none; z-index: 9999;">
                    <span></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
		}

		const notification = $('#notification');
		notification
			.removeClass('alert-success alert-danger')
			.addClass(`alert-${type}`)
			.find('span')
			.text(message);

		notification.show();
		setTimeout(function () {
			notification.fadeOut();
		}, 5000);
	}

	// Attach event listeners initially
	attachEventListeners();
});
