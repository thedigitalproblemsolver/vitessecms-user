<?php declare(strict_types=1);

namespace VitesseCms\User\Forms;

use VitesseCms\Form\AbstractForm;

class ResetForm extends AbstractForm
{
    public function initialize()
    {
        $this->_(
            'password',
            '%USER_PASSWORD_NEW%',
            'password',
            ['required' => 'required']
        );
        $this->_(
            'password',
            '%USER_PASSWORD_REPEAT%',
            'password2',
            ['required' => 'required']
        );
        $this->_(
            'hidden',
            '',
            'passwordResetToken'
        );
        $this->_(
            'submit',
            '%USER_PASSWORD_CHANGE_MINE%'
        );
    }
}
