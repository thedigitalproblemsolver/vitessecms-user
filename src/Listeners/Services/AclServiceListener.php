<?php declare(strict_types=1);

namespace VitesseCms\User\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\User\Services\AclService;

class AclServiceListener
{
    private AclService $aclService;

    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    public function attach(Event $event): AclService
    {
        return $this->aclService;
    }
}
