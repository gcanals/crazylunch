<?php
namespace crazylunch\app ;

use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use \crazylunch\controllers as control ;
/**
 * App : the class representing the crazylunch application
 * defines the application routes
 * defines and configures the rest resources
 *
 *
 * @author canals
 */
class App {
    public static $origin ;//='https://localhost:8888/';
    public $slimApp;
    
    public function __construct() {
        $this->slimApp = new \Slim\Slim();
        self::EloConfigure();
    }
    
    
    
    public function run() {
        $this->slimApp->run();
    }

    /**
     * resourcesConfigure : resource configuration for the rest controller
     *
     * @access public
     * @return void
     */
    public function resourcesConfigure() {
        $slim=$this->slimApp;
        $img_path=function(\Illuminate\Database\Eloquent\Model $p) use ($slim) {
                $p->imageUri = $slim->request->getRootUri() . '/images/original/' . $p->photo ;
                $p->origin = App::$origin;
                return $p;
                };
        $img_path_list=function(\Illuminate\Database\Eloquent\Model $p) use ($slim) {
                $p->imageUri = $slim->request->getRootUri() . '/images/small/' . $p->photo ;
                $p->origin = App::$origin;
                return $p;
                };
    \crazylunch\controllers\RestController::addManagedResource('theme', '\crazylunch\models\Theme', 'theme', array('id','nom','photo'));
    \crazylunch\controllers\RestController::addManagedResource('resto', '\crazylunch\models\Resto', 'resto', array('id','nom','photo'));
    \crazylunch\controllers\RestController::addManagedResource('plat', '\crazylunch\models\Plat', 'plat', array('id','nom','prix','photo'));
    \crazylunch\controllers\RestController::addClosureForResources(array('theme','resto','plat'), 'one' , $img_path);
    \crazylunch\controllers\RestController::addClosureForResources(array('theme','resto','plat'), 'list', $img_path_list);
    
    }

    /**
     * EloConfigure : configure and boot Eloquent
     *
     * @access public
     * @return void
     * @throws Exception : if the config file could not be read
     */
    public static function EloConfigure() {
     $conf_file = 'db.config.ini';
     $config= parse_ini_file($conf_file,true);

    if (!$config) 
        throw new Exception("App::eloConfigure: could not parse config file $conf_file <br/>");
    
    App::$origin = $config['SERVER']['origin'];
        
 

    $capsule = new DB();
    $capsule->addConnection($config['DB']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
 }


    /**
     * routes : defines the routes of the applciation
     *
     * @access public
     * @return void
     *
     */
    public function routes(){
      
        $app=  $this->slimApp;
        
        $app->get('/themes/', function() use($app) {
            $c= new \crazylunch\controllers\ThemeController($app);
            $c->getMany(function(\crazylunch\models\Theme $p) use ($app) {
                $p->imageUri = $app->request->getRootUri() . '/images/' . $p->photo ;
                return $p;
            });
        });
        
        $app->get('/themes/:id', function($id) use($app) {
            $c= new \crazylunch\controllers\ThemeController($app);
            $c->getOne($id, function(\crazylunch\models\Theme $p) use ($app) {
                $p->imageUri = $app->request->getRootUri() . '/images/' . $p->photo ;
                return $p;
            });
        })->name('theme');
        
        $app->get('/themes/:id/restos/', function($id) use ($app) {
            $c= new \crazylunch\controllers\ThemeController($app);
            $c->getHasMany($id, 'restos', 'resto');
        }
        );
        
        $app->get('/restos/', function() use($app) {
            $c= new \crazylunch\controllers\RestoController($app);
            $c->getMany(function(\crazylunch\models\Resto $p) use ($app) {
                $p->imageUri = $app->request->getRootUri() . '/images/' . $p->photo ;
                return $p;
            });
        });
        
        $app->get('/restos/:id', function($id) use($app) {
            $c= new \crazylunch\controllers\RestoController($app);
            $c->getOne($id, function(\crazylunch\models\Resto $p) use ($app) {
                $p->imageUri = $app->request->getRootUri() . '/images/' . $p->photo ;
                return $p;
            });
        })->name('resto');
        
        $app->get('/restos/:id/plats/', function($id) use ($app) {
            $c= new \crazylunch\controllers\RestoController($app);
            $c->getHasMany($id, 'plats', 'plat');
        }
        );
        $app->get('/restos/:id/themes/', function($id) use ($app) {
            $c= new \crazylunch\controllers\RestoController($app);
            $c->getBelongsTo($id, 'theme', 'theme');
        }
        );
        
        $app->get('/plats/', function() use($app) {
            $c= new \crazylunch\controllers\PlatController($app);
            $c->getMany();
        });
        
        $app->get('/plats/:id', function($id) use($app) {
            $c= new \crazylunch\controllers\PlatController($app);
            $c->getOne($id, function(\crazylunch\models\Plat $p) use ($app) {
                $p->imageUri = $app->request->getRootUri() . '/images/' . $p->photo ;
                return $p;
            });
        })->name('plat');
        
        $app->get('/plats/:id/restos/', function($id) use ($app) {
            $c= new \crazylunch\controllers\PlatController($app);
            $c->getBelongsTo($id, 'resto', 'resto');
        }
        );
        
        $app->get('/', function() use ($app){
          $st = '
           welcome to ze crazy lunch app service<br />
           available Uris <b> GET ONLY </b>:<br /> 
          <ul>
         
          <li>'. $app->request->getRootUri() . '/themes/ :(tableau) liste des themes - chaque theme est désigné par son uri
          <li>'. $app->request->getRootUri() . '/themes/1 :(objet) le theme 1, en détail
          <li>'. $app->request->getRootUri() . '/themes/1/restos :(tableau) liste des restos pour un thème donné- chaque resto est désigné par son uri
          <li>'. $app->request->getRootUri() . '/restos/ :(tableau) liste des restos - chaque resto est désigné par son uri
          <li>'. $app->request->getRootUri() . '/restos/1 :(objet) le resto 1 en détail
          <li>'. $app->request->getRootUri() . '/plats/ :(tableau) la liste des plats  - chaque plat est désigné par son uri 
          <li>'. $app->request->getRootUri() . '/plats/1 :(objet) le resto 1 en détail - contient une uri vers la photo du plat
          </ul>' ;

	  $app->response()->setStatus(200);
	  $app->response->headers->set('Content-Type','text/html; charset=utf-8');
          echo $st;
      }) ;
    } 
}


