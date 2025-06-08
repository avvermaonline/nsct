<?php
// admin/members.php
require_once "../includes/config_nosession.php";

// Set current page for sidebar highlighting
$current_page = 'members';
$page_title = 'Member Management';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Add button for page header
$page_buttons = '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="fas fa-plus"></i> Add Member
                 </button>';

// Include header
include "includes/header.php";
?>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="membersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">Add New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" maxlength="10" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-select" id="state" name="state" required>
                                <option value="">Select State</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="Bihar">Bihar</option>
                                <!-- Add more states -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                            <select class="form-select" id="district" name="district" required>
                                <option value="">Select District</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="aadhar" class="form-label">Aadhar Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="aadhar" name="aadhar" maxlength="12" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveMemberBtn">Save Member</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone" maxlength="10" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_dob" name="dob" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_state" class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_state" name="state" required>
                                <option value="">Select State</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="Bihar">Bihar</option>
                                <!-- Add more states -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_district" class="form-label">District <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_district" name="district" required>
                                <option value="">Select District</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_aadhar" class="form-label">Aadhar Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_aadhar" name="aadhar" maxlength="12" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateMemberBtn">Update Member</button>
            </div>
        </div>
    </div>
</div>

<!-- View Member Modal -->
<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-labelledby="viewMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMemberModalLabel">Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <img src="../assets/default-user.png" alt="Member Photo" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 id="view_member_id" class="badge bg-primary"></h5>
                    </div>
                    <div class="col-md-9">
                        <h4 id="view_name" class="mb-1"></h4>
                        <p id="view_status" class="mb-3"></p>
                        
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Phone:</strong> <span id="view_phone"></span></p>
                                <p class="mb-1"><strong>Email:</strong> <span id="view_email"></span></p>
                                <p class="mb-1"><strong>Date of Birth:</strong> <span id="view_dob"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>District:</strong> <span id="view_district"></span></p>
                                <p class="mb-1"><strong>State:</strong> <span id="view_state"></span></p>
                                <p class="mb-1"><strong>Registered On:</strong> <span id="view_created_at"></span></p>
                            </div>
                        </div>
                        
                        <p class="mb-1"><strong>Address:</strong> <span id="view_address"></span></p>
                        <p class="mb-1"><strong>Aadhar:</strong> <span id="view_aadhar"></span></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h5>Sahyog Contributions</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="memberContributionsTable">
                                <thead>
                                    <tr>
                                        <th>Sahyog Title</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="view_contributions">
                                    <!-- Contributions will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMemberModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this member? This action cannot be undone.</p>
                <input type="hidden" id="delete_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php
// Page scripts
$page_scripts = <<<EOT
<script>
    // Initialize DataTable
    $(document).ready(function() {
        const membersTable = $('#membersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'api/get_members.php',
                type: 'POST'
            },
            columns: [
                { data: 'member_id' },
                { data: 'name' },
                { data: 'phone' },
                { data: 'email' },
                { data: 'district' },
                { data: 'state' },
                { 
                    data: 'status',
                    render: function(data) {
                        if (data === 'active') {
                            return '<span class="badge bg-success">Active</span>';
                        } else if (data === 'inactive') {
                            return '<span class="badge bg-danger">Inactive</span>';
                        } else {
                            return '<span class="badge bg-warning">Pending</span>';
                        }
                    }
                },
                { 
                    data: 'created_at',
                    render: function(data) {
                        return new Date(data).toLocaleDateString();
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-info view-btn" data-id="\${data.id}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-primary edit-btn" data-id="\${data.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger delete-btn" data-id="\${data.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[7, 'desc']]
        });
        
        // Handle state change to populate districts
        function populateDistricts(stateSelect, districtSelect) {
            const state = $(stateSelect).val();
            $(districtSelect).empty().append('<option value="">Select District</option>');
            
            if (state === 'Uttar Pradesh') {
                const districts = ['आगरा', 'अलीगढ़', 'प्रयागराज', 'अम्बेडकर नगर', 'अमेठी', 'अमरोहा', 'औरैया', 'आजमगढ़', 'बागपत', 'बहराइच', 'बलिया', 'बलरामपुर', 'बांदा', 'बाराबंकी', 'बरेली', 'बस्ती', 'भदोही', 'बिजनौर', 'बदायूं', 'बुलंदशहर', 'चंदौली', 'चित्रकूट', 'देवरिया', 'एटा', 'इटावा', 'अयोध्या', 'फर्रुखाबाद', 'फतेहपुर', 'फिरोजाबाद', 'गौतमबुद्ध नगर', 'गाजियाबाद', 'गाजीपुर', 'गोंडा', 'गोरखपुर', 'हमीरपुर', 'हापुड़', 'हरदोई', 'हाथरस', 'जालौन', 'जौनपुर', 'झांसी', 'कन्नौज', 'कानपुर देहात', 'कानपुर नगर', 'कासगंज', 'कौशाम्बी', 'कुशीनगर', 'लखीमपुर खीरी', 'ललितपुर', 'लखनऊ', 'महाराजगंज', 'महोबा', 'मैनपुरी', 'मथुरा', 'मऊ', 'मेरठ', 'मिर्जापुर', 'मुरादाबाद', 'मुजफ्फरनगर', 'पीलीभीत', 'प्रतापगढ़', 'रायबरेली', 'रामपुर', 'सहारनपुर', 'संभल', 'संत कबीर नगर', 'शाहजहांपुर', 'शामली', 'श्रावस्ती', 'सिद्धार्थनगर', 'सीतापुर', 'सोनभद्र', 'सुल्तानपुर', 'उन्नाव', 'वाराणसी'];
                
                districts.forEach(district => {
                    $(districtSelect).append(`<option value="\${district}">\${district}</option>`);
                });
            } else if (state === 'Bihar') {
                const districts = ['अररिया', 'अरवल', 'औरंगाबाद', 'बांका', 'बेगूसराय', 'भागलपुर', 'भोजपुर', 'बक्सर', 'दरभंगा', 'पूर्वी चंपारण', 'गया', 'गोपालगंज', 'जमुई', 'जहानाबाद', 'कैमूर', 'कटिहार', 'खगड़िया', 'किशनगंज', 'लखीसराय', 'मधेपुरा', 'मधुबनी', 'मुंगेर', 'मुजफ्फरपुर', 'नालंदा', 'नवादा', 'पटना', 'पूर्णिया', 'रोहतास', 'सहरसा', 'समस्तीपुर', 'सारण', 'शेखपुरा', 'शिवहर', 'सीतामढ़ी', 'सिवान', 'सुपौल', 'वैशाली', 'पश्चिम चंपारण'];
                
                districts.forEach(district => {
                    $(districtSelect).append(`<option value="\${district}">\${district}</option>`);
                });
            }
        }
        
        $('#state').change(function() {
            populateDistricts('#state', '#district');
        });
        
        $('#edit_state').change(function() {
            populateDistricts('#edit_state', '#edit_district');
        });
        
        // Add Member
        $('#saveMemberBtn').click(function() {
            const form = $('#addMemberForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            
            $.ajax({
                url: 'api/add_member.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addMemberModal').modal('hide');
                        form.reset();
                        membersTable.ajax.reload();
                        showAlert('Member added successfully');
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('An error occurred while adding the member', 'danger');
                }
            });
        });
        
        // View Member
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: 'api/get_member.php',
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const member = response.data;
                        
                        $('#view_member_id').text(member.member_id);
                        $('#view_name').text(member.name);
                        $('#view_phone').text(member.phone);
                        $('#view_email').text(member.email || 'N/A');
                        $('#view_dob').text(new Date(member.dob).toLocaleDateString());
                        $('#view_district').text(member.district);
                        $('#view_state').text(member.state);
                        $('#view_address').text(member.address);
                        $('#view_aadhar').text(member.aadhar);
                        $('#view_created_at').text(new Date(member.created_at).toLocaleDateString());
                        
                        if (member.status === 'active') {
                            $('#view_status').html('<span class="badge bg-success">Active</span>');
                        } else if (member.status === 'inactive') {
                            $('#view_status').html('<span class="badge bg-danger">Inactive</span>');
                        } else {
                            $('#view_status').html('<span class="badge bg-warning">Pending</span>');
                        }
                        
                        // Load contributions
                        $('#view_contributions').empty();
                        if (member.contributions && member.contributions.length > 0) {
                            member.contributions.forEach(contribution => {
                                $('#view_contributions').append(`
                                    <tr>
                                        <td>\${contribution.title}</td>
                                        <td>₹\${contribution.amount}</td>
                                        <td>\${new Date(contribution.date).toLocaleDateString()}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            $('#view_contributions').append('<tr><td colspan="3" class="text-center">No contributions found</td></tr>');
                        }
                        
                        $('#viewMemberModal').modal('show');
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('An error occurred while fetching member details', 'danger');
                }
            });
        });
        
        // Edit Member
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: 'api/get_member.php',
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const member = response.data;
                        
                        $('#edit_id').val(member.id);
                        $('#edit_name').val(member.name);
                        $('#edit_phone').val(member.phone);
                        $('#edit_email').val(member.email);
                        $('#edit_dob').val(member.dob);
                        $('#edit_state').val(member.state);
                        populateDistricts('#edit_state', '#edit_district');
                        setTimeout(() => {
                            $('#edit_district').val(member.district);
                        }, 100);
                        $('#edit_address').val(member.address);
                        $('#edit_aadhar').val(member.aadhar);
                        $('#edit_status').val(member.status);
                        
                        $('#editMemberModal').modal('show');
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('An error occurred while fetching member details', 'danger');
                }
            });
        });
        
        // Update Member
        $('#updateMemberBtn').click(function() {
            const form = $('#editMemberForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            
            $.ajax({
                url: 'api/update_member.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editMemberModal').modal('hide');
                        membersTable.ajax.reload();
                        showAlert('Member updated successfully');
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('An error occurred while updating the member', 'danger');
                }
            });
        });
        
        // Delete Member
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            $('#delete_id').val(id);
            $('#deleteMemberModal').modal('show');
        });
        
        $('#confirmDeleteBtn').click(function() {
            const id = $('#delete_id').val();
            
            $.ajax({
                url: 'api/delete_member.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#deleteMemberModal').modal('hide');
                        membersTable.ajax.reload();
                        showAlert('Member deleted successfully');
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('An error occurred while deleting the member', 'danger');
                }
            });
        });
    });
</script>
EOT;

// Include footer
include "includes/footer.php";
?>
