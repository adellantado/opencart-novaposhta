<?php

/**
 * OpenCart Ukrainian Community
 * Made in Ukraine
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 *
 * @category   OpenCart
 * @package    OCU Nova Poshta
 * @copyright  Copyright (c) 2011 Eugene Lifescale (a.k.a. Shaman) by OpenCart Ukrainian Community (http://opencart-ukraine.tumblr.com)
 * @license    http://www.gnu.org/copyleft/gpl.html     GNU General Public License, Version 3
 * @version    $Id: catalog/model/shipping/ocu_ukrposhta.php 1.2 2014-12-27 19:18:40
 */
/**
 * @category   OpenCart
 * @package    OCU OCU Nova Poshta
 * @copyright  Copyright (c) 2011 Eugene Lifescale (a.k.a. Shaman) by OpenCart Ukrainian Community (http://opencart-ukraine.tumblr.com)
 * @license    http://www.gnu.org/copyleft/gpl.html     GNU General Public License, Version 3
 */

class ModelShippingNovaPoshta extends Model {

    private $_weight = 0.1;
    private $_width  = 1;
    private $_height = 1;
    private $_length = 1;
    private $_total  = 1;

    function getQuote($address) {

        if ($this->cart->hasProducts()) {

            $this->_total = $this->cart->getTotal();

            foreach ($this->cart->getProducts() as $product) {
                if ($product['shipping']) {
                    $this->_weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
                    $this->_height += $product['height'];
                    $this->_width  += $product['width'];
                    $this->_height += $product['height'];
                    $this->_length += $product['length'];
                }
            }
        }

        $this->load->language('shipping/novaposhta');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('novaposhta_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('novaposhta_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            if ($this->config->get('novaposhta_api_key') && $this->config->get('novaposhta_sender_city') && isset($address['city']) && !empty($address['city'])) {

                $quote_data = array();

                // Get Warehouse Quote
                $warehouse_response = $this->_getQuote($address['city'], 4, 1, 1, 0);

                if ($warehouse_response) {
                    $quote_data['warehouse'] = array(
                        'code'         => 'novaposhta.warehouse',
                        'title'        => $this->language->get('text_novaposhta_warehouse'),
                        'cost'         => $warehouse_response->cost,
                        'tax_class_id' => 0,
                        'text'         => $this->currency->format($warehouse_response->cost)
                    );
                }

                // Get Express Quote
                $express_response = $this->_getQuote($address['city'], 3, 1, 1, 0);

                if ($express_response) {
                    $quote_data['express'] = array(
                        'code'         => 'novaposhta.express',
                        'title'        => $this->language->get('text_novaposhta_express'),
                        'cost'         => $express_response->cost,
                        'tax_class_id' => 0,
                        'text'         => $this->currency->format($express_response->cost)
                    );
                }

                if ($quote_data) {
                    $method_data = array(
                        'code'       => 'novaposhta',
                        'title'      => $this->language->get('text_title'),
                        'quote'      => $quote_data,
                        'sort_order' => $this->config->get('novaposhta_sort_order'),
                        'error'      => false
                    );
                }
            }
        }

        return $method_data;
    }

    private function _getQuote($city, $delivery_type_id, $load_type_id, $floor_count, $postpay_sum) {

        $xml  = '<?xml version="1.0" encoding="utf-8" ?>';
        $xml .= '<file>';
            $xml .= '<auth>' . $this->config->get('novaposhta_api_key') . '</auth>';
            $xml .= '<countPrice>';
                $xml .= '<senderCity>' . $this->config->get('novaposhta_sender_city') . '</senderCity>';
                $xml .= '<recipientCity>' . trim($city) . '</recipientCity>';
                $xml .= '<mass>' . $this->_weight . '</mass>';
                $xml .= '<height>' . $this->_height . '</height>';
                $xml .= '<width>' . $this->_width . '</width>';
                $xml .= '<depth>' . $this->_height . '</depth>';
                $xml .= '<publicPrice>' . $this->_total . '</publicPrice>';
                $xml .= '<deliveryType_id>' . $delivery_type_id . '</deliveryType_id>';
                $xml .= '<loadType_id>' . $load_type_id . '</loadType_id>';
                $xml .= '<floor_count>' . $floor_count . '</floor_count>';
                $xml .= '<postpay_sum>' . $postpay_sum . '</postpay_sum>';
                $xml .= '<date>' . $this->_send . '</date>';
            $xml .= '</countPrice>';
        $xml .= '</file>';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response) {
            return simplexml_load_string($response);
        } else {
            return false;
        }
    }
}

