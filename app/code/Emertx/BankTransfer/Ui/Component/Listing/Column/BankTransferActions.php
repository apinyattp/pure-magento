<?php

namespace Emertx\BankTransfer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;


/**
 * Description of BankTransferActions
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankTransferActions extends Column {

	/** Url path */
	const ACTION_URL_PATH_APPROVE = 'emertx_bank_transfer/manage/approve';
	const ACTION_URL_PATH_CANCEL = 'emertx_bank_transfer/manage/cancel';

	/** @var UrlInterface */
	protected $urlBuilder;

	/**
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param UrlInterface $urlBuilder
	 * @param array $components
	 * @param array $data
	 * @param string $editUrl
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = []
	) {
		$this->urlBuilder = $urlBuilder;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as & $item) {
				$name = $this->getData('name');
				if (isset($item[\Emertx\BankTransfer\Api\Data\BankTransferInterface::MODEL_ID])) {
					$item[$name]['approve'] = [
						'href' => $this->urlBuilder->getUrl(self::ACTION_URL_PATH_APPROVE, [\Emertx\BankTransfer\Api\Data\BankTransferInterface::MODEL_ID => $item[\Emertx\BankTransfer\Api\Data\BankTransferInterface::MODEL_ID]]),
						'label' => __('Approve')
					];
					$item[$name]['cancel'] = [
						'href' => $this->urlBuilder->getUrl(self::ACTION_URL_PATH_CANCEL, [\Emertx\BankTransfer\Api\Data\BankTransferInterface::MODEL_ID => $item[\Emertx\BankTransfer\Api\Data\BankTransferInterface::MODEL_ID]]),
						'label' => __('Cancel'),
						'confirm' => [
							'title' => __('Delete record'),
							'message' => __('Are you sure you wan\'t to cancel this record?')
						]
					];
				}
			}
		}

		return $dataSource;
	}

}
