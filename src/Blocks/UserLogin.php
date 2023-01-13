<?php declare(strict_types=1);

namespace VitesseCms\User\Blocks;

use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;

class UserLogin extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        $block->set('loginUrl', 'user/loginform');
        $block->set('loggendIn', 0);

        if ($this->getDi()->getUser()->isLoggedIn()):
            $block->set('loginUrl', 'user/logout');
            $block->set('loggendIn', 1);
        endif;
    }
}
