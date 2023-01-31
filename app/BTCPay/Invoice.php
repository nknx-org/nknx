<?php

namespace App\BTCPay;

use BTCPayServer\Client\InvoiceCheckoutOptions;
use BTCPayServer\Util\PreciseNumber;

class Invoice extends \BTCPayServer\Client\Invoice
{
    public function __construct()
    {
        parent::__construct(config('btcpay.host'), config('btcpay.api-key'));
    }

    public function createInvoiceForProduct(string $name, \App\Models\User $user, string $price, array $metadata = [])
    {

        try {

            // Setup custom metadata. This will be visible in the invoice and can include
            // arbitrary data. Example below will show up on the invoice details page on
            // BTCPay Server.
            $meta = [
                'buyerName' => $user->name,
                'posData' => array_key_exists('metadata',$metadata) ? json_encode($metadata['metadata']) : null,
                'itemDesc' => array_key_exists('description',$metadata) ? $metadata['description'] : null,
                'itemCode' => $name,
                'physical' => false, // indicates if physical product
                'quantity' => array_key_exists('quantity',$metadata) ? $metadata['quantity'] : 1,
                'taxIncluded' => array_key_exists('taxIncluded',$metadata) ? $metadata['taxIncluded'] : 0,
            ];

            $checkoutOptions = new InvoiceCheckoutOptions();
            $checkoutOptions->setRedirectURL('https://nknx.org/nodes');

            $invoice = $this->createInvoice(
                config('btcpay.store-id'),
                config('btcpay.currency'),
                PreciseNumber::parseString(($price * (array_key_exists('quantity',$metadata) ? $metadata['quantity'] : 1))/ 100),
                'product',
                $user->email,
                $meta,
                $checkoutOptions
            );
            return $invoice;
        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
        return false;
    }
}
