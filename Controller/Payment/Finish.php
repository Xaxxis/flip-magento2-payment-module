<?php

namespace Flip\Checkout\Controller\Payment;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

class Finish extends AbstractAction
{

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     * @throws NotFoundException
     */
    public function execute(): ResultInterface
    {

        $orderId = $this->requestInterface->getParam('state');
        if (!$orderId) {
            return $this->redirectFactory->create()->setPath('checkout/cart');
        }

        try {
            $order = $this->orderRepository->getOrderById($orderId);

            if (!$order) return $this->redirectFactory->create()->setPath('checkout/cart');

            // Pass order details to the view
            $resultPage = $this->pageFactory->create();
            $message = ($order) ? "Payment Order Confirmation" : "Oops, something went wrong!";
            $resultPage->getConfig()->getTitle()->set(__($message));

            // Add order data to the view (via block or directly)
            $resultPage->getLayout()->getBlock('finish.page')->setData('order', $order);
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->logErrorException("Finish.php->execute(): Facing an error.", $e);
            return $this->redirectFactory->create()->setPath('checkout/cart');
        }

    }
}
