<?php declare(strict_types=1);

namespace ILIAS\UI\Implementation\Component\Symbol;

use ILIAS\UI\Component;
use ILIAS\UI\Component\Symbol\Avatar as IAvatar;
use ILIAS\UI\Component\Symbol\Glyph as IGlyph;
use ILIAS\UI\Component\Symbol\Icon as IIcon;

class Factory implements Component\Symbol\Factory
{
    protected Icon\Factory $icon_factory;
    protected Glyph\Factory $glyph_factory;
    protected Avatar\Factory $avatar_factory;

    public function __construct(
        Icon\Factory $icon_factory,
        Glyph\Factory $glyph_factory,
        Avatar\Factory $avatar_factory
    ) {
        $this->icon_factory = $icon_factory;
        $this->glyph_factory = $glyph_factory;
        $this->avatar_factory = $avatar_factory;
    }

    /**
     * @inheritdoc
     */
    public function icon() : IIcon\Factory
    {
        return $this->icon_factory;
    }

    /**
     * @inheritdoc
     */
    public function glyph() : IGlyph\Factory
    {
        return $this->glyph_factory;
    }

    /**
     * @inheritdoc
     */
    public function avatar() : IAvatar\Factory
    {
        return $this->avatar_factory;
    }
}
