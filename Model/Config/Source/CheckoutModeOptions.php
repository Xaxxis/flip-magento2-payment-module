<?php

namespace Flip\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CheckoutModeOptions implements OptionSourceInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['redirect' => 1, 'label' => __('Redirect Mode')],
            ['popup' => 2, 'label' => __('Popup Mode')]
        ];
    }

    /**
     * Retrieve options as a key-value pair
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            1 => __('Redirect Mode'),
            2 => __('Popup Mode')
        ];
    }
}
