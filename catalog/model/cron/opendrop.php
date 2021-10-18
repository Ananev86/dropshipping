<?php
class ModelCronOpendrop extends Model {
            public function getSupplier()
            {
                $sql='select * from suppliers where status=1 order by date_syn asc, name asc limit 1';
                $query=$this->db->query($sql);
                return $query->row;
            }
            public function parseXml($file,$id)
            {


                $this->db->query('update suppliers set date_syn=NOW() where id='.$id);

                $xmlfile=simplexml_load_file($file);
               return $xmlfile;

            }
            public function insertCat($arr_cat)
            {
                  foreach($arr_cat as $cat) {
                $query_cat=$this->db->query('select * from oc_category_description where language_id=1 and name="'.$cat['name'].'"');
                if(!$query_cat->num_rows)
                {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category SET parent_id = 0, `top` = 1, `column` = 0, sort_order = 0,status = 1, date_modified = NOW(), date_added = NOW(),old_id=".$cat['id']."");
                    $category_id = $this->db->getLastId();
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = 1, name = '" . $cat['name'] . "', description = '" . $cat['name'] . "', meta_title = '" . $cat['name'] . "', meta_description = '" . $cat['name'] . "', meta_keyword = '" . $this->db->escape($cat['name']) . "'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = 0");

                    $level = 0;

                    $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = 0");
                    echo  'Создались категории'.$cat['name'].'id-'.$category_id."<br>";
                }
                else { echo 'ne sozdalis'.$cat['name']."<br>";}
            }
            $this->cache->delete('category');
            }
}