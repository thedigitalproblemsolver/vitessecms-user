<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class ResetForm extends AbstractForm
{
    public function initialize()
    {
        $this->addPassword('%USER_PASSWORD_NEW%', 'password', (new Attributes())->setRequired())
            ->addPassword('%USER_PASSWORD_REPEAT%', 'password2', (new Attributes())->setRequired())
            ->addHidden('passwordResetToken')
            ->addSubmitButton('%USER_PASSWORD_CHANGE_MINE%');
    }
}
