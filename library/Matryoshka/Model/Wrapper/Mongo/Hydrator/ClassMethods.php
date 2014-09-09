<?php
/**
 * MongoDB matryoshka wrapper
 *
 * @link        https://github.com/matryoshka-model/mongo-wrapper
 * @copyright   Copyright (c) 2014, Ripa Club
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace Matryoshka\Model\Wrapper\Mongo\Hydrator;

use Matryoshka\Model\Hydrator\ClassMethods as MatryoshkaClassMethods;
use Matryoshka\Model\Wrapper\Mongo\Hydrator\Strategy\MongoIdStrategy;
use Matryoshka\Model\Wrapper\Mongo\Hydrator\Strategy\NamingStrategy;

/**
 * Class ClassMethods
 */
class ClassMethods extends MatryoshkaClassMethods
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->setNamingStrategy(new NamingStrategy());
        $this->addStrategy('_id', new MongoIdStrategy());
    }
}
