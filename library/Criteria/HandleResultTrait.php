<?php
/**
 * MongoDB matryoshka wrapper
 *
 * @link        https://github.com/matryoshka-model/mongo-wrapper
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace Matryoshka\Model\Wrapper\Mongo\Criteria;

use Matryoshka\Model\Wrapper\Mongo\Exception\MongoResultException;

/**
 * Class HandleResultTrait
 */
trait HandleResultTrait
{
    /**
     * @param $result
     * @param $isRemoveOperation
     * @return int|null
     */
    protected function handleResult($result, $isRemoveOperation = false)
    {
        // No info available
        if ($result === true) {
            return null;
        }

        if (is_array($result)) {
            // $result['ok'] should always be 1 (unless last_error itself failed)
            if (isset($result['ok']) && $result['ok']) {
                if ($isRemoveOperation || isset($result['updatedExisting'])) {
                    return isset($result['n']) ? (int) $result['n'] : null;
                } else {
                    return 1; // MongoDB returns 0 on insert operation
                }
            }

            if (isset($result['err']) && $result['err'] !== null) {
                throw new MongoResultException($result['errmsg'], $result['code']);
            }
        }

        return null;
    }
}
