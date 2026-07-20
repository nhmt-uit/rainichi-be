<?php
/**
 * Created by PhpStorm.
 * User: thuc.phan
 * Date: 2019-11-20
 * Time: 13:27
 */

namespace App\Service;

use App\Models\MoneyToCredit;
use Google_Client;
use Google_Service_AndroidPublisher;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use Google_Service_AndroidPublisher_InAppProductListing;
use Google_Service_AndroidPublisher_InAppProduct;
use Google_Service_AndroidPublisher_Price;

class GoogleService
{
//    private static $instance;
    private $publisherService;
    private $googleClient;
    const PUBLIC_KEY = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgv5GRIffPrdMyQISPjd5STxj72MFufz5Nk7X3mijseWc/QT/mcQxmlS8/rwuOFtgPcK6RrZabxwX+iFQa3JpLqf6kp96IV2gxkanHRuhpnSzw01FqL6gOlWDOzjxhY833H28Nsh/hSbffwPLjMmZ4VQ0vWsniZRU4PUXcKZWOHzGPBK51QlrHlf4DbloMoYvl8Qr1YQtGW4tod06dPDilvrD2GZi3KYUYXmYBzv4qz7TMS299193O/NMH/MCshge78Ee6LRuXwvYybjAgsud9ANtuUJVCH9aaT+rjhU92sj4Hz5HauFweJSo7Ec2WWBw9jY7SdSujXuD2/9p6yCQ6QIDAQAB';
    const PACKAGE_NAME = 'com.rainichi';

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/../../config/service_account.json');
        $this->googleClient = new Google_Client();

        $this->googleClient->useApplicationDefaultCredentials();
        $this->googleClient->addScope('https://www.googleapis.com/auth/androidpublisher');
        $this->publisherService = new Google_Service_AndroidPublisher($this->googleClient);

    }
//
//    public static function getInstance()
//    {
//        if (self::$instance == null) {
//            self::$instance == new GoogleService();
//        }
//        return self::$instance;
//    }

    public function validateReceiptAndroid($packageName, $productId, $purchaseToken)
    {

        $purchase = $this->publisherService->purchases_products->get($packageName, $productId, $purchaseToken);
        return $purchase;
    }

    public function verifySignature($message, $signature)
    {
        $publicKey = $this::PUBLIC_KEY;
        $key = trim($publicKey);
        $key_raw = <<<EOD
-----BEGIN PUBLIC KEY-----
$key
-----END PUBLIC KEY-----
EOD;
        $rsa = PublicKeyLoader::load($key_raw)->withPadding(RSA::SIGNATURE_PKCS1);

        return $rsa->verify(json_encode($message), base64_decode($signature));
    }

    public function getAllProduct()
    {

//        $res = $this->publisherService->inappproducts->get($this::PACKAGE_NAME, "com.rainichi.9000cre");
//        $list = $res->getListings();
//                Log::debug("getAllProduct", $list);


        return $this->publisherService->inappproducts->listInappproducts($this::PACKAGE_NAME);
        return $res;
    }

    public function insertProduct(MoneyToCredit $order)
    {

        $product = $this->covertOrderToGoogleIAPProduct($order);

        $product->setSku($this->generateSku($order->credit));

        $opt = array("autoConvertMissingPrices" => true);

        return $this->publisherService->inappproducts->insert($this::PACKAGE_NAME, $product, $opt);
    }

    public function deleteProduct(MoneyToCredit $order)
    {
        return $this->publisherService->inappproducts->delete($this::PACKAGE_NAME, $order->package_id);
    }

    public function editProduct(MoneyToCredit $order)
    {
        $product = $this->covertOrderToGoogleIAPProduct($order);
        $opt = array("autoConvertMissingPrices" => true);

        return $this->publisherService->inappproducts->patch($this::PACKAGE_NAME, $order->package_id, $product, $opt);
    }

    private function generateSku($credit)
    {
        return $this::PACKAGE_NAME . "." . $credit . "cre." . $this->generateRandomString();
    }

    private function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function covertOrderToGoogleIAPProduct($order)
    {
        $product = new Google_Service_AndroidPublisher_InAppProduct();

        $product->setDefaultLanguage("vi");


        $productPrice = new Google_Service_AndroidPublisher_Price();
        $productPrice->setPriceMicros($order->discount * 1000000);
        $productPrice->setCurrency("VND");
        $product->setDefaultPrice($productPrice);
        $product->setStatus("active");
        $product->setPackageName($this::PACKAGE_NAME);

        $viPrice = array("VN" => $productPrice);
        $product->setPrices($viPrice);

        $productListing = new Google_Service_AndroidPublisher_InAppProductListing();
        $productListing->setDescription("Gói " . "$order->credit" . " Xu");
        $productListing->setTitle($order->credit);

        $listing = array("vi" => $productListing);

        $product->setListings($listing);
        return $product;
    }

}