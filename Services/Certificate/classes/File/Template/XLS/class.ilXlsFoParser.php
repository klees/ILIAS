<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilXlsFoParser
{
    private ilSetting $settings;
    private ilPageFormats $pageFormats;
    private ilXMLChecker $xmlChecker;
    private ilCertificateUtilHelper $utilHelper;
    private ilCertificateXlstProcess $xlstProcess;
    private ilLanguage $language;
    private ilCertificateXlsFileLoader $certificateXlsFileLoader;

    public function __construct(
        ilSetting $settings,
        ilPageFormats $pageFormats,
        ?ilXMLChecker $xmlChecker = null,
        ?ilCertificateUtilHelper $utilHelper = null,
        ?ilCertificateXlstProcess $xlstProcess = null,
        ?ilLanguage $language = null,
        ?ilCertificateXlsFileLoader $certificateXlsFileLoader = null
    ) {
        $this->settings = $settings;
        $this->pageFormats = $pageFormats;

        if (null === $xmlChecker) {
            $xmlChecker = new ilXMLChecker();
        }
        $this->xmlChecker = $xmlChecker;

        if (null === $utilHelper) {
            $utilHelper = new ilCertificateUtilHelper();
        }
        $this->utilHelper = $utilHelper;

        if (null === $xlstProcess) {
            $xlstProcess = new ilCertificateXlstProcess();
        }
        $this->xlstProcess = $xlstProcess;

        if (null === $language) {
            global $DIC;
            $language = $DIC->language();
        }
        $this->language = $language;

        if (null === $certificateXlsFileLoader) {
            $certificateXlsFileLoader = new ilCertificateXlsFileLoader();
        }
        $this->certificateXlsFileLoader = $certificateXlsFileLoader;
    }

    /**
     * @param array $formData
     * @return string
     * @throws Exception
     */
    public function parse(array $formData) : string
    {
        $content = "<html><body>" . $formData['certificate_text'] . "</body></html>";
        $content = preg_replace("/<p>(&nbsp;){1,}<\\/p>/", "<p></p>", $content);
        $content = preg_replace("/<p>(\\s)*?<\\/p>/", "<p></p>", $content);
        $content = str_replace("<p></p>", "<p class=\"emptyrow\"></p>", $content);
        $content = str_replace("&nbsp;", "&#160;", $content);
        $content = preg_replace("//", "", $content);

        $this->xmlChecker->setXMLContent($content);
        $this->xmlChecker->startParsing();

        if ($this->xmlChecker->hasError()) {
            throw new Exception($this->language->txt("certificate_not_well_formed"));
        }

        $xsl = $this->certificateXlsFileLoader->getXlsCertificateContent();

        // additional font support
        $xsl = str_replace(
            'font-family="Helvetica, unifont"',
            'font-family="' . $this->settings->get('rpc_pdf_font', 'Helvetica, unifont') . '"',
            $xsl
        );

        $args = [
            '/_xml' => $content,
            '/_xsl' => $xsl
        ];

        if (strcmp($formData['pageformat'], 'custom') == 0) {
            $pageheight = $formData['pageheight'];
            $pagewidth = $formData['pagewidth'];
        } else {
            $pageformats = $this->pageFormats->fetchPageFormats();
            $pageheight = $pageformats[$formData['pageformat']]['height'];
            $pagewidth = $pageformats[$formData['pageformat']]['width'];
        }

        $params = [
            'pageheight' => $this->formatNumberString($this->utilHelper->stripSlashes($pageheight)),
            'pagewidth' => $this->formatNumberString($this->utilHelper->stripSlashes($pagewidth)),
            'backgroundimage' => '[BACKGROUND_IMAGE]',
            'marginbody' => implode(
                ' ',
                [
                    $this->formatNumberString($this->utilHelper->stripSlashes($formData['margin_body']['top'])),
                    $this->formatNumberString($this->utilHelper->stripSlashes($formData['margin_body']['right'])),
                    $this->formatNumberString($this->utilHelper->stripSlashes($formData['margin_body']['bottom'])),
                    $this->formatNumberString($this->utilHelper->stripSlashes($formData['margin_body']['left']))
                ]
            )
        ];

        $output = $this->xlstProcess->process($args, $params);

        return $output;
    }

    private function formatNumberString(string $a_number) : string
    {
        return str_replace(',', '.', $a_number);
    }
}
