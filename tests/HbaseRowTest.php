<?php
namespace yiiunit\extensions\hbase;

use hxy2015\hbase\HbaseTables;

class HbaseRowTest extends HbaseTestCase
{
    public $testTable = 'test_hxy_hbase';

    /**
     * @var HbaseTables
     */
    public $tables;

    public function setUp()
    {
        $this->tables = $this->getConnection()->tables();
        if (!$this->tables->exists($this->testTable)) {
            $this->tables->create($this->testTable, 'base_info');
        }
    }

    public function testPut()
    {
        $row = $this->tables->table($this->testTable)->row('hehe');
        $row->put('base_info:username', 'zhangsan3');
        $row->put('base_info:age', '14');
        
        $this->assertSame('zhangsan3', $row->get('base_info:username'));
    }

    public function testGet()
    {
        $row = $this->tables->table($this->testTable)->row('haha');
        $row->delete();

        // row不存在
        $this->assertSame(null, $row->get('base_info'));

        // row不存在
        $this->assertSame(null, $row->get('base_info:hehe'));

        $row->put('base_info:username', 'zhangsan3');
        $row->put('base_info:age', '14');

        // 获取 column family
        $this->assertEquals(['username' => 'zhangsan3', 'age' => 14], $row->get('base_info'));

        // 获取 column
        $this->assertSame('zhangsan3', $row->get('base_info:username'));

        $row->delete();
    }

    public function testProfile()
    {
        $table = $this->tables->table($this->testTable);

        $value = [];
        for ($i=0; $i<30; $i++) {
            $value[] = [
                    "zx_usernm" => "好朋友", 
                    "list_username"=> "hehe",
                    "type" => 1,
                    "fx_usernm" => "小红",
                    "credit_tag" => "小贷",
                    "hack_tag" => "hack",
                    "is_business" => 1,
                    "upload_time" => "2017-09-10 10:12:23",
                    "udid" => "abcd",
                    "dfp" => "abcd",
                    "upload_count" => 24
            ];
        }

        $row = $table->row('heheda');
        $row->put('base_info:123432334', json_encode($value));
    }

    public function testReadProfile()
    {
        $table = $this->tables->table($this->testTable);
        $row = $table->row('heheda');
        $value = $row->get('base_info:123432334');
        echo count(json_decode($value, true)) . PHP_EOL;
    }
}
