<?php
/**
 * @category   OpenCart
 * @package    OCU OCU Nova Poshta
 * @copyright  Copyright (c) 2011 Eugene Lifescale (a.k.a. Shaman)
 * @modify     Upgrade up to OpenCart 2.0.x with NovaPoshta API v2.0 by Alex Tymchenko
 * @license    http://www.gnu.org/copyleft/gpl.html     GNU General Public License, Version 3
 */

class ControllerShippingNovaPoshta extends Controller {

    private $error = array();

    public function index() {

        $this->load->language('shipping/novaposhta');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('novaposhta', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['token'] = $this->session->data['token'];

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_select'] = $this->language->get('text_select');

        $data['entry_cost'] = $this->language->get('entry_cost');
        $data['entry_sender_warehouse'] = $this->language->get('entry_sender_warehouse');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_api_key'] = $this->language->get('entry_api_key');
        $data['entry_sender_city'] = $this->language->get('entry_sender_city');
        $data['entry_sender_city_ref'] = $this->language->get('entry_sender_city_ref');
        $data['entry_comment'] = $this->language->get('entry_comment');
        $data['entry_send_order_status'] = $this->language->get('entry_send_order_status');
        $data['entry_weight_class'] = $this->language->get('entry_weight_class');
        $data['entry_sender_organization'] = $this->language->get('entry_sender_organization');
        $data['entry_sender_person'] = $this->language->get('entry_sender_person');
        $data['entry_sender_phone'] = $this->language->get('entry_sender_phone');
        $data['entry_free_total'] = $this->language->get('entry_free_total');
        $data['help_free_total'] = $this->language->get('help_free_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_shipping'),
            'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('shipping/novaposhta', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('shipping/novaposhta', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['novaposhta_api_key'])) {
            $data['novaposhta_api_key'] = $this->request->post['novaposhta_api_key'];
        } else {
            $data['novaposhta_api_key'] = $this->config->get('novaposhta_api_key');
        }

        if (isset($this->request->post['novaposhta_sender_organization'])) {
            $data['novaposhta_sender_organization'] = $this->request->post['novaposhta_sender_organization'];
        } else {
            $data['novaposhta_sender_organization'] = $this->config->get('novaposhta_sender_organization');
        }

        if (isset($this->request->post['novaposhta_sender_person'])) {
            $data['novaposhta_sender_person'] = $this->request->post['novaposhta_sender_person'];
        } else {
            $data['novaposhta_sender_person'] = $this->config->get('novaposhta_sender_person');
        }

        if (isset($this->request->post['novaposhta_sender_phone'])) {
            $data['novaposhta_sender_phone'] = $this->request->post['novaposhta_sender_phone'];
        } else {
            $data['novaposhta_sender_phone'] = $this->config->get('novaposhta_sender_phone');
        }

        if (isset($this->request->post['novaposhta_geo_zone_id'])) {
            $data['novaposhta_geo_zone_id'] = $this->request->post['novaposhta_geo_zone_id'];
        } else {
            $data['novaposhta_geo_zone_id'] = $this->config->get('novaposhta_geo_zone_id');
        }

        if (isset($this->request->post['novaposhta_comment'])) {
            $data['novaposhta_comment'] = $this->request->post['novaposhta_comment'];
        } else {
            $data['novaposhta_comment'] = $this->config->get('novaposhta_comment');
        }


        if (isset($this->request->post['novaposhta_sender_city'])) {
            $data['novaposhta_sender_city'] = $this->request->post['novaposhta_sender_city'];
        } else {
            $data['novaposhta_sender_city'] = $this->config->get('novaposhta_sender_city');
        }

        if (isset($this->request->post['novaposhta_sender_city_ref'])) {
            $data['novaposhta_sender_city_ref'] = $this->request->post['novaposhta_sender_city_ref'];
        } else {
            $data['novaposhta_sender_city_ref'] = $this->config->get('novaposhta_sender_city_ref');
        }

        if (isset($this->request->post['novaposhta_status'])) {
            $data['novaposhta_status'] = $this->request->post['novaposhta_status'];
        } else {
            $data['novaposhta_status'] = $this->config->get('novaposhta_status');
        }

        if (isset($this->request->post['novaposhta_sort_order'])) {
            $data['novaposhta_sort_order'] = $this->request->post['novaposhta_sort_order'];
        } else {
            $data['novaposhta_sort_order'] = $this->config->get('novaposhta_sort_order');
        }

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['novaposhta_send_order_status'])) {
            $data['novaposhta_send_order_status'] = $this->request->post['novaposhta_send_order_status'];
        } else {
            $data['novaposhta_send_order_status'] = $this->config->get('novaposhta_send_order_status');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/zone');

        $data['zones'] = $this->model_localisation_zone->getZones();
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();


        if (isset($this->request->post['novaposhta_sender_warehouse'])) {
            $data['novaposhta_sender_warehouse'] = $this->request->post['novaposhta_sender_warehouse'];
        } else {
            $data['novaposhta_sender_warehouse'] = $this->config->get('novaposhta_sender_warehouse');
        }

        if (isset($this->request->post['novaposhta_free_total'])) {
            $data['novaposhta_free_total'] = $this->request->post['novaposhta_free_total'];
        } else {
            $data['novaposhta_free_total'] = $this->config->get('novaposhta_free_total');
        }


        $this->load->model('localisation/weight_class');

        $data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

        if (isset($this->request->post['novaposhta_weight_class_id'])) {
            $data['novaposhta_weight_class_id'] = $this->request->post['novaposhta_weight_class_id'];
        } else {
            $data['novaposhta_weight_class_id'] = $this->config->get('novaposhta_weight_class_id');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shipping/novaposhta.tpl', $data));
    }


    public function getCities() {
        $json = array();

            if ($this->config->get('novaposhta_api_key')) {

                $methodProperties = $this->request->get['filter'] ? array('FindByString' => $this->request->get['filter']) : array();
                $response = $this->getResponse(
                    $this->getRequest('Address', 'getCities', $methodProperties)
                );

                if ($response['success']) {

                    $data = $response['data'];
                    foreach ($data as $item) {
                        if ($this->language->get('code') == 'ru') {
                            $json[] = array(
                                'city' => (string)$item['DescriptionRu'],
                                'ref' => (string)$item['Ref']
                            );
                        } else {
                            $json[] = array(
                                'city' => (string)$item['Description'],
                                'ref' => (string)$item['Ref']
                            );
                        }
                    }

                }
            }


        $this->response->setOutput(json_encode($json));
    }


    public function getWarehouses() {
        $json = array();

        if (isset($this->request->get['filter']) && $this->config->get('novaposhta_api_key')) {

            $response = $this->getResponse(
                $this->getRequest('Address', 'getWarehouses', array('CityRef' => $this->request->get['filter']))
            );

            if ($response['success']) {

                $data = $response['data'];
                foreach ($data as $item) {
                    if ($this->language->get('code') == 'ru') {
                        $json[] = array(
                            'warehouse' => (string)$item['DescriptionRu']
                        );
                    } else {
                        $json[] = array(
                            'warehouse' => (string)$item['Description']
                        );
                    }
                }
            }
        }

        $this->response->setOutput(json_encode($json));
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

    private function validate() {
        if (!$this->user->hasPermission('modify', 'shipping/novaposhta')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
