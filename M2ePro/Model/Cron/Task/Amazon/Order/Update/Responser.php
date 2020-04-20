<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Cron\Task\Amazon\Order\Update;

/**
 * Class \Ess\M2ePro\Model\Cron\Task\Amazon\Order\Update\Responser
 */
class Responser extends \Ess\M2ePro\Model\Amazon\Connector\Orders\Update\ItemsResponser
{
    /** @var \Ess\M2ePro\Model\Order $order */
    protected $order = [];

    //########################################

    public function __construct(
        \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Amazon\Factory $amazonFactory,
        \Ess\M2ePro\Model\ActiveRecord\Factory $activeRecordFactory,
        \Ess\M2ePro\Model\Connector\Connection\Response $response,
        \Ess\M2ePro\Helper\Factory $helperFactory,
        \Ess\M2ePro\Model\Factory $modelFactory,
        array $params = []
    ) {
        parent::__construct($amazonFactory, $activeRecordFactory, $response, $helperFactory, $modelFactory, $params);

        $this->order = $this->amazonFactory->getObjectLoaded('Order', $params['order']['order_id']);
    }

    //########################################

    /**
     * @param $messageText
     * @return void|null
     */
    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        $this->order->getLog()->setInitiator(\Ess\M2ePro\Helper\Data::INITIATOR_EXTENSION);
        $this->order->addErrorLog('Amazon Order status was not updated. Reason: %msg%', ['msg' => $messageText]);
    }

    //########################################

    /**
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    protected function processResponseData()
    {
        /** @var \Ess\M2ePro\Model\Order\Change $orderChange */
        $orderChange = $this->activeRecordFactory->getObject('Order\Change')->load($this->params['order']['change_id']);
        $this->order->getLog()->setInitiator($orderChange->getCreatorType());
        $orderChange->delete();

        $responseData = $this->getResponse()->getResponseData();

        // Check separate messages
        //----------------------
        $isFailed = false;

        /** @var \Ess\M2ePro\Model\Connector\Connection\Response\Message\Set $messagesSet */
        $messagesSet = $this->modelFactory->getObject('Connector_Connection_Response_Message_Set');
        $messagesSet->init($responseData['messages']);

        foreach ($messagesSet->getEntities() as $message) {
            if ($message->isError()) {
                $isFailed = true;

                $this->order->addErrorLog(
                    'Amazon Order status was not updated. Reason: %msg%',
                    ['msg' => $message->getText()]
                );
            } else {
                $this->order->addWarningLog($message->getText());
            }
        }

        if ($isFailed) {
            return;
        }

        $this->order->addSuccessLog('Amazon Order status was updated to Shipped.');

        if (empty($this->params['order']['tracking_number']) || empty($this->params['order']['carrier_name'])) {
            return;
        }

        $this->order->addSuccessLog(
            'Tracking number "%num%" for "%code%" has been sent to Amazon.',
            [
                '!num' => $this->params['order']['tracking_number'],
                'code' => $this->params['order']['carrier_name']
            ]
        );
    }

    //########################################
}
