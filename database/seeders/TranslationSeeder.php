<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'nav' => [
                'home' => ['sk' => 'Domov', 'en' => 'Home'],
                'products' => ['sk' => 'Produkty', 'en' => 'Products'],
                'about' => ['sk' => 'O nas', 'en' => 'About'],
                'contact' => ['sk' => 'Kontakt', 'en' => 'Contact'],
            ],
            'common' => [
                'add_to_cart' => ['sk' => 'Pridat do kosika', 'en' => 'Add to Cart'],
                'apply' => ['sk' => 'Pouzit', 'en' => 'Apply'],
                'cancel' => ['sk' => 'Zrusit', 'en' => 'Cancel'],
                'save' => ['sk' => 'Ulozit', 'en' => 'Save'],
                'delete' => ['sk' => 'Vymazat', 'en' => 'Delete'],
                'back' => ['sk' => 'Spat', 'en' => 'Back'],
                'search' => ['sk' => 'Hladat', 'en' => 'Search'],
                'loading' => ['sk' => 'Nacitavam...', 'en' => 'Loading...'],
                'no_results' => ['sk' => 'Ziadne vysledky', 'en' => 'No results'],
                'continue_shopping' => ['sk' => 'Pokracovat v nakupovani', 'en' => 'Continue Shopping'],
                'shop_now' => ['sk' => 'Nakupovat', 'en' => 'Shop Now'],
            ],
            'product' => [
                'featured' => ['sk' => 'Odporucane', 'en' => 'Featured'],
                'featured_subtitle' => ['sk' => 'Nase najpopularnejsie rucne vyrabane pochutky', 'en' => 'Our most popular handcrafted treats'],
                'category' => ['sk' => 'Kategoria', 'en' => 'Category'],
                'price_range' => ['sk' => 'Cenove rozpatie', 'en' => 'Price Range'],
                'all' => ['sk' => 'Vsetky', 'en' => 'All'],
                'no_products_found' => ['sk' => 'Ziadne produkty nezodpovedaju vasim filtrom.', 'en' => 'No products found matching your filters.'],
                'clear_filters' => ['sk' => 'Zrusit vsetky filtre', 'en' => 'Clear all filters'],
                'back_to_products' => ['sk' => 'Spat na produkty', 'en' => 'Back to Products'],
                'products' => ['sk' => 'Produkty', 'en' => 'Products'],
                'personalize' => ['sk' => 'Personalizovat', 'en' => 'Personalize'],
                'available_for_collection' => ['sk' => 'Ihned k odberu', 'en' => 'Available for Collection'],
                'soonest_availability' => ['sk' => 'Najskorsi termin: :count den|Najskorsi termin: :count dni|Najskorsi termin: :count dni', 'en' => 'Ready in :count day|Ready in :count days'],
            ],
            'cart' => [
                'title' => ['sk' => 'Nakupny kosik', 'en' => 'Shopping Cart'],
                'empty' => ['sk' => 'Vas kosik je prazdny', 'en' => 'Your cart is empty'],
                'browse_products' => ['sk' => 'Prehladat produkty', 'en' => 'Browse Products'],
                'remove' => ['sk' => 'Odstranit', 'en' => 'Remove'],
                'subtotal' => ['sk' => 'Medzisucet', 'en' => 'Subtotal'],
                'total' => ['sk' => 'Celkovo', 'en' => 'Total'],
                'proceed_to_checkout' => ['sk' => 'Prejst k pokladni', 'en' => 'Proceed to Checkout'],
                'order_summary' => ['sk' => 'Zhrnutie objednavky', 'en' => 'Order Summary'],
                'shipping' => ['sk' => 'Doprava', 'en' => 'Shipping'],
                'free' => ['sk' => 'Zadarmo', 'en' => 'Free'],
            ],
            'checkout' => [
                'title' => ['sk' => 'Pokladna', 'en' => 'Checkout'],
                'customer_info' => ['sk' => 'Informacie o zakaznikovi', 'en' => 'Customer Information'],
                'full_name' => ['sk' => 'Cele meno', 'en' => 'Full Name'],
                'email' => ['sk' => 'E-mail', 'en' => 'Email'],
                'phone' => ['sk' => 'Telefon', 'en' => 'Phone'],
                'shipping_address' => ['sk' => 'Dodacia adresa', 'en' => 'Shipping Address'],
                'order_notes' => ['sk' => 'Poznamky k objednavke', 'en' => 'Order Notes'],
                'optional' => ['sk' => 'volitelne', 'en' => 'optional'],
                'place_order' => ['sk' => 'Odoslat objednavku', 'en' => 'Place Order'],
                'placing_order' => ['sk' => 'Odosielam objednavku...', 'en' => 'Placing Order...'],
                'back_to_cart' => ['sk' => 'Spat do kosika', 'en' => 'Back to Cart'],
                'payment_method' => ['sk' => 'Sposob platby', 'en' => 'Payment Method'],
                'gopay' => ['sk' => 'Online platba (GoPay)', 'en' => 'Online Payment (GoPay)'],
                'gopay_desc' => ['sk' => 'Platba kartou, bankovym prevodom alebo Google/Apple Pay', 'en' => 'Pay by card, bank transfer, or Google/Apple Pay'],
                'invoice_details' => ['sk' => 'Fakturacne udaje', 'en' => 'Invoice Details'],
                'invoice_for_company' => ['sk' => 'Faktura na firmu', 'en' => 'Invoice for a company'],
                'company_name' => ['sk' => 'Nazov firmy', 'en' => 'Company Name'],
                'company_id' => ['sk' => 'ICO', 'en' => 'Company ID (ICO)'],
                'vat_number' => ['sk' => 'DIC / IC DPH', 'en' => 'VAT Number (DIC)'],
                'billing_address' => ['sk' => 'Fakturacna adresa', 'en' => 'Billing Address'],
                'billing_same_as_shipping' => ['sk' => 'Rovnaka ako dodacia adresa', 'en' => 'Same as shipping address'],
                'billing_different' => ['sk' => 'Ina fakturacna adresa', 'en' => 'Different billing address'],
            ],
            'contact' => [
                'title' => ['sk' => 'Kontaktujte nas', 'en' => 'Contact Us'],
                'name' => ['sk' => 'Meno', 'en' => 'Name'],
                'email' => ['sk' => 'E-mail', 'en' => 'Email'],
                'subject' => ['sk' => 'Predmet', 'en' => 'Subject'],
                'message' => ['sk' => 'Sprava', 'en' => 'Message'],
                'send' => ['sk' => 'Odoslat spravu', 'en' => 'Send Message'],
                'success' => ['sk' => 'Sprava bola uspesne odoslana!', 'en' => 'Message sent successfully!'],
                'get_in_touch' => ['sk' => 'Spojte sa s nami', 'en' => 'Get In Touch'],
                'follow_us' => ['sk' => 'Sledujte nas', 'en' => 'Follow Us'],
            ],
            'footer' => [
                'quick_links' => ['sk' => 'Rychle odkazy', 'en' => 'Quick Links'],
                'get_in_touch' => ['sk' => 'Spojte sa s nami', 'en' => 'Get In Touch'],
                'copyright' => ['sk' => 'Vsetky prava vyhradene.', 'en' => 'All rights reserved.'],
                'terms' => ['sk' => 'Obchodné podmienky', 'en' => 'Terms & Conditions'],
            ],
            'auth' => [
                'login' => ['sk' => 'Prihlasit sa', 'en' => 'Login'],
                'register' => ['sk' => 'Registrovat sa', 'en' => 'Register'],
                'email' => ['sk' => 'E-mail', 'en' => 'Email'],
                'password' => ['sk' => 'Heslo', 'en' => 'Password'],
                'remember_me' => ['sk' => 'Zapamatat si ma', 'en' => 'Remember me'],
                'forgot_password' => ['sk' => 'Zabudli ste heslo?', 'en' => 'Forgot password?'],
                'confirm_password' => ['sk' => 'Potvrdit heslo', 'en' => 'Confirm Password'],
                'reset_password' => ['sk' => 'Obnovit heslo', 'en' => 'Reset Password'],
                'logout' => ['sk' => 'Odhlasit sa', 'en' => 'Logout'],
            ],
            'account' => [
                'my_account' => ['sk' => 'Moj ucet', 'en' => 'My Account'],
                'orders' => ['sk' => 'Objednavky', 'en' => 'Orders'],
                'addresses' => ['sk' => 'Adresy', 'en' => 'Addresses'],
            ],
            'order' => [
                'confirmed' => ['sk' => 'Objednavka potvrdena!', 'en' => 'Order Confirmed!'],
                'thank_you' => ['sk' => 'Dakujeme za vasu objednavku. Vase cislo objednavky je', 'en' => 'Thank you for your order. Your order number is'],
                'details' => ['sk' => 'Detaily objednavky', 'en' => 'Order Details'],
                'delivery_info' => ['sk' => 'Informacie o doruceni', 'en' => 'Delivery Information'],
                'name' => ['sk' => 'Meno:', 'en' => 'Name:'],
                'email' => ['sk' => 'E-mail:', 'en' => 'Email:'],
                'phone' => ['sk' => 'Telefon:', 'en' => 'Phone:'],
                'address' => ['sk' => 'Adresa:', 'en' => 'Address:'],
                'notes' => ['sk' => 'Poznamky:', 'en' => 'Notes:'],
                'qty' => ['sk' => 'Mn:', 'en' => 'Qty:'],
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

            ],
            'special_order' => [
                'title' => ['sk' => 'Objednat na mieru', 'en' => 'Request Special Order'],
                'description' => ['sk' => 'Vyplnte formular a my vas budeme kontaktovat', 'en' => 'Fill out the form and we will contact you'],
                'your_name' => ['sk' => 'Vase meno', 'en' => 'Your Name'],
                'your_email' => ['sk' => 'Vas e-mail', 'en' => 'Your Email'],
                'your_phone' => ['sk' => 'Vas telefon', 'en' => 'Your Phone'],
                'message' => ['sk' => 'Sprava / poziadavky', 'en' => 'Message / Requirements'],
                'submit' => ['sk' => 'Odoslat poziadavku', 'en' => 'Submit Request'],
                'view_prices' => ['sk' => 'Zobraziť cenník', 'en' => 'View Price List'],
                'success' => ['sk' => 'Vasa poziadavka bola odoslana! Budeme vas kontaktovat.', 'en' => 'Your request has been submitted! We will contact you.'],
                'size' => ['sk' => 'Velkost', 'en' => 'Size'],
                'select_size' => ['sk' => 'Vyberte velkost', 'en' => 'Select size'],
                'corpus' => ['sk' => 'Korpus', 'en' => 'Corpus'],
                'select_corpus' => ['sk' => 'Vyberte korpus', 'en' => 'Select corpus'],
                'cream_flavor' => ['sk' => 'Prichut kremu', 'en' => 'Cream Flavor'],
                'select_cream_flavor' => ['sk' => 'Vyberte prichut', 'en' => 'Select cream flavor'],
                'additions' => ['sk' => 'Doplnky', 'en' => 'Additions'],
                'total_price' => ['sk' => 'Orientacna cena', 'en' => 'Estimated price'],
            ],
            'email' => [
                'invoice_subject' => ['sk' => 'Faktúra', 'en' => 'Invoice'],
                'invoice_received' => ['sk' => 'Vaša objednávka bola prijatá. Faktúru nájdete v prílohe.', 'en' => 'Your order has been received. Please find your invoice attached.'],
                'order_number' => ['sk' => 'Číslo objednávky', 'en' => 'Order number'],
                'date' => ['sk' => 'Dátum', 'en' => 'Date'],
                'total' => ['sk' => 'Celková suma', 'en' => 'Total'],
                'thank_you' => ['sk' => 'Ďakujeme za vašu objednávku!', 'en' => 'Thank you for your order!'],
            ],
            'messages' => [
                'success_category_created' => ['sk' => 'Kategoria bola vytvorena.', 'en' => 'Category created.'],
                'success_category_updated' => ['sk' => 'Kategoria bola aktualizovana.', 'en' => 'Category updated.'],
                'success_settings_updated' => ['sk' => 'Nastavenia boli aktualizovane.', 'en' => 'Settings updated.'],
                'recaptcha_failed' => ['sk' => 'Overenie reCAPTCHA zlyhalo.', 'en' => 'reCAPTCHA verification failed.'],
                'added_to_cart' => ['sk' => ':name pridany do kosika.', 'en' => ':name added to cart.'],
                'cart_empty' => ['sk' => 'Vas kosik je prazdny.', 'en' => 'Your cart is empty.'],
                'items_unavailable' => ['sk' => 'Polozky vo vasom kosiku uz nie su dostupne.', 'en' => 'The items in your cart are no longer available.'],
            ],
        ];

        foreach ($translations as $group => $keys) {
            foreach ($keys as $key => $locales) {
                foreach ($locales as $locale => $value) {
                    Translation::firstOrCreate(
                        ['locale' => $locale, 'group' => $group, 'key' => $key],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
