<?php
class ControllerExtensionModuleOpendrop extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/opendrop');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_opendrop', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/opendrop', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/opendrop', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_opendrop_status'])) {
            $data['module_opendrop_status'] = $this->request->post['module_opendrop_status'];
        } else {
            $data['module_opendrop_status'] = $this->config->get('module_opendrop_status');
        }
         $data['suppliers_href']=$this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'], true);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/opendrop', $data));
    }

    public function delete() {

        $this->load->language('extension/module/opendrop');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/opendrop');


        if (isset($this->request->post['selected']) ) {

            foreach ($this->request->post['selected'] as $id) {
                $this->model_extension_module_opendrop->deleteSupplier($id);
            }

            $this->session->data['success'] = 'delete success';

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->suppliers();
    }



    public function suppliers()
    {

        $this->load->language('extension/module/opendrop');
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/opendrop', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('all_suppliers'),
            'href' => $this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );
        $data['column_id']=$this->language->get('column_id');
        $data['column_name']=$this->language->get('column_name');
        $data['column_qty']=$this->language->get('column_qty');
        $data['column_status']=$this->language->get('column_status');
        $data['column_data_synchron']=$this->language->get('column_data_synchron');
        $data['column_data_added']=$this->language->get('column_data_added');
        $data['column_feed']=$this->language->get('column_feed');
        $data['add'] = $this->url->link('extension/module/opendrop/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/opendrop/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['suppliers']=[];
        $filter_data=[
        'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin'),
        ];
       $this->load->model('extension/module/opendrop');
        $suppliers_total = $this->model_extension_module_opendrop->getTotalSuppliers();
        $results=$this->model_extension_module_opendrop->getSuppliers($filter_data);

        foreach ($results as $result) {
            $data['suppliers'][] = array(
                'id' => $result['id'],
                'name'        => $result['name'],
                'qty'  => $result['qty'],
                'status'  => $result['status'],
                'date_syn'  => $result['date_syn'],
                'date_added'  => $result['date_added'],
                'feed'  => $result['feed'],


                'edit'        => $this->url->link('extension/module/opendrop/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, true),

            );
        }

        $pagination = new Pagination();
        $pagination->total = $suppliers_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($suppliers_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
            ((($page - 1) * $this->config->get('config_limit_admin')) > ($suppliers_total - $this->config->get('config_limit_admin'))) ?
                $suppliers_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
            $suppliers_total, ceil($suppliers_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/suppliers_list', $data));


    }





    public function install()
    {
        $this->load->model('extension/module/opendrop');
        $this->load->model('setting/setting');
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `suppliers` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(64) DEFAULT NULL,
                  `qty` int(11) DEFAULT NULL,
                  `status` tinyint(1) DEFAULT NULL,
                   `date_syn` datetime NOT NULL,
                   `date_added` datetime NOT NULL,
                   `feed`  varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `opd_supplier_categories` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `supplier_id` int(11) DEFAULT NULL,
                  
                  `category_name` varchar(64) DEFAULT NULL,
                  
                  `category_id` int(11) DEFAULT NULL,
                   
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `opd_supplier_attributes` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `supplier_id` int(11) DEFAULT NULL,
                  
                  `attribute_name` varchar(64) DEFAULT NULL,
                  
                  `attribute_id` int(11) DEFAULT NULL,
                   
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `suppliers`");
        $this->db->query("DROP TABLE IF EXISTS `opd_supplier_categories`");
        $this->db->query("DROP TABLE IF EXISTS `opd_supplier_attributes`");
    }


    public function edit()
    {
        $this->load->language('extension/module/opendrop');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/opendrop');



        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_extension_module_opendrop->editSupplier($this->request->get['id'],$this->request->post);

            $this->session->data['success'] = $this->language->get('text_success_edit');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();



    }








    public function add() {
        $this->load->language('extension/module/opendrop');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/opendrop');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
//debug($this->request->post);
//die();
            $this->model_extension_module_opendrop->addSupplier($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

       $this->getForm();
    }

    protected function validateForm() {
        $post=$this->request->post;
        if (!$this->user->hasPermission('modify', 'extension/module/opendrop')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($post['name']) < 1) || (utf8_strlen($post['name']) > 255)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        if ((utf8_strlen($post['feed']) < 1) || (utf8_strlen($post['feed']) > 955)) {
            $this->error['feed'] = $this->language->get('error_feed');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }


















    protected function getForm() {

        $data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['feed'])) {
            $data['error_feed'] = $this->error['feed'];
        } else {
            $data['error_feed'] = '';
        }


        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/opendrop', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('all_suppliers'),
            'href' => $this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );



        if (!isset($this->request->get['id'])) {
            $data['action'] = $this->url->link('extension/module/opendrop/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/opendrop/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/opendrop/suppliers', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $supplier_info = $this->model_extension_module_opendrop->getSupplier($this->request->get['id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

      $data['entry_name']=$this->language->get('entry_name');//entry_feed
        $data['entry_feed']=$this->language->get('entry_feed');//entry_feed


        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($supplier_info)) {
            $data['name'] = $supplier_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['qty'])) {
            $data['qty'] = $this->request->post['qty'];
        } elseif (!empty($supplier_info['qty'])) {
            $data['qty'] = $supplier_info['qty'];
        } else {
            $data['qty'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($supplier_info)) {
            $data['status'] = $supplier_info['status'];
        } else {
            $data['status'] = 0;
        }


        if (isset($this->request->post['date_syn'])) {
            $data['date_syn'] = $this->request->post['date_syn'];
        } elseif (!empty($supplier_info)) {
            $data['date_syn'] = $supplier_info['date_syn'];
        } else {
            $data['date_syn'] = '';
        }

        if (isset($this->request->post['date_added'])) {
            $data['date_added'] = $this->request->post['date_added'];
        } elseif (!empty($supplier_info)) {
            $data['date_added'] = $supplier_info['date_added'];
        } else {
            $data['date_added'] = '';
        }

        if (isset($this->request->post['feed'])) {
            $data['feed'] = $this->request->post['feed'];
        } elseif (!empty($supplier_info)) {
            $data['feed'] = $supplier_info['feed'];
        } else {
            $data['feed'] = '';
        }







        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/supplier_form', $data));
    }



    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/account')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}