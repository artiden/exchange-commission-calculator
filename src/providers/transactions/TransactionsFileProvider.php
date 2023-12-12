<?php

namespace artiden\exchange\providers\transactions;

use artiden\exchange\providers\transactions\TransactionsProviderInterface;

class TransactionsFileProvider implements TransactionsProviderInterface {
    /**
     * @param string $fileName
     */
    public function __construct(
        private string $fileName
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getTransactions(): array {
        $transactions = [];

        // We could throw an exception, if application unable to open a file.
        // But for our example we'll just return an empty list
        if ($file = fopen($this->fileName, 'r')) {
            while (!feof($file)) {
                if (!$line = fgets($file)) {
                    continue;
                }
                try {
                    $transactions[] = json_decode($line, null, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception){}
            }
        }

        return $transactions;
    }
}