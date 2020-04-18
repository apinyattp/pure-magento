<?php

namespace Emertx\BankTransfer\Model;

/**
 * Description of BankAccountConfigProvider
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankAccountConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface {
    
    /**
     *
     * @var BankAccountRepository 
     */
    protected $repository;
    
    /**
     *
     * @var \Magento\Framework\View\Asset\Repository 
     */
    protected $assetRepo;
    
    /**
     * @param BankAccountRepository $repository
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        BankAccountRepository $repository,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->repository = $repository;
        $this->assetRepo = $assetRepo;
    }
    
    public function getConfig() {
        
        $bankAccounts = $this->repository->getBankAccounts();
        $output['bankAcc'] = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $store = $objectManager->get('Magento\Framework\Locale\Resolver');
        $currentStore = $store->getLocale(); 
        if ($bankAccounts) {
            
            $bankNames = BankAccount::getBanks();
            
            foreach($bankAccounts as $bankAccount) {
                
                $bank = $bankAccount->getBank();

                if($currentStore == 'th_TH'){
                     $accname = $bankAccount->getAccountNameTh();
                }else{
                    $accname = $bankAccount->getAccountName();
                }
                
                $output['bankAcc'][] = [
                    'logo' => $this->assetRepo->getUrl('Emertx_BankTransfer::img/'.$bank.'.png'),
                    'bank' => BankAccount::getBankName($bank),
                    'accname' => $accname,
                    'accno' => $bankAccount->getAccountNumber(),
                ];
            }
        }
        
        return $output; 
    }
}