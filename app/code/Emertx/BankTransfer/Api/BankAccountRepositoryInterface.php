<?php

namespace Emertx\BankTransfer\Api;

/**
 * Description of BankAccountRepositoryInterface
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
interface BankAccountRepositoryInterface {
	
	/**
     * Get list of bank accounts
     *
     * @param int $limit
     * @return 
     */
    public function getBankAccounts($limit = 0);
	
}
