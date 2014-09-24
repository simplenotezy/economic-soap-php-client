<?php


namespace Economic\Api\CashBook;


use Economic\Api\Object;

class DebtorPayment extends Object
{
    protected $cashBookHandle;
    protected $debtorHandle;
    protected $contraAccount = array("Number" => 5820);

    public function setDebtorHandle($debtorHandle)
    {
        $this->debtorHandle = $this->getHandle($debtorHandle);
    }

    public function setCashBookHandle($cashBookHandle)
    {
        $this->cashBookHandle = $this->getHandle($cashBookHandle);
    }

    public function setContraAccountHandle($contraAccountHandle)
    {
        $this->contraAccount = $this->getHandle($contraAccountHandle);
    }
}
