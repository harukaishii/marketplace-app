<?php

namespace App\Http\Controllers;

use App\Enums\ItemCondition;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        $items = Item::all();
        return view('index', compact('items'));
    }

    public function show($item_id)
    {
        $item = Item::with(['categories', 'comments.user'])->findOrFail($item_id);
        return view('item', compact('item'));
    }

    public function storeComment(Request $request, $itemId)
    {
        Comment::create([
            'item_id' => $itemId,
            'user_id' => Auth::id(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('success', 'コメントを送信しました。');
    }


    public function create()
    {
        $categories = Category::all();
        $conditions = ItemCondition::cases();
        return view('sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validatedData = $request->validated();

        $imagePathForDb = null;
        // 画像の保存
        if ($request->hasFile('product_image')) {
            $storedPath = $request->file('product_image')->store('public/images/items');
            $imagePathForDb = str_replace('public/', '', $storedPath);
        }

        // Itemデータの保存
        $item = new Item();
        $item->name = $validatedData['name'];
        $item->detail = $validatedData['detail'];
        $item->brand_name = $validatedData['brand_name'];
        $item->price = $validatedData['price'];
        $item->condition = $validatedData['condition'];
        $item->status = '0';
        $item->listed_by = Auth::id();


        $item->image = $imagePathForDb;
        $item->save();

        // カテゴリの紐付け（中間テーブルへの保存）
        $item->categories()->attach($validatedData['category_ids']);

        return redirect()->route('index')->with('message', '商品を出品しました！');
    }
}
