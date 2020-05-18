<?php


namespace App\Model\Filter;

use Search\Model\Filter\Base;

/**
 * Class MultipleValue
 *
 * MultipleValueフィルタの動作を確認してみます
 *
 * @package App\Model\Filter
 * @see https://github.com/scallacs/fstricks/blob/master/src/Model/Filter/MultipleValue.php
 */
class MultipleValue extends Base
{
    /**
     * Process a LIKE condition ($x LIKE $y).
     * Allow multiple values
     *
     * @return void
     */
    public function process()
    {
        if ($this->skip()) {
            return;
        }
        $values = explode($this->getConfig('delimiter'), $this->value());
        $conditions = [];
        foreach ($this->fields() as $field) {
            $left = $field . ' ' . $this->getConfig('comparison');
            if ($this->getConfig('acceptNull')) {
                $newConditions = [];
                foreach ($values as $value) {
                    $newConditions[] = [$left => $value];
                }
                $conditions = ['OR' => [$this->getConfig('mode') => $newConditions, $field . ' IS NULL']];
            } else {
                foreach ($values as $value) {
                    $conditions[] = [$left => $value];
                }
            }
        }
        $this->getQuery()->andWhere([$this->getConfig('mode') => $conditions]);
    }
}
