<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="main-content">
    <div class="container-fluid px-4">

        <h1 class="h3 mb-2 text-gray-800">Inventory</h1>
        <p class="mb-4">IT Laboratory</p>

        <div class="card mb-4 card shadow-sm border-0 border-top border-4 border-secondary shadow-lg p-3">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i> Inventory
                </div>

                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#itAddModal">
                    <i class="fas fa-plus me-1"></i> Create New
                </a>
            </div>

            <div class="card-body">

                <table id="itEquipmentTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Category</th>
                            <th>Lab</th>
                            <th>Total</th>
                            <th>Available</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($inventories as $inventory) { ?>

                            <tr>

                                <td><?= $inventory['id'] ?></td>

                                <td>
                                    <img src="<?= BASE_URL ?>/public/<?= $inventory['item_img'] ?>"
                                        class="img-fluid rounded"
                                        style="max-width:100px;">
                                </td>

                                <td><?= $inventory['category'] ?></td>

                                <td><?= $inventory['lab'] ?></td>

                                <td><?= $inventory['total'] ?></td>

                                <td><?= $inventory['available'] ?></td>

                                <td>

                                    <div class="dropdown">

                                        <button class="btn btn-secondary btn-sm dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown">

                                            Action

                                        </button>

                                        <ul class="dropdown-menu">

                                            <li>
                                                <button class="dropdown-item phyViewBtn"
                                                    data-id="<?= $inventory['id'] ?>">
                                                    <i class="fas fa-eye me-2"></i> View
                                                </button>
                                            </li>

                                            <li>
                                                <button class="dropdown-item phyEditBtn"
                                                    data-id="<?= $inventory['id'] ?>">
                                                    <i class="fas fa-edit me-2"></i> Edit
                                                </button>
                                            </li>

                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            <li>
                                                <a class="dropdown-item text-danger deleteBtn"
                                                    data-id="<?= $inventory['id'] ?>">
                                                    <i class="fas fa-trash me-2"></i> Delete
                                                </a>
                                            </li>

                                        </ul>

                                    </div>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/itAdd-inventory-modal.php'; ?>
<?php require __DIR__ . '/itView-inventory-modal.php'; ?>
<?php require __DIR__ . '/itEdit-inventory-modal.php'; ?>

<script>
    $(document).ready(function() {
        $('#itEquipmentTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 20, 30, 40]
        });
    });
</script>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>/js/itInventory.js"></script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>