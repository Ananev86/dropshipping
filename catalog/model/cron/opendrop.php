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
}