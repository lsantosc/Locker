<?php
namespace Locker\Controller\Component;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

class LockerComponent extends AuthComponent
{

    public $role = 'public';
    public $roles;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        if($this->user('role')) $this->role = $this->user('role');
        if (!file_exists(CONFIG . "locker.php")) throw new Exception(_('locker.php not found in config directory'));

        //Load configuration directives for Locker
        $params = $this->request->params;
        Configure::load('locker');
        $this->roles = Configure::read('Locker.roles');
        $controllers = Configure::read('Locker.controllers');

        $plugin = empty($params['plugin']) ? '' : ".{$params['plugin']}";

        $config = [
            'base'=>strtolower("Locker.controllers{$plugin}.{$params['controller']}.{$params['action']}"),
            'full'=>strtolower("Locker.controllers{$plugin}.{$params['controller']}.{$params['action']}.".implode($params['pass'])),
            'wildcard'=>strtolower("Locker.controllers{$plugin}.{$params['controller']}.{$params['action']}.*"),
        ];

        pr($config);
        pr(Configure::read('Locker.controllers.locker.chase.another.*'));

        //pr($config);

        //pr($controllers);

        exit;
    }

}