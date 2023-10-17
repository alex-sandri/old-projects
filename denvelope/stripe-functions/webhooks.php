<?php
    require("../vendor/autoload.php");
    require("../php/global-vars.php");
    require("subscription-ended.php");
    require("subscription-payment-failed.php");
    require("subscription-updated.php");

    \Stripe\Stripe::setApiKey($stripeSecretAPIKey);

    $endpoint_secret = 'whsec_Xa2jg9lX2V5sHctZNCn52D0g8JP20Z9r';
    
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;
    
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
    } catch(\Stripe\Error\SignatureVerification $e) {
        // Invalid signature
        http_response_code(400);
        exit();
    }
    
    switch ($event->type) {
        case 'customer.subscription.deleted':
            //Subscription Ended
            $subscription = $event->data->object;
            handleEndedSubscription($subscription);
            break;
        case 'customer.subscription.updated':
            //Subscription Updated (Changed Plan)
            $subscription = $event->data->object;
            handleSubscriptionUpdated($subscription);
            break;
        case 'invoice.payment_failed':
            //Failed Subscription Payment
            $invoice = $event->data->object;
            handleSubscriptionPaymentFailed($invoice);
            break;
        default:
            // Unexpected event type
            http_response_code(400);
            exit();
    }
    
    http_response_code(200);
?>