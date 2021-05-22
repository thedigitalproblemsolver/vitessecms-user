<?php declare(strict_types=1);

namespace VitesseCms\User\Blocks;

use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;

class UserChangePassword extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        $block->set('loggedIn', $this->di->user->isLoggedIn());
    }
}
