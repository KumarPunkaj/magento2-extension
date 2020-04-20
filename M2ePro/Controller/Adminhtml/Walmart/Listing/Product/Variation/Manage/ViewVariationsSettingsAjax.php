<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Walmart\Listing\Product\Variation\Manage;

use Ess\M2ePro\Controller\Adminhtml\Walmart\Main;

/**
 * Class \Ess\M2ePro\Controller\Adminhtml\Walmart\Listing\Product\Variation\Manage\ViewVariationsSettingsAjax
 */
class ViewVariationsSettingsAjax extends Main
{
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');

        if (empty($productId)) {
            return $this->getResponse()->setBody('You should provide correct parameters.');
        }

        $settings = $this->createBlock('Walmart_Listing_Product_Variation_Manage_Tabs_Settings_Form');
        $settings->setListingProduct($this->walmartFactory->getObjectLoaded('Listing\Product', $productId));

        $this->setJsonContent([
            'html' => $settings->toHtml()
        ]);
        return $this->getResult();
    }
}
