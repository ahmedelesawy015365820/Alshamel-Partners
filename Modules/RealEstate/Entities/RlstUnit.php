<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use App\Traits\VideoLink;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstUnit extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait, VideoLink;

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'code',
                'name',
                'name_e',
                'description',
                'description_e',
                'unit_ty',
                'unit_area',
                'building_id',
                'unit_status_id',
                'rooms',
                "unit_net_area",
                'path',
                'view',
                'floor',
                'finishing',

                'properties',
                'module',
            )->with('unitStatus:id,name,name_e', 'building:id,name,name_e', 'media');
    }

    // relations

    public function unitStatus()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnitStatus::class);
    }

    public function building()
    {
        return $this->belongsTo(RlstBuilding::class);
    }

    public function owner()
    {
        return $this->belongsTo(RlstOwner::class);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }
    public function type()
    {
        return $this->belongsTo(RlstUnitType::class , 'unit_ty');
    }

    public function items()
    {
        return $this->hasMany(RlstItem::class, 'unit_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->items()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'items',
                'count' => $this->items()->count(),
                'ids' => $this->items()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Unit')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    // attributes

    protected function attachments(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    protected function properties(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    public function scopeGeneralFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->when($request->building_id, function ($q) use ($request) {
                    $q->where('building_id', $request->building_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_ty, function ($q) use ($request) {
                    $q->where('unit_ty', $request->unit_ty);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_area, function ($q) use ($request) {
                    $q->where('unit_area', $request->unit_area);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->rooms, function ($q) use ($request) {
                    $q->where('rooms', $request->rooms);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->path, function ($q) use ($request) {
                    $q->where('path', $request->path);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->properties, function ($q) use ($request) {
                    $q->whereJsonContains('properties', $request->properties);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->wallet_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereHas('buildingWallet', function ($q) use ($request) {
                            $q->where('wallet_id', $request->wallet_id);
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->owner_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereHas('buildingWallet', function ($q) use ($request) {
                            $q->whereHas('wallet', function ($q) use ($request) {
                                $q->whereHas('walletOwner', function ($q) use ($request) {
                                    $q->where('owner_id', $request->owner_id);
                                });
                            });
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->country_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->where('country_id', $request->country_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->city_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->governorate_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                            $q->where('governorate_id', $request->governorate_id);
                    });

                });
            });

        });
    }

    public function scopeUnSoldFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->when($request->unit_status_id, function ($q) use ($request) {
                    $q->whereIn('unit_status_id', $request->unit_status_id);
                });
            })
                ->where(function ($q) use ($request) {
                    $q->when($request->wallet_id, function ($q) use ($request) {
                        $q->whereHas('building', function ($q) use ($request) {
                            $q->whereHas('buildingWallet', function ($q) use ($request) {
                                $q->whereIn('wallet_id', $request->wallet_id);
                            });
                        });
                    });
                })->where(function ($q) use ($request) {
                $q->when($request->owner_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereHas('buildingWallet', function ($q) use ($request) {
                            $q->whereHas('wallet', function ($q) use ($request) {
                                $q->whereHas('walletOwner', function ($q) use ($request) {
                                    $q->whereIn('owner_id', $request->owner_id);
                                });
                            });
                        });
                    });
                });
            })
                ->where(function ($q) use ($request) {
                    $q->when($request->building_id, function ($q) use ($request) {
                        $q->whereIn('building_id', $request->building_id);
                    });
                })->where(function ($q) use ($request) {
                $q->when($request->country_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereIn('country_id', $request->country_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->city_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereIn('city_id', $request->city_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->governorate_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereIn('governorate_id', $request->governorate_id);
                    });

                });
            })->where(function ($q) use ($request) {
                $q->when($request->properties, function ($q) use ($request) {
                    $q->whereJsonContains('properties', $request->properties);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_area, function ($q) use ($request) {
                    $q->where('unit_area', $request->unit_area);
                });
            })
                ->where(function ($q) use ($request) {
                    $q->when($request->unit_ty, function ($q) use ($request) {
                        $q->where('unit_ty', $request->unit_ty);
                    });
                })->where(function ($q) use ($request) {
                $q->when($request->rooms, function ($q) use ($request) {
                    $q->where('rooms', $request->rooms);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->path, function ($q) use ($request) {
                    $q->where('path', $request->path);
                });
            });

        });
    }

}
