<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php 
    include("nav.php"); 
    include("connection.php"); 

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);

        $query = "INSERT INTO contact (name, email, phone, message) 
                  VALUES ('$name', '$email', '$phone', '$message')";

        if (mysqli_query($conn, $query)) {
            echo ("<script>
                window.alert('Your message has been sent successfully!');
                window.location.href='contact.php';
            </script>");
        } else {
            echo ("<script>
                window.alert('Failed to send your message. Please try again later.');
                window.location.href='contact.php';
            </script>");
        }
    }
    ?>

    <section class="contact-section">
        <div class="contact-container">
        <div class="contact-info">
    <h2>Get In Touch</h2>
    <p>"We're here to help! ...<br>Got a question, feedback, or facing an issue with your booking? We’d love to hear from you! Your thoughts and suggestions help us serve you better and make your travel experience more enjoyable. Feel free to reach out, and our team will get back to you as soon as possible!"</p>
    
    <h3><i class="fas fa-map-marker-alt"></i> ADDRESS</h3>
    <p>2345, Sukedhara, Kathmandu</p>
    
    <h3><i class="fas fa-phone"></i> PHONE</h3>
    <p>9821698155</p>
    
    <h3><i class="fas fa-envelope"></i> EMAIL</h3>
    <p>bhattajagdish606@gmail.com</p>

    <div class="social-links">
    <a href="https://www.facebook.com/share/14ZiNZcYXy/" target="_blank" rel="noopener noreferrer" ><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/bhattajagdish606?igsh=MTQxNnM0bDh4czV0eA==" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                <a href="https://github.com/bhattajagdish"  target="_blank" rel="noopener noreferrer"><i class="fab fa-github"></i></a>
                <a href="https://www.linkedin.com/in/jagdish-bhatta-b5133427a"  target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a>
    </div>
</div>
          
            <div class="contact-form">
                <h2>Contact Us</h2>
                <form action="#" method="post" id="contactForm">
                    <input type="text" placeholder="Name" name="name" required>
                    
                    <div class="input-group">
                        <input type="email" placeholder="Email" name="email" id="emailInput" required>
                        <span class="validation-message" id="emailMessage"></span>
                    </div>
                    
                    <div class="input-group">
                        <input type="tel" placeholder="Phone (98/97 + 8 digits)" name="phone" id="phoneInput" required maxlength="10">
                        <span class="validation-message" id="phoneMessage"></span>
                    </div>
                    
                    <textarea placeholder="Your Message" name="message" rows="5" required></textarea>
                    <button type="submit" id="submitBtn"> Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <style>
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        .validation-message {
            display: block;
            font-size: 12px;
            margin-top: 5px;
            min-height: 16px;
        }

        .validation-message.error {
            color: #f44336;
        }

        .validation-message.success {
            color: #4caf50;
        }

        #emailInput.invalid,
        #phoneInput.invalid {
            border-color: #f44336 !important;
            background-color: #ffebee !important;
            color: #333 !important;
        }

        #emailInput.valid,
        #phoneInput.valid {
            border-color: #4caf50 !important;
            background-color: #ffffff !important;
            color: #333 !important;
        }
    </style>

    <script>
        // Email validation
        const emailInput = document.getElementById('emailInput');
        const emailMessage = document.getElementById('emailMessage');
        const phoneInput = document.getElementById('phoneInput');
        const phoneMessage = document.getElementById('phoneMessage');
        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');

        // Email validation in real-time
        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === '') {
                emailMessage.textContent = '';
                emailInput.classList.remove('valid', 'invalid');
            } else if (emailRegex.test(email)) {
                emailMessage.textContent = '✓ Valid email';
                emailMessage.classList.remove('error');
                emailMessage.classList.add('success');
                emailInput.classList.remove('invalid');
                emailInput.classList.add('valid');
            } else {
                emailMessage.textContent = '✗ Invalid email format';
                emailMessage.classList.remove('success');
                emailMessage.classList.add('error');
                emailInput.classList.remove('valid');
                emailInput.classList.add('invalid');
            }
        });

        // Phone validation in real-time
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            // Phone must start with 98 or 97, followed by 8 more digits (total 10)
            const phoneRegex = /^(98|97)\d{8}$/;

            if (phone === '') {
                phoneMessage.textContent = '';
                phoneInput.classList.remove('valid', 'invalid');
            } else if (phoneRegex.test(phone)) {
                phoneMessage.textContent = '✓ Valid phone number';
                phoneMessage.classList.remove('error');
                phoneMessage.classList.add('success');
                phoneInput.classList.remove('invalid');
                phoneInput.classList.add('valid');
            } else if (phone.length < 10) {
                phoneMessage.textContent = `✗ Phone must be 10 digits (${phone.length}/10)`;
                phoneMessage.classList.remove('success');
                phoneMessage.classList.add('error');
                phoneInput.classList.remove('valid');
                phoneInput.classList.add('invalid');
            } else if (!phone.startsWith('98') && !phone.startsWith('97')) {
                phoneMessage.textContent = '✗ Phone must start with 98 or 97';
                phoneMessage.classList.remove('success');
                phoneMessage.classList.add('error');
                phoneInput.classList.remove('valid');
                phoneInput.classList.add('invalid');
            } else {
                phoneMessage.textContent = '✗ Invalid phone format';
                phoneMessage.classList.remove('success');
                phoneMessage.classList.add('error');
                phoneInput.classList.remove('valid');
                phoneInput.classList.add('invalid');
            }
        });

        // Form submission validation
        contactForm.addEventListener('submit', function(e) {
            const email = emailInput.value.trim();
            const phone = phoneInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^(98|97)\d{8}$/;

            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                emailInput.focus();
                return false;
            }

            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                alert('Phone number must start with 98 or 97 and have 10 digits total');
                phoneInput.focus();
                return false;
            }
        });

        // Allow only numbers in phone input
        phoneInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    </script>
