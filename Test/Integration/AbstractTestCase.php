<?php

namespace Algolia\AlgoliaSearch\Test\Integration;

use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected $indexPrefix = 'magento20tests_';

    public function setUp()
    {
        $this->setConfig('algoliasearch_credentials/credentials/application_id', getenv('APPLICATION_ID'));
        $this->setConfig('algoliasearch_credentials/credentials/search_only_api_key', getenv('SEARCH_ONLY_API_KEY'));
        $this->setConfig('algoliasearch_credentials/credentials/api_key', getenv('API_KEY'));

        $this->setConfig('algoliasearch_credentials/credentials/index_prefix', $this->indexPrefix);
    }

    protected function setConfig($path, $value)
    {
        $this->getObjectManager()->get('Magento\Framework\App\Config\MutableScopeConfigInterface')->setValue(
            $path,
            $value,
            ScopeInterface::SCOPE_STORE,
            'default'
        );
    }

    /** @return \Magento\Framework\ObjectManagerInterface */
    protected function getObjectManager()
    {
        return Bootstrap::getObjectManager();
    }
}
