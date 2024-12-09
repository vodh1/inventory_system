<div class="modal fade" id="addEquipmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEquipmentModalLabel">Add Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-add-equipment" method="post" action="add_equipment.php" enctype="multipart/form-data">
                    <input type="hidden" name="add_equipment" value="1">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category_id">
                                <option value="">--Select--</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2">
                            <label for="max_borrow_days" class="form-label">Maximum Borrow Days</label>
                            <input type="number" class="form-control" id="max_borrow_days" name="max_borrow_days">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2">
                            <label for="units" class="form-label">Number of Units</label>
                            <input type="number" class="form-control" id="units" name="units">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2">
                            <label for="image" class="form-label">Equipment Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary brand-bg white" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger brand-bg-red">Save Equipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
