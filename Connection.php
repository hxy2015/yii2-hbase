<?php

namespace hxy2015\hbase;

use Exception;
use Yii;
use yii\base\Component;

/**
 * Connection to hbase
 * 
 * @author Huangxiaoyuan <huangxy@jiedaibao.com>
 */
class Connection extends Component
{
    /**
     * @event Event an event that is triggered after a DB connection is established
     */
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * @var string the hostname or ip address to use for connecting to the redis server. Defaults to 'localhost'.
     */
    public $host = 'localhost';
    /**
     * @var integer the port to use for connecting to the redis server. Default port is 8086.
     */
    public $port = 8086;

    public $alive = 1;
    /**
     * @var float timeout to use for connection to hbase. If not set the timeout set in php.ini will be used: `ini_get("default_socket_timeout")`.
     */
    public $connectionTimeout = null;
    /**
     * @var float timeout to use for hbase when reading and writing data. If not set the php default value will be used.
     */
    public $dataTimeout = null;

    /**
     * @var resource the curl instance returned by [curl_init()](http://php.net/manual/en/function.curl-init.php).
     */
    private $_curl;

    /**
     * Closes the connection when this component is being serialized.
     * @return array
     */
    public function __sleep()
    {
        $this->close();

        return array_keys(get_object_vars($this));
    }

    /**
     * Returns a value indicating whether the DB connection is established.
     * @return boolean whether the DB connection is established
     */
    public function getIsActive()
    {
        return $this->_curl !== null;
    }

    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     * @throws Exception if connection fails
     */
    public function open()
    {
        if ($this->_curl !== null) {
            return;
        }
        $this->_curl = curl_init();

        Yii::trace('Opening connection to hbase. host: ' . $this->host . ' port: ' . $this->port, __CLASS__);
        $this->initConnection();
    }

    /**
     * Initializes the DB connection.
     * This method is invoked right after the DB connection is established.
     * The default implementation triggers an [[EVENT_AFTER_OPEN]] event.
     */
    protected function initConnection()
    {
        $this->trigger(self::EVENT_AFTER_OPEN);
    }

    public function close()
    {
        curl_close($this->_curl);
    }

    /**
     * Create a DELETE HTTP request.
     */
    public function delete($url){
        return $this->execute('DELETE', $url);
    }

    /**
     * Create a GET HTTP request.
     */
    public function get($url){
        return $this->execute('GET',$url);
    }

    /**
     * Create a POST HTTP request.
     */
    public function post($url, $data){
        return $this->execute('POST', $url, $data);
    }

    /**
     * Create a PUT HTTP request.
     */
    public function put($url, $data=null, $timestamp = null){
        return $this->execute('PUT', $url, $data, true, $timestamp);
    }

    /**
     * Send HTTP REST command.
     *
     * @param $method
     * @param $url
     * @param null $data
     * @param bool $raw
     * @param null $timestamp
     */
    public function execute($method, $url, $data = null, $raw = false, $timestamp = null) 
    {
        $url = (substr($url, 0, 1) == '/' ? $url : '/'.$url);

        if(is_array($data)){
            $data = json_encode($data);
        }

        $method = strtoupper($method);
        
        if (isset($timestamp) && $method == 'PUT') {
            $curl_http_headers =  array(
                'Content-Type: application/octet-stream',
                'Accept: application/octet-stream',
                'Connection: ' . ( $this->alive ? 'Keep-Alive' : 'Close' ),
                'X-Timestamp: ' . $timestamp
            );
        } else {
            $curl_http_headers =  array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Connection: ' . ( $this->alive ? 'Keep-Alive' : 'Close' )
            );
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://'.$this->host.':'.$this->port.$url);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curl_http_headers);
//        curl_setopt($curl, CURLOPT_VERBOSE, !empty($this->options['verbose']));
        switch($method){
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_PUT, 1);
                $file = tmpfile();
                fwrite($file, $data);
                fseek($file, 0);
                curl_setopt($curl, CURLOPT_INFILE, $file);
                curl_setopt($curl, CURLOPT_INFILESIZE, strlen($data));
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, 1);
                break;
        }
//        $start = 1000*microtime(true);
        $data = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//        if ($http_code < 200 || $http_code >=300) {
//            throw new Exception('Error ' . $url. ' in response\n ' . $data);
//        }
//        echo 'http://'.$this->host.':'.$this->port.$url . '  cost: ' . round((microtime(true)*1000 - $start)/1000, 3).  PHP_EOL;

        list($_headers,$body) = explode("\r\n\r\n", $data, 2);
        $_headers = explode("\r\n",$_headers);
        $headers = array();
        foreach($_headers as $_header){
            if ( preg_match( '(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $_header, $matches ) ) {
                $headers['version'] = $matches['version'];
                $headers['status']  = (int) $matches['status'];
            } else {
                list( $key, $value ) = explode( ':', $_header, 2 );
                $headers[strtolower($key)] = ltrim( $value );
            }
        }

        curl_close($curl);
        switch(strtoupper($method)){
            case 'PUT':
                fclose($file);
                break;
        }

        return $raw ? $body : json_decode($body, true);
    }

    private $tables;

    public function tables()
    {
        if(isset($this->tables)) {
            return $this->tables;
        }
        return $this->tables = new HbaseTables(['db' => $this]);
    }
}