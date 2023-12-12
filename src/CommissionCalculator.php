<?php

namespace artiden\exchange;

use artiden\exchange\helpers\CountryHelper;
use artiden\exchange\providers\bin\BinProviderInterface;
use artiden\exchange\providers\bin\exceptions\BinDataUnavailableException;
use artiden\exchange\providers\rate\RateProviderInterface;
use artiden\exchange\providers\transactions\TransactionsProviderInterface;

class CommissionCalculator {
    public function __construct(
        protected TransactionsProviderInterface $transactionsProvider,
        protected BinProviderInterface $binProvider,
        protected RateProviderInterface $rateProvider
    ){}

    /**
     * Returns array of calculated commision values for each transaction.
     *
     * @return array
     */
    public function getCommissions(): array {
        // Just for this case, as a test, We'll return already calculated commissions if some error happens.
        // We need to know more edge cases to handle it.
        $commissions = [];

        try {
            $transactions = $this->transactionsProvider->getTransactions();
            foreach ($transactions as $transaction) {
                $binData = $this->binProvider->getBinData($transaction->bin);
                $isEuCountry = CountryHelper::isEu($binData?->country?->alpha2);
                $rate = $this->rateProvider->getRate($transaction->currency);

                if ($transaction?->currency === 'EUR') {
                    $amount = $transaction->amount;
                } else {
                    $amount = $transaction->amount / $rate;
                }

                $commissions[] = floatval(sprintf(
                    '%.2f',
                    $amount * ($isEuCountry ? 0.01 : 0.02)
                ));
            }
        } catch (\Exception $exception) {
            return $commissions;
        }

        return $commissions;
    }
}
