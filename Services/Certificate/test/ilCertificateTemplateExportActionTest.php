<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateTemplateExportActionTest extends ilCertificateBaseTestCase
{
    public function testExport() : void
    {
        $templateRepository = $this->getMockBuilder(ilCertificateTemplateRepository::class)->getMock();

        $templateRepository->method('fetchCurrentlyActiveCertificate')
            ->willReturn(new ilCertificateTemplate(
                100,
                'crs',
                '<xml> Some Content </xml>',
                md5('<xml> Some Content </xml>'),
                '[]',
                3,
                'v5.4.0',
                123456789,
                true,
                '/some/where/background.jpg',
                '/some/where/thumbnail.jpg',
                50
            ));

        $filesystem = $this->getMockBuilder(ILIAS\Filesystem\Filesystem::class)
            ->getMock();

        $filesystem
            ->expects($this->once())
            ->method('createDir');

        $filesystem
            ->expects($this->once())
            ->method('put');

        $filesystem
            ->expects($this->once())
            ->method('deleteDir');

        $filesystem
            ->expects($this->once())
            ->method('put');

        $objectHelper = $this->getMockBuilder(ilCertificateObjectHelper::class)
            ->getMock();

        $objectHelper->method('lookupType')
            ->willReturn('crs');

        $utilHelper = $this->getMockBuilder(ilCertificateUtilHelper::class)
            ->getMock();

        $utilHelper
            ->expects($this->once())
            ->method('zip');

        $utilHelper
            ->expects($this->once())
            ->method('deliverFile');

        $action = new ilCertificateTemplateExportAction(
            100,
            '/some/where/background.jpg',
            $templateRepository,
            $filesystem,
            $objectHelper,
            $utilHelper
        );

        $action->export('some/where/root', 'phpunit');
    }
}
