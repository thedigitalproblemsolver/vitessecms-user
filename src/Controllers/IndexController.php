<?php declare(strict_types=1);

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
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Forms\LoginForm;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\UserRepository;

class IndexController extends AbstractControllerFrontend
{
    private UrlService $urlService;
    private Security $securityService;
    private Session $sessionService;
    private UserRepository $userRepository;
    private ItemRepository $itemRepository;
    private User $activeUser;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->urlService = $this->eventsManager->fire(UrlEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->securityService = $this->eventsManager->fire(SecurityEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->sessionService = $this->eventsManager->fire(SessionEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->userRepository = $this->eventsManager->fire(UserEnum::GET_REPOSITORY, new stdClass());
        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new stdClass());
        $this->activeUser = $this->eventsManager->fire(UserEnum::GET_ACTIVE_USER_LISTENER, new stdClass());
    }

    public function indexAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $this->viewService->setVar('content', $this->eventsManager->fire(
                BlockPositionEnum::RENDER_POSITION,
                new RenderPositionDTO('myaccount', $this->activeUser->getRole())
            ));
        else :
            $this->redirect('user/loginform', 401, 'Unauthorized');
        endif;
    }

    public function loginAction(): void
    {
        $hasErrors = true;
        $ajax = [];
        $return = null;

        if ($this->activeUser->isLoggedIn()) :
            $this->redirect('user/index');
        else :
            $loginForm = new LoginForm();
            if ($loginForm->validate()) :
                $user = $this->userRepository->getByEmail($this->request->getPost('email'));
                if ($user) :
                    if ($user->hasForcedPasswordReset()) :
                        $return = $this->handleForcedPasswordReset($user);
                        $hasErrors = false;
                    else :
                        $password = $this->request->getPost('password');
                        $return = 'user/index';
                        if ($this->securityService->checkHash($password, $user->_('password'))) :
                            $this->sessionService->set('auth', ['id' => (string)$user->getId()]);
                            $this->eventsManager->fire(UserEnum::ON_LOGIN_SUCCESS_LISTENER, $user);
                            $this->flashService->setSucces('USER_LOGIN_SUCCESS');
                            $ajax = ['successFunction' => 'refresh()'];
                            $hasErrors = false;
                        endif;
                    endif;
                else :
                    $this->securityService->hash(uniqid());
                endif;
            endif;

            if ($hasErrors) :
                $this->flashService->setError('USER_LOGIN_FAILED');
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
        $item = $this->itemRepository->getById($this->setting->get('USER_PAGE_PASSWORDFORCED'));

        return $this->urlService->getBaseUri() . $item->getSlug();
    }

    public function logoutAction(): void
    {
        $this->sessionService->destroy();
        $this->flashService->setSucces('USER_LOGOUT_SUCCESS');
        $this->redirect('/');
    }

    public function loginformAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $this->redirect('user/index');
        else :
            $this->viewService->set('content', (new LoginForm())->renderForm('user/login', 'login'));
        endif;
    }
}
