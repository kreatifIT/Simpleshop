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

    public function usedForFrontend()
    {
        $Settings = \rex::getConfig('simpleshop.OmestShipping.Settings');
        return from_array($Settings, 'used_in_frontend', 1);
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
            'shippingServiceKey' => $this->getValue('extension') ?: 'EC',
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
        } else if ($response['response']['shipment']->priceDetails->price == '' || $response['response']['shipment']->priceDetails->price == '-') {
            if ($response['response']['shipment']->price == '' || $response['response']['shipment']->price == '-') {
            Utils::log('Omest.calculatePriceFromOLC', "No price found!\n" . print_r($data, true) . "\n" . print_r($DAddress, true), 'Error', true);
            throw new OmestShippingException('', 3);
            } else {
                return (float)str_replace(',', '.', $response['response']['shipment']->price);
            }
        } else {
            // return shipping costs
            return (float)str_replace(',', '.', $response['response']['shipment']->priceDetails->price);
        }
    }

    public static function sendOrdersToOLC($order_ids)
    {
        $processed  = [];
        $clientMail = Utils::getSetting('order_notification_email');
        $Settings   = \rex::getConfig('simpleshop.OmestShipping.Settings');


        foreach ($order_ids as $order_id) {
            $Order = Order::get($order_id);
            self::api_createShipment($Order, $Settings);

            $PDF      = $Order->getPackingListPDF(false);
            $filename = \rex_path::addonData('simpleshop', "packing_lists/{$Order->getId()}.pdf");
            $PDF->Output($filename, \Mpdf\Output\Destination::FILE);

            $orderInfo = [
                'attachments' => [$filename],
                'Order'       => $Order,
                'sentToOmest' => false,
            ];

            if ($Settings['omest_pickup'] == 0 && $Settings['warehouse_key'] == 'OMEST S.A.S') {
                // send mail to omest with packing list
                $OMail = new Mail();
                $OMail->addAddress($Settings['sandbox'] ? 'client@kreatif.it' : 'info@omest.com');
                $OMail->addAttachment($filename, $Order->getShippingKey());
                $OMail->setVar('body', "Hallo OMEST,<br/>bitte bereitet die Sendung <strong>{$Order->getShippingKey()}</strong> vor und versendet sie.<br/><br/>Danke", false);
                $OMail->Subject = 'Sendung / Pacchetto ' . $Order->getShippingKey();

                $orderInfo['sentToOmest'] = $OMail->send();
            }
            $processed[] = $orderInfo;
        }

        $Mail          = new Mail();
        $orders        = [];
        $Mail->Subject = strtr(Wildcard::get('simpleshop.omest.orderslist_sent_subject'), ['{COUNT}' => count($processed)]);
        $Mail->addAddress($clientMail);

        foreach ($processed as $item) {
            $orders[] = $item['Order']->getReferenceId() . " ({$item['Order']->getShippingKey()})";

            foreach ($item['attachments'] as $attachment) {
                $Mail->addAttachment($attachment, $item['Order']->getShippingKey());
            }
        }

        $Mail->setVar('body', strtr(Wildcard::get('simpleshop.omest.orderslist_sent'), ['{ORDERS}' => '<strong>' . implode('<br/>', $orders) . '</strong>']), false);
        $Mail->send();


        foreach ($processed as $item) {
            foreach ($item['attachments'] as $attachment) {
                unlink($attachment);
            }
        }

        return count($processed);
    }

    protected static function api_createShipment($Order, $Settings)
    {
        $Settings = \rex::getConfig('simpleshop.OmestShipping.Settings');
        $IAddress = $Order->getInvoiceAddress();
        $DAddress = $Order->getShippingAddress();
        $Country  = Country::get($DAddress->getValue('country'));
        $Customer = $Order->getValue('customer_data');
        $Shipping = $Order->getValue('shipping');
        $parcels  = $Shipping->getValue('parcels');
        $products = OrderProduct::query()
            ->where('order_id', $Order->getId())
            ->find();

        $data = [
            'key'                => $Shipping->getValue('shipping_key'),
            'reference1'         => $Order->getReferenceId(),
            'shippingServiceKey' => $Shipping->getValue('extension') ?: 'EC',
            'shipmentTypeKey'    => 'PARCEL',
            'parcels'            => [],
            'content'            => [],
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
                'countryCode' => $Country->getValue('iso2'),
                'phone'       => $DAddress->getValue('phone'),
                'email'       => $Customer ? $Customer->getValue('email') : $IAddress->getValue('email'),
            ],
        ];


        if ($Settings['submit_order_products'] == 1) {
            $products = $Order->getProducts();

            foreach ($products as $order_product) {
                $Product = $order_product->getValue('data');

                $data['content'][] = [
                    'quantity'    => $Product->getValue('cart_quantity'),
                    'code'        => $Product->getValue('code'),
                    'description' => $Product->getName(),
                    'amount'      => number_format($Product->getPrice(), 2, '.', ''),
                ];
            }
        } else {
            // SKIP if no products
            if (count($products) < 1) {
                throw new OmestShippingException("Order [{$Order->getId()}] has no products", 1);
            }
            if (count($parcels) < 1) {
                throw new OmestShippingException("Order [{$Order->getId()}] has no parcels", 4);
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
        }

        $sdata = html_entity_decode(http_build_query([
            'api_key'      => $Settings['api_key'],
            'customer_key' => $Settings['customer_key'],
            'data'         => json_encode($data),
        ]));

        $Connector = new WSConnector($Settings['sandbox'] ? self::OLC_TEST_URL : self::OLC_URL);
        $Connector->setRespFormat('application/json');
        $response = $Connector->request('/create-update-shipment', $sdata, 'get', 'Omest.sendOrdersToOLC');

        if ($response['response']['status'] != 1) {
            Utils::log('Omest.ext__completeOrder.create-shipment-failed', $response['response']['message'], 'Error', true);
            throw new OrderException($response['response']['message'], 70);
        } else {
            $Shipping->setValue('shipping_key', $response['response']['shipment']->key);
            $Shipping->setValue('api_response', $response);
            $Shipping->setValue('shipping_sent', date('Y-m-d H:i:s'));
            $Shipping->setValue('shipping_sent_by', \rex::getUser()
                ->getName());
            $Order->setValue('shipping', Order::prepareData($Shipping));
            $Order->save();
        }

        return $response;
    }

    public static function ext__getShippingKey(\rex_extension_point $Ep)
    {
        $subject  = $Ep->getSubject();
        $Order    = $Ep->getParam('Order');
        $Shipping = $Order->getValue('shipping');

        if ($Shipping instanceof self) {
            $subject = $Shipping->getValue('shipping_key');

            if ($Ep->getParam('forBarcode')) {
                $subject = str_replace('OME', '', $subject);
            }
        }
        return $subject;
    }

    //    public static function ext__completeOrder(\rex_extension_point $Ep)
    //    {
    //        $result   = $Ep->getSubject();
    //        $Settings = \rex::getConfig('simpleshop.OmestShipping.Settings');
    //
    //        if ($Settings['omest_pickup'] == 0 && $Settings['warehouse_key'] == 'OMEST S.A.S') {
    //            $parcels  = [];
    //            $Order    = $Ep->getParam('Order');
    //            $Shipping = $Order->getValue('shipping');
    //            $products = OrderProduct::query()
    //                ->where('order_id', $Order->getId())
    //                ->find();
    //
    //
    //            foreach ($products as $product) {
    //                for ($i = 0; $i < $product->getValue('cart_quantity'); $i++) {
    //                    $parcels[] = new Parcel($product->getValue('length') / 10, $product->getValue('width') / 10, $product->getValue('height') / 10, $product->getValue('weight') / 1000);
    //                }
    //            }
    //            $Shipping->setParcels($parcels);
    //            $Order->setValue('shipping', $Shipping);
    //
    //            $response = self::api_createShipment($Order, $Settings);
    //
    //            if ($response['response']['status'] != 1) {
    //                Utils::log('Omest.ext__completeOrder.create-shipment-failed', $response['response']['message'], 'Error', true);
    //                throw new OrderException("Could not create Omest-Shipping for Order {$Order->getId()}", 70);
    //            } else {
    //                $Shipping->setValue('shipping_key', $response['response']['shipment']->key);
    //                $Order->setValue('shipping', Order::prepareData($Shipping));
    //                $Order->save();
    //            }
    //        }
    //        return $result;
    //    }
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
            case 4:
                break;
            default:
                $errors = $this->getMessage();
                break;
        }
        return $errors;
    }
}