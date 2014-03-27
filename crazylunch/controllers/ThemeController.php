<?php
namespace crazylunch\controllers;
use \crazylunch\models\Theme;
/**
 * Description of ThemeController
 *
 * @author canals
 */
class ThemeController extends RestController {
    
    public function __construct(\Slim\Slim $app) {
        parent::__construct($app);
       
        $this->resource = 'theme';
    }
  
   
}

