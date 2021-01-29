<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\User\Models\User;
use Phalcon\Security\Random;

class PasswordFactory
{
    public static function createReset(): BaseObjectInterface
    {
        $item = ObjectFactory::create();
        $item->set('passwordResetToken', (new Random())->hex(24));
        $item->set('passwordResetCreatedAt', date('Y-m-d H:i:s'));

        return $item;
    }
}
