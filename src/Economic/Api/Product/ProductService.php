<?php


namespace Economic\Api\Product;


use Economic\Api\Exception\ProductNotFoundException;
use Economic\Api\Service;

class ProductService extends Service
{
    public function createFromData(Product $product)
    {
        $this->client->connect();
        try {
            $data = array("data" => $product->toArray());
            try {
                $product2 = $this->findByNumber($product->getNumber());
                $product->setHandle($product2->getHandle());

                return $product;
            } catch (ProductNotFoundException $e) {
                $response = $this->client->Product_CreateFromData($data);
                if (isset($response->Product_CreateFromDataResult)) {
                    $product->setHandle((array)$response->Product_CreateFromDataResult);

                    return $product;
                }
            }
        } catch (\SoapFault $e) {
            throw $e;
        }
    }

    public function findByNumber($number)
    {
        $this->client->connect();
        try {
            $data     = array("number" => $number);
            $response = $this->client->Product_FindByNumber($data);
            if (isset($response->Product_FindByNumberResult)) {
                $product = new Product();
                $product->setHandle((array)$response->Product_FindByNumberResult);

                return $product;
            }
            throw new ProductNotFoundException();
        } catch (\SoapFault $e) {
            throw $e;
        }
    }

    public function getData(Product $product)
    {
        $this->client->connect();
        try {
            $data     = array("entityHandle" => $product->getHandle());
            $response = $this->client->Product_GetData($data);
            if (isset($response->Product_GetDataResult)) {
                $product->setCostPrice($response->Product_GetDataResult->CostPrice);
                $product->setRecommendedPrice($response->Product_GetDataResult->RecommendedPrice);
                $product->setUnitHandle($response->Product_GetDataResult->UnitHandle);
                $product->setName($response->Product_GetDataResult->Name);
                $product->setDescription($response->Product_GetDataResult->Description);

                return $product;
            }
            throw new ProductNotFoundException();
        } catch (\SoapFault $e) {
            throw $e;
        }
    }

    public function delete($number)
    {
        $this->client->connect();
        try {
            $data = array("productHandle" => array("Number" => $number));


            return $this->client->Product_Delete($data);
        } catch (\SoapFault $e) {
            throw new ProductNotFoundException($e->getMessage());
        }
    }

    public function getProductGroups()
    {
        $this->client->connect();

        $response = $this->client->ProductGroup_GetAll();

        return $response->ProductGroup_GetAllResult->ProductGroupHandle;

    }
}
