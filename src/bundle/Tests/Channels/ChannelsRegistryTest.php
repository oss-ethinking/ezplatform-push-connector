<?php

namespace EzPlatform\PushConnectorBundle\Tests\Channels;

use EzPlatform\PushConnector\Connector\Channels\Exceptions\ChannelNotFoundException;
use EzPlatform\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface;
use EzPlatform\PushConnector\Connector\Channels\Registry\ChannelsRegistry;
use PHPUnit\Framework\TestCase;

class ChannelsRegistryTest extends TestCase
{
    public function testGetChannels()
    {
        $foo = $this->createMock(PushConnectorChannelsInterface::class);
        $bar = $this->createMock(PushConnectorChannelsInterface::class);

        $registry = new ChannelsRegistry([
            'foo' => $foo,
            'bar' => $bar,
        ]);

        $result = $registry->getChannels();

        $this->assertCount(2, $result);
        $this->assertContains($foo, $result);
        $this->assertContains($bar, $result);
    }

    public function testGetChannel()
    {
        $foo = $this->createMock(PushConnectorChannelsInterface::class);

        $registry = new ChannelsRegistry([
            'foo' => $foo,
        ]);

        $this->assertEquals($foo, $registry->getChannel('foo'));
    }

    public function testGetNonExistingChannel()
    {
        $this->expectException(ChannelNotFoundException::class);

        $registry = new ChannelsRegistry([
            'foo' => $this->createMock(PushConnectorChannelsInterface::class),
        ]);

        $registry->getChannel(
            'bar'
        );
    }

    public function testAddChannel()
    {
        $foo = $this->createMock(PushConnectorChannelsInterface::class);
        $foo->method('getName')->willReturn('foo');

        $registry = new ChannelsRegistry();
        $registry->addChannel($foo);
        $this->assertTrue($registry->hasChannel('foo'));
    }

    public function testHasChannel()
    {
        $registry = new ChannelsRegistry([
            'foo' => $this->createMock(PushConnectorChannelsInterface::class),
        ]);

        $this->assertTrue($registry->hasChannel('foo'));
        $this->assertFalse($registry->hasChannel('bar'));
    }
}
