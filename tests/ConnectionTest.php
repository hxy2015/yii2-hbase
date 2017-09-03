<?php

namespace yiiunit\extensions\hbase;

/**
 * @author Huangxiaoyuan <huangxy@jiedaibao.com>
 */
class ConnectionTest extends HbaseTestCase
{
    public function testReturn()
    {
        $db = $this->getConnection(true);
        $version = $db->execute('get', 'version');
//        var_dump($version);
        $this->assertTrue(is_array($version));
        $this->assertSame(array('REST','JVM','OS','Server','Jersey'),array_keys($version));
    }

}