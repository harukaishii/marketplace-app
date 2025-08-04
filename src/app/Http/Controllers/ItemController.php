<?php

namespace App\Http\Controllers;

use App\Enums\ItemCondition;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\Favorite;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    //一覧表示
    public function index(Request $request)
    {
        $page = $request->input('page');
        $keyword = $request->input('keyword');

        // デフォルトはすべての商品（出品者自身の商品を除く）
        $query = Item::query();

        // ログインしている場合は、自分の出品した商品を除外
        if (Auth::check()) {
            $query->where('listed_by', '!=', Auth::id());
        }

        // マイリスト表示の場合
        if ($page === 'mylist') {
            if (!Auth::check()) {
                $items = collect(); // 未ログインの場合は空のコレクション
            } else {
                $likedItemIds = Auth::user()->favorites()->pluck('item_id');
                // マイリストの場合も、自分の出品した商品を除外する
                $query->whereIn('id', $likedItemIds)
                    ->where('listed_by', '!=', Auth::id());
            }
        }

        // 検索条件があれば追加
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 最終的なアイテムを取得
        $items = isset($query) ? $query->latest()->get() : collect();

        return view('index', compact('items', 'keyword'));
    }

    //検索
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $items = Item::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            // 検索結果でも自身の出品した商品を除外する
            ->when(Auth::check(), function ($query) {
                $query->where('listed_by', '!=', Auth::id());
            })
            ->get();

        return view('index', compact('items', 'keyword'));
    }

    //詳細表示
    public function show($item_id)
    {
        $item = Item::with(['categories', 'comments.user'])->findOrFail($item_id);
        return view('item', compact('item'));
    }

    //コメント
    public function storeComment(CommentRequest $request, $itemId)
    {
        Comment::create([
            'item_id' => $itemId,
            'user_id' => Auth::id(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('success', 'コメントを送信しました。');
    }

    //出品画面
    public function create()
    {
        $categories = Category::all();
        $conditions = ItemCondition::cases();
        return view('sell', compact('categories', 'conditions'));
    }

    //出品
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
