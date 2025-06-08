<?php
require_once "../includes/config.php";
require_once "../includes/functions.php";

// Initialize response
$response = ['success' => false, 'message' => ''];

session_start();

// --- Easy Authenticator OTP verification via RapidAPI ---
function verify_easy_authenticator_otp($secretCode, $token) {
    $curl = curl_init();
    $url = "https://easy-authenticator.p.rapidapi.com/verify?secretCode=" . urlencode($secretCode) . "&token=" . urlencode($token);
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
            "x-rapidapi-host: easy-authenticator.p.rapidapi.com",
            "x-rapidapi-key: f5ad25f2eamsh817c619f70ad853p179ed4jsne9e74c940380"
        ],
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    // Log API response for debugging
    file_put_contents(__DIR__ . '/../sms_debug.log', date('Y-m-d H:i:s') . "\nEasy Authenticator OTP Verify\nResponse: $response\nError: $err\n\n", FILE_APPEND);
    if ($err) return false;
    $json = json_decode($response, true);
    return isset($json['success']) && $json['success'] === true;
}

// Handle OTP verification
if (isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp'] ?? '');
    $secretCode = 'XXXXXXXXXXXXXXXXXXX'; // TODO: Replace with your actual Easy Authenticator secret
    if (empty($entered_otp)) {
        $response['message'] = 'कृपया OTP दर्ज करें।';
    } elseif (!isset($_SESSION['reg_data'])) {
        $response['message'] = 'OTP सत्र समाप्त हो गया है, कृपया पुनः पंजीकरण करें।';
    } elseif (!verify_easy_authenticator_otp($secretCode, $entered_otp)) {
        $response['message'] = 'गलत OTP, कृपया पुनः प्रयास करें।';
    } else {
        // OTP verified, insert user into DB
        $data = $_SESSION['reg_data'];
        $stmt = $pdo->prepare("INSERT INTO members (name, mobile, email, password, id_proof, photo, state, district, country, gender, dob, pan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([
            $data['name'],
            $data['mobile'],
            $data['email'],
            $data['password_hash'],
            $data['id_proof_name'],
            $data['photo_name'],
            $data['state'],
            $data['district'],
            $data['country'],
            $data['gender'],
            $data['dob'],
            $data['pan']
        ]);
        if ($result) {
            unset($_SESSION['reg_data']);
            $member_id = $pdo->lastInsertId();
            // Auto-login after registration
            $_SESSION['user_id'] = $member_id;
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_mobile'] = $data['mobile'];
            header("Location: dashboard.php");
            exit;
        } else {
            $response['message'] = 'पंजीकरण में त्रुटि हुई, कृपया पुनः प्रयास करें।';
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['verify_otp'])) {
    // Collect and sanitize input
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (!$name || !$mobile || !$email || !$password || !$confirm_password) {
        $response['message'] = "सभी फ़ील्ड भरना अनिवार्य है।";
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        $response['message'] = "Name field must contain only English letters and spaces.";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $response['message'] = "कृपया 10 अंकों का मोबाइल नंबर दर्ज करें।";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "कृपया मान्य ईमेल दर्ज करें।";
    } elseif ($password !== $confirm_password) {
        $response['message'] = "पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।";
    } elseif (!isset($_FILES['id_proof']) || $_FILES['id_proof']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = "आधार/पैन कार्ड अपलोड करें।";
    } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = "फोटो अपलोड करें।";
    } else {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM members WHERE mobile = ? OR email = ?");
        $stmt->execute([$mobile, $email]);
        if ($stmt->fetch()) {
            $response['message'] = "यह मोबाइल नंबर या ईमेल पहले से पंजीकृत है।";
        } else {
            // Handle file uploads
            $uploads_dir = "../uploads";
            if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

            $id_proof_ext = strtolower(pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION));
            $photo_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed_id_ext = ['jpg','jpeg','png','pdf'];
            $allowed_photo_ext = ['jpg','jpeg','png'];

            if (!in_array($id_proof_ext, $allowed_id_ext)) {
                $response['message'] = "आधार/पैन कार्ड के लिए jpg, jpeg, png, pdf फ़ाइलें ही मान्य हैं।";
            } elseif (!in_array($photo_ext, $allowed_photo_ext)) {
                $response['message'] = "फोटो के लिए jpg, jpeg, png फ़ाइलें ही मान्य हैं।";
            } else {
                $id_proof_name = uniqid("id_") . "." . $id_proof_ext;
                $photo_name = uniqid("photo_") . "." . $photo_ext;
                $id_proof_path = $uploads_dir . "/" . $id_proof_name;
                $photo_path = $uploads_dir . "/" . $photo_name;

                if (move_uploaded_file($_FILES['id_proof']['tmp_name'], $id_proof_path) &&
                    move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    // Store all data in session
                    $_SESSION['reg_data'] = [
                        'name' => $name,
                        'mobile' => $mobile,
                        'email' => $email,
                        'password_hash' => $password_hash,
                        'id_proof_name' => $id_proof_name,
                        'photo_name' => $photo_name,
                        'state' => $_POST['state'] ?? null,
                        'district' => $_POST['district'] ?? null,
                        'country' => $_POST['country'] ?? 'India',
                        'gender' => $_POST['gender'] ?? null,
                        'dob' => $_POST['dob'] ?? null,
                        'pan' => $_POST['pan'] ?? null
                    ];
                    $response['success'] = false;
                    $response['show_otp'] = true;
                    $response['message'] = 'कृपया Easy Authenticator ऐप से OTP दर्ज करें।';
                } else {
                    $response['message'] = "फ़ाइल अपलोड में समस्या है।";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSCT - पंजीकरण</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: url('../assets/bg-register.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #003366cc;
            color: #fff;
            padding: 0.7rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            margin-left: 2rem;
        }
        .navbar h1 {
            margin: 0;
            font-size: 1.7rem;
            letter-spacing: 1px;
        }
        .navbar nav {
            margin-right: 2rem;
        }
        .navbar nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 1.2rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .navbar nav a:hover {
            color: #ffd700;
        }
        .register-bg {
            background: rgba(255,255,255,0.97);
            max-width: 800px;
            margin: 40px auto 30px auto;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
            padding: 30px 30px 20px 30px;
            border-top: 6px solid #ed620c;
        }
        .register-title {
            text-align: center;
            color: #ed620c;
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .register-note {
            text-align: center;
            color: #d32f2f;
            font-size: 1.05rem;
            margin-bottom: 18px;
        }
        .register-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group.full-width {
            grid-column: span 2;
        }
        .register-form label {
            display: block;
            margin-bottom: 6px;
            color: #003366;
            font-weight: 500;
        }
        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"],
        .register-form input[type="file"],
        .register-form select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            background: #f8f9fa;
            box-sizing: border-box;
        }
        .register-form input[type="date"] {
            width: 100%;
            padding: 9px 12px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            background: #f8f9fa;
            box-sizing: border-box;
        }
        .register-form input[type="file"] {
            padding: 8px;
            background: #fff;
        }
        .register-form .document-upload {
            margin-bottom: 20px;
        }
        .register-form .upload-item {
            margin-bottom: 15px;
        }
        .register-form .submit-btn {
            width: 100%;
            background: #ed620c;
            color: #fff;
            border: none;
            padding: 12px 0;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            grid-column: span 2;
        }
        .register-form .submit-btn:hover {
            background: #c85000;
        }
        .success, .error {
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
            padding: 10px;
            border-radius: 5px;
            grid-column: span 2;
        }
        .success { 
            color: #155724; 
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .error { 
            color: #721c24; 
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .footer {
            background: #003366cc;
            color: #fff;
            text-align: center;
            padding: 1.2rem 0 0.7rem 0;
            margin-top: 2rem;
        }
        .footer .contact {
            margin-bottom: 0.5rem;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.95rem;
            grid-column: span 2;
        }
        .login-link a {
            color: #003366;
            text-decoration: none;
            transition: color 0.2s;
        }
        .login-link a:hover {
            color: #ed620c;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .register-bg {
                margin: 20px;
                padding: 20px 15px;
            }
            .register-form {
                grid-template-columns: 1fr;
            }
            .form-group.full-width, .success, .error, .register-form .submit-btn, .login-link {
                grid-column: span 1;
            }
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar .logo {
                margin-left: 1rem;
            }
            .navbar nav {
                margin: 0.5rem 0 0.5rem 1rem;
            }
            .navbar nav a {
                margin-left: 0;
                margin-right: 1rem;
                font-size: 0.9rem;
            }
        }
    </style>   
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <h1>NSCT - नन्दवंशी सेल्फ केयर टीम</h1>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="SadasyaSuchi.php">Sadasya Suchi</a>
            <a href="SahyogList.php">Sahyog Suchi</a>
            <a href="VywasthaSuchi.php">Vywastha Suchi</a>
            <a href="Niyamawali.php">Niyamawali</a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </div>
    <div class="register-bg">
        <div class="register-title">NSCT सदस्य पंजीकरण</div>
        <div class="register-note">
            <b>नोट:</b> केवल व्हाट्सप्प/टेलीग्राम ग्रुप से जुड़ने या केवल रजिस्ट्रेशन कर देने से कोई भी सदस्य वैधानिक सदस्य नही माना जायेगा। उसे प्रत्येक अपील पर सहयोग करना अनिवार्य है।
        </div>
        <?php if(isset($response) && !$response['success'] && $response['message']): ?>
            <div class="error"><?= htmlspecialchars($response['message']) ?></div>
        <?php endif; ?>
        <?php if (isset($response['show_otp']) && $response['show_otp']): ?>
            <form class="register-form" method="POST" autocomplete="off" id="otp-form">
                <div class="form-group full-width">
                    <label for="otp"><i class="fas fa-key"></i> Easy Authenticator ऐप से OTP दर्ज करें</label>
                    <input type="text" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}" required placeholder="6-digit OTP">
                    <div style="margin-top:8px;color:#003366;font-size:0.98rem;">
                        कृपया अपने Easy Authenticator ऐप में NSCT के लिए दिखाया गया 6-अंकों का OTP दर्ज करें।<br>
                        <b>OTP SMS द्वारा नहीं भेजा जाएगा।</b>
                    </div>
                </div>
                <button type="submit" name="verify_otp" class="submit-btn">OTP सत्यापित करें</button>
                <div class="login-link">
                    पहले से सदस्य हैं? <a href="login.php">लॉगिन करें</a>
                </div>
            </form>
        <?php else: ?>
        <form class="register-form" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <label for="country"><i class="fas fa-globe"></i> देश</label>
                <select id="country" name="country" required>
                    <option value="India" selected>India</option>
                </select>
            </div>

            <div class="form-group">
                <label for="state"><i class="fas fa-map-marker-alt"></i> राज्य</label>
                <select id="state" name="state" required>
                    <option value="">राज्य चुनें</option>
                </select>
            </div>

            <div class="form-group">
                <label for="district"><i class="fas fa-city"></i> जनपद</label>
                <select id="district" name="district" required>
                    <option value="">जनपद चुनें</option>
                </select>
            </div>

            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> पूरा नाम (Only English letters allowed)</label>
                <input type="text" id="name" name="name" required 
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    pattern="^[A-Za-z\s]+$"
                    title="Please enter only English letters and spaces">
            </div>

            <div class="form-group">
                <label for="dob"><i class="fas fa-calendar-alt"></i> जन्म तिथि (Date of Birth)</label>
                <input type="date" id="dob" name="dob" required 
                    value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>"
                    placeholder="YYYY-MM-DD"
                    pattern="\d{4}-\d{2}-\d{2}"
                    title="Please enter date in YYYY-MM-DD format or use the date picker">
            </div>

            <div class="form-group">
                <label for="gender"><i class="fas fa-venus-mars"></i> लिंग (Gender)</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="pan"><i class="fas fa-id-card"></i> पैन नंबर (PAN Number)</label>
                <input type="text" id="pan" name="pan" maxlength="10" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter valid PAN number (e.g. ABCDE1234F)" value="<?= htmlspecialchars($_POST['pan'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="mobile"><i class="fas fa-mobile-alt"></i> मोबाइल नंबर</label>
                <input type="text" id="mobile" name="mobile" pattern="[0-9]{10}" maxlength="10" required placeholder="10 अंकों का मोबाइल नंबर" value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> ईमेल</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> पासवर्ड</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> पासवर्ड की पुष्टि करें</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group full-width">
                <h3 style="margin-bottom:10px;color:#ed620c;font-size:1.1rem;"><i class="fas fa-file-upload"></i> आवश्यक दस्तावेज़</h3>
                <div class="upload-item">
                    <label>आधार कार्ड / पैन कार्ड</label>
                    <input type="file" name="id_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
                <div class="upload-item">
                    <label>फोटो</label>
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png" required>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">पंजीकरण करें</button>
            
            <div class="login-link">
                पहले से सदस्य हैं? <a href="login.php">लॉगिन करें</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
    <div class="footer">
        <div class="contact">
            <strong>Contact:</strong> info@nsct.com | +91-12345-67890
        </div>
        &copy; <?php echo date('Y'); ?> NSCT. All rights reserved.
    </div>

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
        
        fetch('api/send_otp.php', {
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
        
        fetch('api/verify_otp.php', {
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
    
// Indian states and districts data
const indianStates = [
    {state: "Andhra Pradesh", districts: ["Anantapur", "Chittoor", "East Godavari", "Guntur", "Kadapa", "Krishna", "Kurnool", "Nellore", "Prakasam", "Srikakulam", "Visakhapatnam", "Vizianagaram", "West Godavari"]},
    {state: "Arunachal Pradesh", districts: ["Anjaw", "Changlang", "Dibang Valley", "East Kameng", "East Siang", "Kamle", "Kra Daadi", "Kurung Kumey", "Lepa Rada", "Lohit", "Longding", "Lower Dibang Valley", "Lower Siang", "Lower Subansiri", "Namsai", "Pakke Kessang", "Papum Pare", "Shi Yomi", "Siang", "Tawang", "Tirap", "Upper Siang", "Upper Subansiri", "West Kameng", "West Siang"]},
    {state: "Assam", districts: ["Baksa", "Barpeta", "Biswanath", "Bongaigaon", "Cachar", "Charaideo", "Chirang", "Darrang", "Dhemaji", "Dhubri", "Dibrugarh", "Dima Hasao", "Goalpara", "Golaghat", "Hailakandi", "Hojai", "Jorhat", "Kamrup", "Kamrup Metropolitan", "Karbi Anglong", "Karimganj", "Kokrajhar", "Lakhimpur", "Majuli", "Morigaon", "Nagaon", "Nalbari", "Sivasagar", "Sonitpur", "South Salmara-Mankachar", "Tinsukia", "Udalguri", "West Karbi Anglong"]},
    {state: "Bihar", districts: ["Araria", "Arwal", "Aurangabad", "Banka", "Begusarai", "Bhagalpur", "Bhojpur", "Buxar", "Darbhanga", "East Champaran", "Gaya", "Gopalganj", "Jamui", "Jehanabad", "Kaimur", "Katihar", "Khagaria", "Kishanganj", "Lakhisarai", "Madhepura", "Madhubani", "Munger", "Muzaffarpur", "Nalanda", "Nawada", "Patna", "Purnia", "Rohtas", "Saharsa", "Samastipur", "Saran", "Sheikhpura", "Sheohar", "Sitamarhi", "Siwan", "Supaul", "Vaishali", "West Champaran"]},
    {state: "Chhattisgarh", districts: ["Balod", "Baloda Bazar", "Balrampur", "Bastar", "Bemetara", "Bijapur", "Bilaspur", "Dantewada", "Dhamtari", "Durg", "Gariaband", "Gaurela Pendra Marwahi", "Janjgir-Champa", "Jashpur", "Kabirdham", "Kanker", "Kondagaon", "Korba", "Koriya", "Mahasamund", "Mungeli", "Narayanpur", "Raigarh", "Raipur", "Rajnandgaon", "Sukma", "Surajpur", "Surguja"]},
    {state: "Delhi", districts: ["Central Delhi", "East Delhi", "New Delhi", "North Delhi", "North East Delhi", "North West Delhi", "Shahdara", "South Delhi", "South East Delhi", "South West Delhi", "West Delhi"]},
    {state: "Goa", districts: ["North Goa", "South Goa"]},
    {state: "Gujarat", districts: ["Ahmedabad", "Amreli", "Anand", "Aravalli", "Banaskantha", "Bharuch", "Bhavnagar", "Botad", "Chhota Udaipur", "Dahod", "Dang", "Devbhoomi Dwarka", "Gandhinagar", "Gir Somnath", "Jamnagar", "Junagadh", "Kheda", "Kutch", "Mahisagar", "Mehsana", "Morbi", "Narmada", "Navsari", "Panchmahal", "Patan", "Porbandar", "Rajkot", "Sabarkantha", "Surat", "Surendranagar", "Tapi", "Vadodara", "Valsad"]},
    {state: "Haryana", districts: ["Ambala", "Bhiwani", "Charkhi Dadri", "Faridabad", "Fatehabad", "Gurugram", "Hisar", "Jhajjar", "Jind", "Kaithal", "Karnal", "Kurukshetra", "Mahendragarh", "Nuh", "Palwal", "Panchkula", "Panipat", "Rewari", "Rohtak", "Sirsa", "Sonipat", "Yamunanagar"]},
    {state: "Himachal Pradesh", districts: ["Bilaspur", "Chamba", "Hamirpur", "Kangra", "Kinnaur", "Kullu", "Lahaul and Spiti", "Mandi", "Shimla", "Sirmaur", "Solan", "Una"]},
    {state: "Jharkhand", districts: ["Bokaro", "Chatra", "Deoghar", "Dhanbad", "Dumka", "East Singhbhum", "Garhwa", "Giridih", "Godda", "Gumla", "Hazaribagh", "Jamtara", "Khunti", "Koderma", "Latehar", "Lohardaga", "Pakur", "Palamu", "Ramgarh", "Ranchi", "Sahebganj", "Seraikela Kharsawan", "Simdega", "West Singhbhum"]},
    {state: "Karnataka", districts: ["Bagalkot", "Ballari", "Belagavi", "Bengaluru Rural", "Bengaluru Urban", "Bidar", "Chamarajanagar", "Chikballapur", "Chikkamagaluru", "Chitradurga", "Dakshina Kannada", "Davanagere", "Dharwad", "Gadag", "Hassan", "Haveri", "Kalaburagi", "Kodagu", "Kolar", "Koppal", "Mandya", "Mysuru", "Raichur", "Ramanagara", "Shivamogga", "Tumakuru", "Udupi", "Uttara Kannada", "Vijayapura", "Yadgir"]},
    {state: "Kerala", districts: ["Alappuzha", "Ernakulam", "Idukki", "Kannur", "Kasaragod", "Kollam", "Kottayam", "Kozhikode", "Malappuram", "Palakkad", "Pathanamthitta", "Thiruvananthapuram", "Thrissur", "Wayanad"]},
    {state: "Madhya Pradesh", districts: ["Agar Malwa", "Alirajpur", "Anuppur", "Ashoknagar", "Balaghat", "Barwani", "Betul", "Bhind", "Bhopal", "Burhanpur", "Chhatarpur", "Chhindwara", "Damoh", "Datia", "Dewas", "Dhar", "Dindori", "Guna", "Gwalior", "Harda", "Hoshangabad", "Indore", "Jabalpur", "Jhabua", "Katni", "Khandwa", "Khargone", "Mandla", "Mandsaur", "Morena", "Narsinghpur", "Neemuch", "Panna", "Raisen", "Rajgarh", "Ratlam", "Rewa", "Sagar", "Satna", "Sehore", "Seoni", "Shahdol", "Shajapur", "Sheopur", "Shivpuri", "Sidhi", "Singrauli", "Tikamgarh", "Ujjain", "Umaria", "Vidisha"]},
    {state: "Maharashtra", districts: ["Ahmednagar", "Akola", "Amravati", "Aurangabad", "Beed", "Bhandara", "Buldhana", "Chandrapur", "Dhule", "Gadchiroli", "Gondia", "Hingoli", "Jalgaon", "Jalna", "Kolhapur", "Latur", "Mumbai City", "Mumbai Suburban", "Nagpur", "Nanded", "Nandurbar", "Nashik", "Osmanabad", "Palghar", "Parbhani", "Pune", "Raigad", "Ratnagiri", "Sangli", "Satara", "Sindhudurg", "Solapur", "Thane", "Wardha", "Washim", "Yavatmal"]},
    {state: "Manipur", districts: ["Bishnupur", "Chandel", "Churachandpur", "Imphal East", "Imphal West", "Jiribam", "Kakching", "Kamjong", "Kangpokpi", "Noney", "Pherzawl", "Senapati", "Tamenglong", "Tengnoupal", "Thoubal", "Ukhrul"]},
    {state: "Meghalaya", districts: ["East Garo Hills", "East Jaintia Hills", "East Khasi Hills", "North Garo Hills", "Ri Bhoi", "South Garo Hills", "South West Garo Hills", "South West Khasi Hills", "West Garo Hills", "West Jaintia Hills", "West Khasi Hills"]},
    {state: "Mizoram", districts: ["Aizawl", "Champhai", "Hnahthial", "Khawzawl", "Kolasib", "Lawngtlai", "Lunglei", "Mamit", "Saiha", "Saitual", "Serchhip"]},
    {state: "Nagaland", districts: ["Chümoukedima", "Dimapur", "Kiphire", "Kohima", "Longleng", "Mokokchung", "Mon", "Niuland", "Peren", "Phek", "Shamator", "Tseminyu", "Tuensang", "Wokha", "Zünheboto"]},
    {state: "Odisha", districts: ["Angul", "Balangir", "Balasore", "Bargarh", "Bhadrak", "Boudh", "Cuttack", "Debagarh", "Dhenkanal", "Gajapati", "Ganjam", "Jagatsinghapur", "Jajpur", "Jharsuguda", "Kalahandi", "Kandhamal", "Kendrapara", "Kendujhar", "Khordha", "Koraput", "Malkangiri", "Mayurbhanj", "Nabarangpur", "Nayagarh", "Nuapada", "Puri", "Rayagada", "Sambalpur", "Sonepur", "Sundargarh"]},
    {state: "Punjab", districts: ["Amritsar", "Barnala", "Bathinda", "Faridkot", "Fatehgarh Sahib", "Fazilka", "Ferozepur", "Gurdaspur", "Hoshiarpur", "Jalandhar", "Kapurthala", "Ludhiana", "Malerkotla", "Mansa", "Moga", "Mohali", "Muktsar", "Pathankot", "Patiala", "Rupnagar", "Sangrur", "SAS Nagar", "SBS Nagar", "Sri Muktsar Sahib", "Tarn Taran"]},
    {state: "Rajasthan", districts: ["Ajmer", "Alwar", "Banswara", "Baran", "Barmer", "Bharatpur", "Bhilwara", "Bikaner", "Bundi", "Chittorgarh", "Churu", "Dausa", "Dholpur", "Dungarpur", "Ganganagar", "Hanumangarh", "Jaipur", "Jaisalmer", "Jalore", "Jhalawar", "Jhunjhunu", "Jodhpur", "Karauli", "Kota", "Nagaur", "Pali", "Pratapgarh", "Rajsamand", "Sawai Madhopur", "Sikar", "Sirohi", "Tonk", "Udaipur"]},
    {state: "Sikkim", districts: ["East Sikkim", "North Sikkim", "South Sikkim", "West Sikkim", "Pakyong", "Soreng"]},
    {state: "Tamil Nadu", districts: ["Ariyalur", "Chengalpattu", "Chennai", "Coimbatore", "Cuddalore", "Dharmapuri", "Dindigul", "Erode", "Kallakurichi", "Kanchipuram", "Kanyakumari", "Karur", "Krishnagiri", "Madurai", "Mayiladuthurai", "Nagapattinam", "Namakkal", "Nilgiris", "Perambalur", "Pudukkottai", "Ramanathapuram", "Ranipet", "Salem", "Sivaganga", "Tenkasi", "Thanjavur", "Theni", "Thoothukudi", "Tiruchirappalli", "Tirunelveli", "Tirupathur", "Tiruppur", "Tiruvallur", "Tiruvannamalai", "Tiruvarur", "Vellore", "Viluppuram", "Virudhunagar"]},
    {state: "Telangana", districts: ["Adilabad", "Bhadradri Kothagudem", "Hyderabad", "Jagtial", "Jangaon", "Jayashankar Bhupalpally", "Jogulamba Gadwal", "Kamareddy", "Karimnagar", "Khammam", "Komaram Bheem", "Mahabubabad", "Mahabubnagar", "Mancherial", "Medak", "Medchal–Malkajgiri", "Mulugu", "Nagarkurnool", "Nalgonda", "Narayanpet", "Nirmal", "Nizamabad", "Peddapalli", "Rajanna Sircilla", "Ranga Reddy", "Sangareddy", "Siddipet", "Suryapet", "Vikarabad", "Wanaparthy", "Warangal Rural", "Warangal Urban", "Yadadri Bhuvanagiri"]},
    {state: "Tripura", districts: ["Dhalai", "Gomati", "Khowai", "North Tripura", "Sepahijala", "South Tripura", "Unakoti", "West Tripura"]},
    {state: "Uttar Pradesh", districts: ["Agra", "Aligarh", "Ambedkar Nagar", "Amethi", "Amroha", "Auraiya", "Ayodhya", "Azamgarh", "Baghpat", "Bahraich", "Ballia", "Balrampur", "Banda", "Barabanki", "Bareilly", "Basti", "Bhadohi", "Bijnor", "Budaun", "Bulandshahr", "Chandauli", "Chitrakoot", "Deoria", "Etah", "Etawah", "Ayodhya", "Farrukhabad", "Fatehpur", "Firozabad", "Gautam Buddha Nagar", "Ghaziabad", "Ghazipur", "Gonda", "Gorakhpur", "Hamirpur", "Hapur", "Hardoi", "Hathras", "Jalaun", "Jaunpur", "Jhansi", "Kannauj", "Kanpur Dehat", "Kanpur Nagar", "Kasganj", "Kaushambi", "Kheri", "Kushinagar", "Lakhimpur", "Lalitपुर", "Lucknow", "Maharajganj", "Mahoba", "Mainpuri", "Mathura", "Mau", "Meerut", "Mirzapur", "Moradabad", "Muzaffarnagar", "Pilibhit", "Pratapgarh", "Prayagraj", "Raebareli", "Rampur", "Saharanpur", "Sambhal", "Sant Kabir Nagar", "Sant Ravidas Nagar", "Shahjahanpur", "Shamli", "Shravasti", "Siddharthnagar", "Sitapur", "Sonbhadra", "Sultanpur", "Unnao", "Varanasi"]},
    {state: "Uttarakhand", districts: ["Almora", "Bageshwar", "Chamoli", "Champawat", "Dehradun", "Haridwar", "Nainital", "Pauri Garhwal", "Pithoragarh", "Rudraprayag", "Tehri Garhwal", "Udham Singh Nagar", "Uttarkashi"]},
    {state: "West Bengal", districts: ["Alipurduar", "Bankura", "Birbhum", "Cooch Behar", "Dakshin Dinajpur", "Darjeeling", "Hooghly", "Howrah", "Jalpaiguri", "Jhargram", "Kalimpong", "Kolkata", "Malda", "Murshidabad", "Nadia", "North 24 Parganas", "Paschim Bardhaman", "Paschim Medinipur", "Purba Bardhaman", "Purba Medinipur", "Purulia", "South 24 Parganas", "Uttar Dinajpur"]}
];

// Initialize dropdowns when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state');
    const districtSelect = document.getElementById('district');

    // Populate states dropdown
    stateSelect.innerHTML = '<option value="">राज्य चुनें</option>';
    indianStates.forEach(stateObj => {
        const opt = document.createElement('option');
        opt.value = stateObj.state;
        opt.textContent = stateObj.state;
        stateSelect.appendChild(opt);
    });

    // When state changes, populate districts
    stateSelect.addEventListener('change', function() {
        const selectedState = indianStates.find(s => s.state === this.value);
        districtSelect.innerHTML = '<option value="">जनपद चुनें</option>';
        if (selectedState) {
            selectedState.districts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist;
                opt.textContent = dist;
                districtSelect.appendChild(opt);
            });
        }
    });

    // Restore previous selection if form was submitted
    <?php if (!empty($_POST['state'])): ?>
        stateSelect.value = "<?= htmlspecialchars($_POST['state']) ?>";
        stateSelect.dispatchEvent(new Event('change'));
        <?php if (!empty($_POST['district'])): ?>
            districtSelect.value = "<?= htmlspecialchars($_POST['district']) ?>";
        <?php endif; ?>
    <?php endif; ?>
});
</script>
</body>
</html>
