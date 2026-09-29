<!-- ====== SETTINGS ====== -->
<div class="tab-content" id="tab-settings">
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-cog"></i> Library Settings</h3></div>
        <div class="grid-2">
            <div>
                <div class="form-group">
                    <label>Library Name</label>
                    <input type="text" id="settingLibraryName" value="<?= htmlspecialchars($libraryName) ?>">
                </div>
                <div class="form-group">
                    <label>Max Borrow Days</label>
                    <input type="number" id="settingMaxBorrowDays" value="14" min="1" max="365">
                </div>
                <div class="form-group">
                    <label>Max Books per Member</label>
                    <input type="number" id="settingMaxBooks" value="3" min="1" max="20">
                </div>
                <div class="form-group">
                    <label>Daily Fine Rate (₱)</label>
                    <input type="number" id="settingFineRate" value="0.50" min="0" step="0.10">
                </div>
                <button class="btn btn-success" onclick="app.settings.save()">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
            <div>
                <h4 class="mb-1">Database Information</h4>
                <div class="card" style="background:var(--light);box-shadow:none;padding:1.25rem">
                    <p><strong>Host:</strong> <?= htmlspecialchars($dbHost) ?></p>
                    <p><strong>Database:</strong> <?= htmlspecialchars($dbName) ?></p>
                    <p><strong>Charset:</strong> <?= htmlspecialchars($dbCharset) ?></p>
                    <p style="margin:0"><strong>PHP Version:</strong> <?= htmlspecialchars($phpVersion) ?></p>
                </div>
                <h4 class="mt-2 mb-1">Session Info</h4>
                <div class="card" style="background:var(--light);box-shadow:none;padding:1.25rem">
                    <p><strong>Logged in as:</strong> <?= htmlspecialchars($userName ?? 'Librarian') ?></p>
                    <p style="margin:0"><strong>Role:</strong> <?= htmlspecialchars($userRole ?? 'Staff') ?></p>
                </div>
                <div class="d-flex flex-wrap gap-1 mt-2">
                    <a href="auth/logout.php" class="btn btn-sm btn-outline btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
