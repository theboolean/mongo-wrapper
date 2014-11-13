<?php
/**
 * MongoDB matryoshka wrapper
 *
 * @link        https://github.com/matryoshka-model/mongo-wrapper
 * @copyright   Copyright (c) 2014, Ripa Club
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace Matryoshka\Model\Wrapper\Mongo\Hydrator\Strategy;

use DateTime;
use MongoDate;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Class MongoDateStrategy
 */
class MongoDateStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    protected $format;

    /**
     * Ctor
     * @param null|string $format
     */
    public function __construct($format = null)
    {
        $this->setFormat(DateTime::ISO8601);
        if ($format !== null) {
            $this->setFormat($format);
        }
    }

    /**
     * @param mixed $value
     * @return DateTime|mixed
     */
    public function extract($value)
    {
        if ($value instanceof DateTime) {
            $value = new MongoDate($value->format('U'));
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed|MongoDate
     */
    public function hydrate($value)
    {
        if ($value instanceof MongoDate) {

            $value = new DateTime(date($this->getFormat(), $value->sec));
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}