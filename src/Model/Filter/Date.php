<?php


namespace App\Model\Filter;

use Search\Model\Filter\Base;

/**
 * Class Date
 *
 * croogoのDateフィルタの動作を確認してみます
 *
 * @package App\Model\Filter
 * @see https://github.com/croogo/croogo/blob/master/Core/src/Model/Filter/Date.php
 */
class Date extends Base
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'mode' => 'AND'
    ];

    /**
     * @inheritDoc
     */
    public function process()
    {
        $start = $this->value();
        if (!is_scalar($start)) {
            return false;
        }

        $field = $this->field();
        $end = new DateTime($start);
        $end = $end->add(new DateInterval('P1D'));
        $conditions = [
            $field . ' >' => $start,
            $field . ' <=' => $end->format('Y-m-d H:i:s'),
        ];

        $this->getQuery()->andWhere([$this->getConfig('mode') => $conditions]);

        return true;
    }
}
