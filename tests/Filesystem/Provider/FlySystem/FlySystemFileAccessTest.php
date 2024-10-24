<?php

namespace ILIAS\Filesystem\Provider\FlySystem;

use ILIAS\Data\DataSize;
use ILIAS\Filesystem\Exception\FileAlreadyExistsException;
use ILIAS\Filesystem\Exception\IOException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use League\Flysystem\AdapterInterface;

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/
/**
 * Class FlySystemFileAccessTest
 * @package                Filesystem\Provider\FlySystem
 * @runTestsInSeparateProcesses
 * @preserveGlobalState    disabled
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class FlySystemFileAccessTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private \ILIAS\Filesystem\Provider\FlySystem\FlySystemFileAccess $subject;
    /**
     * @var Filesystem | MockInterface
     */
    private $filesystemMock;
    /**
     * @var AdapterInterface|Mockery\LegacyMockInterface|MockInterface
     */
    private $adapterMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->filesystemMock = Mockery::mock(FilesystemInterface::class);
        $this->adapterMock = Mockery::mock(AdapterInterface::class);
        $this->subject = new FlySystemFileAccess($this->filesystemMock);
    }

    /**
     * @Test
     * @small
     */
    public function testReadWhichShouldSucceed(): void
    {
        $fileContent = 'Test file content.';

        $this->adapterMock->shouldReceive('read')
                          ->once()
                          ->andReturn(['contents' => $fileContent]);

        $this->adapterMock->shouldReceive('has')
                          ->once()
                          ->andReturn(true);

        $this->filesystemMock->shouldReceive('getAdapter')
                             ->once()
                             ->andReturn($this->adapterMock);

        $actualContent = $this->subject->read('/path/to/your/file');
        $this->assertSame($fileContent, $actualContent);
    }

    /**
     * @Test
     * @small
     */
    public function testReadWithGeneralFileAccessErrorWhichShouldFail(): void
    {
        $path = 'path/to/your/file';

        $this->adapterMock->shouldReceive('has')
                          ->once()
                          ->andReturn(true);

        $this->adapterMock->shouldReceive('read')
                          ->once()
                          ->andReturn(['contents' => false]);

        $this->filesystemMock->shouldReceive('getAdapter')
                             ->once()
                             ->andReturn($this->adapterMock);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not access the file \"$path\".");

        $this->subject->read($path);
    }

    /**
     * @Test
     * @small
     */
    public function testReadWithMissingFileWhichShouldFail(): void
    {
        $path = 'path/to/your/file';

        $this->adapterMock->shouldReceive('has')
                          ->once()
                          ->andReturn(false);

        $this->filesystemMock->shouldReceive('getAdapter')
                             ->once()
                             ->andReturn($this->adapterMock);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$path\" not found.");

        $this->subject->read('/' . $path);
    }

    /**
     * @Test
     * @small
     */
    public function testGetMimeTypeWhichShouldSucceed(): void
    {
        $mimeType = 'image/jpeg';
        $this->filesystemMock->shouldReceive('getMimetype')
                             ->once()
                             ->andReturn($mimeType);

        $actualMimeType = $this->subject->getMimeType('/path/to/your/file');
        $this->assertSame($mimeType, $actualMimeType);
    }

    /**
     * @Test
     * @small
     */
    public function testGetMimeTypeWithUnknownMimeTypeWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $this->filesystemMock->shouldReceive('getMimetype')
                             ->with($path)
                             ->once()
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not determine the MIME type of the file \"$path\".");

        $this->subject->getMimeType($path);
    }

    /**
     * @Test
     * @small
     */
    public function testGetMimeTypeWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $this->filesystemMock->shouldReceive('getMimetype')
                             ->once()
                             ->with($path)
                             ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$path\" not found.");

        $this->subject->getMimeType($path);
    }

    /**
     * @Test
     * @small
     */
    public function testGetTimestampWhichShouldSucceed(): void
    {
        $timestamp = '06.02.2012';
        $this->filesystemMock->shouldReceive('getTimestamp')
                             ->once()
                             ->andReturn($timestamp);

        $actualTimestamp = $this->subject->getTimestamp('/path/to/your/file');

        /*
         * needs to be equals instead of same because == checks if the object content is the same and === seems to check the reference too
         * eg.
         * $a == $b => true
         * $a === $b => false
         * $a === $a => true
         * $b === $b => true
         *
         * Danger; this is only the observed behaviour and was not documented at least the part with the === operator.
         * Tested with DateTime objects (PHP 7.1.6)
         */
        $this->assertEquals(new \DateTime($timestamp), $actualTimestamp);
    }

    /**
     * @Test
     * @small
     */
    public function testGetTimestampWithUnknownErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $this->filesystemMock->shouldReceive('getTimestamp')
                             ->with($path)
                             ->once()
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not lookup timestamp of the file \"$path\".");

        $this->subject->getTimestamp($path);
    }

    /**
     * @Test
     * @small
     */
    public function testGetTimestampWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $this->filesystemMock->shouldReceive('getTimestamp')
                             ->once()
                             ->with($path)
                             ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$path\" not found.");

        $this->subject->getTimestamp($path);
    }

    /**
     * @Test
     * @small
     */
    public function testGetSizeWhichShouldSucceed(): void
    {
        $rawSize = 1024;
        $size = new DataSize($rawSize, DataSize::KiB);
        $delta = 0.00001; //floating point is never that precise.

        $this->filesystemMock->shouldReceive('getSize')
                             ->once()
                             ->andReturn($rawSize);

        $actualSize = $this->subject->getSize('/path/to/your/file', DataSize::KiB);
        $this->assertSame($size->getSize(), $actualSize->getSize(), '', $delta);
    }

    /**
     * @Test
     * @small
     */
    public function testGetSizeWithUnknownAdapterErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $this->filesystemMock->shouldReceive('getSize')
                             ->with($path)
                             ->once()
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not calculate the file size of the file \"$path\".");

        $this->subject->getSize($path, DataSize::MiB);
    }

    /**
     * @Test
     * @small
     */
    public function testGetSizeWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $this->filesystemMock->shouldReceive('getSize')
                             ->once()
                             ->with($path)
                             ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$path\" not found.");

        $this->subject->getSize($path, DataSize::GiB);
    }

    /**
     * @Test
     * @small
     */
    public function testSetVisibilityWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';
        $visibility = "private";

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(true);

        $this->filesystemMock->shouldReceive('setVisibility')
                             ->once()
                             ->withArgs([$path, $visibility])
                             ->andReturn(true);

        $operationSuccessful = $this->subject->setVisibility($path, $visibility);
        $this->assertTrue($operationSuccessful);
    }

    /**
     * @Test
     * @small
     */
    public function testSetVisibilityThatFailedDueToAdapterFailureWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $visibility = "private";

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(true);

        $this->filesystemMock->shouldReceive('setVisibility')
                             ->once()
                             ->withArgs([$path, $visibility])
                             ->andReturn(false);

        $operationSuccessful = $this->subject->setVisibility($path, $visibility);
        $this->assertFalse($operationSuccessful);
    }

    /**
     * @Test
     * @small
     */
    public function testSetVisibilityWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $visibility = "private";

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(false);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("Path \"$path\" not found.");

        $this->subject->setVisibility($path, $visibility);
    }

    /**
     * @Test
     * @small
     */
    public function testSetVisibilityWithInvalidAccessModifierWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $visibility = "not valid";

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The access must be 'public' or 'private' but '$visibility' was given.");

        $this->subject->setVisibility($path, $visibility);
    }

    /**
     * @Test
     * @small
     */
    public function testGetVisibilityWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';
        $visibility = "private";

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(true);

        $this->filesystemMock->shouldReceive('getVisibility')
                             ->once()
                             ->with($path)
                             ->andReturn($visibility);

        $actualVisibility = $this->subject->getVisibility($path);
        $this->assertSame($visibility, $actualVisibility);
    }

    /**
     * @Test
     * @small
     */
    public function testGetVisibilityWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(false);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("Path \"$path\" not found.");

        $this->subject->getVisibility($path);
    }

    /**
     * @Test
     * @small
     */
    public function testGetVisibilityWithAdapterErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';

        $this->filesystemMock->shouldReceive('has')
                             ->once()
                             ->with($path)
                             ->andReturn(true);

        $this->filesystemMock->shouldReceive('getVisibility')
                             ->once()
                             ->with($path)
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not determine visibility for path '$path'.");

        $this->subject->getVisibility($path);
    }

    /**
     * @Test
     * @small
     */
    public function testWriteWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('write')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andReturn(true);

        $this->subject->write($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testWriteWithAlreadyExistingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('write')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andThrow(FileExistsException::class);

        $this->expectException(FileAlreadyExistsException::class);
        $this->expectExceptionMessage("File \"$path\" already exists.");

        $this->subject->write($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testWriteWithAdapterErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('write')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not write to file \"$path\" because a general IO error occurred. Please check that your destination is writable.");

        $this->subject->write($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testUpdateWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('update')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andReturn(true);

        $this->subject->update($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testUpdateWithAdapterErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('update')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not write to file \"$path\" because a general IO error occurred. Please check that your destination is writable.");

        $this->subject->update($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testUpdateWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('update')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$path\" was not found update failed.");

        $this->subject->update($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testPutWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('put')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andReturn(true);

        $this->subject->put($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testPutWithAdapterErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';
        $content = "some awesome content";

        $this->filesystemMock->shouldReceive('put')
                             ->once()
                             ->withArgs([$path, $content])
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not write to file \"$path\" because a general IO error occurred. Please check that your destination is writable.");

        $this->subject->put($path, $content);
    }

    /**
     * @Test
     * @small
     */
    public function testDeleteWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';

        $this->filesystemMock->shouldReceive('delete')
                             ->once()
                             ->with($path)
                             ->andReturn(true);

        $this->subject->delete($path);
    }

    /**
     * @Test
     * @small
     */
    public function testDeleteWithAdapterErrorWhichShouldFail(): void
    {
        $path = '/path/to/your/file';

        $this->filesystemMock->shouldReceive('delete')
                             ->once()
                             ->with($path)
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not delete file \"$path\" because a general IO error occurred. Please check that your target is writable.");

        $this->subject->delete($path);
    }

    /**
     * @Test
     * @small
     */
    public function testDeleteWithMissingFileWhichShouldFail(): void
    {
        $path = '/path/to/your/file';

        $this->filesystemMock->shouldReceive('delete')
                             ->once()
                             ->with($path)
                             ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$path\" was not found delete operation failed.");

        $this->subject->delete($path);
    }

    /**
     * @Test
     * @small
     * Maybe a useless test.
     */
    public function testReadAndDeleteWhichShouldSucceed(): void
    {
        $path = '/path/to/your/file';
        $content = "awesome content";

        //partially mock the subject to intercept the method calls of the own object.
        $this->subject = Mockery::mock(FlySystemFileAccess::class, [$this->filesystemMock])->makePartial();

        $this->subject
            ->shouldReceive('read')
            ->once()
            ->with($path)
            ->andReturn($content)
            ->getMock()
            ->shouldReceive('delete')
            ->once()
            ->with($path);

        $this->subject->readAndDelete($path);
    }

    /**
     * @Test
     * @small
     */
    public function testRenameWhichShouldSucceed(): void
    {
        $source = '/source/path';
        $destination = '/dest/path';

        $this->filesystemMock
            ->shouldReceive('rename')
            ->once()
            ->withArgs([$source, $destination])
            ->andReturn(true);

        $this->subject->rename($source, $destination);
    }

    /**
     * @Test
     * @small
     */
    public function testRenameWithMissingSourceWhichShouldFail(): void
    {
        $source = '/source/path';
        $destination = '/dest/path';

        $this->filesystemMock
            ->shouldReceive('rename')
            ->once()
            ->withArgs([$source, $destination])
            ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File \"$source\" not found.");

        $this->subject->rename($source, $destination);
    }

    /**
     * @Test
     * @small
     */
    public function testRenameWithExistingDestinationWhichShouldFail(): void
    {
        $source = '/source/path';
        $destination = '/dest/path';

        $this->filesystemMock
            ->shouldReceive('rename')
            ->once()
            ->withArgs([$source, $destination])
            ->andThrow(FileExistsException::class);

        $this->expectException(FileAlreadyExistsException::class);
        $this->expectExceptionMessage("File \"$destination\" already exists.");

        $this->subject->rename($source, $destination);
    }

    /**
     * @Test
     * @small
     */
    public function testRenameWithGeneralErrorWhichShouldFail(): void
    {
        $source = '/source/path';
        $destination = '/dest/path';

        $this->filesystemMock
            ->shouldReceive('rename')
            ->once()
            ->withArgs([$source, $destination])
            ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not move file from \"$source\" to \"$destination\".");

        $this->subject->rename($source, $destination);
    }

    /**
     * @Test
     * @small
     */
    public function testCopyWhichShouldSucceed(): void
    {
        $sourcePath = '/path/to/your/source/file';
        $destinationPath = '/path/to/your/destination/file';

        $this->filesystemMock->shouldReceive('copy')
                             ->once()
                             ->withArgs([$sourcePath, $destinationPath])
                             ->andReturn(true);

        $this->subject->copy($sourcePath, $destinationPath);
    }

    /**
     * @Test
     * @small
     */
    public function testCopyWithAdapterErrorWhichShouldFail(): void
    {
        $sourcePath = '/path/to/your/source/file';
        $destinationPath = '/path/to/your/destination/file';

        $this->filesystemMock->shouldReceive('copy')
                             ->once()
                             ->withArgs([$sourcePath, $destinationPath])
                             ->andReturn(false);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("Could not copy file \"$sourcePath\" to destination \"$destinationPath\" because a general IO error occurred. Please check that your destination is writable.");

        $this->subject->copy($sourcePath, $destinationPath);
    }

    /**
     * @Test
     * @small
     */
    public function testCopyWithMissingFileWhichShouldFail(): void
    {
        $sourcePath = '/path/to/your/source/file';
        $destinationPath = '/path/to/your/destination/file';

        $this->filesystemMock->shouldReceive('copy')
                             ->once()
                             ->withArgs([$sourcePath, $destinationPath])
                             ->andThrow(FileNotFoundException::class);

        $this->expectException(\ILIAS\Filesystem\Exception\FileNotFoundException::class);
        $this->expectExceptionMessage("File source \"$sourcePath\" was not found copy failed.");

        $this->subject->copy($sourcePath, $destinationPath);
    }

    /**
     * @Test
     * @small
     */
    public function testCopyWithExistingDestinationFileWhichShouldFail(): void
    {
        $sourcePath = '/path/to/your/source/file';
        $destinationPath = '/path/to/your/destination/file';

        $this->filesystemMock->shouldReceive('copy')
                             ->once()
                             ->withArgs([$sourcePath, $destinationPath])
                             ->andThrow(FileExistsException::class);

        $this->expectException(FileAlreadyExistsException::class);
        $this->expectExceptionMessage("File destination \"$destinationPath\" already exists copy failed.");

        $this->subject->copy($sourcePath, $destinationPath);
    }
}
