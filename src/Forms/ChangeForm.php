<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class ChangeForm extends AbstractForm
{
    public function initialize()
    {
        $this->addPassword('%USER_PASSWORD_OLD%', 'password_old', (new Attributes())->setRequired())
            ->addPassword('%USER_PASSWORD_NEW%', 'password', (new Attributes())->setRequired())
            ->addPassword('%USER_PASSWORD_REPEAT%', 'password2', (new Attributes())->setRequired())
            ->addSubmitButton('%USER_PASSWORD_CHANGE%');
    }
}
