<?php include  __DIR__ .'/../../includes/sidebar.php'; ?>
<?php include  __DIR__ .'/../../includes/header.php'; ?>

<link rel="stylesheet" href="/SchoolManagementSystem/assets/css/style.css">


<main class="main-content">
    <div class="container-fluid px-4">
        <h1 class="h3 mb-2 text-gray-800">Damages</h1>
        <p class="mb-4">Psychology Laboratory</p>

        <div class="card mb-4 card shadow-sm border-0 border-top border-4 border-secondary shadow-lg p-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Barrow Equipment
                </div>

                <a class="btn btn-primary btn-sm" id="psychoAddDamageBtn">
                    <i class="fas fa-plus me-1"></i> Create New
                </a>
            </div>
            <div class="card-body">
                <table id="labEquipmentTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php  foreach ($users as $user) { ?>
                        <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= $user['category'] ?> </td>
                                    <td><?= $user['code'] ?></td>
                            <td>
                                    <span class="badge <?= $user['status'] === 'Fixed' ? 'bg-success' : 'bg-danger' ?>"><?= $user['status'] ?></span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item viewPsychoDamageBtn"  data-id="<?= $user['id'] ?>"><i class="fas fa-eye me-2"></i>View</button></li>
                                        <button class="dropdown-item editPsychoDamageBtn" data-id="<?= $user['id'] ?>">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </button>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger deleteBtn" data-id="<?= $user['id'] ?>"><i class="fas fa-trash me-2"></i>Delete</a></li>
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

<!-- Add New Damage Modal -->
<?php require __DIR__ . '/psychoAdd-damage-modal.php'; ?>

<!-- Edit Damage Modal -->
<?php require __DIR__ . '/psychoEdit-damage-modal.php'; ?>

<!-- View Damage Modal -->
<?php require __DIR__ . '/psychoView-damage-modal.php'; ?>
<script>
    $(document).ready(function() {
        $('#labEquipmentTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 20, 30, 40],
        });
    });
</script>

<script> const BASE_URL = "<?= BASE_URL ?>"; </script>
<script src="<?= BASE_URL ?>/js/psychDamage.js"></script>

<?php include  __DIR__ .'/../../includes/footer.php'; ?>