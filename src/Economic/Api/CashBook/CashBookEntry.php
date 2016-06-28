<?php


namespace Economic\Api\CashBook;


use Economic\Api\Object;

class CashBookEntry extends Object
{
    protected $id1;
    protected $id2;

    protected $handle;

    // /**
    //  * @return mixed
    //  */
    // public function getHandle()
    // {
    //     return $this->handle;
    // }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }
}
