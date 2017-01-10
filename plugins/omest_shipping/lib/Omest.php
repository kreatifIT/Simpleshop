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

    public function getPrice($order, $products = NULL)
    {
        if ($products)
        {
            $this->price = $this->calculatePriceFromOLC($order, $products);
        }
        return parent::getPrice($products);
    }

    public function getTax()
    {
        if (!$this->tax)
        {
            $this->tax = $this->price / ($this->tax_percentage + 100) * $this->tax_percentage;
        }
        return parent::getTax();
    }

    public function getName()
    {
        if ($this->name == '')
        {
            $this->name = checkstr(Wildcard::get(self::NAME), self::NAME);
        }
        return parent::getName();
    }

    protected function calculatePriceFromOLC($Order, $products, $test = FALSE)
    {
        $Settings = \rex::getConfig('simpleshop.OmestShipping.Settings');
        $extras   = $Order->getValue('extras');
        $IAddress = $Order->getValue('address_1');
        $DAddress = $extras['address_extras']['use_shipping_address'] ? $Order->getValue('address_2') : $IAddress;

        // SKIP if no products
        if (count($products) < 1)
        {
            throw new OmestShippingException("Order has no products", 1);
        }

        $data = [
            'shipmentTypeKey'    => 'PARCEL',
            'shippingServiceKey' => $extras['shipping']['service_key'],
            'amountCOD'          => 0,
            'amountInsured'      => 0,
            'parcels'            => [],
            'pickupAddress'      => [
                'pickupOMEST'  => $Settings['omest_pickup'],
                'zipcode'      => $Settings['pickup_zip'],
                'countryCode'  => $Settings['pickup_country_code'],
            ],
            'deliveryAddress' => [
                'zipcode'     => $DAddress->getValue('zip'),
                'countryCode' => $extras['shipping']['country_code'],
            ],
        ];

        if ($Settings['warehouse_key'])
        {
            $data['pickupAddress']['warehouseKey'] = $Settings['warehouse_key'];
        }
        foreach ($products as $product)
        {
            for ($i = 0; $i < $product->getValue('cart_quantity'); $i++)
            {
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
        $Connector->setDebug(FALSE);
        $response = $Connector->request('/shipment-rate', $sdata, 'get', 'Omest.calculatePriceFromOLC');

        if ($response['response']['status'] <= 0)
        {
            throw new OmestShippingException($response['response']['message'], 2);
        }
        else if ($response['response']['shipment']->price == '' || $response['response']['shipment']->price == '-')
        {
            throw new OmestShippingException('', 3);
        }
        else
        {
            // return shipping costs
            return (float) $response['response']['shipment']->price;
        }
    }

    public static function sendOrdersToOLC($order_ids, $test = FALSE)
    {
        $procces_cnt = 0;
        $Settings    = \rex::getConfig('simpleshop.OmestShipping.Settings');

        foreach ($order_ids as $order_id)
        {
            $parcels  = [];
            $Order    = Order::get($order_id);
            $IAddress = $Order->getValue('address_1');
            $DAddress = $Order->getValue('address_2');
            $Customer = $Order->getValue('customer_id') ? Customer::get($Order->getValue('customer_id')) : NULL;
            $products = OrderProduct::query()
                ->where('order_id', $order_id)
                ->find();

            // SKIP if no products
            if (count($products) < 1)
            {
                throw new OmestShippingException("Order [{$order_id}] has no products", 1);
            }
//            foreach ($products as $product_order)
//            {
//                $product = $product_order->getValue('data');
//                $count   = $product_order->getValue('quantity');
//
//                for ($i = 0; $i < $count; $i++)
//                {
//                    $parcels[] = [
//                        'key'       => $product_order->getValue('shipping_key'),
//                        'weight'    => $product->getValue('weight'),
//                        'width'     => $product->getValue('width'),
//                        'length'    => $product->getValue('length'),
//                        'height'    => $product->getValue('height'),
//                        'reference' => $product->getValue('code'),
////                        //                    'palletTypeKey' => "80x120",
//                    ];
//                }
//            }

            $data  = [
                'key'             => $Order->getValue('shipping_key'),
                'reference1'      => $order_id,
                // 'parcels'         => $parcels,
                //                'shippingServiceKey' => 'IT',
                //                'shipmentTypeKey'    => 'DOC',
                //                'codTypeKey'         => 'BM',
                //                'amountCOD'          => 12,
                //                'amountInsured'      => $Order->getValue('initial_total'),
                //                'comment'            => '',
                //                'pickupAddress'      => [
                //                    'personName'   => "person",
                //                    'companyName'  => "company",
                //                    'street'       => "street",
                //                    'zipcode'      => "39100",
                //                    'city'         => "bolzano",
                //                    'countryCode'  => "IT",
                //                    'warehouseKey' => "WH1",
                //                ],
                'deliveryAddress' => [
                    'personName'  => $DAddress->getName(),
                    'companyName' => $DAddress->getValue('company'),
                    'street'      => $DAddress->getValue('street'),
                    'zipcode'     => $DAddress->getValue('zip'),
                    'city'        => $DAddress->getValue('location'),
                    'countryCode' => "IT",
                    //                    'countryCode'  => $DAddress->getValue('country'),
                    'phone'       => $DAddress->getValue('phone'),
                    'email'       => $Customer ? $Customer->getValue('email') : $IAddress->getValue('email'),
                    //                    'warehouseKey' => "WH1",
                ],
            ];
            $sdata = (http_build_query([
                'api_key'      => $test ? $Settings['test_api_key'] : $Settings['api_key'],
                'customer_key' => $test ? $Settings['test_customer_key'] : $Settings['customer_key'],
                'data'         => json_encode($data),
            ]));

            $Connector = new WSConnector($test ? self::OLC_TEST_URL : self::OLC_URL);
            $Connector->setRespFormat('application/json');
            $response = $Connector->request('/create-update-shipment', $sdata, 'get', 'Omest.sendOrdersToOLC');

            if ($response['response']['status'] <= 0)
            {
                throw new OmestShippingException($response['response']['message']);
            }
            else
            {
                // save shipping key
                $Order->setValue('shipping_key', $response['response']['shipment']->key);
                $Order->save(FALSE, TRUE);
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
        switch ($this->getCode())
        {
            case 1:
                $errors = '###shop.error.order_has_no_product###';
                break;
            case 2:
                $msg = $this->getMessage();

                if (preg_match('/zipcode (.*) invalid/', $msg))
                {
                    $errors = '###shop.error.shipping_zipcode_not_valid###';
                }
                else
                {
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