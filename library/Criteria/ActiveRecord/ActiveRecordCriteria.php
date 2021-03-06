<?php
/**
 * MongoDB matryoshka wrapper
 *
 * @link        https://github.com/matryoshka-model/mongo-wrapper
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace Matryoshka\Model\Wrapper\Mongo\Criteria\ActiveRecord;

use Matryoshka\Model\Criteria\ActiveRecord\AbstractCriteria;
use Matryoshka\Model\Exception;
use Matryoshka\Model\ModelStubInterface;
use Matryoshka\Model\Wrapper\Mongo\Criteria\HandleResultTrait;

/**
 * Class ActiveRecordCriteria
 */
class ActiveRecordCriteria extends AbstractCriteria
{
    use HandleResultTrait;

    /**
     * Mongo projection
     *
     * Optional. Controls the fields to return, or the projection.
     * Extended classes can override this property in order to
     * control the fields to return.
     *
     * @var array
     */
    protected $projectionFields = [];

    /**
     * @var array
     */
    protected $mongoOptions = [];

    /**
     * Get options for Mongo save and remove operations
     *
     * @return array
     */
    public function getMongoOptions()
    {
        return $this->mongoOptions;
    }

    /**
     * Set options for Mongo save and remove operations
     *
     * @param array $options
     * @return $this
     */
    public function setMongoOptions(array $options)
    {
        $this->mongoOptions = $options;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ModelStubInterface $model)
    {
        /** @var $dataGateway \MongoCollection */
        $dataGateway = $model->getDataGateway();
        return $dataGateway->find(
            ['_id' => $this->extractId($model)],
            $this->projectionFields
        )->limit(1);
    }

    /**
     * {@inheritdoc}
     */
    public function applyWrite(ModelStubInterface $model, array &$data)
    {
        /** @var $dataGateway \MongoCollection */
        $dataGateway = $model->getDataGateway();

        if ($this->hasId()) {
            $data['_id'] = $this->extractId($model);
        }

        if (array_key_exists('_id', $data) && null === $data['_id']) {
            unset($data['_id']);
        }

        $tmp = $data;  // passing a referenced variable to save will fail in update the content
        $result = $dataGateway->save($tmp, $this->getMongoOptions());
        $data = $tmp;
        return $this->handleResult($result);
    }

    /**
     * {@inheritdoc}
     */
    public function applyDelete(ModelStubInterface $model)
    {
        $result = $model->getDataGateway()->remove(
            ['_id' => $this->extractId($model)],
            ['justOne' => true] + $this->getMongoOptions()
        );
        return $this->handleResult($result, true);
    }

    /**
     * @param ModelStubInterface $model
     * @return mixed
     */
    protected function extractId(ModelStubInterface $model)
    {
        $hydrator = $model->getHydrator();
        if (!method_exists($hydrator, 'extractValue')) {
            throw new Exception\RuntimeException(
                'Hydrator must have extractValue() method ' .
                'in order to extract a single value'
                );
        }
        return $hydrator->extractValue('_id', $this->getId());
    }
}
