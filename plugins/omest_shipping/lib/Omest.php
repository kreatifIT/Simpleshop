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
    const NAME         = 'simpleshop.omest_shipping';
    const OLC_URL      = 'https://olc.omest.com/services';
    const OLC_TEST_URL = 'https://staging.olc.omest.com/services';

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
            $this->name = "###simpleshop.omest_shipping_" . mb_strtolower($this->getValue('extension')) . "###";
        }
        return parent::getName();
    }

    /**
     * @param $Order
     * @param $products
     *
     * @return float
     * @throws OmestShippingException
     */
    protected function calculatePriceFromOLC($Order, $products)
    {
        $Settings  = \rex::getConfig('simpleshop.OmestShipping.Settings');
        $test      = $Settings['sandbox'];
        $DAddress  = $Order->getShippingAddress();
        $countryId = $DAddress->getValue('country');
        $Country   = $countryId ? Country::get($countryId) : null;

        // SKIP if no products
        if (count($products) < 1) {
            throw new OmestShippingException("Order has no products", 1);
        }

        if (!$Settings['calc_costs']) {
            return false;
        }

        $data = [
            'shipmentTypeKey'    => 'PARCEL',
            'shippingServiceKey' => $this->getValue('extension'),
            'amountCOD'          => 0,
            'amountInsured'      => 0,
            'parcels'            => [],
            'pickupAddress'      => [
                'pickupOMEST' => $Settings['omest_pickup'],
                'zipcode'     => $Settings['pickup_zip'],
                'countryCode' => $Settings['pickup_country_code'],
            ],
            'deliveryAddress'    => [
                'zipcode'     => $DAddress->getValue('postal'),
                'countryCode' => $Country->getValue('iso2'),
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
            'api_key'      => $Settings['api_key'],
            'customer_key' => $Settings['customer_key'],
            'data'         => json_encode($data),
        ]));

        $Connector = new WSConnector($test ? self::OLC_TEST_URL : self::OLC_URL);
        $Connector->setRespFormat('application/json');
        $Connector->setDebug(false);
        $response = $Connector->request('/shipment-rate', $sdata, 'get', 'Omest.calculatePriceFromOLC');

        if ($response['response']['status'] <= 0) {
            Utils::log('Omest.calculatePriceFromOLC', $response['response']['message'] . "\n" . print_r($data, true) . "\n" . print_r($DAddress, true), 'Error', true);
            throw new OmestShippingException($response['response']['message'], 2);
        } else if ($response['response']['shipment']->price == '' || $response['response']['shipment']->price == '-') {
            Utils::log('Omest.calculatePriceFromOLC', "No price found!\n" . print_r($data, true) . "\n" . print_r($DAddress, true), 'Error', true);
            throw new OmestShippingException('', 3);
        } else {
            // return shipping costs
            return (float)$response['response']['shipment']->price;
        }
    }

    public static function sendOrdersToOLC($order_ids)
    {
        $procces_cnt = 0;
        $Settings    = \rex::getConfig('simpleshop.OmestShipping.Settings');

        foreach ($order_ids as $order_id) {
            $Order    = Order::get($order_id);
            $response = self::api_createShipment($Order, $Settings);

            if ($response['response']['status'] != 1) {
                throw new OmestShippingException($response['response']['message']);
            } else {
                // save shipping key
                $Shipping = $Order->getValue('shipping');
                $Shipping->setValue('shipping_key', $response['response']['shipment']->key);
                $Order->setValue('shipping', $Shipping);
                $Order->save();

                $procces_cnt++;
            }
        }
        return $procces_cnt;
    }

    protected static function api_createShipment($Order, $Settings)
    {
        $IAddress = $Order->getInvoiceAddress();
        $DAddress = $Order->getShippingAddress();
        $Customer = $Order->getValue('customer_data');
        $Shipping = $Order->getValue('shipping');
        $parcels  = $Shipping->getValue('parcels');
        $products = OrderProduct::query()
            ->where('order_id', $Order->getId())
            ->find();

        // SKIP if no products
        if (count($products) < 1) {
            throw new OmestShippingException("Order [{$Order->getId()}] has no products", 1);
        }
        if (count($parcels) < 1) {
            throw new OmestShippingException("Order [{$Order->getId()}] has no parcels", 2);
        }

        $data = [
            'key'                => $Order->getValue('shipping_key'),
            'reference1'         => $Order->getId(),
            'shippingServiceKey' => $Shipping->getValue('extension'),
            'shipmentTypeKey'    => 'PARCEL',
            'parcels'            => [],
            'pickupAddress'      => [
                'companyName'  => $Settings['pickup_company_name'],
                'street'       => $Settings['pickup_street'],
                'city'         => $Settings['pickup_city'],
                'zipcode'      => $Settings['pickup_zip'],
                'countryCode'  => $Settings['pickup_country_code'],
                'warehouseKey' => $Settings['warehouse_key'],
            ],
            'deliveryAddress'    => [
                'personName'  => $DAddress->getName(),
                'companyName' => $DAddress->getValue('company_name'),
                'street'      => $DAddress->getValue('street'),
                'zipcode'     => $DAddress->getValue('postal'),
                'city'        => $DAddress->getValue('location'),
                'countryCode' => "IT",
                //                    'countryCode'  => $DAddress->getValue('country'),
                'phone'       => $DAddress->getValue('phone'),
                'email'       => $Customer ? $Customer->getValue('email') : $IAddress->getValue('email'),
            ],
        ];

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
            'api_key'      => $Settings['api_key'],
            'customer_key' => $Settings['customer_key'],
            'data'         => json_encode($data),
        ]));

        $Connector = new WSConnector($Settings['sandbox'] ? self::OLC_TEST_URL : self::OLC_URL);
        $Connector->setRespFormat('application/json');
        $response = $Connector->request('/create-update-shipment', $sdata, 'get', 'Omest.sendOrdersToOLC');

        return $response;
    }

    public static function ext__completeOrder(\rex_extension_point $Ep)
    {
        $result   = $Ep->getSubject();
        $Settings = \rex::getConfig('simpleshop.OmestShipping.Settings');

        if ($Settings['omest_pickup'] == 0 && $Settings['warehouse_key'] == 'OMEST S.A.S') {
            $parcels  = [];
            $Order    = $Ep->getParam('Order');
            $Shipping = $Order->getValue('shipping');
            $products = OrderProduct::query()
                ->where('order_id', $Order->getId())
                ->find();


            foreach ($products as $product) {
                for ($i = 0; $i < $product->getValue('cart_quantity'); $i++) {
                    $parcels[] = new Parcel($product->getValue('length') / 10, $product->getValue('width') / 10, $product->getValue('height') / 10, $product->getValue('weight') / 1000);
                }
            }
            $Shipping->setParcels($parcels);
            $Order->setValue('shipping', $Shipping);

            $response = self::api_createShipment($Order, $Settings);

            if ($response['response']['status'] != 1) {
                Utils::log('Omest.ext__completeOrder.create-shipment-failed', $response['response']['message'], 'Error', true);
                throw new OrderException("Could not create Omest-Shipping for Order {$Order->getId()}", 70);
            } else {
                $Shipping->setValue('shipping_key', $response['response']['shipment']->key);
                $Order->setValue('shipping', $Shipping);
                $Order->save();
            }
        }
        return $result;
    }
}

class OmestShippingException extends \Exception
{
    public function getLabelByCode()
    {
        switch ($this->getCode()) {
            case 1:
                $errors = '###simpleshop.error.order_has_no_product###';
                break;
            case 2:
                $msg = $this->getMessage();

                if (preg_match('/zipcode (.*) invalid/', $msg)) {
                    $errors = '###simpleshop.error.shipping_zipcode_not_valid###';
                } else {
                    $errors = $this->getMessage();
                }
                break;
            case 3:
                $errors = '###simpleshop.error.shipping_no_price###';
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}