<?php declare(strict_types=1);

require_once 'tests/UI/AbstractFactoryTest.php';

use ILIAS\UI\Component\Input\Container\Filter;
use ILIAS\UI\Implementation\Component\SignalGenerator;
use ILIAS\Data;
use ILIAS\UI\Implementation\Component\Input\Container\Filter\Factory;

class FilterFactoryTest extends AbstractFactoryTest
{
    public array $kitchensink_info_settings = [
        "standard" => [
            "context" => false,
        ]
    ];

    public string $factory_title = 'ILIAS\\UI\\Component\\Input\\Container\\Filter\\Factory';


    final public function buildFactory() : Factory
    {
        $df = new Data\Factory();
        $language = $this->createMock(ilLanguage::class);
        return new Factory(
            new SignalGenerator(),
            new \ILIAS\UI\Implementation\Component\Input\Field\Factory(
                new SignalGenerator(),
                $df,
                new ILIAS\Refinery\Factory($df, $language),
                $language
            )
        );
    }

    public function test_implements_factory_interface() : void
    {
        $f = $this->buildFactory();

        $filter = $f->standard(
            "#",
            "#",
            "#",
            "#",
            "#",
            "#",
            [],
            []
        );
        $this->assertInstanceOf(Filter\Filter::class, $filter);
        $this->assertInstanceOf(Filter\Standard::class, $filter);
    }
}
