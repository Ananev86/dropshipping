<?php
class ControllerCronOpendrop extends Controller {
    public function index()
    {

        if (!file_exists(DIR_DOWNLOAD.'opendrop')) {     mkdir(DIR_DOWNLOAD.'opendrop', 0777, true); }
        define('DIR_DOWNLOAD_OPENDROP',DIR_DOWNLOAD.'opendrop/');

        $this->load->model('cron/opendrop');
        $supplier=$this->model_cron_opendrop->getSupplier();
            if(!isset($supplier['feed']))
           {
              die('INPUT CORRECT XML');
           }

        $url  = $supplier['feed'];
      $filename=explode('.',basename(parse_url($url)['path']));
    if(!isset($filename[0]) && !isset($filename[1]))
    {
        die('ERROR EXTENSION OF FEED');
    }
    $filename_feed=$filename[0];
    $filename_ext=$filename[1];

        switch ($filename_ext) {
            case 'xml':
                   $data=file_get_contents($url);
          $load_xml=DIR_DOWNLOAD_OPENDROP.$filename_feed.$supplier['id'].'.xml';

         file_put_contents($load_xml, $data);
         if(!is_file($load_xml))
         {
             die('ERROR LOAD XML');
         }
      /*   debug($supplier);
         die();*/
          $res=$this->parseXml($load_xml,$supplier['id'],$supplier['name']);
         debug($res);


                break;
            case 'eml':
                echo "eml";
                break;
            default:
                echo "default";
                break;
        }


    }
    public function doArrayCat($categories)
    {
        $arrcat=[];
        foreach($categories as $cat)
        {
            $cat=(array)$cat;
           $id=$cat['@attributes']['id'];
           $name_cat=$cat[0];
           $arrcat[]=[
            'id'  =>$id,
            'name'=> $name_cat ,
           ];
        }
        return $arrcat;
    }
    public function doArrOffers($offers)
    {
        $items=[];
        foreach($offers as $item) {

            $item=(array)$item;
            // debug($items);
            $attr=[];
            //paramstart
            foreach($item['param'] as $param) {
                // debug($param);
                $param=(array)$param;
                $attr[]=[
                    'name'=>$param['@attributes']['name'],
                    'value'=>$param[0],
                ];
            }
            //paramend
            $model_id= $item['@attributes']['id'];

            $items[]=[
                'model_id'=>$model_id,
                'available'=>$item['@attributes']['available'],
                'name'=>$item['name'],
                'price'=>$item['price'],
                'oldprice'=>isset($item['oldprice'])?$item['oldprice']:0,
                'categoryId'=>$item['categoryId'],
                'vendor'=>$item['vendor'],
                'description'=>$item['description'],
                'images'=>isset($item['picture'])?$item['picture']:[],
                'attr'=>$attr,

            ];

        }

        return $items;

    }


    //model_cron_opendrop
    public function parseXml($file,$supplier_id,$name_supplier)
    {
        //ALTER TABLE `oc_category` ADD `old_id` INT NULL AFTER `date_modified`, ADD INDEX (`old_id`);
        $xmlfile=simplexml_load_file($file);
        $categories=$xmlfile->shop->categories->category;
        //Загрузили категории с фида старт
        $arr_cat=$this->doArrayCat($categories);
        if($arr_cat)
        {
            $this->model_cron_opendrop->insertCat($arr_cat);


         }
        //Загрузили категории с фида конец

       $offers= $xmlfile->shop->offers->offer;
       $arr_offers=$this->doArrOffers($offers);

      if($arr_offers)
      {

             foreach($arr_offers as $i=>$product)
             {
                 if($i==2)
                 {
                     break;
                 }

                 $query_product=$this->db->query('select * from oc_product where  model="'.$product['model_id'].'"');
                 if($query_product->num_rows)
                 {
                     debug('update');
                 } else
                 {
                     debug($product);

                  /*   $this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($product['model_id']) . "',
                     sku = '" . $this->db->escape($product['model_id']) . "', upc = '" . $this->db->escape($product['model_id']) . "', 
                     ean = '" . $this->db->escape($product['model_id']) . "', jan = '" . $this->db->escape($product['model_id']) . "', isbn = '" . $this->db->escape($product['model_id']) . "', 
                     mpn = '" . $this->db->escape($product['model_id']) . "', location = '" . $this->db->escape(' ') . "', quantity = '100', 
                     minimum = 1, subtract = 1, stock_status_id = 5,
                      date_available = NOW(), 
                      manufacturer_id = 5, shipping = 1, 
                      price = '" . (float)$product['price'] . "', points = 0, weight = 0, 
                      weight_class_id = 1, length = 1, width = 1, 
                      height = 1, length_class_id = 1, status = 1,
                      tax_class_id = 9, sort_order = 1, date_added = NOW(), date_modified = NOW()");

                     $product_id = $this->db->getLastId();

                     if (isset($data['image'])) {
                         $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
                     }

                     foreach ($data['product_description'] as $language_id => $value) {
                         $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
                     }

                     if (isset($data['product_store'])) {
                         foreach ($data['product_store'] as $store_id) {
                             $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
                         }
                     }

                     if (isset($data['product_attribute'])) {
                         foreach ($data['product_attribute'] as $product_attribute) {
                             if ($product_attribute['attribute_id']) {
                                 // Removes duplicates
                                 $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

                                 foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
                                     $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

                                     $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
                                 }
                             }
                         }
                     }

                     if (isset($data['product_option'])) {
                         foreach ($data['product_option'] as $product_option) {
                             if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                                 if (isset($product_option['product_option_value'])) {
                                     $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

                                     $product_option_id = $this->db->getLastId();

                                     foreach ($product_option['product_option_value'] as $product_option_value) {
                                         $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
                                     }
                                 }
                             } else {
                                 $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
                             }
                         }
                     }



                     if (isset($data['product_special'])) {
                         foreach ($data['product_special'] as $product_special) {
                             $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
                         }
                     }

                     if (isset($data['product_image'])) {
                         foreach ($data['product_image'] as $product_image) {
                             $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
                         }
                     }



                     if (isset($data['product_category'])) {
                         foreach ($data['product_category'] as $category_id) {
                             $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                         }
                     }

                     $this->cache->delete('product');*/

                     debug('add');
                 }
             }




        /*  debug($name_supplier);
          debug($arr_offers);*/
      }





        $this->db->query('update suppliers set date_syn=NOW() where id='.$supplier_id);

    }
}