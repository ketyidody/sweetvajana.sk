<?php

return [
    'seller_name' => env('INVOICE_SELLER_NAME', 'Sweet Vajana'),
    'seller_address' => env('INVOICE_SELLER_ADDRESS', ''),
    'seller_company_id' => env('INVOICE_SELLER_COMPANY_ID', ''),
    'seller_vat_number' => env('INVOICE_SELLER_VAT_NUMBER', ''),
    'seller_bank_account' => env('INVOICE_SELLER_BANK_ACCOUNT', ''),
    'seller_email' => env('INVOICE_SELLER_EMAIL', env('MAIL_FROM_ADDRESS', '')),
    'seller_phone' => env('INVOICE_SELLER_PHONE', ''),
];
