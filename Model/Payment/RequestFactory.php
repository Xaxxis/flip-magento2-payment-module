<?php

namespace Flip\Checkout\Model\Payment;

use Magento\Sales\Model\Order;

/**
 * Class RequestFactory
 * Factory class for creating the payload to send to the Flip API for payment creation.
 *
 * @package Flip\Checkout\Model\Payment
 */
class RequestFactory
{
    /**
     * Prefix used in the payment title.
     */
    const ORDER_PREFIX = 'Order with ID: ';

    /**
     * Creates the payload array for sending to the Flip API.
     *
     * This method prepares an array with the necessary details of the order, such as the order ID, amount,
     * customer information, and payment redirect URL. The data is formatted as required by the Flip API for
     * creating a payment link.
     *
     * @param Order $order The order object containing details about the purchase.
     * @return array The prepared payload to be sent to the Flip API.
     */
    public function createPayload(Order $order): array
    {
        return [
            'title' => self::ORDER_PREFIX . $order->getRealOrderId(),
            'type' => "SINGLE",
            'amount' => (string)round($order->getGrandTotal()), // The amount for the payment
            'step' => 2, // Payment step, could be a value required by the Flip API
            'redirect_url' => 'https://example.com/flip/payment/finish?state=' . $order->getRealOrderId(),
            'sender_name' => $order->getCustomerName(), // Customer's full name
            'sender_email' => $order->getCustomerEmail(), // Customer's email address
        ];
    }
}
