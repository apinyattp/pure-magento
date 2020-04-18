<?php

namespace Emertx\BankTransfer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Description of InstallSchema
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class InstallSchema implements InstallSchemaInterface {

	/**
	 * Installs DB schema for a module
	 *
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		

		$installer->endSetup();
	}

}
