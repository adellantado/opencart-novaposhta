<?php
/**
 * @category   OpenCart
 * @package    OCU OCU Nova Poshta
 * @copyright  Copyright (c) 2011 Eugene Lifescale (a.k.a. Shaman)
 * @modify     Upgrade up to OpenCart 2.0.x with NovaPoshta API v2.0 by Alex Tymchenko
 * @license    http://www.gnu.org/copyleft/gpl.html     GNU General Public License, Version 3
 */

class ModelShippingNovaPoshta extends Model {

    function getQuote($address) {

        $this->load->language('shipping/novaposhta');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('novaposhta_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('novaposhta_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $city_ref = '';

        $method_data = array();

        if ($status && isset($address['city']) && !empty($address['city'])) {
            $city_response = $this->getResponse(
                $this->getRequest('Address', 'getCities', array('FindByString' => $address['city']))
            );

            if ($city_response['success'] && $city_response['data'] && count($city_response['data']) < 5) {

                $city_lower_case = mb_convert_case($address['city'], MB_CASE_LOWER);
                foreach ($city_response['data'] as $item) {
                    if ($city_lower_case == mb_convert_case($item['Description'], MB_CASE_LOWER) || $city_lower_case == mb_convert_case($item['DescriptionRu'], MB_CASE_LOWER) ) {
                        $city_ref = (string)$item['Ref'];
                        $status = true;
                        break;
                    }
                }
            }

            if (empty($city_ref)) {
                $method_data = array(
                    'code'       => 'novaposhta',
                    'title'      => $this->language->get('text_title'),
                    'quote'      => null,
                    'sort_order' => $this->config->get('novaposhta_sort_order'),
                    'error'      => sprintf($this->language->get('text_city_error'), $this->url->link('information/contact'))
                );
                $status = false;
            }


        }

        if ($status && $city_ref) {
            if ($this->config->get('novaposhta_api_key') && $this->config->get('novaposhta_sender_city_ref') && $this->config->get('novaposhta_weight_class_id')) {

                $quote_data = array();

                $is_free_shipping = (float)$this->cart->getTotal() >= (float)$this->config->get('novaposhta_free_total');

                if ($is_free_shipping) {

                    $quote_data['warehouse'] = array(
                        'code'         => 'novaposhta.warehouse',
                        'title'        => $this->language->get('text_novaposhta_free'),
                        'cost'         => 0,
                        'tax_class_id' => 0,
                        'text'         => $this->currency->format(0)
                    );

                } else {

                    // Get Warehouse Quote
                    $warehouse_response = $this->getDeliveryPrice($city_ref, "WarehouseWarehouse");

                    if ($warehouse_response) {
                        $quote_data['warehouse'] = array(
                            'code'         => 'novaposhta.warehouse',
                            'title'        => $this->language->get('text_novaposhta_warehouse'),
                            'cost'         => $warehouse_response,
                            'tax_class_id' => 0,
                            'text'         => $this->currency->format($warehouse_response)
                        );
                    }

                    // Get Express Quote
                    $express_response = $this->getDeliveryPrice($city_ref, "WarehouseDoors");

                    if ($express_response) {
                        $quote_data['express'] = array(
                            'code'         => 'novaposhta.express',
                            'title'        => $this->language->get('text_novaposhta_express'),
                            'cost'         => $express_response,
                            'tax_class_id' => 0,
                            'text'         => $this->currency->format($express_response)
                        );
                    }
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

    private function getDeliveryPrice($city_ref, $delivery_type) {

        $weight = (float)$this->cart->getWeight() > 100 ? (float)$this->cart->getWeight() : 100;

        $response = $this->getResponse(
            $this->getRequest('InternetDocument', 'getDocumentPrice', array(
                "Weight" => (string)$this->weight->convert($weight, $this->config->get('config_weight_class_id'), $this->config->get('novaposhta_weight_class_id')),
                "Cost" => $this->cart->getTotal(),
                "ServiceType" => $delivery_type,
                "CitySender" => $this->config->get('novaposhta_sender_city_ref'),
                "CityRecipient" => $city_ref
            ))
        );

        if ($response['success']) {
            return count($response['data']) > 0 ? $response['data'][0]['Cost'] : false;
        } else {
            return false;
        }
    }

    private function getRequest($modelName, $calledMethod, $methodProperties) {
        $request = array(
            'modelName' => $modelName,
            'calledMethod' => $calledMethod,
            'methodProperties' => $methodProperties,
            'apiKey' => $this->config->get('novaposhta_api_key')
        );

        return json_encode($request);
    }

    private function getResponse($request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        if ($json) {
            return $json;
        }

        return $response;
    }
}

