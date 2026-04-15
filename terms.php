<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - Bus Ticketing System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-top: 60px;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1a237e;
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-align: center;
        }

        h2 {
            color: #0d47a1;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.5rem;
            border-bottom: 3px solid #ffd600;
            padding-bottom: 10px;
        }

        p {
            color: #333;
            line-height: 1.8;
            margin-bottom: 15px;
            text-align: justify;
        }

        ul, ol {
            margin: 15px 0 15px 30px;
            color: #333;
            line-height: 1.8;
        }

        li {
            margin-bottom: 10px;
        }

        .last-updated {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

     
        .footer p {
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px;
            }

            h1 {
                font-size: 1.8rem;
            }

            h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

<?php include("nav.php"); ?>

<div class="container">
    <h1>Terms & Conditions</h1>

    <h2>1. Introduction</h2>
    <p>
        Welcome to Bus Ticketing System ("Yatriva"). These Terms and Conditions govern your access to and use of our website and services. By accessing or using our bus ticketing platform, you agree to be bound by these terms. If you do not agree to any part of these terms, you may not use our services.
    </p>

    <h2>2. Services Overview</h2>
    <p>
        Bus Ticketing System provides an online platform for users to search, book, and purchase bus tickets for travel across Nepal. Our services include:
    </p>
    <ul>
        <li>Real-time bus availability and route information</li>
        <li>Online ticket booking and reservation system</li>
        <li>Secure payment processing</li>
        <li>Digital ticket downloads and confirmations</li>
        <li>Customer support and booking management</li>
    </ul>

    <h2>3. User Responsibilities</h2>
    <p>
        As a user of our platform, you agree to:
    </p>
    <ul>
        <li>Provide accurate and truthful information during registration and booking</li>
        <li>Maintain the confidentiality of your account credentials</li>
        <li>Not share your login information with anyone else</li>
        <li>Be responsible for all activities conducted through your account</li>
        <li>Use the platform only for lawful purposes</li>
        <li>Not attempt to gain unauthorized access to our systems</li>
        <li>Comply with all applicable laws and regulations</li>
    </ul>

    <h2>4. Booking & Payment</h2>
    <p>
        <strong>Ticket Booking:</strong> All bookings made through our platform are subject to availability. We reserve the right to cancel or modify bookings in case of discrepancies or system errors.
    </p>
    <p>
        <strong>Payment Terms:</strong> Payment must be completed before ticket confirmation. We accept multiple payment methods including credit cards, debit cards, and online banking through our secure Stripe payment gateway.
    </p>
    <p>
        <strong>Booking Confirmation:</strong> Once payment is processed successfully, you will receive a booking confirmation email with your ticket details. Please save this confirmation for your reference.
    </p>

    <h2>5. Ticket Cancellation Policy</h2>
    <p>
        <strong>Cancellation Window:</strong> Users can cancel their booked tickets up to <strong>6 hours before the scheduled departure time</strong> of their bus.
    </p>
    <ul>
        <li>To cancel a ticket, log in to your account and navigate to "Manage Booking" section</li>
        <li>Select the booking you wish to cancel and click the "Cancel Ticket" button</li>
        <li>A cancellation confirmation will be sent to your registered email address</li>
        <li>Cancellations requested after 6 hours before departure will <strong>NOT</strong> be processed</li>
        <li>No-show passengers (passengers who do not board the bus) forfeit their ticket and cannot request refunds</li>
    </ul>
    <p>
        <strong>Refund Process:</strong> Upon successful cancellation within the 6-hour window, refunds will be credited to your original payment method within 7-10 business days. The refund amount will be the full ticket price minus any applicable transaction fees.
    </p>
    <p>
        <strong>Special Cases:</strong> In case of bus operator cancellations or extreme circumstances (natural disasters, accidents, etc.), full refunds will be issued immediately or alternative bus services will be provided at no extra cost.
    </p>

    <h2>6. Ticket Validity & Travel</h2>
    <ul>
        <li>Tickets are valid for the date and route specified on the confirmation</li>
        <li>Passengers must board the bus at the designated boarding point at least 15 minutes before departure</li>
        <li>Boarding at unauthorized locations or times may result in denial of travel</li>
        <li>Ticket transfers to other passengers are not permitted unless explicitly allowed by the bus operator</li>
        <li>Original ID proof must be carried during travel for verification purposes</li>
        <li>Lost or damaged tickets cannot be replaced; digital copies should be retained</li>
    </ul>

    <h2>7. Limitation of Liability</h2>
    <p>
        Bus Ticketing System is an intermediary platform connecting passengers with bus operators. We are not responsible for:
    </p>
    <ul>
        <li>Delays, cancellations, or disruptions caused by bus operators</li>
        <li>Accidents, injuries, or loss of belongings during travel</li>
        <li>Changes in routes, schedules, or bus operators' policies</li>
        <li>Weather-related or unforeseen circumstances affecting travel</li>
        <li>Lost or corrupted digital tickets due to device malfunction</li>
    </ul>
    <p>
        The bus operator remains solely responsible for passenger safety and service delivery. All disputes regarding travel should be directed to the respective bus operator.
    </p>

    <h2>8. Intellectual Property Rights</h2>
    <p>
        All content, design, graphics, and materials on our website are the intellectual property of Bus Ticketing System or our content providers. You may not reproduce, distribute, or transmit any content without our prior written consent. Unauthorized use may result in legal action.
    </p>

    <h2>9. Privacy & Data Protection</h2>
    <p>
        Your personal information is collected and processed in accordance with our Privacy Policy. We use industry-standard security measures to protect your data, including:
    </p>
    <ul>
        <li>SSL encryption for data transmission</li>
        <li>Secure payment gateway integration</li>
        <li>Regular security audits and updates</li>
        <li>Limited access to personal information</li>
    </ul>
    <p>
        We will never share your personal information with third parties without your consent, except as required by law.
    </p>

    <h2>10. Prohibited Activities</h2>
    <p>
        Users must not engage in the following activities:
    </p>
    <ul>
        <li>Hacking, phishing, or attempting unauthorized access to our systems</li>
        <li>Creating fake accounts or impersonating others</li>
        <li>Posting offensive, abusive, or defamatory content</li>
        <li>Spamming or sending unsolicited messages</li>
        <li>Attempting to manipulate prices or availability</li>
        <li>Engaging in fraudulent booking or payment practices</li>
        <li>Reselling tickets for profit without authorization</li>
    </ul>
    <p>
        Violation of these rules may result in account suspension or legal action.
    </p>

    <h2>11. Website Availability & Maintenance</h2>
    <p>
        We strive to maintain our website 24/7; however, we do not guarantee uninterrupted service. The website may be temporarily unavailable for maintenance, updates, or unforeseen technical issues. We will make reasonable efforts to minimize downtime and notify users of scheduled maintenance in advance.
    </p>

    <h2>12. Third-Party Links & Services</h2>
    <p>
        Our website may contain links to third-party websites, including the Stripe payment gateway. We are not responsible for the content, policies, or practices of these external sites. Users access third-party sites at their own risk and should review their respective terms and conditions.
    </p>

    <h2>13. Dispute Resolution</h2>
    <p>
        In case of disputes or complaints, please contact our support team at bhattajagdish606@gmail.com. We aim to resolve all issues amicably within 7 business days. If a resolution cannot be reached, disputes may be escalated to legal proceedings as per Nepal law.
    </p>

    <h2>14. Modification of Terms</h2>
    <p>
        We reserve the right to modify these Terms and Conditions at any time. Changes will be posted on this page with an updated "Last Updated" date. Your continued use of the platform after modifications constitutes acceptance of the new terms. We recommend reviewing this page periodically.
    </p>

    <h2>15. Governing Law</h2>
    <p>
        These Terms and Conditions are governed by and construed in accordance with the laws of Nepal. Both parties agree to submit to the jurisdiction of the courts located in Kathmandu, Nepal.
    </p>

    <h2>16. Contact Information</h2>
    <p>
        For any questions or concerns regarding these Terms and Conditions, please contact us:
    </p>
    <ul>
        <li><strong>Email:</strong> bhattajagdish606@gmail.com</li>
        <li><strong>Phone:</strong> +977 9821698155</li>
        <li><strong>Location:</strong> Kathmandu, Nepal</li>
    </ul>

    <div class="last-updated">
        <p>Last Updated: January 29, 2026</p>
    </div>
</div>

<?php 
// Include footer from home.php or create inline footer
?>

<footer class="footer" style="background: #1a237e; color: white; text-align: center; padding: 20px; margin-top: 30px;">
    <p>&copy; 2026 Bus Ticketing System. All rights reserved.</p>
</footer>

</body>
</html>
