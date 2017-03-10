<?php

namespace Algolia\AlgoliaSearch\Test\Integration;

use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Model\Indexer\Product;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Framework\Indexer\ActionInterface;

class ProductsIndexingTest extends AbstractTestCase
{
    public function testOnlyOnStockProducts()
    {
        $this->setConfig('cataloginventory/options/show_out_of_stock', 0);

        $this->setOneProductOutOfStock();

        /** @var Product $indexer */
        $indexer = $this->getObjectManager()->create('\Algolia\AlgoliaSearch\Model\Indexer\Product');

        $this->processTest($indexer, 'products', 185);
    }

    public function testIncludingOutOfStock()
    {
        $this->setConfig('cataloginventory/options/show_out_of_stock', 1);

        $this->setOneProductOutOfStock();

        /** @var Product $indexer */
        $indexer = $this->getObjectManager()->create('\Algolia\AlgoliaSearch\Model\Indexer\Product');

        $this->processTest($indexer, 'products', 186);
    }

    protected function processTest(ActionInterface $indexer, $indexSuffix, $expectedNbHits)
    {
        /** @var ConfigHelper $config */
        $config = $this->getObjectManager()->create('Algolia\AlgoliaSearch\Helper\ConfigHelper');
        $indexPrefix = $config->getIndexPrefix();

        /** @var AlgoliaHelper $algoliaHelper */
        $algoliaHelper = $this->getObjectManager()->create('Algolia\AlgoliaSearch\Helper\AlgoliaHelper');

        $algoliaHelper->clearIndex($indexPrefix.'default_'.$indexSuffix);

        $indexer->executeFull();

        $algoliaHelper->waitLastTask();

        $resultsDefault = $algoliaHelper->query($indexPrefix.'default_'.$indexSuffix, '', array());

        $this->assertEquals($expectedNbHits, $resultsDefault['nbHits']);
    }

    private function setOneProductOutOfStock()
    {
        /** @var StockRegistry $stockRegistry */
        $stockRegistry = $this->getObjectManager()->create('Magento\CatalogInventory\Model\StockRegistry');
        $stockItem = $stockRegistry->getStockItemBySku('24-MB01');
        $stockItem->setIsInStock(false);
        $stockRegistry->updateStockItemBySku('24-MB01', $stockItem);
    }
}
