<?php

namespace Flip\Checkout\Controller\Payment;

use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\Order;

/**
 * Class Checkout
 * Handles the creation of a payment link using the Flip API during the checkout process.
 *
 * @package Flip\Checkout\Controller\Payment
 */
class Checkout extends AbstractAction
{
    /**
     * Executes the main logic for creating a payment link.
     *
     * This method retrieves the last real order from the checkout session, generates a payload for the Flip API,
     * creates a payment link, and updates the order with the necessary information. If an error occurs,
     * it logs the error and returns a JSON response indicating failure.
     *
     * @return ResultInterface JSON response with payment link information or error details.
     *
     * @throws \Exception If an error occurs during API interaction or order processing.
     */
    public function execute(): ResultInterface
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $resultJson = $this->_resultFactory->create('json');
        $this->logger->logCallback('testtest');
        try {
            // Create payload and call the Flip API
            $payload = $this->requestFactory->createPayload($order);
            $responseApi = $this->flipService->createBill($payload);

            // Prepare successful response data
            $response = [
                'status_code' => 201,
                'status' => 'success',
                'message' => 'Link payment successfully created',
                'payment_url' => 'https://' . str_replace('\/', '/', $responseApi['link_url'])
            ];

            // Update the order with Flip API response data
            $order->setExtOrderId($this->flipModuleConfig->getBusinessId() . '-' . $responseApi['link_id']);
            $order->addCommentToStatusHistory(
                "<strong style='color: green;'>Flip Link Payment Created Successfully!</strong><br>" .
                "- Link ID: {$responseApi['link_id']}<br>- Status: {$responseApi['status']}"
            );

            // Update payment information and save the order
            $this->orderRepository->setAdditionalPaymentInfo($order, key: 'payment_url', value: $response['payment_url'] );
            $this->orderRepository->saveOrder($order);

            // Return success response
            return $resultJson->setData($response);
        } catch (\Exception $e) {
            // Log the error and return error response
            $this->logger->logErrorException("Checkout.class->execute(): Facing an error on Checkout controller. Message", $e);
            return $resultJson->setData([
                'status_code' => 500,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
