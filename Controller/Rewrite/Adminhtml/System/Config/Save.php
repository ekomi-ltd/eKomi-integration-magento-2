<?php
/**
 * EkomiIntegration Rewrite Config Save Controller
 *
 * @category    Ekomi
 * @package     Ekomi_EkomiIntegration
 * @author      Ekomi Private Limited
 *
 */
namespace Ekomi\EkomiIntegration\Controller\Rewrite\Adminhtml\System\Config;

class Save extends \Magento\Config\Controller\Adminhtml\System\Config\Save
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->messageManager->addSuccess("Your feedback is important. Please take a moment to <a target=\"_blank\" href=\"https://marketplace.magento.com/ekomiltd-ekomiintegration.html\">review our plugin</a>");
        return parent::execute();
    }
}