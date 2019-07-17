<?php

namespace App\Http\Controllers;

use App\IranCity;
use App\Population;
use DB;

class IranCityController extends Controller
{
    // reads population from populations table and tries to add them to current table
    public function merge()
    {
        // $this->mergeProvinces();
        // $this->mergeSections();
        // $this->mergeUrbans();
        // $this->mergeRemainingNulls();
        $this->mergeNullsWithPercent();
    }

    private function mergeProvinces()
    {
        $provinces = IranCity::whereType('استان')->get();
        foreach ($provinces as $province) {
            $population = Population::where('province', $province->name)->whereNull('section_1')->first();
            if (!$population) {
                logger('استان');
                logger($province);
            } else {
                $province->population = $population->population;
                $province->save();
            }
        }
    }

    private function mergeSections()
    {
        $provinces = IranCity::whereType('استان')->get();
        foreach ($provinces as $province) {
            $states = IranCity::whereType('شهرستان')->whereParentId($province->id)->get();
            foreach ($states as $state) {
                $population = Population::whereProvince($province->name)->where(function ($q) use ($state) {
                    $q->where('section_1', $state->name)->whereNull('section_2');
                    $q->orWhere('section_2', $state->name);
                })->first();
                if (!$population) {
                    logger('شهرستان');
                    logger($state);
                    // check for without spaces
                } else {
                    $state->population = $population->population;
                    $state->save();
                }
            }
        }
    }

    private function mergeUrbans()
    {
        $provinces = IranCity::whereType('استان')->get();
        foreach ($provinces as $province) {
            $states = IranCity::whereType('شهرستان')->whereParentId($province->id)->get();
            foreach ($states as $state) {
                $cities = IranCity::whereType('منطقه')->whereParentId($state->id)->get();
                foreach ($cities as $city) {
                    $population = Population::whereProvince($province->name)->where(function ($q) use ($state) {
                        $q->where('section_1', $state->name);
                        $q->orWhere('section_2', $state->name);
                    })->where(function ($q) use ($city) {
                        $q->whereUrban($city->name);
                        $q->orWhere('rural', $city->name);
                    })->first();
                    if (!$population) {
                        logger('منطقه');
                        logger($city);
                    } else {
                        $city->population = $population->population;
                        $city->save();
                    }

                }
            }
        }
    }

    public function mergeRemainingNulls()
    {
        $withouts = IranCity::whereNull('population')->get();
        foreach ($withouts as $without) {
            switch ($without->type) {
                case 'استان':
                    break;
                case 'شهرستان':
                    $population = $this->removeFirstSpaceCity($without);
                    if ($population) {
                        $without->population = $population->population;
                        $without->save();
                    } else {
                        $population = $this->removeLastSpaceCity($without);
                        if ($population) {
                            $without->population = $population->population;
                            $without->save();
                        }
                    }
                    break;
                case 'منطقه':
                    $population = $this->removeFirstSpaceAll($without);
                    if ($population) {
                        $without->population = $population->population;
                        $without->save();
                    } else {
                        $population = $this->removeFirstSpaceRural($without);
                        if ($population) {
                            $without->population = $population->population;
                            $without->save();
                        } else {
                            $population = $this->removeLastSpaceAll($without);
                            if ($population) {
                                $without->population = $population->population;
                                $without->save();
                            }
                        }
                    }
                    break;
                default:
                    break;
            }

        }
    }

    public function mergeNullsWithPercent()
    {
        $withouts = IranCity::whereNull('population')->get();
        $count = 0;
        foreach ($withouts as $without) {
            $population = $this->searchWithPercent($without);
            if (!$population) {
                // dd($without);
                continue;
            }
            $count++;

            // dd($population);
            echo $population . '<br>';
        }
        dd($count);
        die();
    }

    private function removeFirstSpaceCity($without)
    {
        $province = IranCity::find($without->parent_id);
        return Population::whereProvince($province->name)->where(function ($q) use ($without) {
            $q->where('section_1', $this->removeSpace($without->name));
            $q->orWhere('section_2', $this->removeSpace($without->name));
        })->first();
    }

    private function removeFirstSpaceAll($without)
    {
        $city = IranCity::find($without->parent_id);
        $province = IranCity::find($city->parent_id);
        return Population::whereProvince($province->name)->where(function ($q) use ($city) {
            $q->where('section_1', $this->removeSpace($city->name));
            $q->orWhere('section_2', $this->removeSpace($city->name));
        })->where(function ($q) use ($without) {
            $q->whereUrban($this->removeSpace($without->name));
            $q->orWhere('rural', $this->removeSpace($without->name));
        })->first();
    }

    private function removeLastSpaceAll($without)
    {
        $city = IranCity::find($without->parent_id);
        $province = IranCity::find($city->parent_id);
        return Population::whereProvince($province->name)->where(function ($q) use ($city) {
            $q->where('section_1', $this->removeLastSpace($city->name));
            $q->orWhere('section_2', $this->removeLastSpace($city->name));
        })->where(function ($q) use ($without) {
            $q->whereUrban($this->removeSpace($without->name));
            $q->orWhere('rural', $this->removeSpace($without->name));
        })->first();
    }

    private function removeFirstSpaceRural($without)
    {
        $city = IranCity::find($without->parent_id);
        $province = IranCity::find($city->parent_id);
        $population = Population::whereProvince($province->name)->where(function ($q) use ($city) {
            $q->where('section_1', $city->name);
            $q->orWhere('section_2', $city->name);
        })->where(function ($q) use ($without) {
            $q->whereUrban($this->removeSpace($without->name));
            $q->orWhere('rural', $this->removeSpace($without->name));
        })->first();
    }

    private function removeLastSpaceCity($without)
    {
        $province = IranCity::find($without->parent_id);
        return Population::whereProvince($province->name)->where(function ($q) use ($without) {
            $q->where('section_1', $this->removeLastSpace($without->name));
            $q->orWhere('section_2', $this->removeLastSpace($without->name));
        })->first();
    }

    public function findFavorites()
    {
        $this->setUpperLayersFavorite();
        $this->findFavoritesByLawyers();
        $this->findFavoritesByPopulation(5000);
        // $this->notFound();
        return 'done';
    }

    private function findFavoritesByLawyers()
    {
        // trying to find favorite places by active locations of active lawyers
        DB::table('ds_user')->where('is_activated_lawyer', 1)->orderBy('state_id')->chunk(250, function ($items) {
            foreach ($items as $item) {
                $lawyer_locations = DB::table('tbl_lawyer_location')->where('fk_lawyer_id', $item->user_id)->where('status', 1)->get();
                foreach ($lawyer_locations as $location) {
                    $this->setFavorite($location->fk_state_id, $location->fk_city_id);
                }
            }
        });

    }

    private function setFavorite($state_id, $city_id)
    {
        // reading data from dadsun tables with given ids
        $state = DB::table('tbs_state')->where('state_id', $state_id)->first();
        $city = DB::table('tbs_city')->where('city_id', $city_id)->first();

        // trying to find given city in iran city
        $province = IranCity::whereType('استان')->whereName(persianConvert($state->state_name, ' '))->first();
        if ($province) {
            $target = IranCity::whereDescendantOf($province)->whereType('منطقه')->whereName(persianConvert($city->city_name, ' '))->first();
            if ($target) {
                $target->favorite = 1;
                $target->save();
            } else {
                logger('set favorite');
                logger($city_id);
            }
        } else {
            logger('province');
            logger($state_id);
            return null;
        }

    }

    private function fix_names()
    {
        // $name_city_new = $fix_city_collection->where('city_id', $citiy_DS->city_id)->first();
    }

    private function setUpperLayersFavorite()
    {
        IranCity::where('type', '!=', 'منطقه')->update(['favorite' => 1]);
    }

    private function findFavoritesByPopulation($minPopulation)
    {

        // all cities with population more than given min population should be selected
        IranCity::where('favorite', 2)->update(['favorite' => 0]);
        IranCity::whereType('منطقه')->where('population', '>', $minPopulation)->update(['favorite' => 2]);
    }

    private function removeSpace($string)
    {
        // return str_replace(' ', '', $string);
        $pos = strpos($string, ' ');
        if ($pos !== false) {
            return substr_replace($string, '', $pos, 1);
        }
        return $string;
    }
    private function removeLastSpace($string)
    {
        // return str_replace(' ', '', $string);
        $pos = strrpos($string, ' ');
        if ($pos !== false) {
            return substr_replace($string, '', $pos, 1);
        }
        return $string;
    }

    private function fix_city_collection()
    {
        return collect([
            [
                'name' => 'امامزاده عبدلله',
                'replace' => 'امامزاده عبدالله',
            ],
            [
                'name' => 'اباده طشت',
                'replace' => 'اباده طشک',
            ],


        ]);
    }

    private function turnSpaceToPercent($string)
    {
        return str_replace(' ', '%', $string);
    }

    private function searchWithPercent($without)
    {
        switch ($without->type) {
            case 'شهرستان':
                $province = IranCity::find($without->parent_id);
                $population = Population::whereProvince($province->name)->where(function ($q) use ($without) {
                    $q->where('section_1', 'like', $this->turnSpaceToPercent($without->name));
                    $q->orWhere('section_2', 'like', $this->turnSpaceToPercent($without->name));
                })->first();
                if ($population) {
                    $without->population = $population->population;
                    $without->save();
                    // logger('شهرستان', $population->toArray());
                    // dd($population->toArray());
                    return true;
                } else {
                    logger('شهرستان', $without->toArray());

                    return false;
                }
                break;
            case 'منطقه':
                $state = IranCity::find($without->parent_id);
                $province = IranCity::find($state->parent_id);

                $population = Population::whereProvince($province->name)->where(function ($q) use ($state) {
                    $q->where('section_1', 'like', $this->turnSpaceToPercent($state->name));
                    $q->orWhere('section_2', 'like', $this->turnSpaceToPercent($state->name));
                })->where(function ($q) use ($without) {
                    $q->where('urban', 'like', $this->turnSpaceToPercent($without->name));
                    $q->orWhere('rural', 'like', $this->turnSpaceToPercent($without->name));
                })->first();
                // dd($population->first());
                if ($population) {
                    $without->population = $population->population;
                    $without->save();
                    // logger('منطقه', $population->toArray());
                    return true;
                } else {
                    logger('منطقه', $without->toArray());
                    return false;
                }
                break;

        }
        return false;
    }

}
