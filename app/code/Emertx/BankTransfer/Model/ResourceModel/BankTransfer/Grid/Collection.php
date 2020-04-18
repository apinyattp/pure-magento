<?php

namespace Emertx\BankTransfer\Model\ResourceModel\BankTransfer\Grid;

use Magento\Framework\Api;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;


/**
 * Description of Collection
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult {

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable,
        $resourceModel
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }
    
    protected function _beforeLoad() {
        parent::_beforeLoad();
        
        $this->getSelect()
            ->where(
                'deleted_time IS NULL AND status <> ?', \Emertx\BankTransfer\Model\BankTransfer::STATUS_CANCELED
            );
        
        return $this;
    }
}
