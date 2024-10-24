<?php declare(strict_types=1);

/* Copyright (c) 2021 - Nils Haagen <nils.haagen@concepts-and-training.de> - Extended GPL, see LICENSE */

/**
 * Activation-Settings for an LSO
 */
class ilLearningSequenceActivation
{
    protected int $ref_id;
    protected bool $online;
    protected bool $effective_online;
    protected ?\DateTime $activation_start;
    protected ?\DateTime $activation_end;

    public function __construct(
        int $ref_id,
        bool $online = false,
        bool $effective_online = false,
        \DateTime $activation_start = null,
        \DateTime $activation_end = null
    ) {
        $this->ref_id = $ref_id;
        $this->online = $online;
        $this->effective_online = $effective_online;
        $this->activation_start = $activation_start;
        $this->activation_end = $activation_end;
    }

    public function getRefId() : int
    {
        return $this->ref_id;
    }

    public function getIsOnline() : bool
    {
        return $this->online;
    }

    public function withIsOnline(bool $online) : ilLearningSequenceActivation
    {
        $clone = clone $this;
        $clone->online = $online;
        return $clone;
    }

    public function getEffectiveOnlineStatus() : bool
    {
        return $this->effective_online;
    }

    public function getActivationStart() : ?\DateTime
    {
        return $this->activation_start;
    }

    public function withActivationStart(\DateTime $activation_start = null) : ilLearningSequenceActivation
    {
        $clone = clone $this;
        $clone->activation_start = $activation_start;
        return $clone;
    }

    public function getActivationEnd() : ?\DateTime
    {
        return $this->activation_end;
    }

    public function withActivationEnd(\DateTime $activation_end = null) : ilLearningSequenceActivation
    {
        $clone = clone $this;
        $clone->activation_end = $activation_end;
        return $clone;
    }
}
