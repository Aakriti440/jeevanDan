<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
     <?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>" style="padding:12px 16px; border-radius:6px; margin-bottom:20px; background:<?php echo $flash['type']==='success'?'#d4edda':'#f8d7da'; ?>; color:<?php echo $flash['type']==='success'?'#155724':'#721c24'; ?>; border:1px solid <?php echo $flash['type']==='success'?'#c3e6cb':'#f5c6cb'; ?>;">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Manage Organizations</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="table-responsive">
            <!-- ===== ADD ORGANIZATION FORM ===== -->
<div style="background:#fff; border:1px solid #e0e0e0; border-radius:10px; padding:25px; margin-bottom:30px;">
    <h3 style="color:#c0392b; margin-bottom:20px;"><i class="fas fa-plus-circle"></i> Add New Organization / Red Cross Center</h3>
    <form method="POST" action="<?php echo APP_URL; ?>/admin/save-organization">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Contact Person Full Name *</label>
                <input type="text" name="full_name" class="form-control" required placeholder="e.g. Ram Prasad Sharma" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Email *</label>
                <input type="email" name="email" class="form-control" required placeholder="org@example.com" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="98XXXXXXXX" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Organization / Center Name *</label>
                <input type="text" name="organization_name" class="form-control" required placeholder="e.g. Nepal Red Cross Kathmandu" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Organization ID / Registration No.</label>
                <input type="text" name="organization_id" class="form-control" placeholder="e.g. REG-2024-001" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Organization Type *</label>
                <select name="organization_type" class="form-control" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
                    <option value="red_cross">Red Cross</option>
                    <option value="hospital">Hospital</option>
                    <option value="blood_bank">Blood Bank</option>
                    <option value="ngo">NGO</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Position of Contact Person</label>
                <input type="text" name="position" class="form-control" placeholder="e.g. Blood Bank Officer" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Province</label>
                <select name="province" class="form-control" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
                    <option value="">Select Province</option>
                    <?php foreach (['Koshi','Madhesh','Bagmati','Gandaki','Lumbini','Karnali','Sudurpashchim'] as $p): ?>
                        <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">District</label>
                <input type="text" name="district" class="form-control" placeholder="e.g. Kathmandu" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div>
                <label style="display:block; font-weight:600; margin-bottom:5px;">Municipality</label>
                <input type="text" name="municipality" class="form-control" placeholder="e.g. Kathmandu Metropolitan" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
            <div style="grid-column:1/-1;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Full Address</label>
                <input type="text" name="address" class="form-control" placeholder="e.g. Kalimati, Kathmandu" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>
        </div>
        <div style="margin-top:20px;">
            <button type="submit" style="padding:12px 30px; background:#c0392b; color:#fff; border:none; border-radius:6px; font-size:1rem; cursor:pointer;">
                <i class="fas fa-plus"></i> Add Organization
            </button>
        </div>
    </form>
</div>
<!-- ===== END ADD ORGANIZATION FORM ===== -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Organization</th>
                        <th>Type</th>
                        <th>District</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orgs as $org): ?>
                        <tr>
                            <td><?php echo $org['id']; ?></td>
                            <td><?php echo htmlspecialchars($org['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($org['email']); ?></td>
                            <td><?php echo htmlspecialchars($org['organization_name'] ?? 'Not provided'); ?></td>
                            <td><?php echo ucfirst($org['organization_type'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($org['district'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($org['is_verified']): ?>
                                    <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                                <?php else: ?>
                                    <span class="pending-badge"><i class="fas fa-clock"></i> <?php echo ucfirst($org['verification_status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($org['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/admin/view-organization/<?php echo $org['id']; ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
