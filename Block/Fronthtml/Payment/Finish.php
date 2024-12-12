<?php

namespace Flip\Checkout\Block\Fronthtml\Payment;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class Finish extends Template
{
    protected CustomerSession $customerSession;
    protected UrlInterface $urlBuilder;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get Order Data
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->getData('order');
    }

    /**
     * Get the customer session
     *
     * @return CustomerSession
     */
    public function getCustomerSession(): CustomerSession
    {
        return $this->customerSession;
    }

    /**
     * Get the order view URL
     *
     * @param int|string $orderId
     * @return string
     */
    public function getOrderViewUrl(int|string $orderId): string
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }
}
