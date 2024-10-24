<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateValueReplacementTest extends ilCertificateBaseTestCase
{
    public function testReplace() : void
    {
        $replacement = new ilCertificateValueReplacement('/some/where');

        $placeholderValues = array('NAME' => 'Peter', 'PRIZE' => 'a fantastic prize');

        $certificateContent = '<xml> 
[BACKGROUND_IMAGE]
Hurray [NAME] you have received [PRIZE]
</xml>';

        $replacedContent = $replacement->replace($placeholderValues, $certificateContent);

        $expected = '<xml> 
[BACKGROUND_IMAGE]
Hurray Peter you have received a fantastic prize
</xml>';

        $this->assertEquals($expected, $replacedContent);
    }

    public function testReplaceClientWebDir() : void
    {
        $replacement = new ilCertificateValueReplacement('/some/where');

        $placeholderValues = array('NAME' => 'Peter', 'PRIZE' => 'a fantastic prize');

        $certificateContent = '<xml> 
[BACKGROUND_IMAGE]
[CLIENT_WEB_DIR]/background.jpg
Hurray [NAME] you have received [PRIZE]
</xml>';

        $replacedContent = $replacement->replace($placeholderValues, $certificateContent);

        $expected = '<xml> 
[BACKGROUND_IMAGE]
[CLIENT_WEB_DIR]/background.jpg
Hurray Peter you have received a fantastic prize
</xml>';

        $this->assertEquals($expected, $replacedContent);
    }
}
