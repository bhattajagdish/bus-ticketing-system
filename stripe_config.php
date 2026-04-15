<?php
// Stripe API Configuration
// Get your API keys from: https://dashboard.stripe.com/test/apikeys

define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_PUBLISHABLE_KEY_HEREpk_test_51Slk8EDEigauR4N8u8Yll3fxPYp4fo3rZrVlL4NuDnQcMG0GzGk13KRbJUcMNdVWHChdKhvc0d1T8PXQLTMZanZP00RAvlmKME');
define('STRIPE_SECRET_KEY', 'sk_test_51Slk8EDEigauR4N8AQp2XCFaEg3CnmmHx5wXSv2HpBDLK0az0EEnXYK3jNcX8J0vPmkhxXZqOa16veLPyXbPyC2H00jD8EpnAT');

// Webhook secret for verifying Stripe events
define('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET_HERE');

// Set this to your domain (for success/cancel URLs)
define('SITE_URL', 'http://localhost/bus-ticketing-system');
?>
