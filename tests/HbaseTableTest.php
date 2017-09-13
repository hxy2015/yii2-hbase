<?php
namespace yiiunit\extensions\hbase;

use hxy2015\hbase\HbaseTable;

class HbaseTableTest extends HbaseTestCase
{
    public function setUp()
    {
        $tables = $this->getConnection()->tables();
        if ($tables->exists('hbase_test_yii2')) {
            $tables->create('hbase_test_yii2');
        }
    }

    public function testTable()
    {
        $table = $this->getConnection()->tables()->table('hbase_test_yii2');
        $this->assertInstanceOf(HbaseTable::className(), $table);
    }
}
