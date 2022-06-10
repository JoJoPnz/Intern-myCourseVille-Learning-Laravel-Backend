<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(Store::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $name = $request->input('name');
        $store = new Store();
        $store->name = $name;
        $store->save();
        $store->owners()->attach($user->id);
        return response()->json($store, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $store = Store::findOrFail($id);
        return response()->json($store, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $store = Store::find($id);
        $isFound = false;
        foreach ($store->owners as $u) {
            if ($user->id === $u->pivot->user_id) {
                $isFound = true;
                break;
            }
        }
        if ($isFound) {
            $store->name = $request->input('name');
            $store->save();
            return response()->json("save successful", 200);
        } else {
            return response()->json("You're not owner of this store", 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $store = Store::find($id);
        // $store->delete();
        return response()->json(Store::destroy($id), 200);
        // return response()->json("delete success", 200);
    }
}
