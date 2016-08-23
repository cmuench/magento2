<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AsynchronousOperations\Model;

use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterface;

class BulkStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AsynchronousOperations\Model\BulkStatus
     */
    private $model;

    protected function setUp()
    {
        $this->model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\AsynchronousOperations\Model\BulkStatus::class
        );
    }

    /**
     * @magentoDataFixture Magento/AsynchronousOperations/_files/bulk.php
     */
    public function testGetBulkStatus()
    {
        $this->assertEquals(BulkSummaryInterface::NOT_STARTED, $this->model->getBulkStatus('bulk-uuid-1'));
        $this->assertEquals(BulkSummaryInterface::IN_PROGRESS, $this->model->getBulkStatus('bulk-uuid-2'));
        $this->assertEquals(BulkSummaryInterface::FINISHED_SUCCESSFULLY, $this->model->getBulkStatus('bulk-uuid-4'));
        $this->assertEquals(BulkSummaryInterface::FINISHED_WITH_FAILURE, $this->model->getBulkStatus('bulk-uuid-5'));
    }

    /**
     * @magentoDataFixture Magento/AsynchronousOperations/_files/bulk.php
     */
    public function testGetBulksByUser()
    {
        /** @var \Magento\AsynchronousOperations\Model\BulkSummary[] $bulks */
        $bulksUuidArray = ['bulk-uuid-1', 'bulk-uuid-2', 'bulk-uuid-3', 'bulk-uuid-4', 'bulk-uuid-5'];
        $bulks =  $this->model->getBulksByUser(1);
        $this->assertEquals(5, count($bulks));
        foreach ($bulks as $bulk) {
            $this->assertTrue(in_array($bulk->getBulkId(), $bulksUuidArray));
        }
    }

    /**
     * @magentoDataFixture Magento/AsynchronousOperations/_files/bulk.php
     */
    public function testGetFailedOperationsByBulkId()
    {
        /** @var  \Magento\AsynchronousOperations\Api\Data\OperationInterface[] $operations */
        $operations =  $this->model->getFailedOperationsByBulkId('bulk-uuid-1');
        $this->assertEquals([], $operations);
        $operations =  $this->model->getFailedOperationsByBulkId('bulk-uuid-5', 3);
        foreach ($operations as $operation) {
            $this->assertEquals(1111, $operation->getErrorCode());
        }
        $operations =  $this->model->getFailedOperationsByBulkId('bulk-uuid-5', 2);
        foreach ($operations as $operation) {
            $this->assertEquals(2222, $operation->getErrorCode());
        }
    }
}
