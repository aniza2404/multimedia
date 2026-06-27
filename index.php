<?php require_once 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Research Archive - Multimedia Retrieval System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0e17;
            color: #e8edf5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }

        /* Navigation */
        .navbar {
            background: rgba(17, 25, 39, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(42, 58, 79, 0.5);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            left: 0;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .navbar .brand h1 {
            font-size: 1.3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4f8cff, #7c5cfc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .navbar .nav-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .navbar .nav-links a {
            color: #8b9bb5;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .navbar .nav-links a:hover {
            color: #e8edf5;
            background: rgba(36, 48, 68, 0.5);
        }

        .navbar .nav-links .btn-primary {
            background: linear-gradient(135deg, #4f8cff, #7c5cfc);
            color: white;
            padding: 0.5rem 1.5rem;
        }

        .navbar .nav-links .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(79, 140, 255, 0.3);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding-top: 80px;
            padding-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .main-content .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Hero */
        .hero {
            text-align: center;
            padding: 20px 0 30px;
            width: 100%;
        }

        .hero .badge {
            display: inline-block;
            background: rgba(79, 140, 255, 0.1);
            border: 1px solid rgba(79, 140, 255, 0.2);
            color: #4f8cff;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .hero h1 {
            font-size: 2.8rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 0.8rem;
        }

        .hero h1 .highlight {
            background: linear-gradient(135deg, #4f8cff, #7c5cfc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1rem;
            color: #8b9bb5;
            max-width: 550px;
            margin: 0 auto 1.5rem;
            line-height: 1.8;
        }

        .hero .cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.7rem 1.8rem;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f8cff, #7c5cfc);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(79, 140, 255, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid rgba(42, 58, 79, 0.5);
            color: #e8edf5;
        }

        .btn-outline:hover {
            border-color: #4f8cff;
            background: rgba(79, 140, 255, 0.05);
        }

        /* Features - HORIZONTAL */
        .features {
            padding: 20px 0 10px;
            border-top: 1px solid rgba(42, 58, 79, 0.3);
            width: 100%;
        }

        .features .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .features .header h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.3rem;
        }

        .features .header p {
            color: #8b9bb5;
            font-size: 1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            align-items: stretch;
            width: 100%;
        }

        .feature-card {
            background: #1a2332;
            border-radius: 12px;
            border: 1px solid rgba(42, 58, 79, 0.3);
            padding: 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #4f8cff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .feature-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .feature-card .icon.blue {
            background: rgba(79, 140, 255, 0.1);
            color: #4f8cff;
        }

        .feature-card .icon.green {
            background: rgba(0, 212, 170, 0.1);
            color: #00d4aa;
        }

        .feature-card .icon.orange {
            background: rgba(255, 159, 74, 0.1);
            color: #ff9f4a;
        }

        .feature-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.2rem;
        }

        .feature-card .subtitle {
            text-align: center;
            color: #4f8cff;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.8rem;
        }

        .feature-card p {
            color: #8b9bb5;
            font-size: 0.85rem;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .feature-card .details {
            list-style: none;
            padding: 0;
            margin-bottom: 1rem;
        }

        .feature-card .details li {
            padding: 0.25rem 0;
            color: #8b9bb5;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-card .details li::before {
            content: '✓';
            color: #00d4aa;
            font-weight: 700;
            flex-shrink: 0;
        }

        .feature-card .btn-try {
            display: block;
            text-align: center;
            padding: 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            margin-top: auto;
        }

        .feature-card .btn-try.blue {
            background: #4f8cff;
            color: white;
        }

        .feature-card .btn-try.blue:hover {
            background: #3a7ae8;
        }

        .feature-card .btn-try.green {
            background: #00d4aa;
            color: white;
        }

        .feature-card .btn-try.green:hover {
            background: #00b894;
        }

        .feature-card .btn-try.orange {
            background: #ff9f4a;
            color: white;
        }

        .feature-card .btn-try.orange:hover {
            background: #e8892e;
        }

        /* Footer */
        .footer {
            border-top: 1px solid rgba(42, 58, 79, 0.3);
            padding: 1.5rem 0;
            text-align: center;
            color: #5a6f8c;
            font-size: 0.85rem;
            margin-top: auto;
            width: 100%;
            background: #0a0e17;
        }

        .footer .links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 0.8rem;
            flex-wrap: wrap;
        }

        .footer .links a {
            color: #8b9bb5;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer .links a:hover {
            color: #e8edf5;
        }

        .footer .heart {
            color: #ff5a7a;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                max-width: 450px;
                margin: 0 auto;
            }

            .navbar .container {
                flex-direction: column;
                gap: 0.5rem;
            }

            .navbar .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .main-content {
                padding-top: 110px;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.6rem;
            }

            .hero .cta {
                flex-direction: column;
                align-items: center;
            }

            .hero .cta .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }

            .footer .links {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="brand">
                <span style="font-size: 1.5rem;">🔬</span>
                <h1>Research Archive</h1>
            </a>
            <div class="nav-links">
                <?php if(isLoggedIn()): ?>
                    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="cbr.php"><i class="fas fa-image"></i> CBR</a>
                    <a href="tbr.php"><i class="fas fa-file-pdf"></i> TBR</a>
                    <a href="abr.php"><i class="fas fa-music"></i> ABR</a>
                    <a href="logout.php" class="btn-primary"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php" class="btn-primary">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Hero -->
            <section class="hero">
                <div class="badge">
                    <i class="fas fa-rocket"></i> Next-Generation Multimedia Retrieval
                </div>
                
                <h1>
                    Intelligent Multimedia<br>
                    <span class="highlight">Retrieval System</span>
                </h1>
                
                <p>
                    Advanced multimedia analysis using three powerful retrieval approaches: 
                    Content-Based, Text-Based, and Attribute-Based Retrieval.
                </p>
                
                <div class="cta">
                    <?php if(isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Get Started Free
                        </a>
                        <a href="login.php" class="btn btn-outline">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Features - HORIZONTAL -->
            <section class="features">
                <div class="header">
                    <h2>Three Powerful Retrieval Approaches</h2>
                    <p>Choose the right method for your multimedia analysis needs</p>
                </div>
                
                <div class="features-grid">
                    <!-- ABR -->
                    <div class="feature-card">
                        <div class="icon blue">
                            <i class="fas fa-music"></i>
                        </div>
                        <h3>Attribute-Based Retrieval</h3>
                        <div class="subtitle">ABR</div>
                        <p>Analyze audio files to detect emotional tone and attributes.</p>
                        <ul class="details">
                            <li>Audio emotion detection</li>
                            <li>Happy, Sad, Angry, Neutral, Surprise</li>
                            <li>Confidence scoring</li>
                            <li>Audio metadata extraction</li>
                        </ul>
                        <?php if(isLoggedIn()): ?>
                            <a href="abr.php" class="btn-try blue">
                                <i class="fas fa-arrow-right"></i> Try ABR
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn-try blue">
                                <i class="fas fa-lock"></i> Login to Try
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- TBR -->
                    <div class="feature-card">
                        <div class="icon green">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h3>Text-Based Retrieval</h3>
                        <div class="subtitle">TBR</div>
                        <p>Extract text from PDF documents and detect language.</p>
                        <ul class="details">
                            <li>PDF to text extraction</li>
                            <li>Multi-language detection</li>
                            <li>Keyword extraction</li>
                            <li>OCR fidelity analysis</li>
                        </ul>
                        <?php if(isLoggedIn()): ?>
                            <a href="tbr.php" class="btn-try green">
                                <i class="fas fa-arrow-right"></i> Try TBR
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn-try green">
                                <i class="fas fa-lock"></i> Login to Try
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- CBR -->
                    <div class="feature-card">
                        <div class="icon orange">
                            <i class="fas fa-image"></i>
                        </div>
                        <h3>Content-Based Retrieval</h3>
                        <div class="subtitle">CBR</div>
                        <p>Analyze images using deep learning to classify content.</p>
                        <ul class="details">
                            <li>Image classification</li>
                            <li>Formal/Non-Formal detection</li>
                            <li>CNN-ResNet50 analysis</li>
                            <li>Confidence scoring</li>
                        </ul>
                        <?php if(isLoggedIn()): ?>
                            <a href="cbr.php" class="btn-try orange">
                                <i class="fas fa-arrow-right"></i> Try CBR
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn-try orange">
                                <i class="fas fa-lock"></i> Login to Try
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    
</body>
</html>
