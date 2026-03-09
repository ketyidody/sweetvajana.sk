<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class NewTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'product' => [
                'personalize' => ['sk' => 'Personalizovat', 'en' => 'Personalize'],
            ],
            'checkout' => [
                'payment_method' => ['sk' => 'Sposob platby', 'en' => 'Payment Method'],
                'gopay' => ['sk' => 'Online platba (GoPay)', 'en' => 'Online Payment (GoPay)'],
                'gopay_desc' => ['sk' => 'Platba kartou, bankovym prevodom alebo Google/Apple Pay', 'en' => 'Pay by card, bank transfer, or Google/Apple Pay'],
                'cash_on_delivery' => ['sk' => 'Dobierka', 'en' => 'Cash on Delivery'],
                'cash_on_delivery_desc' => ['sk' => 'Zaplatite pri prevzati objednavky', 'en' => 'Pay when you receive your order'],
            ],
            'order' => [
                'payment_paid' => ['sk' => 'Platba prijata', 'en' => 'Payment Received'],
                'payment_paid_desc' => ['sk' => 'Vasa platba bola uspesne spracovana.', 'en' => 'Your payment has been successfully processed.'],
                'payment_pending' => ['sk' => 'Platba sa spracuvava', 'en' => 'Payment Processing'],
                'payment_pending_desc' => ['sk' => 'Vasa platba sa spracuvava. Stav aktualizujeme co najskor.', 'en' => 'Your payment is being processed. We will update the status shortly.'],
                'payment_failed' => ['sk' => 'Platba zlyhala', 'en' => 'Payment Failed'],
                'payment_failed_desc' => ['sk' => 'Vasa platba nebola uspesna. Kontaktujte nas prosim.', 'en' => 'Your payment was not successful. Please contact us.'],
                'payment_method' => ['sk' => 'Sposob platby:', 'en' => 'Payment method:'],
                'payment_status' => ['sk' => 'Stav platby:', 'en' => 'Payment status:'],
                'status_paid' => ['sk' => 'Zaplatena', 'en' => 'Paid'],
                'status_pending' => ['sk' => 'Caka na platbu', 'en' => 'Pending'],
                'status_failed' => ['sk' => 'Zlyhana', 'en' => 'Failed'],
                'method_gopay' => ['sk' => 'Online platba (GoPay)', 'en' => 'Online Payment (GoPay)'],
                'method_cash_on_delivery' => ['sk' => 'Dobierka', 'en' => 'Cash on Delivery'],
            ],
            'special_order' => [
                'title' => ['sk' => 'Objednat na mieru', 'en' => 'Request Special Order'],
                'description' => ['sk' => 'Vyplnte formular a my vas budeme kontaktovat', 'en' => 'Fill out the form and we will contact you'],
                'your_name' => ['sk' => 'Vase meno', 'en' => 'Your Name'],
                'your_email' => ['sk' => 'Vas e-mail', 'en' => 'Your Email'],
                'your_phone' => ['sk' => 'Vas telefon', 'en' => 'Your Phone'],
                'message' => ['sk' => 'Sprava / poziadavky', 'en' => 'Message / Requirements'],
                'submit' => ['sk' => 'Odoslat poziadavku', 'en' => 'Submit Request'],
                'success' => ['sk' => 'Vasa poziadavka bola odoslana! Budeme vas kontaktovat.', 'en' => 'Your request has been submitted! We will contact you.'],
            ],
        ];

        foreach ($translations as $group => $keys) {
            foreach ($keys as $key => $locales) {
                foreach ($locales as $locale => $value) {
                    Translation::updateOrCreate(
                        ['locale' => $locale, 'group' => $group, 'key' => $key],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
