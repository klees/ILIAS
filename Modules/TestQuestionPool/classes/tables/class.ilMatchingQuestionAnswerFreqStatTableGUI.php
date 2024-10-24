<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once 'Modules/TestQuestionPool/classes/tables/class.ilAnswerFrequencyStatisticTableGUI.php';

/**
 * Class ilKprimChoiceAnswerFreqStatTableGUI
 *
 * @author    Björn Heyser <info@bjoernheyser.de>
 * @version    $Id$
 *
 * @package    Modules/TestQuestionPool
 */
class ilMatchingQuestionAnswerFreqStatTableGUI extends ilAnswerFrequencyStatisticTableGUI
{
    /**
     * @var assMatchingQuestion
     */
    protected $question;
    
    public function __construct($a_parent_obj, $a_parent_cmd = "", $question = "")
    {
        parent::__construct($a_parent_obj, $a_parent_cmd, $question);
        $this->setDefaultOrderField('term');
    }
    
    public function initColumns()
    {
        $this->addColumn('Term', '');
        $this->addColumn('Definition', '');
        $this->addColumn('Frequency', '');
    }
    
    public function fillRow(array $a_set) : void
    {
        $this->tpl->setCurrentBlock('answer');
        $this->tpl->setVariable('ANSWER', $a_set['term']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('answer');
        $this->tpl->setVariable('ANSWER', $a_set['definition']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('frequency');
        $this->tpl->setVariable('FREQUENCY', $a_set['frequency']);
        $this->tpl->parseCurrentBlock();
    }
}
