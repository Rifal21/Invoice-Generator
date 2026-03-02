<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarItem extends Model
{
    protected $fillable = ['label', 'icon', 'route', 'url', 'parent_id', 'order', 'is_active', 'permissions'];

    public function children()
    {
        return $this->hasMany(SidebarItem::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(SidebarItem::class, 'parent_id');
    }
}
