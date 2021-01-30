<?php declare(strict_types=1);

namespace VitesseCms\User\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\FileUtil;
use VitesseCms\Core\Utils\SystemUtil;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\AbstractForm;
use VitesseCms\User\Repositories\RepositoriesInterface;
use VitesseCms\User\Utils\PermissionUtils;
use Phalcon\Tag;

class AdminpermissionsController extends AbstractAdminController implements RepositoriesInterface
{
    public function adminListAction(): void
    {
        $roles = $this->repositories->permissionRole->getAdminListRoles();
        $modules = SystemUtil::getModules($this->configuration);
        $checks = [];
        if (is_file(PermissionUtils::getAccessFileName())) :
            $checks = PermissionUtils::getAccessFile();
        endif;
        $defaults = PermissionUtils::getDefaults();

        $rolesList = [];
        $headers = ['function'];
        while ($roles->valid()) :
            $role = $roles->current();
            $key = $roles->key();
            $headers[] = $role->getNameField();
            $rolesList[] = $role;
            if ($role->hasChildren()) :
                $children = $this->repositories->permissionRole->getAdminListChildren((string)$role->getId());
                if ($children) :
                    foreach ($children as $child) :
                        $rolesList[] = $child;
                        $headers[] = $child->getNameField().' <small>child of '.$role->getNameField().'</small>';
                    endforeach;
                endif;
            endif;
            $roles->next();
        endwhile;

        $rows = ['modules' => []];
        foreach ($modules as $moduleName => $modulePath) :
            $controllers = DirectoryUtil::getFilelist($modulePath.'/controllers');
            foreach ($controllers as $controllerPath => $controllerNameLong) :
                $adminOnlyAccess = false;
                if (substr_count($controllerNameLong, 'Admin') > 0) :
                    $adminOnlyAccess = true;
                endif;

                $functions = FileUtil::getFunctions($controllerPath, $this->configuration);
                $permissions = [];
                foreach ($functions as $function) :
                    $controllerName = str_replace('controller', '', strtolower(FileUtil::getName($controllerNameLong)));
                    $orgName = $function;
                    $function = strtolower(str_replace('Action', '', $function));

                    $fieldName = 'check['.$moduleName.']['.$controllerName.']['.$function.'][access][]';
                    $cells = [];
                    foreach ($rolesList as $role) :
                        if (
                            $adminOnlyAccess === false
                            || (
                                $adminOnlyAccess === true
                                && $role->_('adminAccess')
                            )
                        ) :
                            $parameters = [
                                $fieldName,
                                'value' => $role->_('calling_name'),
                                'class' => 'form-control',
                            ];
                            //check non-core acl
                            if (
                                isset($checks[$moduleName][$controllerName][$function]['access'])
                                && \in_array(
                                    $role->_('calling_name'),
                                    $checks[$moduleName][$controllerName][$function]['access'],
                                    true
                                )
                            ) :
                                $parameters['checked'] = 'checked';
                            endif;

                            //check core acl
                            if (
                                isset($defaults[$moduleName][$controllerName][$function]['access'])
                                && (
                                    $defaults[$moduleName][$controllerName][$function]['access'] === '*'
                                    || \in_array(
                                        $role->_('calling_name'),
                                        $defaults[$moduleName][$controllerName][$function]['access'],
                                        true
                                    )
                                )
                            ) :
                                $parameters['checked'] = 'checked';
                                $parameters['readonly'] = 'readonly';
                            endif;

                            //check parent role
                            if ($role->_('parentId')) :
                                $parentRole = $this->repositories->permissionRole->getById($role->_('parentId'));
                                if (
                                    isset($checks[$moduleName][$controllerName][$function]['access'])
                                    && \in_array(
                                        $parentRole->_('calling_name'),
                                        $checks[$moduleName][$controllerName][$function]['access'],
                                        true
                                    )
                                ) :
                                    $parameters['checked'] = 'checked';
                                endif;

                                if (
                                    isset($defaults[$moduleName][$controllerName][$function]['access'])
                                    && (
                                        $defaults[$moduleName][$controllerName][$function]['access'] === '*'
                                        || \in_array(
                                            $parentRole->_('calling_name'),
                                            $defaults[$moduleName][$controllerName][$function]['access'],
                                            true
                                        )
                                    )
                                ) :
                                    $parameters['checked'] = 'checked';
                                    $parameters['readonly'] = 'readonly';
                                endif;
                            endif;

                            $element = Tag::checkField($parameters);
                            $cells[] = $element;
                        else :
                            $cells[] = '';
                        endif;
                    endforeach;
                    $permissions[] = $cells;
                endforeach;
                $rowsControllers = [
                    'name' => FileUtil::getName($controllerNameLong),
                    'permissions' => [
                        'name' => $orgName,
                        'cells' => $cells
                    ]
                ];
            endforeach;
            $rows['modules'][] = [
                'name' => $moduleName,
                'controllers' => $rowsControllers
            ];
        endforeach;

        $table = $this->view->renderTemplate(
            'permissionsAdminListItem',
            $this->configuration->getVendorNameDir().'user/src/resources/views/admin/',
            [
                'headers' => $headers,
                'colspan' => count($rolesList) + 1,
                'rows' => $rows
            ]
        );

        $this->view->setVar('content', $table);
        $this->prepareView();
    }

    public function saveAction(?string $itemId = null, AbstractCollection $item = null, AbstractForm $form = null): void
    {
        $hash = gzdeflate(
            base64_encode(
                serialize($this->request->getPost('check'))
            )
        );
        file_put_contents(PermissionUtils::getAccessFileName(), $hash);

        $this->flash->setSucces('ADMIN_PERMISSIONS_SAVED_SUCCESS');

        $this->redirect();
    }
}
