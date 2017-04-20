<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\NameResolver;

use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\NameResolver\EventClassResolver;
use GpsLab\Domain\Event\NameResolver\NameResolverContainer;

class NameResolverContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $resolver = NameResolverContainer::getResolver();
        $this->assertInstanceOf(EventClassLastPartResolver::class, $resolver);
        // test lazeload
        $this->assertEquals($resolver, NameResolverContainer::getResolver());
    }

    public function testChangeDefault()
    {
        $resolver = new EventClassResolver();

        NameResolverContainer::changeResolver($resolver);

        $this->assertEquals($resolver, NameResolverContainer::getResolver());
        // test lazeload
        $this->assertEquals($resolver, NameResolverContainer::getResolver());
    }
}
