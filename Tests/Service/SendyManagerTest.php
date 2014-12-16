<?php

namespace Tzb\SendyBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Tzb\SendyBundle\Service\SendyManagerInterface;
use Tzb\SendyBundle\Tests\Mocks\SendyManager;
use Tzb\SendyBundle\Tests\Mocks\SendyPHP;

/**
 * Test class for SendyManager
 *
 * @author Juraj Kabát <kabat.juraj@gmail.com>
 */
class SendyManagerTest extends \PHPUnit_Framework_TestCase 
{
    /** @var SendyManager */
    private $manager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // create mock for SendyPHP library
        $sendy = new SendyPHP(array(
            'api_key'           => 'example_key',
            'installation_url'  => 'example.host',
            'list_id'           => 'example_list',
        ));

        // create mock for SendyManager
        $this->manager = new SendyManager('example_key', 'example.host', 'example_list');
        $this->manager->setSendy($sendy);
    }

    /**
     * @test
     */
    public function testImplementedInterface()
    {
        $this->assertTrue($this->manager instanceof SendyManagerInterface);
    }

    /**
     * @test
     */
    public function testGetSubscriberCount()
    {
        $this->assertEquals((int) 2, $this->manager->getSubscriberCount());
    }

    /**
     * @expectedException \Tzb\SendyBundle\SendyException
     */
    public function testGetSubscriberCountWithWrongList()
    {
        $this->manager->getSubscriberCount('wrong_list');
    }

    /**
     * @test
     */
    public function testGetSubscriberStatus()
    {
        $this->assertEquals('Subscribed', $this->manager->getSubscriberStatus('john@example.com'));
    }

    /**
     * @expectedException \Tzb\SendyBundle\SendyException
     */
    public function testGetSubscriberStatusWithWrongEmail()
    {
        $this->manager->getSubscriberStatus('fred@example.com');
    }

    /**
     * @expectedException \Tzb\SendyBundle\SendyException
     */
    public function testGetSubscriberStatusWithWrongList()
    {
        $this->manager->getSubscriberStatus('john@example.com', 'wrong_list');
    }

    /**
     * @test
     */
    public function testSubscribeWithNewEmail()
    {
        $this->assertTrue($this->manager->subscribe('Fred Combs', 'fred@example.com'));
    }

    /**
     * @test
     */
    public function testSubscribeWithExistentEmail()
    {
        $this->assertTrue($this->manager->subscribe('John Doe', 'john@example.com'));
    }

    /**
     * @expectedException \Tzb\SendyBundle\SendyException
     */
    public function testSubscribeWithWrongList()
    {
        $this->manager->subscribe('Fred Combs', 'fred@example.com', 'wrong_list');
    }

    /**
     * @test
     */
    public function testUnsubscribe()
    {
        $this->assertTrue($this->manager->unsubscribe('john@example.com'));
    }

    /**
     * @expectedException \Tzb\SendyBundle\SendyException
     */
    public function testUnsubscribeWithWrongEmail()
    {
        $this->manager->unsubscribe('fred@example.com');
    }

    /**
     * @expectedException \Tzb\SendyBundle\SendyException
     */
    public function testUnsubscribeWithWrongList()
    {
        $this->manager->unsubscribe('fred@example.com', 'wrong_list');
    }
}