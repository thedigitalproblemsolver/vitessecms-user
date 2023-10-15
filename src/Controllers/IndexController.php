<?php

declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use Phalcon\Encryption\Security;
use Phalcon\Session\Manager as Session;
use stdClass;
use VitesseCms\Block\DTO\RenderPositionDTO;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Content\Enum\ItemEnum;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractControllerFrontend;
use VitesseCms\Core\Enum\SecurityEnum;
use VitesseCms\Core\Enum\SessionEnum;
use VitesseCms\Setting\Enum\SettingEnum;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\User\Enum\SettingsEnum;
use VitesseCms\User\Enum\TranslationEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Forms\LoginForm;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\UserRepository;

class IndexController extends AbstractControllerFrontend
{
    private Security $securityService;
    private Session $sessionService;
    private UserRepository $userRepository;
    private ItemRepository $itemRepository;
    private SettingService $settingService;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->securityService = $this->eventsManager->fire(SecurityEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->sessionService = $this->eventsManager->fire(SessionEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->userRepository = $this->eventsManager->fire(UserEnum::GET_REPOSITORY->value, new stdClass());
        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new stdClass());
        $this->settingService = $this->eventsManager->fire(SettingEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
    }

    public function indexAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $this->viewService->setVar(
                'content',
                $this->eventsManager->fire(
                    BlockPositionEnum::RENDER_POSITION,
                    new RenderPositionDTO('myaccount', [$this->activeUser->getRole()])
                )
            );
        else :
            $this->redirect($this->urlService->getBaseUri() . 'user/loginform', 401, 'Unauthorized');
        endif;
    }

    public function loginAction(): void
    {
        $hasErrors = true;
        $return = null;

        if ($this->activeUser->isLoggedIn()) :
            $this->redirect($this->urlService->getBaseUri() . 'user/index');
        else :
            $loginForm = new LoginForm();
            if ($loginForm->validate()) :
                $user = $this->userRepository->getByEmail($this->request->getPost('email'));
                if ($user) :
                    if ($user->hasForcedPasswordReset()) :
                        $return = $this->handleForcedPasswordReset($user);
                        $hasErrors = false;
                    else :
                        $return = $this->urlService->getBaseUri() . 'user/index';
                        if ($this->securityService->checkHash(
                            $this->request->getPost('password'),
                            $user->getPassword()
                        )) :
                            $this->sessionService->set('auth', ['id' => (string)$user->getId()]);
                            $this->eventsManager->fire(UserEnum::ON_LOGIN_SUCCESS_LISTENER->value, $user);
                            $this->flashService->setSucces(TranslationEnum::USER_LOGIN_SUCCESS->name);
                            $hasErrors = false;
                        endif;
                    endif;
                else :
                    $this->securityService->hash(uniqid());
                endif;
            endif;

            if ($hasErrors) :
                $this->flashService->setError(TranslationEnum::USER_LOGIN_FAILED->name);
            endif;

            $this->redirect($return);
        endif;
    }

    private function handleForcedPasswordReset(User $user): string
    {
        $this->logService->write(
            $user->getId(),
            User::class,
            'Forced password reset for ' . $user->getEmail()
        );
        $item = $this->itemRepository->getById(
            $this->settingService->get(SettingsEnum::USER_PAGE_PASSWORDFORGOTEMAIL->name)
        );

        return $this->urlService->getBaseUri() . $item->getSlug();
    }

    public function logoutAction(): void
    {
        $this->sessionService->destroy();
        $this->flashService->setSucces(TranslationEnum::USER_LOGOUT_SUCCESS->name);
        $this->redirect($this->urlService->getBaseUri());
    }

    public function loginformAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $this->redirect($this->urlService->getBaseUri() . 'user/index');
        else :
            $this->viewService->set(
                'content',
                (new LoginForm())->renderForm($this->urlService->getBaseUri() . 'user/login', 'login')
            );
        endif;
    }
}
