<?php

namespace Monooso\Unobserve\Tests;

use Monooso\Unobserve\CanMute;
use Monooso\Unobserve\Proxy;
use Orchestra\Testbench\TestCase;

class CanMuteTest extends TestCase
{
    /** @test */
    public function it_mutes_an_array_of_events()
    {
        CanMuteTarget::mute(['cloaked']);

        $target = resolve(CanMuteTarget::class);

        $this->assertNull($target->cloaked());
        $this->assertSame('uncloaked', $target->uncloaked());
    }

    /** @test */
    public function it_mutes_a_single_event()
    {
        CanMuteTarget::mute('cloaked');

        $target = resolve(CanMuteTarget::class);

        $this->assertNull($target->cloaked());
        $this->assertSame('uncloaked', $target->uncloaked());
    }

    /** @test */
    public function it_mutes_all_events()
    {
        CanMuteTarget::mute();

        $target = resolve(CanMuteTarget::class);

        $this->assertNull($target->cloaked());
        $this->assertNull($target->uncloaked());
    }

    /** @test */
    public function it_unmutes_all_events()
    {
        CanMuteTarget::mute();
        CanMuteTarget::unmute();

        $target = resolve(CanMuteTarget::class);

        $this->assertSame('cloaked', $target->cloaked());
        $this->assertSame('uncloaked', $target->uncloaked());
    }

    /** @test */
    public function it_mutes_class_with_constructor_injection()
    {
        WithConstructorInjection::mute();

        $target = resolve(WithConstructorInjection::class);

        $this->assertInstanceOf(Proxy::class, $target);
    }

    /** @test */
    public function it_resolves_to_proxy_on_mute()
    {
        CanMuteTarget::mute();

        $target = resolve(CanMuteTarget::class);

        $this->assertInstanceOf(Proxy::class, $target);
    }

    /** @test */
    public function it_resolves_back_to_class_on_unmute()
    {
        CanMuteTarget::mute();
        CanMuteTarget::unmute();

        $target = resolve(CanMuteTarget::class);

        $this->assertInstanceOf(CanMuteTarget::class, $target);
    }
}

class CanMuteTarget
{
    use CanMute;

    public function cloaked()
    {
        return 'cloaked';
    }

    public function uncloaked()
    {
        return 'uncloaked';
    }
}

class WithConstructorInjection
{
    use CanMute;

    public function __construct(private CanMuteTarget $injection)
    {
    }
}
