<?php

namespace App\Http\Controllers;

use App\Contracts\BookInterface;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Mail\UserInformationMail;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private BookInterface $bookInterface;
    public function __construct(BookInterface $bookInterface)
    {
        $this->bookInterface = $bookInterface;
    }

    public function index()
    {
        $book = $this->bookInterface->all();
        return BookResource::collection($book);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        $validatedData = $request->validated();
        $book = $this->bookInterface->store('Book', $validatedData);
        if (!$book) {
            return response()->json([
                'message' => 'Something wrong and please try again!'
            ], 401);
        }
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $book = $this->bookInterface->findByID('Book', $id);
        if (!$book) {
            return response()->json([
                'message' => 'The book id not found!'
            ], 401);
        }

        $book = $this->bookInterface->update('Book', $validatedData, $id);
        return new bookResource($book);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
      return  $this->bookInterface->delete('Address', $book->id)? response(status:204): response(status:500);
    }

    public function borrow()
    {
        $books = Book::with('author','status','genre');
        $books = $books->borrowBook()->orderBy('id','desc')->paginate(12);
        return BookResource::collection($books);
    }

    public function return()
    {
        $books = Book::with('author','status','genre');
        $books = $books->returnBook()->orderBy('id','desc')->paginate(12);
        return BookResource::collection($books);
    }

    public function filter()
    {
        $filters = [
            'author' => request('author'),
            'genre' => request('genre'),
            'title' => request('title'),
        ];
        $query = Book::SearchingBook($filters);
        $filteredBooks = $query->get();
        if ($filteredBooks->isEmpty()) {
            return response()->json([
                'message' => 'No book match the given criteria'
            ], 200);
        }
        return response()->json([
            "data" => BookResource::collection($filteredBooks)
        ], 200);
    }

    public function borrowBook(Request $request, $id)
    {
        $now = Carbon::now('Asia/Yangon');
        $book = $this->bookInterface->findByID('Book', $id);
        if (!$book) {
            return response()->json([
                'message' => 'The book id not found!'
            ], 401);
        }
        $user = Auth::guard('api')->user();
        if($book->status_id != Config::get('variables.BORROW')){
            $data['user_id'] = $user->id;
            $data['status_id'] = Config::get('variables.BORROW');
            $data['borrow_return_date'] = $now;
            $book = $this->bookInterface->update('Book', $data, $id);
        }else{
            return response()->json([
                'message' => 'The book is not available!'
            ], 401);
        }
        return response()->json(['message' => 'Book borrowed successfully'], 200);
    }

    public function returnBook(Request $request, $id)
    {
        $now = Carbon::now('Asia/Yangon');
        $today = Carbon::today('Asia/Yangon');
        $book = $this->bookInterface->findBorrowBook('Book', $id);
        if (!$book) {
            return response()->json([
                'message' => 'The book id not found!'
            ], 401);
        }
        $differenceInDays = $today->diffInDays($book->borrow_return_date);
        $user = Auth::guard('api')->user();
        if($book->user_id != $user->id)
        {
            return response()->json(['error' => 'You cannot return a book that you have not borrowed'], 403);
        }
        elseif($differenceInDays >= 3)
        {
            Mail::to($book->user->email)->send(new UserInformationMail($book));
        }
        $data['status_id'] = Config::get('variables.RETURN');
        $data['borrow_return_date'] = $now;
        $book = $this->bookInterface->update('Book', $data, $id);
        return response()->json(['message' => 'Book return successfully'], 200);
    }
}
