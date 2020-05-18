<?php


namespace TestCase\Model;

use App\Model\Filter\WithoutSpaceFilter;
use Cake\ORM\TableRegistry;
use \Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Search\Manager;

class WithoutSpaceFilterTest extends TestCase
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
     * 空白なしのテストをします
     */
    public function testProcess()
    {
        $filter = $this->processFilter(['title' => 'テスト投稿です']);

        $pattern = preg_quote("WHERE Topics.title LIKE :c0", '/');
        $string = $filter->getQuery()->sql();
        $this->assertRegExp("/{$pattern}/", $string);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['%テスト投稿です%'], Hash::extract($bindings, ':c0.value'));
    }

    /**
     * スペースの変換をテストします
     */
    public function testSpace()
    {
        $filter = $this->processFilter(['title' => 'テスト　投稿 です']);

        $pattern = preg_quote("WHERE Topics.title LIKE :c0", '/');
        $string = $filter->getQuery()->sql();
        $this->assertRegExp("/{$pattern}/", $string);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['%テスト投稿です%'], Hash::extract($bindings, ':c0.value'));
    }

    /**
     * 検索キーワードがセットされない場合
     */
    public function testNull()
    {
        $filter = $this->processFilter([]);
        $actual = $filter->getQuery()->sql();
        $expected = 'SELECT Topics.id AS `Topics__id` FROM topics Topics';
        $this->assertSame($expected, $actual, '検索キーワードが指定されていないと条件にならない');
    }

    /**
     * 検索キーワードがnullの場合
     */
    public function testValueNull()
    {
        $filter = $this->processFilter(['title' => null]);
        $actual = $filter->getQuery()->sql();
        $expected = 'SELECT Topics.id AS `Topics__id` FROM topics Topics';
        $this->assertSame($expected, $actual, '検索キーワードがnullだと条件にならない');
    }

    /**
     * 検索キーワードが空白の場合
     */
    public function testValueEmpty()
    {
        $filter = $this->processFilter(['title' => '']);

        $pattern = "WHERE Topics.title LIKE :c0";
        $pattern = preg_quote($pattern, '/');
        $string = $filter->getQuery()->sql();
        $this->assertRegExp("/{$pattern}/", $string);

        $bindings = $filter->getQuery()->getValueBinder()->bindings();
        $this->assertEquals(['%%'], Hash::extract($bindings, ':c0.value'));
    }

    /**
     * @param array $args
     * @return WithoutSpaceFilter
     */
    protected function processFilter(array $args = [])
    {
        $topics = TableRegistry::getTableLocator()->get('Topics');
        $manager = new Manager($topics);
        $filter = new WithoutSpaceFilter('title', $manager);
        $filter->setArgs($args);
        $filter->setQuery($topics->find('all', ['fields' => 'id']));
        $filter->process();
        return $filter;
    }
}
