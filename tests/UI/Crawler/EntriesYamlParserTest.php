<?php declare(strict_types=1);

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

require_once("libs/composer/vendor/autoload.php");
include_once("tests/UI/Crawler/Fixture/Fixture.php");

use ILIAS\UI\Implementation\Crawler as Crawler;
use PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{
    protected Crawler\EntriesYamlParser $parser;
    protected ProperEntryFixture $proper_entry;

    protected function setUp() : void
    {
        $this->parser = new Crawler\EntriesYamlParser();
        $this->proper_entry = new ProperEntryFixture();
    }

    /**
     * @throws Crawler\Exception\CrawlerException
     */
    public function testParseInvalidFile() : void
    {
        try {
            $this->parser->parseYamlStringArrayFromFile("Invalid Path");
            $this->assertFalse("This should not happen");
        } catch (Crawler\Exception\CrawlerException $e) {
            $this->assertEquals(Crawler\Exception\CrawlerException::INVALID_FILE_PATH, $e->getCode());
        }
    }

    /**
     * @throws Crawler\Exception\CrawlerException
     */
    public function testParseProperEntryToYamlEntries() : void
    {
        $yaml_entries = $this->parser->parseYamlStringArrayFromFile("tests/UI/Crawler/Fixture/ProperEntry.php");

        $this->assertEquals($this->proper_entry->properEntryYamlString, $yaml_entries[0]);
        $this->assertEquals($this->proper_entry->properEntryYamlString, $yaml_entries[0]);
    }

    public function testParseProperEntryToArray()
    {
        $entries = $this->parser->parseArrayFromFile("tests/UI/Crawler/Fixture/ProperEntry.php");
        $this->assertEquals($this->proper_entry->properEntryYamlArray, $entries);
    }

    /**
     * @throws Crawler\Exception\CrawlerException
     */
    public function testNoDescriptionEntry() : void
    {
        try {
            $this->parser->parseYamlStringArrayFromFile("tests/UI/Crawler/Fixture/NoDescriptionEntry.php");
            $this->assertFalse("This should not happen");
        } catch (Crawler\Exception\CrawlerException $e) {
            $this->assertEquals(Crawler\Exception\CrawlerException::ENTRY_WITH_NO_YAML_DESCRIPTION, $e->getCode());
        }
    }
    /**
     * @throws Crawler\Exception\CrawlerException
     */
    public function testNoReturnValueEntry() : void
    {
        try {
            $this->parser->parseYamlStringArrayFromFile("tests/UI/Crawler/Fixture/NoReturnValueEntry.php");
            $this->assertFalse("This should not happen");
        } catch (Crawler\Exception\CrawlerException $e) {
            $this->assertEquals(Crawler\Exception\CrawlerException::ENTRY_WITH_NO_VALID_RETURN_STATEMENT, $e->getCode());
        }
    }

    /**
     * @throws Crawler\Exception\CrawlerException
     */
    public function testInvalidYamlEntry() : void
    {
        try {
            $this->parser->parseArrayFromFile("tests/UI/Crawler/Fixture/InvalidYamlEntry.php");
            $this->assertFalse("This should not happen");
        } catch (Crawler\Exception\CrawlerException $e) {
            $this->assertEquals(Crawler\Exception\CrawlerException::PARSING_YAML_ENTRY_FAILED, $e->getCode());
        }
    }

    public function testCamelCase() : void
    {
        $test_string = "Hello Camel Case";

        $this->assertEquals("helloCamelCase", Crawler\EntriesYamlParser::toLowerCamelCase($test_string, ' '));
        $this->assertEquals("HelloCamelCase", Crawler\EntriesYamlParser::toUpperCamelCase($test_string, ' '));
    }

    /**
     * @throws Crawler\Exception\CrawlerException
     */
    public function testGenerateEntry() : void
    {
        $entries = $this->parser->parseEntriesFromFile("tests/UI/Crawler/Fixture/ProperEntry.php");

        $this->assertCount(1, $entries);
        $this->assertEquals(
            "CrawlerFixtureProperEntryProperEntry",
            $entries->getEntryById("CrawlerFixtureProperEntryProperEntry")->getId()
        );
        $this->assertEquals(
            "src/UI/Crawler/Fixture/ProperEntry",
            $entries->getEntryById("CrawlerFixtureProperEntryProperEntry")->getPath()
        );
    }
}
