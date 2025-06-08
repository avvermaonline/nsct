<?php
session_start();

// Set current page for navbar highlighting
$current_page = 'niyamawali';
$page_title = 'Niyamawali - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="fw-bold">नियमावली</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Niyamawali</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <h2 class="text-primary border-bottom border-danger pb-2 mb-4">नन्दवंशी सेल्फ केयर टीम - नियमावली</h2>
            
            <div class="alert alert-warning mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="alert-heading">महत्वपूर्ण सूचना</h5>
                        <p class="mb-0">नन्दवंशी सेल्फ केयर टीम के सभी सदस्यों के लिए इन नियमों का पालन करना अनिवार्य है।</p>
                    </div>
                </div>
            </div>
            
            <!-- Introduction -->
            <div class="card mb-4 border-0 bg-light">
                <div class="card-body">
                    <h3 class="h5 text-primary mb-3">परिचय</h3>
                    <p>नन्दवंशी सेल्फ केयर टीम, नंद/नाई/सेन/सविता समाज के द्वारा संचालित है। यह एक नियमावली के अंतर्गत मंच की व्यवस्था संचालित करेगा। किसी भी व्यक्ति के द्वारा मंच की सदस्यता लेने से पहले नियमावली को ध्यान से समझना अनिवार्य है।</p>
                    <ul class="mb-0">
                        <li>मंच के किसी भी मीडिया ग्रुप या किसी मीटिंग में कोई भी राजनैतिक, धार्मिक या किसी संगठन, पार्टी, दल या व्यक्ति विशेष के विरोध या समर्थन पर चर्चा नहीं होगी।</li>
                        <li>सहयोग सीधे नामिनी के खाते में भेजा जाएगा।</li>
                        <li>किसी भी प्रकार का नगद लेनदेन नहीं होगा।</li>
                    </ul>
                </div>
            </div>
            
            <!-- Section 1: Membership -->
            <div class="card mb-4 border-start border-primary border-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">1. सदस्यता</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent">इस मंच से नन्द समाज के 18 से 60 वर्ष उम्र तक के कोई भी महिला, पुरुष जुड़ सकते हैं। वैधानिकता 62 वर्ष तक रहेगी।</li>
                        <li class="list-group-item bg-transparent">NSCT में उत्तर प्रदेश का कोई भी नन्द/नाई/सेन/सविता परिवार का सदस्य गूगल फार्म/ वेबसाइट या ऐप के माध्यम से निशुल्क विधिक सदस्य बन सकता है।</li>
                        <li class="list-group-item bg-transparent">सदस्य को अपने विवरण भरने के साथ पता युक्त स्थाई आईडी (आधार कार्ड संख्या) भरना होगा। रजिस्ट्रेशन के बाद प्राप्त हुए रजिस्ट्रेशन नंबर को NSCT आईडी के रूप में उपयोग किया जाएगा।</li>
                        <li class="list-group-item bg-transparent">रजिस्ट्रेशन अंग्रेजी में सत्य तथ्यों के साथ होना चाहिये। बिमारी या अन्य तथ्य गलत होने पर मंच सहयोग की अपील निरस्त कर सकता है।</li>
                        <li class="list-group-item bg-transparent">मंच अपने पास से न तो कुछ देता है, और न ही किसी से कोई शुल्क लेता है। मंच सहयोग राशि घटने/बढ़ने हेतु जिम्मेदार नहीं होगा।</li>
                    </ul>
                </div>
            </div>
            
            <!-- Section 2: Locking Period -->
            <div class="card mb-4 border-start border-primary border-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">2. लॉकिंग पीरियड</h3>
                </div>
                <div class="card-body">
                    <p>सामान्य रूप से 01 जनवरी 2025 से जुड़ने वाले सदस्यों के लिए (5000 सदस्य संख्या तक) लॉकिंग पीरियड 02 माह का होगा।</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent">गंभीर बीमारी की स्थिति में जुड़ने वाले सदस्यों को 01 अगस्त 24 से जुड़ने वाले लोगों के लिए लॉकिंग पीरियड 06 माह होगा।</li>
                        <li class="list-group-item bg-transparent">पूर्ण दिवस रात 12 बजे तक माना जाएगा। लाकिंग अवधि में प्रत्येक विधिक सदस्य को, की गई अपीलों पर सहयोग करना अनिवार्य होगा।</li>
                        <li class="list-group-item bg-transparent">गम्भीर बीमारियों की श्रेणी में इंडियन मेडिकल एसोसिएशन द्वारा सूचीबद्ध की गई बीमारियां मान्य होंगी।</li>
                    </ul>
                </div>
            </div>
            
            <!-- Section 3: Validity -->
            <div class="card mb-4 border-start border-primary border-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">3. वैधानिकता</h3>
                </div>
                <div class="card-body">
                    <p>अनवरत रूप से वैधानिक सदस्यता बनी रखने के लिए प्रत्येक सदस्य को, की गई अपीलों पर सहयोग करना होगा।</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>स्थिति</th>
                                    <th>नियम</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>प्रथम बार सहयोग छूटने पर</td>
                                    <td>01 अगस्त 2024 से किसी विशेष परिस्थिति के कारण प्रथम बार एक से अधिक सहयोग छूटने पर पुनः किए गए सहयोग से 03 माह या 05 सहयोग, (जो पहले हो) पूर्ण होने के बाद वैधानिक माना जाएगा।</td>
                                </tr>
                                <tr>
                                    <td>दूसरी बार सहयोग छूटने पर</td>
                                    <td>01 अगस्त 2024 से दूसरी या अन्य बार सहयोग छूटने पर पुनः सहयोग करने के 06 माह और 05 सहयोग अपील दोनों पूर्ण होने पर वैधानिक माना जायेगा।</td>
                                </tr>
                                <tr>
                                    <td>सहयोग अवधि</td>
                                    <td>सहयोग की अपील प्रत्येक माह 5 तारीख से 10 तारीख के बीच किया जाएगा। सहयोग भेजने के लिए 20 दिन का अवसर दिया जाएगा।</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sections 4-7 -->
            <div class="accordion mb-4" id="rulesAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                            <strong>4. सहयोग अपील</strong>
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour" data-bs-parent="#rulesAccordion">
                        <div class="accordion-body">
                            <p>किसी भी विधिक सदस्य की मृत्यु के बाद परिजन मंच को सूचना देगें। जिसके बाद मंच भौतिक सत्यापन करा कर सहयोग की अपील करेगा।</p>
                            <ul>
                                <li>कई मृत्यु की स्थिति में मृत्यु तिथि/सत्यापन तिथि के क्रम में अपील की जाएगी।</li>
                                <li>अपील में मृतक का डिटेल और नामिनी का डिटेल दिया जाएगा।</li>
                                <li>प्रत्येक सदस्य को की गई अपीलों में दिए गए खातों में न्यूनतम ₹50 का सहयोग भेजना होगा।</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <strong>5. नामिनी</strong>
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#rulesAccordion">
                        <div class="accordion-body">
                            <p>सहयोग पूर्व निर्धारित प्रथम नामिनी के खाते में ही किया जाएगा। प्रथम नामिनी न होने पर द्वितीय नामिनी को सहयोग किया जाएगा।</p>
                            <ul>
                                <li>नामिनी की स्थाई आइडी नंबर लिखना अनिवार्य है।</li>
                                <li>दोनों नामिनी न होने की स्थिति में सदस्य पर आश्रित वैकल्पिक नामिनी क्रमशः पुत्र, पुत्री, माता, पिता, पौत्र, पौत्री या बहन को सहयोग की अपील की जाएगी।</li>
                                <li>आत्महत्या या सदस्य की हत्या की दोषी नामिनी को अथवा विवाद की स्थिति में सहयोग निलम्बित रहेगा।</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            <strong>6. धनवापसी की अपील</strong>
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#rulesAccordion">
                        <div class="accordion-body">
                            <p>किसी विधिक सदस्य द्वारा यदि भूलवश या तकनीकी कमियों के कारण अधिक धनराशि स्थानांतरित हो जाती है और दाता द्वारा एक माह के अंदर धन वापसी की मांग की जाती है तो नामिनी को अपने खाते की जांच करके 1 महीने के अंदर दान की न्यूनतम धनराशि के अलावा शेष धनराशि को उसी खाते में भेज कर मंच को सूचित करना होगा।</p>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            <strong>7. विवाद</strong>
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#rulesAccordion">
                        <div class="accordion-body">
                            <p>NSCT किसी प्रकार की सदस्यता राशि अपने पास नहीं लेता। यह केवल सहयोग का मंच प्रदान करता है। सूचनाओं का आदान प्रदान तथा रिकार्ड अपने पास रखता है। फिर भी किसी विवाद की स्थिति में न्यायिक क्षेत्र हाईकोर्ट प्रयागराज होगा।</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 8: Organizational Structure -->
            <div class="card mb-4 border-start border-primary border-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">8. संगठनात्मक ढांचा</h3>
                </div>
                <div class="card-body">
                    <p>मंच से जुड़ने वाले सभी सदस्यों के अधिकार और कर्तव्य एक समान होगें। कुछ सक्रिय सदस्यों को मंच के विस्तार हेतु निम्न प्रकार की जिम्मेदारी दी जाएगी।</p>
                    
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">संस्थापक मंडल/ प्रबंध कार्यकारिणी</h5>
                                    <p class="card-text">यह कार्यकारिणी सभी आदेशों, योजनाओं कार्यों का अंतिम निर्णायक होगी। किसी भी नई योजना या सहयोग अपील का निर्णय करेगी।</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">कोर टीम या मुख्य संचालन टीम</h5>
                                    <p class="card-text">यह कार्यकारिणी पूरी व्यवस्था की निगरानी और संचालन करेगी। तकनीकी सहायक या पदाधिकारियों का चयन करेगी।</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">जिला कार्यकारिणी</h5>
                                    <p class="card-text">यह कार्यकारिणी जिले में अपनी देखरेख में ब्लॉक/ न्यायपंचायत /स्थानीय कार्यकारिणी का गठन करेगी।</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">ब्लॉक कार्यकारिणी</h5>
                                    <p class="card-text">ब्लाक कार्यकारिणी ऊपरी कार्यकारिणी के निर्देशों के अनुसार सदस्य विस्तार, सत्यापन, सहयोग में अहम भूमिका निभाएगी।</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">स्थानीय कार्यकारिणी</h5>
                                    <p class="card-text">यह कार्यकारिणी स्थानीय स्तर पर ऊपरी टीमों के निर्देशों के क्रम में कार्य करेगी।</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sections 9-10 -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-start border-primary border-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">9. वित्तीय व्यवस्था</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-transparent">यह मंच अपने सदस्यों से किसी भी प्रकार का सदस्यता शुल्क नहीं लेगा।</li>
                                <li class="list-group-item bg-transparent">मंच के सुचारू संचालन के लिए संचालन खर्च (न्यूनतम 50 रु0 प्रति सदस्य वार्षिक) की व्यवस्था करेगा। यह ऐक्षिक होगा।</li>
                                <li class="list-group-item bg-transparent">व्यय से बचे हुए धन का 50% भाग आगामी खर्च हेतु सुरक्षित रखा जाएगा।</li>
                                <li class="list-group-item bg-transparent">सभी आय-व्यय का वार्षिक लेखा-जोखा सार्वजनिक किया जाएगा।</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-start border-primary border-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">10. सूचना का माध्यम</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-transparent">सूचनाओं के लिए टेलीग्राम का "NSCT" ग्रुप विधिक माध्यम होगा।</li>
                                <li class="list-group-item bg-transparent">इसके अलावा अन्य माध्यम जैसे व्हाट्सएप, फेसबुक, पेज, ट्विटर आदि के माध्यम से भी सूचनाओं का आदान प्रदान किया जाएगा।</li>
                                <li class="list-group-item bg-transparent">किसी मृत्यु की स्थिति में सदस्य के परिजनों द्वारा मंच को सूचना ग्रुप में या फोन से देना होगा।</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-4">
                <p class="mb-0">यह आरम्भिक नियमावली है। समय के साथ मंच और सदस्यों के हित को ध्यान रखते हुए, मूल उद्देश्य अप्रभावित रखते हुए संस्थापक मंडल द्वारा संशोधन परिवर्धन या विलोपन किया जाएगा।</p>
            </div>
            
            <div class="mt-5 text-center">
                <p class="mb-0"><strong>निवेदक:</strong> नन्दवंशी सेल्फ केयर टीम उत्तर-प्रदेश</p>
                <p class="text-muted small">अंतिम संशोधन: 15 जनवरी, 2023</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
