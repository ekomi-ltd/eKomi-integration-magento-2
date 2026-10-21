<?php
/**
 * Ekomi
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class Validate
 *
 * @package Ekomi\EkomiIntegration\Model
 */
class ValidateExcludeProduct extends \Magento\Framework\App\Config\Value
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Magento\Config\Model\ResourceModel\Config\Data::class);
    }

    /**
     * Triggers on saving configuration form
     *
     * @return \Magento\Framework\App\Config\Value
     * @throws LocalizedException
     */
    public function beforeSave(){
        $value = (string) $this->getValue();
    
        if (mb_strlen($value) > 255) {
            $errorPhrase = new Phrase('Error! Exclude Products cannot exceed more than 255 Characters');
            throw new LocalizedException($errorPhrase);
        }

        return parent::beforeSave();
    }
}