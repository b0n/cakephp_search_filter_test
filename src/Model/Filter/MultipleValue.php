<?php


namespace App\Model\Filter;

use Search\Model\Filter\Base;

/**
 * Class Date
 *
 * MultipleValueフィルタの動作を確認してみます
 *
 * @package App\Model\Filter
 * @see https://github.com/scallacs/fstricks/blob/master/src/Model/Filter/MultipleValue.php
 */
class MultipleValue extends Base
{
    /**
     * @inheritDoc
     */
    public function process()
    {
        return true;
    }
}
