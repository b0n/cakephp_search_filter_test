<?php


namespace TestCase\Model;

use App\Model\Filter\Date;
use Cake\ORM\TableRegistry;
use \Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Search\Manager;

class DateFilterTest extends TestCase
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
     * まる１日を検索でした
     */
    public function testProcess()
    {
        $filter = $this->processFilter(['created' => '2020-04-30 23:59:59']);

        $pattern = preg_quote("WHERE (Topics.created > :c0 AND Topics.created <= :c1)", '/');
        $string = $filter->getQuery()->sql();
        $this->assertRegExp("/{$pattern}/", $string);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['2020-04-30 23:59:59'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['2020-05-01 23:59:59'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * 日付だけだと条件が 2020-05-01 00:00:00 だけになってしまいました
     * 与えられた値はDateTimeとして生成されるのではなく、そのまま使われるから
     */
    public function testOnlyDate()
    {
        $filter = $this->processFilter(['created' => '2020-04-30']);

        $pattern = preg_quote("WHERE (Topics.created > :c0 AND Topics.created <= :c1)", '/');
        $string = $filter->getQuery()->sql();
        $this->assertRegExp("/{$pattern}/", $string);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['2020-04-30'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['2020-05-01 00:00:00'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * @param array $args
     * @return Date
     */
    protected function processFilter(array $args = [])
    {
        $topics = TableRegistry::getTableLocator()->get('Topics');
        $manager = new Manager($topics);
        $filter = new Date('created', $manager);
        $filter->setArgs($args);
        $filter->setQuery($topics->find('all', ['fields' => 'id']));
        $filter->process();
        return $filter;
    }
}
