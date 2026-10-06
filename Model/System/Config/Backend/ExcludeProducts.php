<?php
/**
 * Backend model for Exclude Products config field.
 * Enforces a maximum length of 255 characters to match the plugins-dashboard DB column limit.
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ExcludeProducts
 *
 * @package Ekomi\EkomiIntegration\Model\System\Config\Backend
 */
class ExcludeProducts extends Value
{
    const MAX_LENGTH = 255;

    /**
     * Validate the value before saving.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = (string) $this->getValue();
        if (strlen($value) > self::MAX_LENGTH) {
            throw new LocalizedException(
                __(
                    'Exclude Products must not exceed %1 characters. Current length: %2.',
                    self::MAX_LENGTH,
                    strlen($value)
                )
            );
        }

        return parent::beforeSave();
    }
}
