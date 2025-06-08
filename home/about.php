<?php
session_start();

// Set current page for navbar highlighting
$current_page = 'about';
$page_title = 'About Us - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <h2 class="text-primary border-bottom border-danger pb-2 mb-4">About NSCT</h2>
            <div class="about-content">
                <p>नन्दवंशी सेल्फ केयर टीम (NSCT) एक समर्पित संगठन है जो नाई समाज के विभिन्न वर्गों के लोगों की सहायता और सशक्तिकरण के लिए काम करता है। हमारा उद्देश्य सामाजिक कल्याण और सामुदायिक विकास को बढ़ावा देना है।</p>
                
                <p>NSCT की स्थापना वर्ष 2021 में प्रदीप कुमार वर्मा 'नन्दवंशी' और जितेंद्र कुमार शर्मा द्वारा की गई थी। तब से, हमने कई सामाजिक पहल की हैं और जरूरतमंद लोगों को सहायता प्रदान की है।</p>
                
                <p>हमारा संगठन पारदर्शिता, ईमानदारी और सेवा के मूल्यों पर आधारित है। हम अपने सदस्यों के बीच एकता और भाईचारे को बढ़ावा देते हैं, और समाज के कल्याण के लिए निरंतर प्रयास करते हैं।</p>
                
                <div class="row mt-5 gx-4">
                    <div class="col-md-6 mb-4">
                        <div class="bg-light p-4 h-100 border-start border-danger border-4 rounded-3">
                            <h3 class="text-primary mb-3">हमारा मिशन</h3>
                            <p class="mb-0">समाज के हर वर्ग के लोगों को सशक्त बनाना और उन्हें आत्मनिर्भर बनने में सहायता करना। हम शिक्षा, स्वास्थ्य और आर्थिक सहायता के माध्यम से समुदायों का समर्थन करते हैं।</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="bg-light p-4 h-100 border-start border-danger border-4 rounded-3">
                            <h3 class="text-primary mb-3">हमारा विजन</h3>
                            <p class="mb-0">एक ऐसा समाज बनाना जहां हर व्यक्ति को अपनी क्षमता का पूरा उपयोग करने का अवसर मिले और जहां सामाजिक न्याय और समानता हो।</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <h2 class="text-primary text-center mb-5">हमारी टीम</h2>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4 text-center">
                    <div class="founder-img">
                        <img src="img/founder2.jpg" alt="Founder 2" class="img-fluid">
                    </div>
                    <h3 class="h4 text-primary mt-3">प्रदीप कुमार वर्मा 'नन्दवंशी'</h3>
                    <p class="text-danger fw-medium">संस्थापक</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 text-center">
                    <div class="founder-img">
                        <img src="img/founder1.jpg" alt="Founder 1" class="img-fluid">
                    </div>
                    <h3 class="h4 text-primary mt-3">जितेंद्र कुमार शर्मा</h3>
                    <p class="text-danger fw-medium">संस्थापक</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 text-center">
                    <div class="founder-img">
                        <img src="img/team1.jpg" alt="Team Member" class="img-fluid">
                    </div>
                    <h3 class="h4 text-primary mt-3">प्रभाशंकर शर्मा</h3>
                    <p class="text-danger fw-medium">सह संस्थापक</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .founder-img {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto;
        border: 5px solid #e74c3c;
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
</style>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>