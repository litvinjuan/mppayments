<?php

namespace litvinjuan\LaravelPayments\Handlers;

use litvinjuan\LaravelPayments\Exceptions\InvalidGatewayException;
use litvinjuan\LaravelPayments\Exceptions\InvalidRequestException;
use litvinjuan\LaravelPayments\Gateways\GatewayFactory;
use litvinjuan\LaravelPayments\Payments\Payment;

class PurchasePaymentHandler
{

    /**
     * @param Payment $payment
     * @return Payment
     * @throws InvalidRequestException
     * @throws InvalidGatewayException
     */
    public function handle(Payment $payment): Payment
    {
        // Create Gateway from Factory
        $gateway = GatewayFactory::make($payment);

        // Verify the gateway supports this method
        if (! $gateway->supportsPurchase()) {
            throw InvalidRequestException::notSupported();
        }

        // Create and send the request
        $response = $gateway->purchase()->payment($payment)->send();

        // Save response data
        $payment->gateway_id = $response->getGatewayId();
        $payment->state = $response->getState();
        $payment->save();

        return $payment;
    }

}
