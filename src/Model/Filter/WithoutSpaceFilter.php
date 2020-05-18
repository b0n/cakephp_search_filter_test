<?php
/**
 * Created by PhpStorm.
 * User: takeuchi2
 * Date: 2019-01-15
 * Time: 20:13
 */

namespace App\Model\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Search\Model\Filter\Base;

/**
 * Class WithoutSpaceFilter
 * @package App\Model\Filter
 */
class WithoutSpaceFilter extends Base
{
    /**
     * @return bool|\Cake\Datasource\QueryInterface|null
     */
    public function process()
    {
        if ($this->value() === null) {
            return false;
        }
        $this->getQuery()
            ->where(function (QueryExpression $exp, Query $q) {
                $value = preg_replace('/( |　)/', '', $this->value());
                return $exp->like($this->field(), "%" . $value . "%");
            });
        return true;
    }
}
