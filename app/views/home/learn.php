<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <div class="page-header" style="margin-top:30px;">
            <h1><i class="fas fa-book"></i> Learn About Blood Donation</h1>
        </div>

        <div class="learn-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:25px;">
            
            <div class="form-section">
                <h2 style="color:var(--success);"><i class="fas fa-check-circle"></i> Who Can Donate</h2>
                <ul style="list-style:none;padding:0;">
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-birthday-cake" style="color:var(--primary);width:25px;"></i> Age: 18 - 65 years</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-weight" style="color:var(--primary);width:25px;"></i> Weight: Minimum 45 kg</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-heartbeat" style="color:var(--primary);width:25px;"></i> Hemoglobin: At least 12.5 g/dL</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-calendar" style="color:var(--primary);width:25px;"></i> Gap: 3 months between donations</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-thermometer-half" style="color:var(--primary);width:25px;"></i> Normal temperature</li>
                    <li style="padding:10px 0;"><i class="fas fa-heart" style="color:var(--primary);width:25px;"></i> Pulse: 50-100 bpm</li>
                </ul>
            </div>

            <div class="form-section" style="border-top:4px solid var(--danger);">
                <h2 style="color:var(--danger);"><i class="fas fa-times-circle"></i> Who Cannot Donate</h2>
                <ul style="list-style:none;padding:0;">
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-virus" style="color:var(--danger);width:25px;"></i> HIV/AIDS patients</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-disease" style="color:var(--danger);width:25px;"></i> Hepatitis B or C</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-syringe" style="color:var(--danger);width:25px;"></i> Injectable drug users</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-baby" style="color:var(--danger);width:25px;"></i> Pregnant/breastfeeding women</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-pills" style="color:var(--danger);width:25px;"></i> Certain medications</li>
                    <li style="padding:10px 0;"><i class="fas fa-procedures" style="color:var(--danger);width:25px;"></i> Recent surgery patients</li>
                </ul>
            </div>

            <div class="form-section">
                <h2 style="color:var(--info);"><i class="fas fa-clipboard-list"></i> Before Donation</h2>
                <ul style="list-style:none;padding:0;">
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-utensils" style="color:var(--info);width:25px;"></i> Have a healthy meal</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-glass-water" style="color:var(--info);width:25px;"></i> Drink plenty of water</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-bed" style="color:var(--info);width:25px;"></i> Get enough sleep</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-id-card" style="color:var(--info);width:25px;"></i> Bring your Nagarikta</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-tshirt" style="color:var(--info);width:25px;"></i> Wear comfortable clothes</li>
                    <li style="padding:10px 0;"><i class="fas fa-ban" style="color:var(--info);width:25px;"></i> No alcohol 24hrs before</li>
                </ul>
            </div>

            <div class="form-section">
                <h2 style="color:var(--success);"><i class="fas fa-hand-holding-heart"></i> After Donation</h2>
                <ul style="list-style:none;padding:0;">
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-cookie" style="color:var(--success);width:25px;"></i> Have refreshments</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-couch" style="color:var(--success);width:25px;"></i> Rest 10-15 minutes</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-glass-water" style="color:var(--success);width:25px;"></i> Extra fluids 24-48 hrs</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-apple-alt" style="color:var(--success);width:25px;"></i> Eat iron-rich foods</li>
                    <li style="padding:10px 0;border-bottom:1px solid #eee;"><i class="fas fa-dumbbell" style="color:var(--success);width:25px;"></i> No heavy lifting 24 hrs</li>
                    <li style="padding:10px 0;"><i class="fas fa-band-aid" style="color:var(--success);width:25px;"></i> Keep bandage on</li>
                </ul>
            </div>

            <!-- Blood Compatibility Table -->
            <div class="form-section" style="grid-column:1/-1;">
                <h2><i class="fas fa-exchange-alt"></i> Blood Compatibility Chart</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Blood Type</th>
                                <th>Can Donate To</th>
                                <th>Can Receive From</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><span class="blood-badge small">O-</span></td><td>All (Universal Donor)</td><td>O-</td></tr>
                            <tr><td><span class="blood-badge small">O+</span></td><td>O+, A+, B+, AB+</td><td>O+, O-</td></tr>
                            <tr><td><span class="blood-badge small">A-</span></td><td>A-, A+, AB-, AB+</td><td>A-, O-</td></tr>
                            <tr><td><span class="blood-badge small">A+</span></td><td>A+, AB+</td><td>A+, A-, O+, O-</td></tr>
                            <tr><td><span class="blood-badge small">B-</span></td><td>B-, B+, AB-, AB+</td><td>B-, O-</td></tr>
                            <tr><td><span class="blood-badge small">B+</span></td><td>B+, AB+</td><td>B+, B-, O+, O-</td></tr>
                            <tr><td><span class="blood-badge small">AB-</span></td><td>AB-, AB+</td><td>A-, B-, AB-, O-</td></tr>
                            <tr><td><span class="blood-badge small">AB+</span></td><td>AB+ only</td><td>All (Universal Recipient)</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
