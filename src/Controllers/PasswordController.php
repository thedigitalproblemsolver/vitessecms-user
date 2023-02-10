<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use Phalcon\Encryption\Security;
use stdClass;
use VitesseCms\Content\Enum\ItemEnum;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractControllerFrontend;
use VitesseCms\Core\Enum\SecurityEnum;
use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Setting\Enum\SettingEnum;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\User\Enum\SettingsEnum;
use VitesseCms\User\Enum\TranslationEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Factories\PasswordFactory;
use VitesseCms\User\Forms\ChangeForm;
use VitesseCms\User\Forms\ForgotPasswordForm;
use VitesseCms\User\Forms\LoginForm;
use VitesseCms\User\Forms\ResetForm;
use VitesseCms\User\Repositories\UserRepository;

class PasswordController extends AbstractControllerFrontend
{
    private Security $securityService;
    private UserRepository $userRepository;
    private ItemRepository $itemRepository;
    private SettingService $settingService;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->securityService = $this->eventsManager->fire(SecurityEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->userRepository = $this->eventsManager->fire(UserEnum::GET_REPOSITORY->value, new stdClass());
        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new stdClass());
        $this->settingService = $this->eventsManager->fire(SettingEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
    }

    public function indexAction(): void
    {
        $this->redirect($this->urlService->getBaseUri());
    }

    public function changeFormAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $this->viewService->setVar('content', (new ChangeForm())->renderForm(
                $this->urlService->getBaseUri() . 'user/password/parseChangeForm'
            ));
        else :
            $this->viewService->setVar('content', (new LoginForm())->renderForm(
                $this->urlService->getBaseUri() . 'user/login'
            ));
        endif;
    }

    public function forgotFormAction(): void
    {
        $this->viewService->setVar('content', (new ForgotPasswordForm())->renderForm(
            $this->urlService->getBaseUri() . 'user/password/parseForgotForm')
        );
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
                (new ResetForm($item))->renderForm($this->urlService->getBaseUri() . 'user/password/parseResetForm')
            );
            $hasErrors = false;
        endif;

        if ($hasErrors) :
            $this->flashService->setError(TranslationEnum::CORE_SOMETHING_IS_WRONG->name);
        endif;
    }

    public function parseForgotFormAction(): void
    {
        $hasErrors = true;
        $return = $this->urlService->getBaseUri() . 'user/password/forgotForm';

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
                $this->flashService->setSucces(TranslationEnum::USER_PASSWORD_FORGOT_REQUEST_SAVED_SUCCESS->name);

                $item = $this->itemRepository->getById(
                    $this->settingService->get(SettingsEnum::USER_PAGE_PASSWORDFORGOTEMAIL->name)
                );
                if ($item !== null):
                    $return = $this->urlService->getBaseUri() . $item->getSlug();
                endif;
            endif;
        endif;

        if ($hasErrors) :
            $this->flashService->setError('CORE_SOMETHING_IS_WRONG');
        endif;

        $this->redirect($return);
    }

    public function parseChangeFormAction(): void
    {
        $hasErrors = true;
        $redirect = $this->urlService->getBaseUri();
        if ($this->activeUser->isLoggedIn()) :
            $form = new ChangeForm();
            $form->bind($this->request->getPost(), new stdClass());
            if (
                $form->validate($this)
                && $this->request->get('password') === $this->request->get('password2')
            ) :
                $this->activeUser->setForcePasswordReset(false)
                    ->setPassword($this->securityService->hash($this->request->get('password')))
                    ->save();

                $hasErrors = false;
                $this->flashService->setSucces(TranslationEnum::USER_PASSWORD_CHANGE_SUCCESS->name);
            endif;

            $redirect = null;
        endif;

        if ($hasErrors) :
            $this->flashService->setError(TranslationEnum::CORE_SOMETHING_IS_WRONG->name);
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
            $user = $this->userRepository->getByPasswordResetToken($this->request->get('passwordResetToken'));
            if ($user !== null) :
                $user->setPassword($this->securityService->hash($this->request->get('password')))
                    ->setPasswordReset(false)
                    ->setForcePasswordReset(false)
                    ->save();

                $hasErrors = false;
                $this->flashService->setSucces(TranslationEnum::USER_PASSWORD_CHANGE_SUCCESS->name);
            endif;
        endif;

        if ($hasErrors) :
            $this->flashService->setError(TranslationEnum::CORE_SOMETHING_IS_WRONG->name);
        endif;

        $this->redirect($this->urlService->getBaseUri() . 'user/loginform');
    }
}
