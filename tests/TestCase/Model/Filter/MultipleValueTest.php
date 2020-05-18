<?php


namespace TestCase\Model;

use App\Model\Filter\Date;
use App\Model\Filter\MultipleValue;
use Cake\ORM\TableRegistry;
use \Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Search\Manager;

class MultipleValueTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Topics',
    ];

    /**
     * 検索キーワードを複数渡せるテストでした
     */
    public function testProcess()
    {
        $filter = $this->processFilter(['title' => 'テスト,ハット']);

        $pattern = preg_quote("WHERE (Topics.title = :c0 OR Topics.title = :c1)", '/');
        $string = $filter->getQuery()->sql();
        $this->assertRegExp("/{$pattern}/", $string);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['テスト'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['ハット'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * @param array $args
     * @return Date
     */
    protected function processFilter(array $args = [])
    {
        $topics = TableRegistry::getTableLocator()->get('Topics');
        $manager = new Manager($topics);
        $filter = new MultipleValue('created', $manager);
        $filter->setArgs($args);
        $filter->setQuery($topics->find('all', ['fields' => 'id']));
        $filter->process();
        return $filter;
    }
}
