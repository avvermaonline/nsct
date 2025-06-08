<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: home/member/dashboard.php');
    exit();
}

// Set current page for navbar highlighting
$current_page = 'index';
$page_title = 'NSCT - नन्दवंशी सेल्फ केयर टीम';

// Include header
include 'home/header.php';
// Include navbar
include 'home/navbar.php';
?>

<style>
    /* Hero section */
    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../assets/images/banner.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 150px 0 100px;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: linear-gradient(to top, var(--light), transparent);
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .hero-text {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .btn-hero {
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: all 0.3s;
    }
    
    .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    
    /* Notice card */
    .notice-card {
        background: linear-gradient(135deg, #fff8f2, #fff);
        border-left: 5px solid var(--secondary);
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transform: translateY(-30px);
        position: relative;
        z-index: 10;
    }
    
    /* Founders section */
    .founder-img {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 25px;
        border: 5px solid var(--secondary);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        transition: all 0.3s;
    }
    
    .founder-img:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .founder-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s;
    }
    
    .founder-img:hover img {
        transform: scale(1.1);
    }
    
    /* Stats section */
    .stats-section {
        background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), url('img/stats-bg.jpg');
        background-size: cover;
        background-attachment: fixed;
        color: white;
        padding: 100px 0;
        position: relative;
    }
    
    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(231, 76, 60, 0.3), rgba(41, 128, 185, 0.3));
    }
    
    .stat-item {
        text-align: center;
        padding: 20px;
        position: relative;
        z-index: 1;
    }
    
    .stat-number {
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 10px;
        display: block;
    }
    
    .stat-text {
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Team and objectives cards */
    .card-img-top {
        height: 220px;
        object-fit: cover;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 2.8rem;
        }
        
        .section-title {
            font-size: 2.2rem;
        }
        
        .founder-img {
            width: 180px;
            height: 180px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.3rem;
        }
        
        .hero-text {
            font-size: 1.1rem;
        }
        
        .stat-number {
            font-size: 2.8rem;
        }
        
        .stat-text {
            font-size: 1rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container hero-content">
        <h1 class="hero-title mb-4">नन्दवंशी सेल्फ केयर टीम</h1>
        <p class="hero-text">एक साथ मिलकर समाज के लिए काम करें। हमारे साथ जुड़ें और सामाजिक परिवर्तन का हिस्सा बनें।</p>
        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
            <a href="register.php" class="btn btn-danger btn-hero">सदस्य बनें</a>
            <a href="about.php" class="btn btn-outline-light btn-hero">और जानें</a>
        </div>
    </div>
</section>

<!-- Notice Section -->
<div class="container">
    <div class="card notice-card p-4">
        <div class="card-body text-center">
            <h3 class="card-title text-danger mb-3">महत्वपूर्ण सूचना</h3>
            <p class="card-text text-danger fw-bold">केवल व्हाट्सप्प/टेलीग्राम ग्रुप से जुड़ने या केवल रजिस्ट्रेशन कर देने से कोई भी सदस्य वैधानिक सदस्य नही माना जायेगा। उसे प्रत्येक अपील पर सहयोग करना अनिवार्य है।</p>
        </div>
    </div>
</div>

<!-- Founders Section -->
<section class="py-5 mt-5">
    <div class="container">
        <h2 class="section-title">हमारे संस्थापक</h2>
        <p class="text-center text-muted mb-5">नन्दवंशी सेल्फ केयर टीम के संस्थापक जिन्होंने इस पहल की शुरुआत की</p>

        <div class="row justify-content-center">
            <div class="col-md-4 mb-5 text-center">
                <div class="founder-img">
                    <img src="img/founder2.jpg" alt="Founder 2">
                </div>
                <h3 class="h4 text-primary">प्रदीप कुमार वर्मा 'नन्दवंशी'</h3>
                <p class="text-danger fw-medium">संस्थापक</p>
            </div>
            <div class="col-md-4 mb-5 text-center">
                <div class="founder-img">
                    <img src="img/founder1.jpg" alt="Founder 1">
                </div>
                <h3 class="h4 text-primary">जितेंद्र कुमार शर्मा</h3>
                <p class="text-danger fw-medium">संस्थापक</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <div class="stat-text">सक्रिय सदस्य</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <div class="stat-text">जिले</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <span class="stat-number">₹2.78L</span>
                    <div class="stat-text">वितरित सहायता</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <span class="stat-number">4</span>
                    <div class="stat-text">सहायता प्राप्त परिवार</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">हमारी टीम</h2>
        <p class="text-center text-muted mb-5">नन्दवंशी सेल्फ केयर टीम के प्रमुख सदस्य जो संगठन के विकास में महत्वपूर्ण भूमिका निभाते हैं</p>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="team-img">
                        <img src="img/sudhesh_pandey.jpeg" class="card-img-top" alt="Team 1">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">प्रभाशंकर शर्मा, श्याम जी शर्मा, आशा शर्मा, दिनेश शर्मा</h5>
                        <p class="card-text text-danger fw-medium">सह संस्थापक</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="team-img">
                        <img src="img/mahendra_varma.jpeg" class="card-img-top" alt="Team 2">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">घनश्याम शर्मा, सुरेन्द्र कुमार शर्मा, चंद्र प्रकाश सविता</h5>
                        <p class="card-text text-danger fw-medium">सदस्य कोर टीम</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="team-img">
                        <img src="img/sanjeev_rajak.jpeg" class="card-img-top" alt="Team 3">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">प्रकाश शर्मा, लाल बहादुर शर्मा, सूरज सेन</h5>
                        <p class="card-text text-danger fw-medium">प्रांतीय टीम</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Objectives Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">हमारे उद्देश्य</h2>
        <p class="text-center text-muted mb-5">नन्दवंशी सेल्फ केयर टीम के मुख्य उद्देश्य और लक्ष्य</p>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="img/mission.jpg" class="card-img-top" alt="Mission">
                    <div class="card-body">
                        <h5 class="card-title">हमारा मिशन</h5>
                        <p class="card-text">समाज के हर वर्ग के लोगों को सशक्त बनाना और उन्हें आत्मनिर्भर बनने में सहायता करना।</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="img/vision.jpg" class="card-img-top" alt="Vision">
                    <div class="card-body">
                        <h5 class="card-title">हमारा विजन</h5>
                        <p class="card-text">एक ऐसा समाज बनाना जहां हर व्यक्ति को अपनी क्षमता का पूरा उपयोग करने का अवसर मिले।</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="img/values.jpg" class="card-img-top" alt="Values">
                    <div class="card-body">
                        <h5 class="card-title">हमारे मूल्य</h5>
                        <p class="card-text">पारदर्शिता, ईमानदारी और सेवा के मूल्यों पर आधारित संगठन।</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'home/footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>