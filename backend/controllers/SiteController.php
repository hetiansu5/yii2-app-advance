<?php
namespace backend\controllers;

use backend\components\BaseController;

/**
 * Site controller
 */
class SiteController extends BaseController
{

    protected $curModule = 'index';

    protected $isAuth = true;

    private static $navMenus = [];

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $navMenus = $this->getNavMenus();
        $firstNavMenu = current($navMenus);
        if (!empty($firstNavMenu['url'])) {
            $this->response->redirect($firstNavMenu['url']);
        }

        $this->_render();
    }

    /**
     * @return array
     */
    protected function getNavMenus()
    {
        if (!self::$navMenus) {
            $menus = $this->getMenus();
            foreach ($menus as $item) {
                if ($item['id'] != 'index') {
                    self::$navMenus[] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'url' => $item['url'],
                        'icon' => isset($item['icon']) ? $item['icon'] : ''
                    ];
                }
            }
        }
        return self::$navMenus;
    }
}
