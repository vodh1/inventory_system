<div class="p-8 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-2">Inventory Management</h2>
            <p class="text-gray-600">Manage and track all inventory items</p>
        </div>
        <button type="button" id="add-equipment" class="px-6 py-2.5 bg-red-800 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add New Item
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table id="table-equipment" class="w-full">
                <thead>
                    <tr class="text-left text-gray-600 text-sm">
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Available</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Units</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($equipment_list as $equipment):
                    ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4"><?= $i ?></td>
                            <td class="px-6 py-4">
                                <img src="<?= htmlspecialchars($equipment['image_path'] ?? '../uploads/equipment/default_image_equipment.png')  ?>"
                                    alt="<?= htmlspecialchars($equipment['name']) ?>"
                                    class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($equipment['name']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                                    <?= htmlspecialchars($equipment['category_name']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($equipment['description']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-medium <?= $equipment['available_units'] < 5 ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= htmlspecialchars($equipment['available_units']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-medium"><?= htmlspecialchars($equipment['total_units']) ?></td>
                            <td class="px-6 py-4 text-center text-gray-600"><?= htmlspecialchars($equipment['created_at']) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button class="px-3 py-1.5 text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200 edit-equipment"
                                        data-id="<?= $equipment['id'] ?>">
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Edit
                                        </span>
                                    </button>
                                    <button class="px-3 py-1.5 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors duration-200 deleteBtn"
                                        data-id="<?= $equipment['id'] ?>"
                                        data-name="<?= htmlspecialchars($equipment['name']) ?>">
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Delete
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php
                        $i++;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>