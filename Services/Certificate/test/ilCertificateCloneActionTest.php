<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateCloneActionTest extends ilCertificateBaseTestCase
{
    public function testCloneCertificate() : void
    {
        $database = $this->getMockBuilder(ilDBInterface::class)->getMock();

        $database
            ->expects($this->once())
            ->method('query');

        $database
            ->expects($this->once())
            ->method('numRows')
            ->willReturn(1);

        $database
            ->expects($this->once())
            ->method('replace');



        $templateRepository = $this->getMockBuilder(ilCertificateTemplateRepository::class)->getMock();

        $templateRepository->method('fetchCertificateTemplatesByObjId')
            ->willReturn(
                array(
                    new ilCertificateTemplate(
                        10,
                        'crs',
                        '<xml> Some Content </xml>',
                        md5('<xml> Some Content </xml>'),
                        '[]',
                        3,
                        'v5.3.0',
                        123456789,
                        true,
                        '/some/where/background.jpg',
                        '/some/where/card_thumb.jpg',
                        $id = null
                    ),
                    new ilCertificateTemplate(
                        20,
                        'crs',
                        '<xml> Some Content </xml>',
                        md5('<xml> Some Content </xml>'),
                        '[]',
                        3,
                        'v5.3.0',
                        123456789,
                        true,
                        '/some/where/background.jpg',
                        '/some/where/card_thumb.jpg',
                        $id = null
                    ),
                    new ilCertificateTemplate(
                        30,
                        'crs',
                        '<xml> Some Content </xml>',
                        md5('<xml> Some Content </xml>'),
                        '[]',
                        3,
                        'v5.3.0',
                        123456789,
                        true,
                        '/certificates/default/background.jpg',
                        '/some/where/card_thumb.jpg',
                        $id = null
                    )
                )
            );

        $templateRepository
            ->expects($this->exactly(3))
            ->method('save');

        $fileSystem = $this->getMockBuilder(\ILIAS\Filesystem\Filesystem::class)
            ->getMock();

        $fileSystem->method('has')
            ->willReturn(true);

        $fileSystem
            ->expects($this->exactly(7))
            ->method('copy');

        $logger = $this->getMockBuilder(ilLogger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $objectHelper = $this->getMockBuilder(ilCertificateObjectHelper::class)
            ->getMock();

        $objectHelper->method('lookupObjId')
            ->willReturn(1000);

        $cloneAction = new ilCertificateCloneAction(
            $database,
            new ilCertificatePathFactory(),
            $templateRepository,
            $fileSystem,
            $logger,
            $objectHelper,
            'some/web/directory',
            '/certificates/default/background.jpg'
        );

        $oldObject = $this->getMockBuilder(ilObject::class)
            ->disableOriginalConstructor()
            ->getMock();

        $oldObject->method('getType')
            ->willReturn('crs');

        $oldObject->method('getId')
            ->willReturn(10);

        $newObject = $this->getMockBuilder(ilObject::class)
            ->disableOriginalConstructor()
            ->getMock();

        $newObject->method('getType')
            ->willReturn('crs');

        $newObject->method('getId')
            ->willReturn(10);

        $cloneAction->cloneCertificate($oldObject, $newObject, 'v5.4.0', '/some/web/dir');
    }
}
