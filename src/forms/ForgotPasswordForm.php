<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;

class ForgotPasswordForm extends AbstractForm
{

    public function initialize()
    {
        $this->_(
            'email',
            '%CORE_EMAIL%',
            'email',
            ['required' => 'required']
        );
        $this->_(
            'submit',
            '%USER_PASSWORD_FORGOT_REQUEST_SEND_EMAIL%'
        );
    }
}
