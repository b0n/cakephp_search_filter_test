<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Model\Behavior\SearchBehavior;

/**
 * Topics Model
 *
 * @method \App\Model\Entity\Topic get($primaryKey, $options = [])
 * @method \App\Model\Entity\Topic newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Topic[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Topic|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Topic saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Topic patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Topic[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Topic findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin SearchBehavior
 */
class TopicsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('topics');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Search.Search');
        $this->SearchManager()
            ->value('title')
            ->like('q', ['field' => 'title', 'before' => true, 'after' => true])
            ->add('space', 'WithoutSpaceFilter', ['field' => 'title']);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('body')
            ->allowEmptyString('body');

        return $validator;
    }
}
