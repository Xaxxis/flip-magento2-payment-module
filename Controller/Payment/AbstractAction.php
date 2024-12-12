<?php

namespace Flip\Checkout\Controller\Payment;

use Flip\Checkout\Logger\FlipLogger;
use Flip\Checkout\Model\Config\Payment\ModuleConfig;
use Flip\Checkout\Model\Order\OrderRepository;
use Flip\Checkout\Model\Payment\RequestFactory;
use Flip\Checkout\Service\FlipService;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface as ActionApp;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * AbstractAction class for handling payment-related actions in the Flip Checkout module.
 *
 * This abstract class serves as the base class for payment-related controllers in the Flip Checkout module.
 * It provides common dependencies and initialization for actions, including session management, JSON response generation,
 * logging, and communication with Flip Service for payment-related tasks.
 *
 * @package Flip\Checkout\Controller\Payment
 */
abstract class AbstractAction implements ActionApp
{
    /**
     * @var FlipLogger
     * Logger instance for logging actions, requests, and errors.
     */
    public FlipLogger $logger;

    /**
     * @var FlipService
     * Service class for handling payment creation and communication with Flip API.
     */
    public FlipService $flipService;

    /**
     * @var OrderRepository
     * Repository class for managing order data.
     */
    public OrderRepository $orderRepository;

    /**
     * @var Session
     * Checkout session for managing cart and order session data.
     */
    protected Session $_checkoutSession;

    /**
     * @var Context
     * Action context for accessing controller parameters and settings.
     */
    private Context $_context;

    /**
     * @var JsonFactory
     * Factory class for creating JSON response results.
     */
    private JsonFactory $_resultJsonFactory;

    /**
     * @var ResultFactory
     * Factory class for generating other types of result objects.
     */
    protected ResultFactory $_resultFactory;

    /**
     * @var ModuleConfig
     * Class to get Flip Module Config value
     */
    public ModuleConfig $flipModuleConfig;

    /**
     * @var RequestFactory
     * Factory class for creating payment request payloads.
     */
    protected RequestFactory $requestFactory;

    public RequestInterface $requestInterface;

    public RedirectFactory $redirectFactory;

    public PageFactory $pageFactory;

    /**
     * Constructor for AbstractAction class.
     *
     * @param Context $context The context for the action, which contains the request and response objects.
     * @param Session $checkoutSession The session object for managing the checkout process.
     * @param JsonFactory $resultJsonFactory The JSON result factory for generating JSON responses.
     * @param FlipService $flipService The service responsible for interacting with Flip API for payment processing.
     * @param FlipLogger $logger Logger instance for logging events and errors.
     * @param ModuleConfig $flipModuleConfig The Config class to get value config for Flip Checkout Module
     * @param OrderRepository $orderRepository The repository for managing Magento orders.
     * @param RequestFactory $requestFactory The request factory for generating payment payloads.
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        JsonFactory $resultJsonFactory,
        FlipService $flipService,
        FlipLogger $logger,
        ModuleConfig $flipModuleConfig,
        OrderRepository $orderRepository,
        RequestFactory $requestFactory,
        PageFactory $pageFactory
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_context = $context;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultFactory = $context->getResultFactory();
        $this->flipService = $flipService;
        $this->logger = $logger;
        $this->flipModuleConfig = $flipModuleConfig;
        $this->orderRepository = $orderRepository;
        $this->requestFactory = $requestFactory;
        $this->pageFactory = $pageFactory;
        $this->requestInterface = $context->getRequest();
        $this->redirectFactory = $context->getResultRedirectFactory();
    }
}
