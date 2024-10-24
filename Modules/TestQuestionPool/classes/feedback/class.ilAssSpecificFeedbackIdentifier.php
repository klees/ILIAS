<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilAssClozeTestSpecificFeedbackIdentifier
 *
 * @author    Björn Heyser <info@bjoernheyser.de>
 * @version    $Id$
 *
 * @package    Modules/TestQuestionPool
 */
class ilAssSpecificFeedbackIdentifier
{
    protected int $feedbackId;
    
    protected int $questionId;
    
    protected int $questionIndex;
    
    protected int $answerIndex;
    
    public function getFeedbackId() : int
    {
        return $this->feedbackId;
    }
    
    public function setFeedbackId(int $feedbackId) : void
    {
        $this->feedbackId = $feedbackId;
    }
    
    public function getQuestionId() : int
    {
        return $this->questionId;
    }
    
    public function setQuestionId(int $questionId) : void
    {
        $this->questionId = $questionId;
    }
    
    public function getQuestionIndex() : int
    {
        return $this->questionIndex;
    }
    
    public function setQuestionIndex(int $questionIndex) : void
    {
        $this->questionIndex = $questionIndex;
    }
    
    public function getAnswerIndex() : int
    {
        return $this->answerIndex;
    }
    
    public function setAnswerIndex(int $answerIndex) : void
    {
        $this->answerIndex = $answerIndex;
    }
}
