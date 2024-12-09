<div class="modal fade" id="editEquipmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEquipmentModalLabel">Edit Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-edit-equipment" method="post" action="update_equipment.php" enctype="multipart/form-data">
                <input type="hidden" name="equipment_id" id="edit-equipment-id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="edit-description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit-description" name="description"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="edit-category" class="form-label">Category</label>
                        <select class="form-select" id="edit-category" name="category_id">
                            <option value="">--Select--</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="edit-max_borrow_days" class="form-label">Maximum Borrow Days</label>
                        <input type="number" class="form-control" id="edit-max_borrow_days" name="max_borrow_days">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="edit-units" class="form-label">Number of Units</label>
                        <input type="number" class="form-control" id="edit-units" name="units">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="edit-image" class="form-label">Equipment Image</label>
                        <input type="file" class="form-control" id="edit-image" name="image">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary brand-bg-color">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>