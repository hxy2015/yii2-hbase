<?php
namespace hxy2015\hbase;
use yii\base\Component;

/**
 * @author Huangxiaoyuan <huangxy@jiedaibao.com>
 */
class HbaseTable extends Component
{
    /**
     * @var Connection
     */
    public $db;

    /**
     * @var string table name
     */
    public $table;
    

    public function create()
    {
        call_user_func_array(
            array($this->db->tables, 'create'),
            array_merge(array($this->name), func_get_args()));
        return $this;
    }

    public function delete()
    {
        $this->db->tables->delete($this->name);
        return $this;
    }

    public function exists()
    {
        return $this->db->tables->exists($this->name);
    }

    public function row($row)
    {
        return new HbaseRow([
            'db' => $this->db, 
            'table' => $this->table, 
            'key' => $row
        ]);
    }
}