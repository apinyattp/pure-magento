<?php

namespace Emertx\BankTransfer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Description of UpgradeSchema
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class UpgradeSchema implements UpgradeSchemaInterface {

	/**
	 * {@inheritdoc}
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			
			echo 'Upgrade Emertx_BankTransfer DB Scheme to 1.0.2';
			
			$table = $setup->getConnection()
				->newTable($setup->getTable('emertx_bank_transfer'))
				->addColumn(
					'request_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Request ID'
				)
                ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null,], 'Customer ID')
				->addColumn('name', Table::TYPE_TEXT, 180, ['nullable' => true, 'default' => null, 'length' => 180], 'Name')
				->addColumn('phone', Table::TYPE_TEXT, 50, ['nullable' => true, 'default' => null, 'length' => 50], 'Phone')
				->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'Order ID (member)')
                ->addColumn('order_number', Table::TYPE_TEXT, 50, ['nullable' => true, 'default' => null, 'length' => 50], 'Order Number (manual)')
                ->addColumn('transfer_datetime', Table::TYPE_DATETIME, null, ['nullable' => false], 'Transfer Time')
                ->addColumn('slip', Table::TYPE_TEXT, 180, ['nullable' => true, 'default' => null, 'length' => 180], 'Slip filename')
                ->addColumn('bank_acc_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Bank Account ID')
                ->addColumn('status', Table::TYPE_TEXT, 20, ['nullable' => false, 'default' => 'Pending', 'length' => 20], 'Request status')
                ->addColumn('deleted_time', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Delete Time')
				->addColumn('creation_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Time')
				->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Update Time')
                ->addIndex($setup->getIdxName('emertx_banktr_idx', ['status', 'deleted_time']), ['status', 'deleted_time'])
				->setComment('Emertx Bank Transfer Request');

			$setup->getConnection()->createTable($table);
		}
        
        
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
			
			echo 'Upgrade Emertx_BankTransfer DB Scheme to 1.0.3';
			
			$table = $setup->getConnection()
				->newTable($setup->getTable('emertx_bank_account'))
				->addColumn(
					'bank_account_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Account ID'
				)
				->addColumn('bank', Table::TYPE_TEXT, 180, ['nullable' => false, 'length' => 180], 'Bank name')
				->addColumn('account_number', Table::TYPE_TEXT, 50, ['nullable' => false, 'length' => 50], 'Account number')
                ->addColumn('account_name', Table::TYPE_TEXT, 180, ['nullable' => false, 'length' => 180], 'Account name')
                ->addColumn('sort', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 9999], 'Sort')
				->addColumn('is_active', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Is Active?')
                ->addColumn('deleted_time', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Delete Time')
				->addColumn('creation_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Time')
				->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Update Time')
                ->addIndex($setup->getIdxName('emertx_bankacc_idx', ['sort', 'is_active', 'deleted_time']), ['sort', 'is_active', 'deleted_time'])
				->setComment('Emertx Bank Account');

			$setup->getConnection()->createTable($table);
		}
        
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
			
			echo 'Upgrade Emertx_BankTransfer DB Scheme to 1.0.4';
			
			$setup->getConnection()->addColumn(
                $setup->getTable('emertx_bank_transfer'),
                'comment',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'length' => 500,
                    'comment' => 'Comment',
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('emertx_bank_transfer'),
                'amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => false,
                    'comment' => 'Transfer amount',
                ]
            );
            
		}
        
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
			
			echo 'Upgrade Emertx_BankTransfer DB Scheme to 1.0.5';
            
            $setup->getConnection()->addColumn(
                $setup->getTable('emertx_bank_account'),
                'account_name_th',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'length' => 180,
                    'comment' => 'Account name (Thai)',
                ]
            );
            
        }
        
        
		$setup->endSetup();
	}

}
