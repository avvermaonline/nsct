<?php
// register.php
session_start();
$apiDir = __DIR__ . '/../api';
if (!file_exists($apiDir)) {
    mkdir($apiDir, 0755, true);
}

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Set current page for navbar highlighting
$current_page = 'register';
$page_title = 'Register - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h2 class="text-primary border-bottom border-danger pb-2 mb-4">सदस्य पंजीकरण</h2>
                    
                    <div id="step1" class="registration-step">
                        <div class="mb-3">
                            <label for="phone" class="form-label">मोबाइल नंबर <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+91</span>
                                <input type="tel" class="form-control" id="phone" name="phone" maxlength="10" required>
                                <button type="button" class="btn btn-primary" id="sendOtpBtn">OTP भेजें</button>
                            </div>
                            <div class="form-text">अपना 10 अंकों का मोबाइल नंबर दर्ज करें</div>
                        </div>
                        
                        <div class="mb-3 d-none" id="otpSection">
                            <label for="otp" class="form-label">OTP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="otp" name="otp" maxlength="6" required>
                                <button type="button" class="btn btn-success" id="verifyOtpBtn">OTP सत्यापित करें</button>
                            </div>
                            <div class="form-text">आपके मोबाइल नंबर पर भेजे गए 6 अंकों का OTP दर्ज करें</div>
                            <div class="mt-2">
                                <span id="resendTimer" class="text-muted"></span>
                                <button type="button" class="btn btn-link p-0 d-none" id="resendOtpBtn">OTP पुनः भेजें</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="step2" class="registration-step d-none">
                        <form id="registrationForm">
                            <input type="hidden" id="verifiedPhone" name="verifiedPhone">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">पूरा नाम <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">ईमेल</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="dob" class="form-label">जन्म तिथि <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="dob" name="dob" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="state" class="form-label">राज्य <span class="text-danger">*</span></label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">राज्य चुनें</option>
                                        <option value="Uttar Pradesh">उत्तर प्रदेश</option>
                                        <option value="Bihar">बिहार</option>
                                        <!-- Add more states -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="district" class="form-label">जिला <span class="text-danger">*</span></label>
                                    <select class="form-select" id="district" name="district" required>
                                        <option value="">जिला चुनें</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">पता <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="aadhar" class="form-label">आधार कार्ड नंबर <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="aadhar" name="aadhar" maxlength="12" required>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">पासवर्ड <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">पासवर्ड की पुष्टि करें <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">मैं <a href="Niyamawali.php" target="_blank">नियम और शर्तें</a> से सहमत हूं</label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">पंजीकरण करें</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <p>पहले से ही सदस्य हैं? <a href="login.php">लॉगिन करें</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const otpSection = document.getElementById('otpSection');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const phoneInput = document.getElementById('phone');
    const otpInput = document.getElementById('otp');
    const verifiedPhoneInput = document.getElementById('verifiedPhone');
    const resendTimer = document.getElementById('resendTimer');
    const registrationForm = document.getElementById('registrationForm');
    
    let resendTimerId;
    
    // Send OTP
    sendOtpBtn.addEventListener('click', function() {
        const phone = phoneInput.value.trim();
        if (phone.length !== 10 || !/^\d+$/.test(phone)) {
            alert('कृपया 10 अंकों का वैध मोबाइल नंबर दर्ज करें');
            return;
        }
        
        sendOtpBtn.disabled = true;
        sendOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> भेज रहा है...';
        
        fetch('../api/send_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ phone: phone }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                otpSection.classList.remove('d-none');
                startResendTimer();
                alert('OTP सफलतापूर्वक भेजा गया है');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('OTP भेजने में त्रुटि हुई');
        })
        .finally(() => {
            sendOtpBtn.disabled = false;
            sendOtpBtn.innerHTML = 'OTP भेजें';
        });
    });
    
    // Verify OTP
    verifyOtpBtn.addEventListener('click', function() {
        const phone = phoneInput.value.trim();
        const otp = otpInput.value.trim();
        
        if (otp.length !== 6 || !/^\d+$/.test(otp)) {
            alert('कृपया 6 अंकों का वैध OTP दर्ज करें');
            return;
        }
        
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> सत्यापित कर रहा है...';
        
        fetch('../api/verify_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ phone: phone, otp: otp }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                step1.classList.add('d-none');
                step2.classList.remove('d-none');
                verifiedPhoneInput.value = phone;
                clearInterval(resendTimerId);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('OTP सत्यापित करने में त्रुटि हुई');
        })
        .finally(() => {
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = 'OTP सत्यापित करें';
        });
    });
    
    // Resend OTP
    resendOtpBtn.addEventListener('click', function() {
        resendOtpBtn.classList.add('d-none');
        sendOtpBtn.click();
    });
    
    // Start resend timer
    function startResendTimer() {
        let seconds = 60;
        resendOtpBtn.classList.add('d-none');
        resendTimer.textContent = `${seconds} सेकंड में OTP पुनः भेजें`;
        
        clearInterval(resendTimerId);
        resendTimerId = setInterval(() => {
            seconds--;
            resendTimer.textContent = `${seconds} सेकंड में OTP पुनः भेजें`;
            
            if (seconds <= 0) {
                clearInterval(resendTimerId);
                resendTimer.textContent = '';
                resendOtpBtn.classList.remove('d-none');
            }
        }, 1000);
    }
    
    // Handle registration form submission
    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            alert('पासवर्ड मेल नहीं खाते');
            return;
        }
        
        const formData = new FormData(registrationForm);
        const submitBtn = registrationForm.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> पंजीकरण हो रहा है...';

        fetch('../api/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('पंजीकरण सफल! अब आप लॉगिन कर सकते हैं');
                window.location.href = 'login.php';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('पंजीकरण में त्रुटि हुई');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'पंजीकरण करें';
        });
    });
    
    // Populate districts based on state selection
    document.getElementById('state').addEventListener('change', function() {
        const state = this.value;
        const districtSelect = document.getElementById('district');
        
        // Clear existing options
        districtSelect.innerHTML = '<option value="">जिला चुनें</option>';
        
        if (state === 'Uttar Pradesh') {
            const districts = ['आगरा', 'अलीगढ़', 'प्रयागराज', 'अम्बेडकर नगर', 'अमेठी', 'अमरोहा', 'औरैया', 'आजमगढ़', 'बागपत', 'बहराइच', 'बलिया', 'बलरामपुर', 'बांदा', 'बाराबंकी', 'बरेली', 'बस्ती', 'भदोही', 'बिजनौर', 'बदायूं', 'बुलंदशहर', 'चंदौली', 'चित्रकूट', 'देवरिया', 'एटा', 'इटावा', 'अयोध्या', 'फर्रुखाबाद', 'फतेहपुर', 'फिरोजाबाद', 'गौतमबुद्ध नगर', 'गाजियाबाद', 'गाजीपुर', 'गोंडा', 'गोरखपुर', 'हमीरपुर', 'हापुड़', 'हरदोई', 'हाथरस', 'जालौन', 'जौनपुर', 'झांसी', 'कन्नौज', 'कानपुर देहात', 'कानपुर नगर', 'कासगंज', 'कौशाम्बी', 'कुशीनगर', 'लखीमपुर खीरी', 'ललितपुर', 'लखनऊ', 'महाराजगंज', 'महोबा', 'मैनपुरी', 'मथुरा', 'मऊ', 'मेरठ', 'मिर्जापुर', 'मुरादाबाद', 'मुजफ्फरनगर', 'पीलीभीत', 'प्रतापगढ़', 'रायबरेली', 'रामपुर', 'सहारनपुर', 'संभल', 'संत कबीर नगर', 'शाहजहांपुर', 'शामली', 'श्रावस्ती', 'सिद्धार्थनगर', 'सीतापुर', 'सोनभद्र', 'सुल्तानपुर', 'उन्नाव', 'वाराणसी'];
            
            districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        } else if (state === 'Bihar') {
            const districts = ['अररिया', 'अरवल', 'औरंगाबाद', 'बांका', 'बेगूसराय', 'भागलपुर', 'भोजपुर', 'बक्सर', 'दरभंगा', 'पूर्वी चंपारण', 'गया', 'गोपालगंज', 'जमुई', 'जहानाबाद', 'कैमूर', 'कटिहार', 'खगड़िया', 'किशनगंज', 'लखीसराय', 'मधेपुरा', 'मधुबनी', 'मुंगेर', 'मुजफ्फरपुर', 'नालंदा', 'नवादा', 'पटना', 'पूर्णिया', 'रोहतास', 'सहरसा', 'समस्तीपुर', 'सारण', 'शेखपुरा', 'शिवहर', 'सीतामढ़ी', 'सिवान', 'सुपौल', 'वैशाली', 'पश्चिम चंपारण'];
            
            districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        }
    });
});
</script>
</body>
</html>
