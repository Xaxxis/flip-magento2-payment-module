<?php

namespace Flip\Checkout\Model\Order;

use Exception;
use Flip\Checkout\Logger\FlipLogger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;

/**
 * Class InvoiceRepository
 *
 * Handles the creation and management of invoices for Flip Checkout orders.
 * Provides functionality to generate invoices and save them to the Magento system.
 *
 * @package Flip\Checkout\Model\Order
 */
class InvoiceRepository
{
    /**
     * @var OrderRepository
     * Repository for managing Flip Checkout orders.
     */
    protected OrderRepository $flipOrderRepository;

    /**
     * @var FlipLogger
     * Logger instance for logging Flip Checkout-specific operations.
     */
    private FlipLogger $logger;

    /**
     * @var Invoice
     * Model instance for working with Magento invoices.
     */
    protected Invoice $invoice;

    /**
     * @var InvoiceService
     * Service class for preparing and managing invoices.
     */
    protected InvoiceService $invoiceService;

    /**
     * @var InvoiceRepositoryInterface
     * Magento's invoice repository interface for saving invoice data.
     */
    private InvoiceRepositoryInterface $magentoInvoiceRepository;

    /**
     * @var MessageManagerInterface
     * Interface for managing system messages displayed to the user.
     */
    protected MessageManagerInterface $messageManager;

    /**
     * InvoiceRepository constructor.
     *
     * @param OrderRepository $flipOrderRepository Repository for managing Flip Checkout orders.
     * @param FlipLogger $logger Logger instance for debugging and error tracking.
     * @param Invoice $invoice Invoice model for handling invoice operations.
     * @param InvoiceService $invoiceService Service class for preparing invoices.
     * @param InvoiceRepositoryInterface $magentoInvoiceRepository Interface for saving invoices.
     * @param MessageManagerInterface $messageManager Manager for displaying messages in the Magento admin.
     */
    public function __construct(
        OrderRepository $flipOrderRepository,
        FlipLogger $logger,
        Invoice $invoice,
        InvoiceService $invoiceService,
        InvoiceRepositoryInterface $magentoInvoiceRepository,
        MessageManagerInterface $messageManager
    ) {
        $this->flipOrderRepository = $flipOrderRepository;
        $this->logger = $logger;
        $this->invoice = $invoice;
        $this->invoiceService = $invoiceService;
        $this->magentoInvoiceRepository = $magentoInvoiceRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * Create and save an invoice for a given order.
     *
     * This method handles the invoice generation process for a Magento order.
     * It checks if the order allows invoice creation, prepares the invoice,
     * and saves it to the Magento system. If an error occurs, it logs the
     * error and displays an error message to the admin user.
     *
     * @param Order $order The Magento order for which to create an invoice.
     * @return void
     */
    public function createInvoice(Order $order): void
    {
        try {
            if ($order->isEmpty()) {
                $this->logger->logErrorException("InvoiceRepository.class->createInvoice(): The order no longer exists.");
            }
            if (!$order->canInvoice()) {
                $this->logger->logErrorException("InvoiceRepository.class->createInvoice(): The order does not allow an invoice to be created.");
            }

            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice) {
                $this->logger->logErrorException("InvoiceRepository.class->createInvoice(): We can\'t save the invoice right now.");
            }
            if (!$invoice->getTotalQty()) {
                $this->logger->logErrorException("InvoiceRepository.class->createInvoice(): You can\'t create an invoice without products.");
            }

            if ($order->getExtOrderId()) {
                $invoice->setTransactionId($order->getExtOrderId());
                $order->getPayment()->setLastTransId($order->getExtOrderId());
            }
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(true);

            $this->magentoInvoiceRepository->save($invoice);
            $this->flipOrderRepository->saveOrder($order);
        } catch (Exception $e) {
            $this->logger->logErrorException("InvoiceRepository.class->createInvoice():Facing an error during create Invoice", $e);
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
