<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> All Verified Donors</h1>
            <a href="<?php echo APP_URL; ?>/organization/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <!-- Search/Filter -->
        <div class="form-section" style="margin-bottom:30px;">
            <div class="form-grid">
                <div class="form-group">
                    <label>Filter by Blood Group</label>
                    <select id="filterBloodGroup" class="form-control" onchange="filterDonors()">
                        <option value="">All Blood Groups</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Search by Name or District</label>
                    <input type="text" id="searchDonor" class="form-control" placeholder="Type to search..." onkeyup="filterDonors()">
                </div>
            </div>
        </div>

        <?php if (!empty($donors)): ?>
            <div class="table-responsive">
                <table class="data-table" id="donorsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Blood Group</th>
                            <th>District</th>
                            <th>Municipality</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Last Donation</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($donors as $donor): ?>
                            <tr class="donor-row" 
                                data-blood="<?php echo $donor['blood_group']; ?>"
                                data-name="<?php echo strtolower($donor['full_name']); ?>"
                                data-district="<?php echo strtolower($donor['district'] ?? ''); ?>">
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($donor['full_name']); ?></strong>
                                    <?php if ($donor['is_verified']): ?>
                                        <i class="fas fa-check-circle" style="color:var(--success);"></i>
                                    <?php endif; ?>
                                </td>
                                <td><span class="blood-badge small"><?php echo $donor['blood_group']; ?></span></td>
                                <td><?php echo htmlspecialchars($donor['district'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($donor['municipality'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($donor['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($donor['email']); ?></td>
                                <td><?php echo $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'Never'; ?></td>
                                <td>
                                    <?php if ($donor['phone']): ?>
                                        <a href="tel:<?php echo $donor['phone']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="mailto:<?php echo $donor['email']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="margin-top:15px;color:var(--secondary);">
                <i class="fas fa-info-circle"></i> Total: <?php echo count($donors); ?> verified donors
            </p>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-users"></i>
                <p>No verified donors found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterDonors() {
    var bloodFilter = document.getElementById('filterBloodGroup').value;
    var searchFilter = document.getElementById('searchDonor').value.toLowerCase();
    var rows = document.querySelectorAll('.donor-row');
    
    rows.forEach(function(row) {
        var blood = row.getAttribute('data-blood');
        var name = row.getAttribute('data-name');
        var district = row.getAttribute('data-district');
        
        var bloodMatch = !bloodFilter || blood === bloodFilter;
        var searchMatch = !searchFilter || name.includes(searchFilter) || district.includes(searchFilter);
        
        row.style.display = (bloodMatch && searchMatch) ? '' : 'none';
    });
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
