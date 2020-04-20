<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Magento\Product\Websites;

/**
 * Class \Ess\M2ePro\Model\ResourceModel\Magento\Product\Websites\Update
 */
class Update extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\AbstractModel
{
    //########################################

    public function _construct()
    {
        $this->_init('m2epro_magento_product_websites_update', 'id');
    }

    //########################################
}
