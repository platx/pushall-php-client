<?php
/**
 * @author Vladyslav Platonov <vlad@plat-x.com>
 * @link http://plat-x.com/
 */

namespace platx\pushall;

require __DIR__.'/bootstrap.php';

use platx\pushall\exceptions\InvalidIdException;
use platx\pushall\exceptions\InvalidKeyException;
use platx\pushall\exceptions\RequiredParameterException;


/**
 * Class PushAll
 * @package platx\pushall
 *
 * See README.md for usage
 */
class PushAll 
{
    /**
     * Message type constants
     */
    const TYPE_SELF = 'self';
    const TYPE_BROADCAST = 'broadcast';
    const TYPE_UNICAST = 'unicast';

    /**
     * Hide message constants
     */
    const HIDDEN_FALSE = 0;
    const HIDDEN_HISTORY = 1;
    const HIDDEN_BAND = 2;

    /**
     * Priority constants
     */
    const PRIORITY_DEFAULT = 0;
    const PRIORITY_NOT_IMPORTANT = -1;
    const PRIORITY_IMPORTANT = 1;

    /**
     * Return response type constants
     */
    const RESPONSE_TYPE_JSON = 0;
    const RESPONSE_TYPE_ARRAY = 1;

    /**
     * API URL
     * @var string
     */
    private $_apiUrl = 'https://pushall.ru/api.php';

    /**
     * Feed id
     * @var int
     */
    private $_id;

    /**
     * Feed key
     * @var string
     */
    private $_key;

    /**
     * Message type
     * @var string
     */
    public $type = self::TYPE_SELF;

    /**
     * User id
     * @var int
     */
    public $uid;

    /**
     * Message title
     * @var string
     */
    public $title;

    /**
     * Message text
     * @var string
     */
    public $text;

    /**
     * Icon URL (max - 512kb)
     * @var string
     */
    public $icon;

    /**
     * Message URL
     * @var string
     */
    public $url;

    /**
     * Message hiding
     * @var int
     */
    public $hidden = self::HIDDEN_FALSE;

    /**
     * Message encoding
     * @var string
     */
    public $encode = 'UTF-8';

    /**
     * Message priority
     * @var int
     */
    public $priority = self::PRIORITY_DEFAULT;

    /**
     * @var int
     */
    public $responseType = self::RESPONSE_TYPE_ARRAY;

    /**
     * Entry point
     * @param int $id - feed id
     * @param string $key - feed key
     * @param int|null $responseType - return response type
     * @throws InvalidIdException
     * @throws InvalidKeyException
     * @throws RequiredParameterException
     */
    public function __construct($id, $key, $responseType = null)
    {
        if (empty($id)) {
            throw new RequiredParameterException('Set parameter "id"!');
        } else if(!is_integer($id)) {
            throw new InvalidIdException('Parameter "id" must be integer!');
        }

        if (empty($key)) {
            throw new RequiredParameterException('Set parameter "key"!');
        } else if(!preg_match('/^[a-f0-9]{32}$/i', $key)) {
            throw new InvalidKeyException('Parameter key is wrong!');
        }

        $this->_id = $id;
        $this->_key = $key;

        if(is_null($responseType)) {
            $this->responseType = $responseType;
        }
    }

    /**
     * Send message
     * @param array $params
     * @return mixed
     * @throws RequiredParameterException
     */
    protected function send($params = array())
    {
        if (!empty($params)) {
            $this->setParams($params);
        }

        $this->_validateParams();

        $result = $this->_post();

        switch($this->responseType) {
            case self::RESPONSE_TYPE_ARRAY :
                $result = json_decode($result, true);
                break;
        }

        return $result;
    }

    /**
     * Set parameters from array
     * @param array $params
     */
    public function setParams($params)
    {
        foreach($params as $param => $value) {
            if($this->_hasProperty($param)) {
                $this->{$param} = $value;
            }
        }
    }

    /**
     * Validate api parameters
     * @throws RequiredParameterException
     */
    private function _validateParams()
    {
        if (empty($this->title)) {
            throw new RequiredParameterException('Parameter "title" is required!');
        }
        if (empty($this->text)) {
            throw new RequiredParameterException('Parameter "text" is required!');
        }
        if (empty($this->type)) {
            throw new RequiredParameterException('Parameter "type" is required!');
        }
    }

    /**
     * Get parameters for api request
     * @return array
     */
    public function getParams()
    {
        $params = array(
            'id' => $this->_id,
            'key' => $this->_key,
            'type' => $this->type,
            'title' => $this->title,
            'text' => $this->text,
            'hidden' => $this->hidden,
            'encode' => $this->encode,
            'priority' => $this->priority
        );

        if (!empty($this->url)) {
            $params['url'] = $this->url;
        }

        if (!empty($this->icon)) {
           $params['icon'] = $this->icon;
        }

        return $params;
    }

    /**
     * Send API request
     * @return mixed
     */
    protected function _post()
    {
        if (function_exists('curl_init')) {
            curl_setopt_array($ch = curl_init(), array(
                CURLOPT_URL => $this->_apiUrl,
                CURLOPT_POSTFIELDS => $this->getParams(),
                CURLOPT_SAFE_UPLOAD => true,
                CURLOPT_RETURNTRANSFER => true
            ));

            $result = curl_exec($ch); //получить ответ или ошибку

            curl_close($ch);
        } else {
            $queryParams = http_build_query($this->getParams());

            $result = file_get_contents("{$this->_apiUrl}?{$queryParams}");
        }

        return $result;
    }

    /**
     * Check if class property exists
     * @param $propertyName
     * @return bool
     */
    protected function _hasProperty($propertyName)
    {
        return property_exists($this, $propertyName);
    }
} 