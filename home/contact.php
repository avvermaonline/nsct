<?php
session_start();

// Set current page for navbar highlighting
$current_page = 'contact';
$page_title = 'Contact Us - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="fw-bold">संपर्क करें</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <h2 class="text-primary border-bottom border-danger pb-2 mb-4">संपर्क जानकारी</h2>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h5>पता</h5>
                            <p class="mb-0"> मेन स्ट्रीट, प्रयागराज, उत्तर प्रदेश, भारत</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h5>फोन नंबर</h5>
                            <p class="mb-0"><a href="tel:7071677676" class="text-decoration-none">7071677676</a></p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h5>ईमेल</h5>
                            <p class="mb-0"><a href="mailto:nsctwork@gmail.com" class="text-decoration-none">nsctwork@gmail.com</a></p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h5>कार्यालय समय</h5>
                            <p class="mb-0">सोमवार - शनिवार: सुबह 10:00 बजे - शाम 6:00 बजे</p>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h5 class="mb-3">हमें सोशल मीडिया पर फॉलो करें</h5>
                        <div class="d-flex">
                            <a href="http://facebook.com/nandvanshiselfcareteam" class="btn btn-outline-primary me-2" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="http://twitter.com/nandvanshiselfcareteam" class="btn btn-outline-info me-2" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="http://instagram.com/nandvanshiselfcareteam" class="btn btn-outline-danger me-2" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="http://youtube.com/nandvanshiselfcareteam" class="btn btn-outline-danger" title="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <h2 class="text-primary border-bottom border-danger pb-2 mb-4">संपर्क फॉर्म</h2>
                    
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">आपका नाम <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">ईमेल <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">फोन नंबर</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">विषय <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">संदेश <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">संदेश भेजें</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm border-0 rounded-3 mt-2">
        <div class="card-body p-4">
            <h2 class="text-primary border-bottom border-danger pb-2 mb-4">हमारा स्थान</h2>
            <div class="ratio ratio-16x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d227748.99973450298!2d80.80242945!3d26.848522749999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399bfd991f32b16b%3A0x93ccba8909978be7!2sLucknow%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1651234567890!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('आपका संदेश भेज दिया गया है। हम जल्द ही आपसे संपर्क करेंगे।');
        this.reset();
    });
</script>
</body>
</html>