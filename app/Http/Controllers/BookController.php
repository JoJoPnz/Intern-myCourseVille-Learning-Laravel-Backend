<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Book;
use App\Models\BookType;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $all_book = array();

        foreach ($user->stores as $store) {
            foreach ($store->books as $book) {
                array_push($all_book, $book);
            }
        }

        return response()->json(['User' => $user, 'allBooks' => $all_book], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'book_type' => ['required', 'string'],
            'store_id' => ['required', 'integer'],
            'price' => ['required', 'integer']
        ]);
        $user = $request->user();
        $book_type = BookType::where('name', $request->book_type)->firstOrFail();
        $store = Store::where('id', $request->store_id)->firstOrFail();

        $this->authorize('create', [Book::class, $store]);

        $book = new Book();
        $book->name = $request->name;
        $book->price = $request->price;
        $book->bookType()->associate($book_type);
        $book->store()->associate($store);
        $book->save();

        return response()->json(["Book Created" => $book], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $request->validate(['name' => ['required', 'string']]);
        $user = $request->user();

        $this->authorize('update', $book);

        $book->name = $request->input('name');
        $book->save();
        return response()->json("Update Book name to $book->name successful : Action by user $user->id", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();

        $this->authorize('delete', $book);

        $book->store()->dissociate();
        // $book->bookType()->dissociate();
        $book->save();
        return response()->json("Book $book->name is deleted", 200);
    }
}
