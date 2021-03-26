<?php
/**
 * EkomiIntegration Rewrite Config Save Controller
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Controller\Rewrite\Adminhtml\System\Config;

/**
 * Class Save
 *
 * @package Ekomi\EkomiIntegration\Controller\Rewrite\Adminhtml\System\Config
 */
class Save extends \Magento\Config\Controller\Adminhtml\System\Config\Save
{
    const MARKETPLACE_LINK = 'https://marketplace.magento.com/ekomiltd-ekomiintegration.html';
    
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $reviewAnchor = '<a target="_blank" href="' . self::MARKETPLACE_LINK . '">review our plugin</a>';
        $reviewNotification = 'Your feedback is important. Please take a moment to ' . $reviewAnchor;
        $this->messageManager->addSuccess($reviewNotification);

        return parent::execute();
    }
}
