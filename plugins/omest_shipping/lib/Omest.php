<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

use Sprog\Wildcard;

class Omest extends ShippingAbstract
{
    const NAME         = 'shop.omest_shipping';
    const OLC_URL      = 'https://olc.omest.com/services';
    const OLC_TEST_URL = 'http://debug.olc.omest.com/services';

    protected $tax_percentage = 22;

    public function getPrice($order, $products = null)
    {
        if ($products) {
            $this->price = $this->calculatePriceFromOLC($order, $products);
        }
        return parent::getPrice($products);
    }

    public function getNetPrice($order, $products = null)
    {
        $price = $this->getPrice($order, $products);
        return $price / ($this->tax_percentage + 100) * 100;
    }

    public function getTax()
    {
        if (!$this->tax) {
            $this->tax = $this->price / ($this->tax_percentage + 100) * $this->tax_percentage;
        }
        return parent::getTax();
    }

    public function getName()
    {
        if ($this->name == '') {
            $this->name = checkstr(Wildcard::get(self::NAME), self::NAME);
        }
        return parent::getName();
    }

    protected function calculatePriceFromOLC($Order, $products)
    {
        $Settings = \rex::getConfig('simpleshop.OmestShipping.Settings');
        $test     = $Settings['sandbox'];
        $extras   = $Order->getValue('extras');
        $IAddress = $Order->getValue('address_1');
        $DAddress = $extras['address_extras']['use_shipping_address'] ? $Order->getValue('address_2') : $IAddress;

        // SKIP if no products
        if (count($products) < 1) {
            throw new OmestShippingException("Order has no products", 1);
        }

        $data = [
            'shipmentTypeKey'    => 'PARCEL',
            'shippingServiceKey' => $extras['shipping']['service_key'],
            'amountCOD'          => 0,
            'amountInsured'      => 0,
            'parcels'            => [],
            'pickupAddress'      => [
                'pickupOMEST' => $Settings['omest_pickup'],
                'zipcode'     => $Settings['pickup_zip'],
                'countryCode' => $Settings['pickup_country_code'],
            ],
            'deliveryAddress'    => [
                'zipcode'     => $DAddress->getValue('zip'),
                'countryCode' => $extras['shipping']['country_code'],
            ],
        ];

        if ($Settings['warehouse_key']) {
            $data['pickupAddress']['warehouseKey'] = $Settings['warehouse_key'];
        }
        foreach ($products as $product) {
            for ($i = 0; $i < $product->getValue('cart_quantity'); $i++) {
                $data['parcels'][] = [
                    'weight' => $product->getValue('weight') / 1000,
                    'width'  => $product->getValue('width') / 10,
                    'length' => $product->getValue('length') / 10,
                    'height' => $product->getValue('height') / 10,
                ];
            }
        }

        $sdata = html_entity_decode(http_build_query([
            'api_key'      => $test ? $Settings['test_api_key'] : $Settings['api_key'],
            'customer_key' => $test ? $Settings['test_customer_key'] : $Settings['customer_key'],
            'data'         => json_encode($data),
        ]));

        $Connector = new WSConnector($test ? self::OLC_TEST_URL : self::OLC_URL);
        $Connector->setRespFormat('application/json');
        $Connector->setDebug(false);
        $response = $Connector->request('/shipment-rate', $sdata, 'get', 'Omest.calculatePriceFromOLC');

        if ($response['response']['status'] <= 0) {
            Utils::log('Omest.calculatePriceFromOLC', $response['response']['message'] . "\n" . print_r($data, true) . "\n" . print_r($DAddress, true), 'Error', true);
            throw new OmestShippingException($response['response']['message'], 2);
        } else if ($response['response']['shipment']->priceDetails->price == '' || $response['response']['shipment']->priceDetails->price == '-') {
            if ($response['response']['shipment']->price == '' || $response['response']['shipment']->price == '-') {
                Utils::log('Omest.calculatePriceFromOLC', "No price found!\n" . print_r($data, true) . "\n" . print_r($DAddress, true), 'Error', true);
                throw new OmestShippingException('', 3);
            } else {
                return (float)$response['response']['shipment']->price;
            }
        } else {
            // return shipping costs
            return (float)$response['response']['shipment']->priceDetails->price;
        }
    }

    public static function sendOrdersToOLC($order_ids)
    {
        $procces_cnt = 0;
        $Settings    = \rex::getConfig('simpleshop.OmestShipping.Settings');
        $test        = $Settings['sandbox'];

        foreach ($order_ids as $order_id) {
            $Order    = Order::get($order_id);
            $IAddress = $Order->getValue('address_1');
            $DAddress = $Order->getValue('address_2');
            $Customer = $Order->getValue('customer_id') ? Customer::get($Order->getValue('customer_id')) : null;
            $extras   = $Order->getValue('extras');
            $Shipping = $Order->getValue('shipping');
            $parcels  = $Shipping->getValue('parcels');
            $products = OrderProduct::query()->where('order_id', $order_id)->find();

            // SKIP if no products
            if (count($products) < 1) {
                throw new OmestShippingException("Order [{$order_id}] has no products", 1);
            }
            if (count($parcels) < 1) {
                throw new OmestShippingException("Order [{$order_id}] has no parcels", 2);
            }

            $country = trim($DAddress->getValue('country'));

            $data = [
                'key'                => $Order->getValue('shipping_key'),
                'reference1'         => $order_id,
                'shippingServiceKey' => $extras['shipping']['service_key'],
                'shipmentTypeKey'    => 'PARCEL',
                'parcels'            => [],
                'pickupAddress'      => [],
                'deliveryAddress'    => [
                    'personName'  => $DAddress->getName(),
                    'companyName' => $DAddress->getValue('company'),
                    'street'      => $DAddress->getValue('street'),
                    'zipcode'     => $DAddress->getValue('zip'),
                    'city'        => $DAddress->getValue('location'),
                    'countryCode' => strlen($country) == 2 ? $country : 'IT',
                    'phone'       => $DAddress->getValue('phone'),
                    'email'       => $Customer ? $Customer->getValue('email') : $IAddress->getValue('email'),
                ],
            ];

            if (trim($Settings['warehouse_key']) == '') {
                $data['pickupAddress'] = [
                    'pickupOMEST'  => $Settings['omest_pickup'],
                    'companyName'  => $Settings['pickup_company_name'],
                    'street'       => $Settings['pickup_street'],
                    'city'         => $Settings['pickup_city'],
                    'zipcode'      => $Settings['pickup_zip'],
                    'countryCode'  => $Settings['pickup_country_code'],
                    'warehouseKey' => trim($Settings['warehouse_key']),
                ];
            } else {
                $data['pickupAddress'] = [
                    'warehouseKey' => trim($Settings['warehouse_key']),
                ];
            }

            foreach ($parcels as $parcel) {
                $_parcel = [
                    'key'    => null,
                    'weight' => $parcel->getValue('weight') / 1000,
                    'width'  => $parcel->getValue('width'),
                    'length' => $parcel->getValue('length'),
                    'height' => $parcel->getValue('height'),
                ];
                if ($parcel->getValue('pallett')) {
                    $data['shipmentTypeKey']  = 'PALLET';
                    $_parcel['palletTypeKey'] = $parcel->getValue('pallett');
                }
                $data['parcels'][] = $_parcel;
            }

            $sdata = html_entity_decode(http_build_query([
                'api_key'      => $test ? $Settings['test_api_key'] : $Settings['api_key'],
                'customer_key' => $test ? $Settings['test_customer_key'] : $Settings['customer_key'],
                'data'         => json_encode($data),
            ]));

            $Connector = new WSConnector($test ? self::OLC_TEST_URL : self::OLC_URL);
            $Connector->setRespFormat('application/json');
            $response = $Connector->request('/create-update-shipment', $sdata, 'get', 'Omest.sendOrdersToOLC');

            if ($response['response']['status'] <= 0) {
                throw new OmestShippingException($response['response']['message']);
            }
            else {
                // save shipping key
                $Order->setValue('shipping_key', $response['response']['shipment']->key);
                $Order->save(false, true);
                $procces_cnt++;
            }
        }
        return $procces_cnt;
    }
}

class OmestShippingException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode()) {
            case 1:
                $errors = '###shop.error.order_has_no_product###';
                break;
            case 2:
                $msg = $this->getMessage();

                if (preg_match('/zipcode (.*) invalid/', $msg)) {
                    $errors = '###shop.error.shipping_zipcode_not_valid###';
                }
                else {
                    $errors = $this->getMessage();
                }
                break;
            case 3:
                $errors = '###shop.error.shipping_no_price###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}