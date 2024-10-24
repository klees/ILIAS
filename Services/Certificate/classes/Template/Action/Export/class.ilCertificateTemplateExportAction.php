<?php declare(strict_types=1);

/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

use ILIAS\Filesystem\Filesystem;
use ILIAS\Filesystem\Exception\FileAlreadyExistsException;
use ILIAS\Filesystem\Exception\FileNotFoundException;
use ILIAS\Filesystem\Exception\IOException;
use ILIAS\Filesystem\Visibility;

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateTemplateExportAction
{
    private int $objectId;
    private string $certificatePath;
    private ilCertificateTemplateRepository $templateRepository;
    private Filesystem $filesystem;
    private ilCertificateObjectHelper $objectHelper;
    private ilCertificateUtilHelper $utilHelper;

    public function __construct(
        int $objectId,
        string $certificatePath,
        ilCertificateTemplateRepository $templateRepository,
        Filesystem $filesystem,
        ?ilCertificateObjectHelper $objectHelper = null,
        ?ilCertificateUtilHelper $utilHelper = null
    ) {
        $this->objectId = $objectId;
        $this->certificatePath = $certificatePath;
        $this->templateRepository = $templateRepository;
        $this->filesystem = $filesystem;

        if (null === $objectHelper) {
            $objectHelper = new ilCertificateObjectHelper();
        }
        $this->objectHelper = $objectHelper;

        if (null === $utilHelper) {
            $utilHelper = new ilCertificateUtilHelper();
        }
        $this->utilHelper = $utilHelper;
    }

    /**
     * Creates a downloadable file via the browser
     * @param string $rootDir
     * @param string $installationId
     * @throws FileAlreadyExistsException
     * @throws FileNotFoundException
     * @throws IOException
     */
    public function export(string $rootDir = CLIENT_WEB_DIR, string $installationId = IL_INST_ID) : void
    {
        $time = time();

        $type = $this->objectHelper->lookupType($this->objectId);
        $certificateId = $this->objectId;

        $exportPath = $this->certificatePath . $time . '__' . $installationId . '__' . $type . '__' . $certificateId . '__certificate/';

        $this->filesystem->createDir($exportPath, Visibility::PUBLIC_ACCESS);

        $template = $this->templateRepository->fetchCurrentlyUsedCertificate($this->objectId);

        $xslContent = $template->getCertificateContent();

        $this->filesystem->put($exportPath . 'certificate.xml', $xslContent);

        $backgroundImagePath = $template->getBackgroundImagePath();
        if ($backgroundImagePath !== '' && true === $this->filesystem->has($backgroundImagePath)) {
            $this->filesystem->copy($backgroundImagePath, $exportPath . 'background.jpg');
        }

        $thumbnailImagePath = $template->getThumbnailImagePath();
        if ($thumbnailImagePath !== '' && true === $this->filesystem->has($backgroundImagePath)) {
            $this->filesystem->copy($thumbnailImagePath, $exportPath . 'thumbnail.svg');
        }

        $objectType = $this->objectHelper->lookupType($this->objectId);

        $zipFileName = $time . '__' . $installationId . '__' . $objectType . '__' . $this->objectId . '__certificate.zip';

        $zipPath = $rootDir . $this->certificatePath . $zipFileName;
        $this->utilHelper->zip($exportPath, $zipPath);
        $this->filesystem->deleteDir($exportPath);

        $this->utilHelper->deliverFile($zipPath, $zipFileName, 'application/zip');
    }
}
