<?php
namespace App\Test\TestCase\Controller;

use App\Controller\TopicsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\TopicsController Test Case
 *
 * @uses \App\Controller\TopicsController
 */
class TopicsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Topics',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/topics');
        $this->assertResponseOk();
    }

    /**
     */
    public function testIndexQuery()
    {
        $this->get('/topics?page=1');
        $this->assertResponseOk();

        $this->get('/topics?page=2');
        $this->assertResponseError();
    }

    /**
     */
    public function testIndexTitle()
    {
        $this->get('/topics?title=' . rawurlencode('テスト投稿です'));
        $this->assertResponseContains('テスト投稿です');

        $this->get('/topics?title=' . rawurlencode('テスト'));
        $this->assertResponseNotContains('テスト投稿です');
    }

    /**
     */
    public function testIndexQ()
    {
        $this->get('/topics?q=' . rawurlencode('スト'));
        $this->assertResponseContains('テスト投稿です');

        $this->get('/topics?q=' . rawurlencode('テ　ス　ト'));
        $this->assertResponseNotContains('テスト投稿です');
    }

    /**
     */
    public function testIndexSpace()
    {
        $this->get('/topics?space=' . rawurlencode('スト'));
        $this->assertResponseContains('テスト投稿です');

        $this->get('/topics?space=' . rawurlencode('テ　トリ ス'));
        $this->assertResponseNotContains('テスト投稿です');

        $this->get('/topics?space=' . rawurlencode('テ ス　ト'));
        $this->assertResponseContains('テスト投稿です');
    }
}
