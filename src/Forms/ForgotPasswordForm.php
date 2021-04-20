<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class ForgotPasswordForm extends AbstractForm
{

    public function initialize()
    {
        $this->addEmail('%CORE_EMAIL%', 'email', (new Attributes())->setRequired())
            ->addSubmitButton('%USER_PASSWORD_FORGOT_REQUEST_SEND_EMAIL%');
    }
}
