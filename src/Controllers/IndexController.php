<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use Phalcon\Encryption\Security;
use Phalcon\Session\Manager as Session;
use stdClass;
use VitesseCms\Block\Enum\BlockEnum;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Content\Enum\ItemEnum;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractControllerFrontend;
use VitesseCms\Core\Enum\SecurityEnum;
use VitesseCms\Core\Enum\SessionEnum;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Forms\LoginForm;
use VitesseCms\User\Models\User;
use VitesseCms\User\Repositories\BlockPositionRepository;
use VitesseCms\User\Repositories\UserRepository;

class IndexController extends AbstractControllerFrontend
{
    private UrlService $urlService;
    private Security $securityService;
    private Session $sessionService;
    private BlockPositionRepository $blockPositionRepository;
    private BlockRepository $blockRepository;
    private UserRepository $userRepository;
    private ItemRepository $itemRepository;
    private User $activeUser;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->urlService = $this->eventsManager->fire(UrlEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->securityService = $this->eventsManager->fire(SecurityEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->sessionService = $this->eventsManager->fire(SessionEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->blockPositionRepository = $this->eventsManager->fire(BlockPositionEnum::GET_REPOSITORY, new stdClass());
        $this->blockRepository = $this->eventsManager->fire(BlockEnum::GET_REPOSITORY, new stdClass());
        $this->userRepository = $this->eventsManager->fire(UserEnum::GET_REPOSITORY, new stdClass());
        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new stdClass());
        $this->activeUser = $this->eventsManager->fire(UserEnum::GET_ACTIVE_USER_LISTENER, new stdClass());
    }

    public function indexAction(): void
    {
        if ($this->activeUser->isLoggedIn()) :
            $block = ObjectFactory::create();
            $block->set('items', $this->getTabsByPosition());

            $this->viewService->setVar('content', $this->viewService->renderTemplate(
                'scrollspy',
                'partials/bootstrap',
                ['block' => $block]
            ));
        else :
            $this->redirect('user/loginform', 401, 'Unauthorized');
        endif;
    }

    private function getTabsByPosition(): array
    {
        $tabs = [];
        $blockPositions = $this->blockPositionRepository->getByMyAccountPosition($this->activeUser->getRole());
        while ($blockPositions->valid()) :
            $blockPosition = $blockPositions->current();
            $block = $this->blockRepository->getById($blockPosition->getBlock());
            $tmp = [
                'id' => $block->getId(),
                'name' => $block->getNameField(),
                'content' => $this->eventsManager->fire(BlockEnum::BLOCK_LISTENER . ':renderBlock', $block)
            ];
            $tabs[] = $tmp;
            $blockPositions->next();
        endwhile;

        return $tabs;
    }

    public function loginAction(): void
    {
        $hasErrors = true;
        $ajax = [];
        $return = null;

        if ($this->activeUser->isLoggedIn()) :
            $this->redirect('user/index', [], true, true);
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

            $this->redirect($return, $ajax, true, true);
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
