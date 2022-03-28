<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use VitesseCms\Block\Enum\BlockEnum;
use VitesseCms\Core\AbstractController;
use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\User\Forms\LoginForm;
use VitesseCms\User\Repositories\RepositoriesInterface;
use VitesseCms\User\Models\User;

class IndexController extends AbstractController implements RepositoriesInterface
{
    public function indexAction(): void
    {
        if ($this->user->isLoggedIn()) :
            $tabs = [];
            $blockPositions = $this->repositories->blockPosition->getByMyAccountPosition($this->user->getPermissionRole());
            while ($blockPositions->valid()) :
                $blockPosition = $blockPositions->current();
                $block = $this->repositories->block->getById($blockPosition->getBlock());
                $tmp = [
                    'id' => $block->getId(),
                    'name' => $block->getNameField(),
                    'content' => $this->eventsManager->fire(BlockEnum::BLOCK_LISTENER . ':renderBlock', $block)
                ];
                $tabs[] = $tmp;
                $blockPositions->next();
            endwhile;

            $block = ObjectFactory::create();
            $block->set('items', $tabs);

            $this->view->setVar('content', $this->view->renderTemplate(
                'scrollspy',
                'partials/bootstrap',
                ['block' => $block]
            ));
            $this->prepareView();
        else :
            $this->redirect('user/loginform');
        endif;
    }

    public function loginAction(): void
    {
        $hasErrors = true;
        $ajax = [];
        $return = null;

        if ($this->user->isLoggedIn()) :
            $this->redirect('user/index', [], true, true);
        else :
            $loginForm = new LoginForm();
            if ($loginForm->validate($this)) :
                $user = $this->repositories->user->getByEmail($this->request->getPost('email'));
                if ($user) :
                    if ($user->hasForcedPasswordReset()) :
                        $this->log->write(
                            $user->getId(),
                            User::class,
                            'Forced password reset for ' . $user->_('email')
                        );
                        $item = $this->repositories->item->getById($this->setting->get('USER_PAGE_PASSWORDFORCED'));
                        $return = $this->url->getBaseUri() . $item->getSlug();
                        $hasErrors = false;
                    else :
                        $password = $this->request->getPost('password');
                        $return = 'user/index';
                        if ($this->security->checkHash($password, $user->_('password'))) :
                            $this->session->set('auth', ['id' => (string)$user->getId()]);
                            $this->eventsManager->fire('user:onLoginSuccess', $user);
                            $this->flash->setSucces('USER_LOGIN_SUCCESS');
                            $ajax = ['successFunction' => 'refresh()'];
                            $hasErrors = false;
                        endif;
                    endif;
                else :
                    $this->security->hash(mt_rand());
                endif;
            endif;

            if ($hasErrors) :
                $this->flash->setError('USER_LOGIN_FAILED');
            endif;

            $this->redirect($return, $ajax, true, true);
        endif;
    }

    public function logoutAction(): void
    {
        $this->session->destroy();
        $this->flash->setSucces('USER_LOGOUT_SUCCESS');

        $this->redirect('/');
    }

    public function loginformAction(): void
    {
        if ($this->user->isLoggedIn()) :
            $this->redirect('user/index', [], true, true);
        else :
            $this->view->set('content', (new LoginForm())->renderForm('user/login', 'login'));
        endif;
        $this->prepareView();
    }
}
