<?php
/**
 * Ekomi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 */
namespace Ekomi\EkomiIntegration\Model;

class Validate extends \Magento\Framework\App\Config\Value
{
    const XML_PATH_SHOP_ID = 'integration/general/shop_id';
    const XML_PATH_SHOP_PASSWORD = 'integration/general/shop_password';
    protected $request;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->request = $request;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return mixed
     *
     */
    public function execute()
    {
        $this->messageManager->addSuccess('Message from new admin controller.');
        return parent::execute();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function beforeSave()
    {
        $ApiUrl='http://api.ekomi.de/v3/getSettings';
        $value = $this->getValue();
        $postData = $this->request->getPostValue();
        $postValues = $postData['groups']['general']['fields'];
        $shopId = $postValues['shop_id']['value'];
        $shopPw = $postValues['shop_password']['value'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$ApiUrl."?auth=".$shopId."|".$shopPw."&version=cust-1.0.0&type=request&charset=iso");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        if($server_output=='Access denied'){
            throw new \Exception($server_output);
            $this->setValue(0);
        }
        else {
            return parent::beforeSave();
        }
    }

    /**
     * @return mixed
     */
    protected function getShopId()
    {
        return $this->_config->getValue(
            self::XML_PATH_SHOP_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    protected function getPassword()
    {
        return $this->_config->getValue(
            self::XML_PATH_SHOP_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
