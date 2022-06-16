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
        return response()->json(['User' => $request->user()->id, 'AllStores' => $request->user()->stores], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string']]);

        $user = $request->user();
        $store = Store::create([
            'name' => $request->name,
        ]);
        $store->owners()->attach($user->id);
        return response()->json($store, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        $this->authorize('view', $store);
        return response()->json(['books' => $store->books], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $request->validate(['name' => ['required', 'string']]);
        $user = $request->user();

        $this->authorize('update', $store);

        $store->name = $request->input('name');
        $store->save();
        return response()->json("Update Store name to $store->name successful : Action by user $user->id", 200);

        // 1. not using policy
        // $user = $request->user();
        // $store = Store::find($id);
        // $isFound = false;
        // foreach ($store->owners as $u) {
        //     if ($user->id === $u->pivot->user_id) {
        //         $isFound = true;
        //         break;
        //     }
        // }
        // if ($isFound) {
        //     $store->name = $request->input('name');
        //     $store->save();
        //     return response()->json("save successful", 200);
        // } else {
        //     return response()->json("You're not owner of this store", 403);
        // }

    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Store $store)
    {
        $user = $request->user();

        $this->authorize('delete', $store);

        // delete relation between every books and that store
        foreach ($store->books as $book) {
            $book->store()->dissociate();
            $book->save();
        };

        // delete every user from the store
        $store->owners()->detach();
        $store->delete();
        return response()->json("Store $store->name is deleted", 200);

        // 2.
        // $store = Store::find($id);
        // $store->delete();
        // return response()->json("delete success", 200);

        // 3.
        // return response()->json(Store::destroy($id), 200);
    }
}
