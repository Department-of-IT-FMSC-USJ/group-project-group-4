<?php $title = 'Admin Dashboard - OneID'; ?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<style>
    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .admin-header {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .admin-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111;
        margin: 0;
    }

    .admin-header p {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0.25rem 0 0 0;
    }

    .logout-button {
        padding: 0.5rem 1rem;
        background: #111;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .logout-button:hover {
        background: #000;
    }

    .tabs-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .tabs-nav {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        flex-wrap: wrap;
    }

    .tab-button {
        flex: 1;
        min-width: 150px;
        padding: 1rem;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.15s;
        font-size: 0.875rem;
    }

    .tab-button:hover {
        color: #111;
        background: #f3f4f6;
    }

    .tab-button.active {
        color: #111;
        background: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    thead {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.875rem;
        color: #374151;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    .app-id {
        font-weight: 600;
        color: #111;
    }

    .doc-button {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        background: #111;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: background 0.15s;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        white-space: nowrap;
    }

    .doc-button:hover {
        background: #000;
    }

    .doc-button.secondary {
        background: #6b7280;
    }

    .doc-button.secondary:hover {
        background: #4b5563;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-failed {
        background: #fee2e2;
        color: #991b1b;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state h3 {
        font-size: 1.125rem;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .text-muted {
        color: #6b7280;
    }

    .processed-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #111;
    }

    .processed-checkbox:checked {
        background-color: #111;
    }

    .processed-row {
        text-decoration: line-through;
        opacity: 0.6;
    }

    .no-checkbox {
        text-align: center;
        color: #9ca3af;
        font-size: 0.875rem;
    }
</style>

<main class="page">
    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Welcome, <?= htmlspecialchars($_SESSION['admin_full_name'] ?? $_SESSION['admin_username']) ?></p>
            </div>
            <a href="/admin.php?logout=1" class="logout-button">Logout</a>
        </div>

        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-button active" onclick="switchTab('nic')">
                    NIC Applications (<?= count($nicApplications) ?>)
                </button>
                <button class="tab-button" onclick="switchTab('birth')">
                    Birth Certificates (<?= count($birthCertificates) ?>)
                </button>
                <button class="tab-button" onclick="switchTab('fines')">
                    Fines (<?= count($fines) ?>)
                </button>
            </div>

            <!-- NIC Applications Tab -->
            <div id="nic-tab" class="tab-content active">
                <?php if (empty($nicApplications)): ?>
                    <div class="empty-state">
                        <h3>No NIC Applications</h3>
                        <p>No applications have been submitted yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Processed</th>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Birth Cert No</th>
                                    <th>Contact</th>
                                    <th>Documents</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($nicApplications as $app): ?>
                                    <tr<?= $app['processed_by_admin'] ? ' class="processed-row"' : '' ?>>
                                        <td>
                                            <?php if ($app['payment_status'] === 'Completed'): ?>
                                                <input type="checkbox"
                                                    class="processed-checkbox"
                                                    data-type="nic"
                                                    data-id="<?= htmlspecialchars($app['application_id']) ?>"
                                                    <?= $app['processed_by_admin'] ? 'checked' : '' ?>
                                                    title="Mark as processed">
                                            <?php else: ?>
                                                <span class="no-checkbox">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="app-id">#<?= htmlspecialchars($app['application_id']) ?></span>
                                            <div class="text-xs text-muted"><?= htmlspecialchars($app['application_purpose']) ?></div>
                                        </td>
                                        <td>
                                            <div class="app-id"><?= htmlspecialchars($app['family_name'] . ' ' . $app['name_'] . ' ' . $app['surname']) ?></div>
                                            <div class="text-xs text-muted">DOB: <?= htmlspecialchars(date('d M Y', strtotime($app['date_of_birth']))) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($app['birth_certificate_no']) ?></td>
                                        <td>
                                            <?php if ($app['phone_mobile']): ?>
                                                <div class="text-xs"><?= htmlspecialchars($app['phone_mobile']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($app['email']): ?>
                                                <div class="text-xs text-muted"><?= htmlspecialchars($app['email']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/api/nic_applications.php?pdf=1&id=<?= $app['application_id'] ?>&type=birth_certificate"
                                                target="_blank"
                                                class="doc-button">
                                                Birth Cert
                                            </a>
                                            <a href="/api/nic_applications.php?pdf=1&id=<?= $app['application_id'] ?>&type=police_report"
                                                target="_blank"
                                                class="doc-button secondary">
                                                Police Report
                                            </a>
                                            <?php if ($app['photo_link']): ?>
                                                <a href="<?= htmlspecialchars($app['photo_link']) ?>"
                                                    target="_blank"
                                                    class="doc-button">
                                                    Photo
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($app['payment_status']): ?>
                                                <span class="status-badge status-<?= strtolower($app['payment_status']) ?>">
                                                    <?= htmlspecialchars($app['payment_status']) ?>
                                                </span>
                                                <?php if ($app['payment_amount']): ?>
                                                    <div class="text-xs text-muted">LKR <?= number_format($app['payment_amount'], 2) ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="status-badge status-pending">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="text-xs"><?= htmlspecialchars(date('d M Y', strtotime($app['application_date']))) ?></div>
                                            <div class="text-xs text-muted"><?= htmlspecialchars(date('h:i A', strtotime($app['application_date']))) ?></div>
                                        </td>
                                        </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Birth Certificates Tab -->
            <div id="birth-tab" class="tab-content">
                <?php if (empty($birthCertificates)): ?>
                    <div class="empty-state">
                        <h3>No Birth Certificates</h3>
                        <p>No birth certificates have been registered yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Processed</th>
                                    <th>Certificate No</th>
                                    <th>Date of Birth</th>
                                    <th>Place of Birth</th>
                                    <th>Father DOB</th>
                                    <th>Mother DOB</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($birthCertificates as $cert): ?>
                                    <tr<?= $cert['processed_by_admin'] ? ' class="processed-row"' : '' ?>>
                                        <td>
                                            <?php if ($cert['payment_status'] === 'Completed'): ?>
                                                <input type="checkbox"
                                                    class="processed-checkbox"
                                                    data-type="birth"
                                                    data-id="<?= htmlspecialchars($cert['birth_certificate_number']) ?>"
                                                    <?= $cert['processed_by_admin'] ? 'checked' : '' ?>
                                                    title="Mark as processed">
                                            <?php else: ?>
                                                <span class="no-checkbox">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="app-id"><?= htmlspecialchars($cert['birth_certificate_number']) ?></td>
                                        <td><?= htmlspecialchars(date('d M Y', strtotime($cert['date_of_birth']))) ?></td>
                                        <td><?= htmlspecialchars($cert['place_of_birth']) ?></td>
                                        <td><?= $cert['father_date_of_birth'] ? htmlspecialchars(date('d M Y', strtotime($cert['father_date_of_birth']))) : '-' ?></td>
                                        <td><?= $cert['mother_date_of_birth'] ? htmlspecialchars(date('d M Y', strtotime($cert['mother_date_of_birth']))) : '-' ?></td>
                                        <td>
                                            <?php if ($cert['payment_status']): ?>
                                                <span class="status-badge status-<?= strtolower($cert['payment_status']) ?>">
                                                    <?= htmlspecialchars($cert['payment_status']) ?>
                                                </span>
                                                <?php if ($cert['payment_amount']): ?>
                                                    <div class="text-xs text-muted">LKR <?= number_format($cert['payment_amount'], 2) ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="status-badge status-pending">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($cert['payment_date']): ?>
                                                <div class="text-xs"><?= htmlspecialchars(date('d M Y', strtotime($cert['payment_date']))) ?></div>
                                                <div class="text-xs text-muted"><?= htmlspecialchars(date('h:i A', strtotime($cert['payment_date']))) ?></div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Fines Tab -->
            <div id="fines-tab" class="tab-content">
                <?php if (empty($fines)): ?>
                    <div class="empty-state">
                        <h3>No Fines</h3>
                        <p>No fines have been issued yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Processed</th>
                                    <th>Fine ID</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>License</th>
                                    <th>Violation</th>
                                    <th>Place</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Issued Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fines as $fine): ?>
                                    <tr<?= $fine['processed_by_admin'] ? ' class="processed-row"' : '' ?>>
                                        <td>
                                            <?php if ($fine['payment_status'] === 'Completed'): ?>
                                                <input type="checkbox"
                                                    class="processed-checkbox"
                                                    data-type="fine"
                                                    data-id="<?= htmlspecialchars($fine['fine_id']) ?>"
                                                    <?= $fine['processed_by_admin'] ? 'checked' : '' ?>
                                                    title="Mark as processed">
                                            <?php else: ?>
                                                <span class="no-checkbox">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="app-id">#<?= htmlspecialchars($fine['fine_id']) ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($fine['driver_name']) ?></div>
                                            <div class="text-xs text-muted"><?= htmlspecialchars($fine['driver_address']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($fine['vehicle_number']) ?></td>
                                        <td><?= htmlspecialchars($fine['license_number']) ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($fine['mistake']) ?></div>
                                            <div class="text-xs text-muted">LKR <?= number_format($fine['fine_amount'], 2) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($fine['place']) ?></td>
                                        <td class="app-id">LKR <?= number_format($fine['fine_amount'], 2) ?></td>
                                        <td>
                                            <?php if ($fine['payment_status']): ?>
                                                <span class="status-badge status-<?= strtolower($fine['payment_status']) ?>">
                                                    <?= htmlspecialchars($fine['payment_status']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-pending">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="text-xs"><?= htmlspecialchars(date('d M Y', strtotime($fine['issued_at']))) ?></div>
                                            <div class="text-xs text-muted"><?= htmlspecialchars(date('h:i A', strtotime($fine['issued_at']))) ?></div>
                                            <?php if ($fine['due_at']): ?>
                                                <div class="text-xs text-muted">Due: <?= htmlspecialchars(date('d M Y', strtotime($fine['due_at']))) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });

        // Show selected tab content
        document.getElementById(tabName + '-tab').classList.add('active');

        // Add active class to clicked button
        event.target.classList.add('active');
    }

    // Handle processed checkbox changes
    document.querySelectorAll('.processed-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;
            const processed = this.checked;
            const row = this.closest('tr');

            // Determine the action based on type
            let action = '';
            if (type === 'nic') {
                action = 'update_nic_processed';
            } else if (type === 'birth') {
                action = 'update_birth_processed';
            } else if (type === 'fine') {
                action = 'update_fine_processed';
            }

            // Send AJAX request
            const formData = new FormData();
            formData.append('ajax_action', action);
            formData.append('id', id);
            formData.append('processed', processed);

            fetch('/admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to show the item moved to bottom with strikethrough
                        location.reload();
                    } else {
                        alert('Error updating status: ' + (data.error || 'Unknown error'));
                        // Revert checkbox state
                        this.checked = !processed;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update status');
                    // Revert checkbox state
                    this.checked = !processed;
                });
        });
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>