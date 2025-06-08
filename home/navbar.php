<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #2c3e50, #1a252f); padding: 15px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../assets/images/ns.jpg" alt="NSCT Logo" class="rounded-circle" style="height: 45px; border: 2px solid white;">
            <span class="ms-2 fw-bold" style="font-size: 1.4rem; letter-spacing: 1px;">नन्दवंशी सेल्फ केयर टीम</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                style="box-shadow: none;">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'index') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="../index.php" 
                       style="transition: all 0.3s ease;">Home</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'about') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="about.php" 
                       style="transition: all 0.3s ease;">About</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'sadasya') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="SadasyaSuchi.php" 
                       style="transition: all 0.3s ease;">Sadasya Suchi</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'sahyog') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="SahyogList.php" 
                       style="transition: all 0.3s ease;">Sahyog Suchi</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'sanchalan') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="SanchalanSamiti.php" 
                       style="transition: all 0.3s ease;">Sanchalan Samiti</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'niyamawali') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="Niyamawali.php" 
                       style="transition: all 0.3s ease;">Niyamawali</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'contact') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="contact.php" 
                       style="transition: all 0.3s ease;">Contact</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link <?= ($current_page === 'login') ? 'active' : '' ?> px-3 py-2 rounded-pill" href="member/login.php" 
                       style="transition: all 0.3s ease;">Login</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link btn btn-danger px-3 py-2 text-white" href="member/register.php" 
                       style="transition: all 0.3s ease;">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar-dark .navbar-nav .nav-link {
        color: rgba(255,255,255,0.85);
        padding: 8px 16px;
        border-radius: 30px;
        transition: all 0.3s;
        margin: 0 5px;
    }
    
    .navbar-dark .navbar-nav .nav-link:hover {
        background-color: rgba(255,255,255,0.1);
        color: white;
    }
    
    .navbar-dark .navbar-nav .nav-link.active {
        background-color: var(--secondary);
        color: white;
    }
    
    @media (max-width: 992px) {
        .navbar-nav .nav-item {
            margin: 5px 0;
        }
    }
</style>