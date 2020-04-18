<?php

namespace Emertx\BankTransfer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Description of BankAccountActions
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankAccountActions extends Column {

	/** Url path */
	const ACTION_URL_PATH_EDIT = 'emertx_bank_transfer/account/edit';
	const ACTION_URL_PATH_DELETE = 'emertx_bank_transfer/account/delete';

	/** @var UrlInterface */
	protected $urlBuilder;

	/**
	 * @var string
	 */
	private $editUrl;

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
		array $data = [],
		$editUrl = self::ACTION_URL_PATH_EDIT
	) {
		$this->urlBuilder = $urlBuilder;
		$this->editUrl = $editUrl;
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
				if (isset($item[\Emertx\BankTransfer\Api\Data\BankAccountInterface::MODEL_ID])) {
					$item[$name]['edit'] = [
						'href' => $this->urlBuilder->getUrl($this->editUrl, [\Emertx\BankTransfer\Api\Data\BankAccountInterface::MODEL_ID => $item[\Emertx\BankTransfer\Api\Data\BankAccountInterface::MODEL_ID]]),
						'label' => __('Edit')
					];
					$item[$name]['delete'] = [
						'href' => $this->urlBuilder->getUrl(self::ACTION_URL_PATH_DELETE, [\Emertx\BankTransfer\Api\Data\BankAccountInterface::MODEL_ID => $item[\Emertx\BankTransfer\Api\Data\BankAccountInterface::MODEL_ID]]),
						'label' => __('Delete'),
						'confirm' => [
							'title' => __('Delete record'),
							'message' => __('Are you sure you wan\'t to delete this record?')
						]
					];
				}
			}
		}

		return $dataSource;
	}

}
