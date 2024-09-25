<?php

class SpringCourier
{
    public function newPackage(array $order, array $params)
    {
        echo __FUNCTION__;
    }

    public function packagePDF(string $trackingNumber)
    {
        echo __FUNCTION__;
    }
}