<?php declare(strict_types=1);

/* Copyright (c) 2018 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

require_once(__DIR__ . "/../../../../../libs/composer/vendor/autoload.php");
require_once(__DIR__ . "/../../../Base.php");

use ILIAS\UI\Implementation\Component as I;
use ILIAS\UI\Implementation\Component\SignalGenerator;
use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\UI\Implementation\Component\Input\NameSource;
use ILIAS\UI\Component\Input\Field;
use ILIAS\Data;
use ILIAS\Refinery\Factory as Refinery;

class MultiSelectInputTest extends ILIAS_UI_TestBase
{
    protected DefNamesource $name_source;

    public function setUp() : void
    {
        $this->name_source = new DefNamesource();
    }

    protected function buildFactory() : I\Input\Field\Factory
    {
        $df = new Data\Factory();
        $language = $this->createMock(ilLanguage::class);
        return new I\Input\Field\Factory(
            new SignalGenerator(),
            $df,
            new Refinery($df, $language),
            $language
        );
    }

    public function test_implements_factory_interface() : void
    {
        $f = $this->buildFactory();
        $options = array(
            "1" => "Pick 1",
            "2" => "Pick 2"
        );
        $ms = $f->multiSelect("label", $options, "byline");
        $this->assertInstanceOf(Field\Input::class, $ms);
        $this->assertInstanceOf(Field\MultiSelect::class, $ms);
    }

    public function test_options() : void
    {
        $f = $this->buildFactory();
        $options = array(
            "1" => "Pick 1",
            "2" => "Pick 2"
        );
        $ms = $f->multiSelect("label", $options, "byline");
        $this->assertEquals($options, $ms->getOptions());
    }

    public function test_only_accepts_actual_options_from_client_side() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $f = $this->buildFactory();
        $options = array(
            "1" => "Pick 1",
            "2" => "Pick 2"
        );
        $ms = $f->multiSelect("label", $options, "byline")
            ->withNameFrom(new class() implements NameSource {
                public function getNewName() : string
                {
                    return "name";
                }
            });
        $ms = $ms->withInput(new class() implements InputData {
            public function getOr($_, $__)
            {
                return ["3"];
            }
            public function get($_)
            {
            }
        });
        $ms->getContent();
    }

    public function test_render() : void
    {
        $r = $this->getDefaultRenderer();
        $f = $this->buildFactory();
        $options = array(
            "1" => "Pick 1",
            "2" => "Pick 2"
        );
        $ms = $f->multiSelect("label", $options, "byline")
            ->withNameFrom($this->name_source);

        $name = $ms->getName();
        $label = $ms->getLabel();
        $byline = $ms->getByline();
        $expected = ""
            . "<div class=\"form-group row\">"
                . "<label class=\"control-label col-sm-3\">$label</label>"
                . "<div class=\"col-sm-9\">"
                    . "<ul class=\"il-input-multiselect\" id=\"id_1\">";

        foreach ($options as $opt_value => $opt_label) {
            $expected .= ""
                        . "<li>"
                            . "<input type=\"checkbox\" name=\"$name" . "[]\" value=\"$opt_value\" />"
                            . "<span>$opt_label</span>"
                        . "</li>";
        }

        $expected .= ""
                    . "</ul>"
                    . "<div class=\"help-block\">$byline</div>"
                . "</div>"
            . "</div>";
        $this->assertHTMLEquals($expected, $r->render($ms));
    }

    public function test_render_value() : void
    {
        $r = $this->getDefaultRenderer();
        $f = $this->buildFactory();
        $options = array(
            "1" => "Pick 1",
            "2" => "Pick 2"
        );
        $value = array_keys($options)[1];
        $ms = $f->multiSelect("label", $options, "byline")
            ->withNameFrom($this->name_source)
            ->withValue([$value]);

        $name = $ms->getName();
        $label = $ms->getLabel();
        $byline = $ms->getByline();
        $expected = ""
            . "<div class=\"form-group row\">"
                . "<label class=\"control-label col-sm-3\">$label</label>"
                . "<div class=\"col-sm-9\">"
                    . "<ul class=\"il-input-multiselect\" id=\"id_1\">";

        foreach ($options as $opt_value => $opt_label) {
            if ($opt_value === $value) {
                $expected .= ""
                        . "<li>"
                            . "<input type=\"checkbox\" name=\"$name" . "[]\" value=\"$opt_value\" checked=\"checked\" />"
                            . "<span>$opt_label</span>"
                        . "</li>";
            } else {
                $expected .= ""
                        . "<li>"
                            . "<input type=\"checkbox\" name=\"$name" . "[]\" value=\"$opt_value\" />"
                            . "<span>$opt_label</span>"
                        . "</li>";
            }
        }

        $expected .= ""
                    . "</ul>"
                    . "<div class=\"help-block\">$byline</div>"
                . "</div>"
            . "</div>";
        $this->assertHTMLEquals($expected, $r->render($ms));
    }

    public function test_render_disabled() : void
    {
        $r = $this->getDefaultRenderer();
        $f = $this->buildFactory();
        $options = array(
            "1" => "Pick 1",
            "2" => "Pick 2"
        );
        $ms = $f->multiSelect("label", $options, "byline")
            ->withNameFrom($this->name_source)->withDisabled(true);

        $name = $ms->getName();
        $label = $ms->getLabel();
        $byline = $ms->getByline();
        $expected = ""
            . "<div class=\"form-group row\">"
            . "<label class=\"control-label col-sm-3\">$label</label>"
            . "<div class=\"col-sm-9\">"
            . "<ul class=\"il-input-multiselect\" id=\"id_1\">";

        foreach ($options as $opt_value => $opt_label) {
            $expected .= ""
                . "<li>"
                . "<input type=\"checkbox\" name=\"$name" . "[]\" value=\"$opt_value\" disabled=\"disabled\" />"
                . "<span>$opt_label</span>"
                . "</li>";
        }

        $expected .= ""
            . "</ul>"
            . "<div class=\"help-block\">$byline</div>"
            . "</div>"
            . "</div>";
        $this->assertHTMLEquals($expected, $r->render($ms));
    }

    public function testRenderNoOptions() : void
    {
        $r = $this->getDefaultRenderer();
        $f = $this->buildFactory();
        $options = [];
        $ms = $f->multiSelect("label", $options, "byline")
            ->withNameFrom($this->name_source)->withDisabled(true);

        $name = $ms->getName();
        $label = $ms->getLabel();
        $byline = $ms->getByline();
        $expected = ""
            . "<div class=\"form-group row\">"
            . "<label class=\"control-label col-sm-3\">$label</label>"
            . "<div class=\"col-sm-9\">"
            . "<ul class=\"il-input-multiselect\" id=\"id_1\">"
            . "<li>-</li>"
            . "</ul>"
            . "<div class=\"help-block\">$byline</div>"
            . "</div>"
            . "</div>";

        $this->assertHTMLEquals($expected, $r->render($ms));
    }
}
