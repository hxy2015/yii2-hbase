<?php
namespace yiiunit\extensions\hbase;

class HbaseTablesTest extends HbaseTestCase
{
    public function testTables()
    {
        $db = $this->getConnection();
        $tables = $db->tables();
        echo count($tables);
    }

    public function testCreate()
    {
        $tables = $this->getConnection()->tables();
        $testTable = 'test_hxy_hbase';
//        $this->assertFalse($tables->exists($testTable));
        $tables->create($testTable, 'base_info');
        $this->assertTrue($tables->exists($testTable));
        $tables->delete($testTable);
        $this->assertFalse($tables->exists($testTable));
    }
}
