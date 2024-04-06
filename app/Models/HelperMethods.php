<?php
namespace App\Models;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait HelperMethods
{

  public function scopeSearchable($query, Request $request, array $arr = [])
  {
    if ($request->has('q')) {
      $curQuery = $query;
      $q = $request->input('q');
      if (empty($arr) && $this->searchable)
        $arr = $this->searchable;
      foreach ($arr as $k => $column) {
        if ($k == 0)
          $curQuery = $curQuery->where($column, 'like', '%' . $q . '%');
        else
          $curQuery = $curQuery->orWhere($column, 'like', '%' . $q . '%');
      }
      return $curQuery;
    } else {
      return $query;
    }
  }

  public function scopeOrderable($query, Request $request, array $arr = [])
  {
    // dd($query->getModel());
    if ($request->has('order_by')) {
      $orderBy = explode(':', $request->input('order_by'));
      $orderDir = isset($orderBy[1]) ? $orderBy[1] : 'asc';
      $orderDir = $orderDir === 'desc' ? 'desc' : 'asc';
      $orderCol = $orderBy[0];

      if (empty($arr) && $this->searchable)
        $arr = $this->searchable;

      if (!in_array($orderCol, $arr)) {
        return $query;
      }

      return $query->orderBy($orderCol, $orderDir);
    } else {
      return $query;
    }
  }

  public function scopeSearchableWith($query, Request $request, $relation)
  {
    if (array_key_exists($relation, $query->getEagerLoads()) && $request->has('q'))
      return $query->orWhereHas($relation, function ($q) use ($request) {
        return $q->searchable($request);
      });
    else
      return $query;
  }

  public function scopeWithJoin($query, $relations)
  {
    if (is_string($relations))
      $arr = [$relations];
    else if (is_array($relations))
      $arr = $relations;
    else
      throw new Exception("Error: withJoin arguments provided is not compatible should be string or array of strings", 1);
    $table = $this->getTable();
    $primaryKey = $this->getKeyName();
    $foreignKey = $this->getForeignKey();
    if (is_array($query->searchableArray))
      $query->searchableArray = [];
    foreach ($arr as $relation) {
      if (!method_exists(self::class, $relation))
        throw new Exception("Relation {$relation} doesn't exists", 1);
      $relationClass = app(get_class($this->{$relation}()->getRelated()));
      $relationTable = $relationClass->getTable();
      $columns = Schema::getColumnListing($relationTable);
      $query->join($relationTable, "{$table}.{$primaryKey}", "{$relationTable}.{$foreignKey}")->selectRaw('users.*');
      foreach ($columns as $col) {
        if (in_array($col, $relationClass->getHidden()))
          continue;
        $query->selectRaw("{$relationTable}.{$col} AS `{$relation}.{$col}`");
      }
    }
    return $query;
  }

}