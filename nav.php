<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticketing System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        nav {
            background: linear-gradient(135deg, #0d94ee, #149fe4);
            padding: 0 2rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
    width: 50px;  /* Adjust based on your logo size */
    height: 50px; /* Maintain aspect ratio */
     /* Remove default inline spacing */
    object-fit: contain; 
        }

        .brand-text {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ffd600;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: #ffd600;
        }

        .menu-toggle {
            display: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            nav {
                padding: 0 1rem;
            }
            
            .nav-links {
                position: fixed;
                top: 60px;
                right: -100%;
                width: 60%;
                height: calc(100vh - 70px);
                background: #1a237e;
                flex-direction: column;
                align-items: center;
                padding: 2rem 0;
                transition: 0.3s ease;
                box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            }

            .nav-links.active {
                right: 0;
            }

            .menu-toggle {
                display: block;
            }

            .brand-text {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="brand">
            <img src="image/logo.png" alt="Logo" class="logo">
            <span class="brand-text">Bus Ticketing System</span>
        </div>
        
        <ul class="nav-links">
        <li><a href="home.php"><i class="fa-solid fa-home"></i> Home</a></li>
        <li>
        <a href="AboutUs.php"> <i class="fa-solid fa-circle-info"></i> About Us</a></li>
        <li>
        <a href="login-menu.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
        <li>
        <a href="contact.php"><i class="fa-solid fa-phone"></i> Contact Us</a></li>
        <li><a href="Service.php"><i class="fa-solid fa-concierge-bell"></i> Service</a></li>
        </ul>
        
        <div class="menu-toggle">
            <i class="fas fa-bars"></i> <!-- Add Font Awesome for icons -->
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Close menu when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !menuToggle.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
    
   
</body>
</html>
     
 