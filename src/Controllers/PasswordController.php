<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use stdClass;
use VitesseCms\Core\AbstractControllerFrontend;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Setting\Enum\SettingEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Factories\PasswordFactory;
use VitesseCms\User\Forms\ChangeForm;
use VitesseCms\User\Forms\ForgotPasswordForm;
use VitesseCms\User\Forms\LoginForm;
use VitesseCms\User\Forms\ResetForm;
use VitesseCms\User\Repositories\UserRepository;

class PasswordController extends AbstractControllerFrontend
{
    private UrlService $urlService;
    //private Security $securityService;
    //private Session $sessionService;
    private UserRepository $userRepository;
    //private ItemRepository $itemRepository;
    //private SettingService $settingService;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->urlService = $this->eventsManager->fire(UrlEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        //$this->securityService = $this->eventsManager->fire(SecurityEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        //$this->sessionService = $this->eventsManager->fire(SessionEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->userRepository = $this->eventsManager->fire(UserEnum::GET_REPOSITORY->value, new stdClass());
        //$this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new stdClass());
        $this->settingService = $this->eventsManager->fire(SettingEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
    }

    public function indexAction(): void
    {
        $this->redirect('/');
    }

    public function changeFormAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $this->viewService->setVar('content', (new ChangeForm())->renderForm('user/password/parseChangeForm'));
        else :
            $this->viewService->setVar('content', (new LoginForm())->renderForm('user/login'));
        endif;
    }

    public function forgotFormAction(): void
    {
        $this->viewService->setVar('content', (new ForgotPasswordForm())->renderForm('user/password/parseForgotForm'));
    }

    public function resetFormAction(string $token): void
    {
        $hasErrors = true;
        $user = $this->userRepository->getByPasswordResetToken($token);
        if ($user !== null) :
            $item = ObjectFactory::create();
            $item->set('passwordResetToken', $token);
            $this->viewService->setVar(
                'content',
                (new ResetForm($item))->renderForm('user/password/parseResetForm')
            );
            $hasErrors = false;
        endif;

        if ($hasErrors) :
            $this->flashService->setError('CORE_SOMETHING_IS_WRONG');
        endif;
    }

    public function parseForgotFormAction(): void
    {
        $hasErrors = true;
        $return = null;

        $form = new ForgotPasswordForm();
        $form->bind($this->request->getPost(), new stdClass());
        if ($form->validate($this)) :
            $user = $this->userRepository->getByEmail($this->request->get('email'));
            if ($user !== null) :
                $user->set('passwordReset', PasswordFactory::createReset());
                $user->save();
                $this->viewService->set('systemEmailToAddress', $user->getEmail());
                $this->viewService->set(
                    'resetLink',
                    $this->urlService->getBaseUri() .
                    'user/password/resetForm/' .
                    $user->_('passwordReset')->_('passwordResetToken')
                );

                $hasErrors = false;
                $this->flashService->setSucces('USER_PASSWORD_FORGOT_REQUEST_SAVED_SUCCESS');

                $item = $this->repositories->item->getById($this->setting->get('USER_PAGE_PASSWORDFORGOTEMAIL'));
                if ($item !== null):
                    $return = $this->url->getBaseUri() . $item->_('slug');
                endif;
            endif;
        endif;

        if ($hasErrors) :
            $this->flash->setError('CORE_SOMETHING_IS_WRONG');
        endif;

        $this->redirect($return);
    }

    public function parseChangeFormAction(): void
    {
        $hasErrors = true;
        $redirect = '/';
        if ($this->user->isLoggedIn()) :
            $form = new ChangeForm();
            $form->bind($this->request->getPost(), new stdClass());
            if (
                $form->validate($this)
                && $this->request->get('password') === $this->request->get('password2')
            ) :
                $this->user->set('forcePasswordReset', false);
                $this->user->set('password', $this->security->hash($this->request->get('password')));
                $this->user->save();

                $hasErrors = false;
                $this->flash->setSucces('USER_PASSWORD_CHANGE_SUCCESS');
            endif;

            $redirect = null;
        endif;

        if ($hasErrors) :
            $this->flash->setError('CORE_SOMETHING_IS_WRONG');
        endif;

        $this->redirect($redirect);
    }

    public function parseResetFormAction(): void
    {
        $hasErrors = true;

        $form = new ResetForm();
        $form->bind($this->request->getPost(), new stdClass());
        if (
            $form->validate($this)
            && $this->request->get('passwordResetToken') !== null
            && $this->request->get('password') === $this->request->get('password2')
        ) :
            $user = $this->repositories->user->getByPasswordResetToken($this->request->get('passwordResetToken'));
            if ($user !== null) :
                $user->setPassword($this->security->hash($this->request->get('password')))
                    ->setPasswordReset(false)
                    ->setForcePasswordReset(false)
                    ->save();

                $hasErrors = false;
                $this->flash->setSucces('USER_PASSWORD_CHANGE_SUCCESS');
            endif;
        endif;

        if ($hasErrors) :
            $this->flash->setError('CORE_SOMETHING_IS_WRONG');
        endif;

        $this->redirect('user/loginform');
    }
}
