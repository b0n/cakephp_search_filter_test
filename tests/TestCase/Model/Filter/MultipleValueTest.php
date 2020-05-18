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
     *
     * 下記の設定が必須でした
     * - config['delimiter']
     * - config['comparison']
     */
    public function testProcess()
    {
        $filter = $this->processFilter(['title' => 'テスト,ハット']);
        $actual = $filter->getQuery()->sql();
        $this->assertNotContains('WHERE (Topics.title = :c0 OR Topics.title = :c1)', $actual);

        $this->assertArrayNotHasKey('delimiter', $filter->getConfig());
        $this->assertArrayNotHasKey('comparison', $filter->getConfig());
        $this->assertArrayNotHasKey('acceptNull', $filter->getConfig());
        $this->assertArrayNotHasKey('mode', $filter->getConfig());
    }

    /**
     * configを設定したテスト
     */
    public function testProcessWithConfig()
    {
        $filter = $this->processFilter([
            'title' => 'テスト,ハット'
        ], [
            'delimiter' => ',',
            'comparison' => '=',
            'acceptNull' => false,
            'mode' => 'OR',
        ]);
        $sql = $filter->getQuery()->sql();
        $this->assertContains('WHERE (Topics.title = :c0 OR Topics.title = :c1)', $sql);

        $this->assertArrayHasKey('delimiter', $filter->getConfig());
        $this->assertArrayHasKey('comparison', $filter->getConfig());
        $this->assertArrayHasKey('acceptNull', $filter->getConfig());
        $this->assertArrayHasKey('mode', $filter->getConfig());

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['テスト'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['ハット'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * config['mode']を設定したテスト
     */
    public function testConfigMode()
    {
        $filter = $this->processFilter([
            'title' => 'テスト,ハット'
        ], [
            'delimiter' => ',',
            'comparison' => '=',
            'acceptNull' => false,
            'mode' => 'AND',
        ]);
        $sql = $filter->getQuery()->sql();
        $this->assertContains('WHERE (Topics.title = :c0 AND Topics.title = :c1)', $sql);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['テスト'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['ハット'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * config['comparion']を設定したテスト
     */
    public function testConfigComparison()
    {
        $filter = $this->processFilter([
            'title' => 'テスト,ハット'
        ], [
            'delimiter' => ',',
            'comparison' => '<>',
            'acceptNull' => false,
            'mode' => 'AND',
        ]);
        $sql = $filter->getQuery()->sql();
        $this->assertContains('WHERE (Topics.title <> :c0 AND Topics.title <> :c1)', $sql);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['テスト'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['ハット'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * config['acceptNull']を設定したテスト
     */
    public function testConfigAcceptNull()
    {
        $filter = $this->processFilter([
            'title' => 'テスト,ハット'
        ], [
            'delimiter' => ',',
            'comparison' => '<>',
            'acceptNull' => true,
            'mode' => 'AND',
        ]);
        $sql = $filter->getQuery()->sql();
        $expected = 'WHERE ((Topics.title <> :c0 AND Topics.title <> :c1) OR Topics.title IS NULL)';
        $this->assertContains($expected, $sql);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['テスト'], Hash::extract($bindings, ':c0.value'));
        $this->assertEquals(['ハット'], Hash::extract($bindings, ':c1.value'));
    }

    /**
     * @param array $args
     * @param array $config
     * @return MultipleValue
     */
    protected function processFilter(array $args = [], array $config = [])
    {
        $topics = TableRegistry::getTableLocator()->get('Topics');
        $manager = new Manager($topics);
        $filter = new MultipleValue('title', $manager);
        $filter->setArgs($args);
        $filter->setConfig($config);
        $filter->setQuery($topics->find('all', ['fields' => 'id']));
        $filter->process();
        return $filter;
    }
}
