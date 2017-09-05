<?php
namespace hxy2015\hbase;

use yii\base\Component;

/**
 * @author huangxy <huangxy10@qq.com>
 */
abstract class HbaseIterator extends Component implements \Countable, \Iterator
{
    abstract function load();

    //public $data = array();

    /**
     * Implement the "current" method of the PHP native Iterator interface.
     *
     * @return mixed Returns current value.
     */
    public function current()
    {
        $this->load();
        return current($this->__data['data']);
    }

    /**
     * Return the key of the current element.
     *
     * Implement the "key" method of the PHP native Iterator interface.
     *
     * @return mixed Returns scalar on success, integer 0 on failure.
     */
    public function key()
    {
        $this->load();
        return key($this->__data['data']);
    }

    /**
     * Move forward to next element.
     *
     * Implement the "next" method of the PHP native Iterator interface.
     *
     * @return mixed Returns next value.
     */
    public function next()
    {
        $this->load();
        return next($this->__data['data']);
    }

    /**
     * Move backward to previous element.
     *
     * Note, this method is not part the PHP navite Iterator interface.
     *
     * @return mixed Returns previous value.
     */
    public function prev()
    {
        $this->load();
        return prev($this->__data['data']);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * Implement the "rewind" method of the PHP native Iterator interface.
     *
     * @return mixed The value of the first array element, or FALSE if the array is empty.
     */
    public function rewind()
    {
        $this->load();
        return reset($this->__data['data']);
    }

    /**
     * Checks if current position is valid.
     *
     * Implement the "valid" method of the PHP native Iterator interface.
     *
     * @return boolean Wether the iterated array is in a valid state or not.
     */
    public function valid()
    {
        return (bool)$this->current();
    }

    /**
     * Count the number of stored elements.
     *
     * Implement the "count" method of the PHP native Countable interface.
     *
     * return integer Number of stored elements.
     */
    public function count()
    {
        $this->load();
        return count($this->__data['data']);
    }

}