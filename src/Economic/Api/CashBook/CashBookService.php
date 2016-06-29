<?php
namespace Economic\Api\CashBook;

use Economic\Api\Exception\EconomicException;
use Economic\Api\Invoice\Invoice;
use Economic\Api\Service;
use Economic\Api;

class CashBookService extends Service
{
    public function createDebtorPayment(DebtorPayment $debtor)
    {
        $this->client->connect();
        try {
            $response = $this->client->CashBookEntry_CreateDebtorPayment(array(
                'cashBookHandle' => $debtor->getCashBookHandle(),
                'debtorHandle' => $debtor->getDebtorHandle(),
                'contraAccountHandle' => $debtor->getContraAccount(),
            ));

            $entry = new CashBookEntry();
            $entry->setHandle($response->CashBookEntry_CreateDebtorPaymentResult);

            return $entry;
        } catch (\SoapFault $e) {
            throw new EconomicException($e->getMessage());
        }
    }
    
    public function createCashBookEntry(array $parameters)
    {
        $this->client->connect();

        try {
            $response = $this->client->CashBookEntry_CreateFromDataArray($parameters);

            return $response;
        } catch (\SoapFault $e) {
            throw new EconomicException($e->getMessage());
        }
    }

    public function updateEntryFromInvoice(CashBookEntry $entry, Invoice $invoice, $amount)
    {
        $this->client->connect();

        try {
            $this->client->CashBookEntry_SetAmount(array('cashBookEntryHandle' => $entry->getHandle(), 'value' => $amount));
            $number = $invoice->getHandle();
            if (is_array($number)) {
                $number = current($number);
            }
            $this->client->CashBookEntry_SetDebtorInvoiceNumber(array('cashBookEntryHandle' => $entry->getHandle(), 'value' => $number));
            $this->client->CashBookEntry_SetText(array('cashBookEntryHandle' => $entry->getHandle(), 'value' => "Invoice: {$number}, Other Reference: {$invoice->getOtherReference()}"));

            return $entry;
        } catch (\SoapFault $e) {
            throw new EconomicException($e->getMessage());
        }
    }

    public function findByName($name)
    {
        $this->client->connect();
        try {
            $response = $this->client->CashBook_FindByName(array('name' => $name));

            $cashbook = new CashBook();
            $cashbook->setHandle((array) $response->CashBook_FindByNameResult);

            return $cashbook;
        } catch (\SoapFault $e) {
            throw new EconomicException($e->getMessage());
        }
    }

    public function getAll()
    {
        $this->client->connect();
        try {
            $response = $this->client->CashBook_GetAll();

            $response = $this->client->CashBook_GetDataArray(array('entityHandles' => $response->CashBook_GetAllResult->CashBookHandle));

            if(!isset($response->CurrentInvoiceLine_GetDataArrayResult)) {
                throw new Api\Exception\EconomicException(sprintf("Failed fetching cashbook data"));
            }

            $cashbooks = array();

            foreach($response->CashBook_GetDataArrayResult->CashBookData as $cashbookData) {
                $cashbook = new CashBook();
                $cashbook->setName($cashbookData->Name);
                $cashbook->setNumber($cashbookData->Number);
                $cashbook->setHandle((array) $cashbookData->Handle);

                $lines[$cashbookData->Number] = $cashbook;
            }

            return $cashbooks;
        } catch (\SoapFault $e) {
            throw new EconomicException($e->getMessage());
        }
    }
}
