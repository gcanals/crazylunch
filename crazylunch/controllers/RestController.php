<?php
namespace crazylunch\controllers ;


/**
 * Description of RestController
 *
 * @author canals
 */
abstract class RestController {
    protected $app;
    protected $req;
    protected $res;
    

    protected $resource ;//= 'resto';
    
    public function __construct( \Slim\Slim $app) {
        $this->app=$app;
        $this->req= $app->request();
        $this->res=$app->response();

    }
    
    public static function addManagedResource($name, $model,$route,Array $attsForList) {
        $slim= \Slim\Slim::getInstance();
        $slim->$name=array('model'=>$model,'route'=>$route,
                                    'type'=>$name, 'attsForList'=>$attsForList);
        
    }
    
    public static function addClosureForResources(Array $resources, $oneOrList, $closure) {
        $slim= \Slim\Slim::getInstance();
        foreach ($resources as $name) {
            $res =  $slim->$name;
            $res[$oneOrList]=$closure;
            $slim->$name = $res; 
            
        }
    }
    
    public function setResponseStatus($s) {
        $this->res->setStatus($s);
    }
    
    public function json_response() {
      $this->res->headers->set('Content-Type', 
                                'application/json; charset=utf-8');
  }
  
  public function getMany() {
      $app=$this->app;
      $resource=$this->resource;
      $resDesc=$app->$resource;  
      $model=$resDesc['model']; 
      $q = $model::orderBy('id');
      foreach( $resDesc['attsForList'] as $att) $q->addSelect($att); 
      if (!is_null($app->request->get())) {
          foreach($app->request->get() as $att=>$val ) {
              $q->where($att,$val);
          }
      }
      $list=$q->get();
      $list->each( function($r) use($resDesc, $app) {
          //$r->uri= $this->app->urlFor($resDesc['route'],
          //                            array('id'=> $r->id) );
          $r->uri= $app->urlFor($resDesc['route'],
                                      array('id'=> $r->id) );
          $r->type= $resDesc['route'];
          
      });
      if (isset($resDesc['list'])) $list->each($resDesc['list']);
   
      
      $this->setResponseStatus(200);
      $this->json_response();
      echo $list->toJson();
  }
  
  public function getOne($id) {
      $res=$this->resource;
      $resDesc=$this->app->$res;   
      $model = $resDesc['model'];
      $r=$model::find($id);
      if (is_null($r)) {
          $this->setResponseStatus(404);
          $this->json_response();
          echo json_encode(array('msg'=>'ressource not found'));
          
      } else {
          $this->setResponseStatus(200);
          $this->json_response();
          $r->type= $resDesc['type'];
          if (isset($resDesc['one']))  $r=$resDesc['one']($r);
      
          echo $r->toJson();
      }
  }
  
  public function getHasMany($id,$rel, $assResource) {
      $res=$this->resource;
      $resDesc=$this->app->$res;      
      $model = $resDesc['model'];
      $q=$model::find($id)->$rel();
      $resRel = $this->app->$assResource; 
      
      foreach ($resRel['attsForList'] as $att) $q->addSelect($att);
      $list=$q->get();
      $slim=$this->app;
      $list->each( function($r) use($resRel, $slim){
          $r->uri= $slim->urlFor($resRel['route'],
                                      array('id'=> $r->id) );
          $r->type= $resRel['type'];
          
      });
      if (isset($resRel['list'])) $list->each($resDesc['list']);
      
      $this->setResponseStatus(200);
      $this->json_response();
      echo $list->toJson();
      
  }
  
  public function getBelongsTo($id, $rel, $assResource) {
      $res=$this->resource;
      $resDesc=$this->app->$res; 
      $resRel = $this->app->$assResource; 
      $model = $resDesc['model']; 
      
      $o=$model::find($id); 
        $r=$o->$rel;
      
      $r->type= $resRel['type'];
      if (isset($resRel['one']))  $r=$resRel['one']($r);
      
      $this->setResponseStatus(200);
      $this->json_response();
      echo $r->toJson();
      
  }
}

