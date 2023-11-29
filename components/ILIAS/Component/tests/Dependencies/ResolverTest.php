<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

namespace ILIAS\Component\Tests\Dependencies;

use PHPUnit\Framework\TestCase;
use ILIAS\Component\Dependencies\Resolver;
use ILIAS\Component\Dependencies as D;
use ILIAS\Component\Dependencies\ResolutionDirective as RD;
use ILIAS\Component\Component;

interface Component1 extends Component
{
}

interface Component2 extends Component
{
}

interface Component3 extends Component
{
}

interface Component4 extends Component
{
}

class ResolverTest extends TestCase
{
    protected Resolver $resolver;

    public function setUp(): void
    {
        $this->resolver = new Resolver();
    }

    public function testEmptyComponentSet(): void
    {
        $result = $this->resolver->resolveDependencies([]);

        $this->assertEquals([], $result);
    }

    public function testResolvePull(): void
    {
        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $pull = new D\In(D\InType::PULL, $name);
        $provide = new D\Out(D\OutType::PROVIDE, $name, null, []);

        $c1 = new D\OfComponent($component, $pull);
        $c2 = new D\OfComponent($component, $provide);

        $result = $this->resolver->resolveDependencies([], $c1, $c2);

        $pull = new D\In(D\InType::PULL, $name);
        $provide = new D\Out(D\OutType::PROVIDE, $name, null, []);

        $c1 = new D\OfComponent($component, $pull);
        $c1->addResolution($pull, $provide);
        $c2 = new D\OfComponent($component, $provide);

        $this->assertEquals([$c1, $c2], $result);
    }

    public function testPullFailsNotExistent(): void
    {
        $this->expectException(\LogicException::class);

        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $pull = new D\In(D\InType::PULL, $name);

        $c1 = new D\OfComponent($component, $pull);

        $this->resolver->resolveDependencies([], $c1);
    }

    public function testPullFailsDuplicate(): void
    {
        $this->expectException(\LogicException::class);

        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $pull = new D\In(D\InType::PULL, $name);
        $provide1 = new D\Out(D\OutType::PROVIDE, $name, null, []);
        $provide2 = new D\Out(D\OutType::PROVIDE, $name, null, []);

        $c1 = new D\OfComponent($component, $pull);
        $c2 = new D\OfComponent($component, $provide1);
        $c3 = new D\OfComponent($component, $provide2);

        $this->resolver->resolveDependencies([], $c1, $c2, $c3);
    }

    public function testEmptySeek(): void
    {
        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $seek = new D\In(D\InType::SEEK, $name);

        $c1 = new D\OfComponent($component, $seek);

        $result = $this->resolver->resolveDependencies([], $c1);

        $this->assertEquals([$c1], $result);
    }

    public function testResolveSeek(): void
    {
        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $seek = new D\In(D\InType::SEEK, $name);
        $contribute1 = new D\Out(D\OutType::CONTRIBUTE, $name, null, []);
        $contribute2 = new D\Out(D\OutType::CONTRIBUTE, $name, null, []);

        $c1 = new D\OfComponent($component, $seek);
        $c2 = new D\OfComponent($component, $contribute1);
        $c3 = new D\OfComponent($component, $contribute2);

        $result = $this->resolver->resolveDependencies([], $c1, $c2, $c3);

        $seek = new D\In(D\InType::SEEK, $name);
        $contribute1 = new D\Out(D\OutType::CONTRIBUTE, $name, null, []);
        $contribute2 = new D\Out(D\OutType::CONTRIBUTE, $name, null, []);

        $c1 = new D\OfComponent($component, $seek);
        $c1->addResolution($seek, [$contribute1, $contribute2]);
        $c2 = new D\OfComponent($component, $contribute1);
        $c3 = new D\OfComponent($component, $contribute2);

        $this->assertEquals([$c1, $c2, $c3], $result);
    }

    public function testResolveUseOneOption(): void
    {
        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $use = new D\In(D\InType::USE, $name);
        $implement = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);

        $c1 = new D\OfComponent($component, $use);
        $c2 = new D\OfComponent($component, $implement);

        $result = $this->resolver->resolveDependencies([], $c1, $c2);

        $use = new D\In(D\InType::USE, $name);
        $implement = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);

        $c1 = new D\OfComponent($component, $use);
        $c1->addResolution($use, $implement);
        $c2 = new D\OfComponent($component, $implement);

        $this->assertEquals([$c1, $c2], $result);
    }

    public function testUseFailsNotExistent(): void
    {
        $this->expectException(\LogicException::class);

        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $use = new D\In(D\InType::USE, $name);

        $c1 = new D\OfComponent($component, $use);

        $this->resolver->resolveDependencies([], $c1);
    }

    public function testUseFailsDuplicate(): void
    {
        $this->expectException(\LogicException::class);

        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $use = new D\In(D\InType::USE, $name);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);

        $c1 = new D\OfComponent($component, $use);
        $c2 = new D\OfComponent($component, $implement1);
        $c3 = new D\OfComponent($component, $implement2);

        $this->resolver->resolveDependencies([], $c1, $c2, $c3);
    }

    public function testUseDisambiguateDuplicateSpecific(): void
    {
        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $use = new D\In(D\InType::USE, $name);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\OtherClass"], []);

        $c1 = new D\OfComponent($component, $use);
        $c2 = new D\OfComponent($component, $implement1);
        $c3 = new D\OfComponent($component, $implement2);

        $directives = [
            new RD\InComponent(
                get_class($component),
                new RD\ForXUseY(TestInterface::class, "Some\\OtherClass")
            )
        ];

        $result = $this->resolver->resolveDependencies($directives, $c1, $c2, $c3);

        $use = new D\In(D\InType::USE, $name);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\OtherClass"], []);

        $c1 = new D\OfComponent($component, $use);
        $c1->addResolution($use, $implement2);
        $c2 = new D\OfComponent($component, $implement1);
        $c3 = new D\OfComponent($component, $implement2);

        $this->assertEquals([$c1, $c2, $c3], $result);
    }

    public function testUseDisambiguateDuplicateGeneric(): void
    {
        $component = $this->createMock(Component::class);

        $name = TestInterface::class;

        $use = new D\In(D\InType::USE, $name);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\OtherClass"], []);

        $c1 = new D\OfComponent($component, $use);
        $c2 = new D\OfComponent($component, $implement1);
        $c3 = new D\OfComponent($component, $implement2);

        $directives = [
            new RD\ForXUseY(TestInterface::class, "Some\\OtherClass")
        ];

        $result = $this->resolver->resolveDependencies($directives, $c1, $c2, $c3);

        $use = new D\In(D\InType::USE, $name);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\Class"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name, ["class" => "Some\\OtherClass"], []);

        $c1 = new D\OfComponent($component, $use);
        $c1->addResolution($use, $implement2);
        $c2 = new D\OfComponent($component, $implement1);
        $c3 = new D\OfComponent($component, $implement2);

        $this->assertEquals([$c1, $c2, $c3], $result);
    }

    public function testDisambiguateTransitive(): void
    {
        $component1 = $this->createMock(Component1::class);
        $component2 = $this->createMock(Component2::class);
        $component3 = $this->createMock(Component3::class);
        $component4 = $this->createMock(Component4::class);

        $name = TestInterface::class;
        $name2 = TestInterface2::class;

        $pull1 = new D\In(D\InType::PULL, $name);
        $pull2 = new D\In(D\InType::PULL, $name);
        $use = new D\In(D\InType::USE, $name2);
        $provide = new D\Out(D\OutType::PROVIDE, $name, ["class" => "Some\\Class"], [$use]);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name2, ["class" => "Some\\OtherClass"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name2, ["class" => "Some\\DifferentClass"], []);

        $c1 = new D\OfComponent($component1, $pull1);
        $c2 = new D\OfComponent($component2, $pull2);
        $c3 = new D\OfComponent($component3, $provide, $use);
        $c4 = new D\OfComponent($component4, $implement1, $implement2);

        $directives = [
            new RD\InComponent(
                get_class($component1),
                new RD\WhenPulling(
                    $name,
                    new RD\ForXUseY($name2, "Some\\OtherClass")
                )
            ),
            new RD\InComponent(
                get_class($component2),
                new RD\WhenPulling(
                    $name,
                    new RD\ForXUseY($name2, "Some\\DifferentClass")
                )
            )
        ];

        $result = $this->resolver->resolveDependencies($directives, $c1, $c2, $c3, $c4);

        $pull1 = new D\In(D\InType::PULL, $name);
        $pull2 = new D\In(D\InType::PULL, $name);
        $use1 = new D\In(D\InType::USE, $name2);
        $use2 = new D\In(D\InType::USE, $name2);
        $provide1 = new D\Out(D\OutType::PROVIDE, $name, ["class" => "Some\\Class"], [$use1]);
        $provide2 = new D\Out(D\OutType::PROVIDE, $name, ["class" => "Some\\Class"], [$use2]);
        $implement1 = new D\Out(D\OutType::IMPLEMENT, $name2, ["class" => "Some\\OtherClass"], []);
        $implement2 = new D\Out(D\OutType::IMPLEMENT, $name2, ["class" => "Some\\DifferentClass"], []);

        $use1->addResolution($implement1);
        $use2->addResolution($implement2);
        $pull1->addResolution($provide1);
        $pull1->addResolution($provide2);

        $c1 = new D\OfComponent($component, $use);
        $c2 = new D\OfComponent($component, $implement1);
        $c3 = new D\OfComponent($component, $implement2);

        $this->assertEquals([$c1, $c2, $c3], $result);
    }

    public function testFindSimpleCycle(): void
    {
        $this->expectException(\LogicException::class);

        $component = $this->createMock(Component::class);

        $name = TestInterface::class;
        $name2 = TestInterface2::class;

        $pull = new D\In(D\InType::PULL, $name);
        $provide = new D\Out(D\OutType::PROVIDE, $name2, "Some\\Class", [$pull], []);
        $c1 = new D\OfComponent($component, $pull, $provide);

        $pull = new D\In(D\InType::PULL, $name2);
        $provide = new D\Out(D\OutType::PROVIDE, $name, "Some\\OtherClass", [$pull], []);
        $c2 = new D\OfComponent($component, $pull, $provide);


        $result = $this->resolver->resolveDependencies([], $c1, $c2);
    }

    public function testFindLongerCycle(): void
    {
        $this->expectException(\LogicException::class);

        $component = $this->createMock(Component::class);

        $name = TestInterface::class;
        $name2 = TestInterface2::class;
        $name3 = TestInterface3::class;

        $pull = new D\In(D\InType::PULL, $name);
        $provide = new D\Out(D\OutType::PROVIDE, $name2, "Some\\Class", [$pull], []);
        $c1 = new D\OfComponent($component, $pull, $provide);

        $pull = new D\In(D\InType::PULL, $name2);
        $provide = new D\Out(D\OutType::PROVIDE, $name3, "Some\\OtherClass", [$pull], []);
        $c2 = new D\OfComponent($component, $pull, $provide);

        $pull = new D\In(D\InType::PULL, $name3);
        $provide = new D\Out(D\OutType::PROVIDE, $name, "Some\\OtherOtherClass", [$pull], []);
        $c3 = new D\OfComponent($component, $pull, $provide);


        $result = $this->resolver->resolveDependencies([], $c1, $c2, $c3);
    }
}
