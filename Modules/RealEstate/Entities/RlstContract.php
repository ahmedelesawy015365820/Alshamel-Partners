<?php

namespace Modules\RealEstate\Entities;

use App\Models\GeneralCustomer;
use App\Models\Salesman;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstContract extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;

    protected $fillable = [
        "salesman_id",
        "customer_id",
        "company_id",
        "date",
        "branch_id",
        "document_id",
        "serial_id",
        "module_type",
        "start_date",
        "end_date",
        "serial_number",
        "prefix",
    ];

    // relations

    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class, 'customer_id', 'id');
    }

    public function reservation()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstReservation::class, 'reservation_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstContractDetail::class, 'contract_id', 'id');
    }

    public function contDetails()
    {
        $filter = $this->details()->with(['building.buildingWallet' => function ($q) {
            $q->with(['wallet.owners']);
        }]);

        return $filter;

    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id', 'id');
    }

    public function serial()
    {
        return $this->belongsTo(\App\Models\Serial::class, 'serial_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class, 'document_id', 'id');
    }

    public function breakDowns()
    {
        return $this->hasMany(RpBreakDown::class, 'break_id');
    }

    // public function hasChildren()
    // {
    //     return $this->details()->count() > 0 ||
    //     $this->breakDowns()->count() > 0;

    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->details()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'details',
                'count' => $this->details()->count(),
                'ids' => $this->details()->pluck('id')->toArray()
            ];
        }
        if ($this->breakDowns()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'breakDowns',
                'count' => $this->breakDowns()->count(),
                'ids' => $this->breakDowns()->pluck('id')->toArray()
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Contracts')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function scopeGeneralFilter($query, $request)
    {

        return $query->where(function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->when($request->salesman_id, function ($q) use ($request) {
                    $q->where('salesman_id', $request->salesman_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('start_date', $request->start_date)->whereDate('end_date', $request->end_date);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->building_id, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {
                        $q->where('building_id', $request->building_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_id, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {
                        $q->where('unit_id', $request->unit_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->wallet_id, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {
                        $q->whereHas('building', function ($q) use ($request) {
                            $q->whereHas('buildingWallet', function ($q) use ($request) {
                                $q->where('wallet_id', $request->wallet_id);
                            });
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->owner_id, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {
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
                });
            })->where(function ($q) use ($request) {
                $q->when($request->price, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {

                        $q->whereHas('unit', function ($q) use ($request) {

                            $q->whereHas('items', function ($q) use ($request) {

                                if ($request->price_from && $request->price_to) {
                                    $q->whereBetween('price', [$request->price_from, $request->price_to]);
                                }

                                if ($request->price_from || !$request->price_to) {
                                    $q->where('price', '>=', $request->price_from);
                                }

                                if ($request->price_to || !$request->price_from) {
                                    $q->where('price', '<=', $request->price_to);
                                }

                            });
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->properties, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {
                        $q->whereHas('unit', function ($q) use ($request) {
                            $q->whereJsonContains('properties', $request->properties);
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->lat && $request->lng, function ($q) use ($request) {
                    $q->whereHas('details', function ($q) use ($request) {
                        $q->whereHas('building', function ($q) use ($request) {
                            $q->where('lat', $request->lat)->where('lng', $request->lng);
                        });
                    });
                });
            });
        });
    }

    // public function scopeFilter($query, $request)
    // {
    //     return $query->where(function ($q) use ($request) {

    //         if ($request->salesman_id) {
    //             $q->where('salesman_id', $request->salesman_id);
    //         }
    //         if ($request->start_date) {
    //             $q->whereDate('start_date', $request->start_date);
    //         }

    //         if ($request->end_date) {
    //             $q->whereDate('end_date', $request->end_date);
    //         }

    //         if (
    //             $request->building_id || $request->wallet_id ||
    //             $request->owner_id || $request->unit_value_from || $request->unit_value_to ||
    //             $request->properties || $request->lat || $request->lng
    //         ) {
    //             $q->whereHas('details', function ($q) use ($request) {
    //                 if ($request->building_id) {
    //                     $q->where('building_id', $request->building_id);
    //                 }
    //                 if ($request->unit_id) {
    //                     $q->where('unit_id', $request->unit_id);
    //                 }

    //                 if ($request->wallet_id || $request->owner_id) {
    //                     $q->whereHas("building", function ($q) use ($request) {
    //                         if ($request->wallet_id) {
    //                             $q->whereHas("buildingWallet", function ($q) use ($request) {
    //                                 $q->where('wallet_id', $request->wallet_id);
    //                             });
    //                         }
    //                         if ($request->owner_id) {
    //                             $q->whereHas("buildingWallet", function ($q) use ($request) {
    //                                 $q->whereHas("wallet", function ($q) use ($request) {
    //                                     $q->whereHas("walletOwner", function ($q) use ($request) {
    //                                         $q->where('owner_id', $request->owner_id);
    //                                     });
    //                                 });
    //                             });
    //                         }


    //                     });

    //                 }

    //                 if ($request->unit_value_from && $request->unit_value_to) {
    //                     $q->whereBetween('unit_value', [$request->unit_value_from, $request->unit_value_to]);
    //                 }

    //                 if ($request->unit_value_from || !$request->unit_value_to) {
    //                     $q->where('unit_value', '>=', $request->unit_value_from);
    //                 }

    //                 if ($request->unit_value_to || !$request->unit_value_from) {
    //                     $q->where('unit_value', '<=', $request->unit_value_to);
    //                 }

    //                 if ($request->properties) {
    //                     $q->whereHas('unit', function ($q) use ($request) {
    //                         $q->whereJsonContains('properties', $request->properties);
    //                     });
    //                 }



    //             });
    //         }

    //         // normal search

    //         if ($request->has('date')) {
    //             $q->whereDate('date', $request->date);
    //         }

    //         if ($request->search && $request->columns) {
    //             foreach ($request->columns as $column) {
    //                 if (strpos($column, ".") > 0) {
    //                     $column = explode(".", $column);
    //                     $q->orWhereRelation($column[0], $column[1], 'like', '%' . $request->search . '%');
    //                     // $q->orWhereHas($column[0], function ($q) use ($column, $request) {
    //                     //     $q->where($column[1], 'like', '%' . $request->search . '%');
    //                     // });
    //                 } else {
    //                     $q->orWhere($column, 'like', '%' . $request->search . '%');
    //                 }
    //             }
    //         }

    //         if (request()->header('company-id')) {
    //             if (in_array('company_id', $this->fillable)) {
    //                 $q->where('company_id', request()->header('company-id'));
    //             }
    //         }

    //         if ($request->key && $request->value) {
    //             $q->where($request->key, $request->value);
    //         }

    //     });
    // }
}
