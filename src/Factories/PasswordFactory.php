<?php declare(strict_types=1);

namespace VitesseCms\User\Factories;

use Phalcon\Encryption\Security\Random;
use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Core\Interfaces\BaseObjectInterface;

class PasswordFactory
{
    //TODO crete own model for PassWord reset
    public static function createReset(): BaseObjectInterface
    {
        $item = ObjectFactory::create();
        $item->set('passwordResetToken', (new Random())->hex(24));
        $item->set('passwordResetCreatedAt', date('Y-m-d H:i:s'));

        return $item;
    }
}
