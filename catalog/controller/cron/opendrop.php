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

          $filename=explode('.',basename(parse_url($url)['path']))[0];

          $data=file_get_contents($url);
           $load_xml=DIR_DOWNLOAD_OPENDROP.$filename.'.xml';
       /* debug($load_xml);
        die();*/
          file_put_contents($load_xml, $data);
          if(!is_file($load_xml))
          {
              die('ERROR LOAD XML');
          }

           $res=$this->model_cron_opendrop->parseXml($load_xml,$supplier['id']);

          debug($res);




    }
}