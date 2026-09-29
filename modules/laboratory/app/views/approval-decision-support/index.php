<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/sidebar.php'; ?>

<?php require __DIR__ . '/main-content.php'; ?>

<script>
    document.getElementById("department-filter").addEventListener("change", function() {
        const dept = this.value;
        let url = "approval-decision-support";

        if (dept) {
            url += "?department=" + dept;
        }

        window.location.href = url;
    });
    document.querySelectorAll('.btn-decision').forEach(btn => {
        btn.addEventListener('click', function() {
            const approvalId = this.dataset.id;
            const action = this.dataset.action;

            document.getElementById('modalApprovalId').value = approvalId;
            document.getElementById('modalAction').value = action;

            document.getElementById('approvalDecisionLabel').textContent =
                action.charAt(0).toUpperCase() + action.slice(1) + " Remarks";

            new bootstrap.Modal(document.getElementById('approvalDecisionModal')).show();
        });
    });
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>