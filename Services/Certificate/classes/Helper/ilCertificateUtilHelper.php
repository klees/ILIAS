<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Just a wrapper class to create Unit Test for other classes.
 * Can be remove when the static method calls have been removed
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateUtilHelper
{
    public function deliverData(string $data, string $fileName, string $mimeType) : void
    {
        ilUtil::deliverData(
            $data,
            $fileName,
            $mimeType
        );
    }

    public function prepareFormOutput(string $string) : string
    {
        return ilLegacyFormElementsUtil::prepareFormOutput($string);
    }

    public function convertImage(
        string $from,
        string $to,
        string $targetFormat = '',
        string $geometry = '',
        string $backgroundColor = ''
    ) : void {
        ilShellUtil::convertImage($from, $to, $targetFormat, $geometry, $backgroundColor);
    }

    /**
     * @param string $string
     * @return mixed|null|string|string[]
     */
    public function stripSlashes(string $string)
    {
        return ilUtil::stripSlashes($string);
    }

    /**
     * @param string $exportPath
     * @param string $zipPath
     */
    public function zip(string $exportPath, string $zipPath) : void
    {
        ilFileUtils::zip($exportPath, $zipPath);
    }

    public function deliverFile(string $zipPath, string $zipFileName, string $mime) : void
    {
        ilFileDelivery::deliverFileLegacy($zipPath, $zipFileName, $mime);
    }

    public function getDir(string $copyDirectory) : array
    {
        return ilFileUtils::getDir($copyDirectory);
    }

    public function unzip(string $file, bool $overwrite) : void
    {
        ilFileUtils::unzip($file, $overwrite);
    }

    public function delDir(string $path) : void
    {
        ilFileUtils::delDir($path);
    }

    /**
     * @param string $file
     * @param string $name
     * @param string $target
     * @param bool   $raise_errors
     * @param string $mode
     * @return bool
     * @throws ilException
     */
    public function moveUploadedFile(
        string $file,
        string $name,
        string $target,
        bool $raise_errors = true,
        string $mode = 'move_uploaded'
    ) : bool {
        return ilFileUtils::moveUploadedFile(
            $file,
            $name,
            $target,
            $raise_errors,
            $mode
        );
    }

    public function getImagePath(
        string $img,
        string $module_path = "",
        string $mode = "output",
        bool $offline = false
    ) : string {
        return ilUtil::getImagePath(
            $img,
            $module_path,
            $mode,
            $offline
        );
    }
}
