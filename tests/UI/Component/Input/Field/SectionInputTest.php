<?php declare(strict_types=1);

/* Copyright (c) 2020 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

require_once(__DIR__ . "/../../../../../libs/composer/vendor/autoload.php");
require_once(__DIR__ . "/../../../Base.php");

use ILIAS\UI\Implementation\Component\Input\Field;
use ILIAS\Data;

class SectionInputTest extends ILIAS_UI_TestBase
{
    public function getFieldFactory() : Field\Factory
    {
        $factory = new Field\Factory(
            new IncrementalSignalGenerator(),
            new Data\Factory(),
            $this->getRefinery(),
            $this->getLanguage()
        );
        return $factory;
    }

    public function testSectionRendering() : void
    {
        $f = $this->getFieldFactory();
        $r = $this->getDefaultRenderer();
        $inputs = [
            $f->text("input1", "in 1"),
            $f->text("input2", "in 2")
        ];
        $label = 'section label';
        $byline = 'section byline';
        $section = $f->section($inputs, $label, $byline);
        $actual = $this->brutallyTrimHTML($r->render($section));
        $expected = <<<EOT
            <div class="il-section-input">
                <div class="il-section-input-header">
                    <h2>section label</h2>
                    <div class="il-section-input-header-byline">section byline</div>
                </div>
                <div class="form-group row">
                    <label for="id_1" class="control-label col-sm-3">input1</label>
                    <div class="col-sm-9">
                        <input id="id_1" type="text" name="" class="form-control form-control-sm" />
                        <div class="help-block">in 1</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="id_2" class="control-label col-sm-3">input2</label>
                    <div class="col-sm-9">
                        <input id="id_2" type="text" name="" class="form-control form-control-sm" />
                        <div class="help-block">in 2</div>
                    </div>
                </div>
            </div>
EOT;
        $expected = $this->brutallyTrimHTML($expected);
        $this->assertEquals($expected, $actual);
    }

    public function testSectionRenderingWithError() : void
    {
        $f = $this->getFieldFactory();
        $r = $this->getDefaultRenderer();
        $inputs = [
            $f->text("input1", "in 1")
        ];
        $label = 'section label';
        $byline = 'section byline';
        $section = $f->section($inputs, $label, $byline);
        $actual = $this->brutallyTrimHTML($r->render($section->withError("Some Error")));
        $expected = <<<EOT
            <div class="il-section-input">
                <div class="il-section-input-header">
                    <h2>section label</h2>
                    <div class="il-section-input-header-byline">section byline</div>
                </div>
                <div class="help-block alert alert-danger" role="alert"> Some Error </div>
                <div class="form-group row">
                    <label for="id_1" class="control-label col-sm-3">input1</label>
                    <div class="col-sm-9">
                        <input id="id_1" type="text" name="" class="form-control form-control-sm" />
                        <div class="help-block">in 1</div>
                    </div>
                </div>
            </div>
EOT;
        $expected = $this->brutallyTrimHTML($expected);
        $this->assertEquals($expected, $actual);
    }
}
