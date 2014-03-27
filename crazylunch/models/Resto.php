<?php
namespace crazylunch\models;
use \Illuminate\Database\Eloquent\Model;
/**
 *
 * Resto model, extends the Eloquent Model
 * table : restaurant
 * PK : id
 *
 * belongsTo : theme()
 * hasMany  : plats()
 *
 * @author canals
 */
 *
 * @author canals
 */
class Resto extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'restaurant';
    protected $primaryKey = 'id';
    public $timestamps=false;
    
    public function plats() {
        return $this->hasMany('\crazylunch\models\Plat', 'id_resto');
    }
    public function theme() {
        return $this->belongsTo('\crazylunch\models\Theme', 'id_theme');
    }
}

