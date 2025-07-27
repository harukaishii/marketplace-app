<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;

class ItemCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $item1 = Item::find(1);
        $item2 = Item::find(2);
        $item3 = Item::find(3);
        $item4 = Item::find(4);
        $item5 = Item::find(5);
        $item6 = Item::find(6);
        $item7 = Item::find(7);
        $item8 = Item::find(8);
        $item9 = Item::find(9);
        $item10 = Item::find(10);


        $category1 = Category::find(1);
        $category2 = Category::find(2);
        $category3 = Category::find(3);
        $category4 = Category::find(4);
        $category5 = Category::find(5);
        $category6 = Category::find(6);
        $category7 = Category::find(7);
        $category8 = Category::find(8);
        $category9 = Category::find(9);
        $category10 = Category::find(10);
        $category11 = Category::find(11);
        $category12 = Category::find(12);
        $category13 = Category::find(13);
        $category14 = Category::find(14);




        // 中間テーブルへのデータの紐付け
        if ($item1 && $category1) {
            $item1->categories()->attach($category1);
        }
        if ($item1 && $category5) {
            $item1->categories()->attach($category5);
        }
        if ($item1 && $category12) {
            $item1->categories()->attach($category12);
        }
        if ($item2 && $category2) {
            $item2->categories()->attach($category2);
        }
        if ($item3 && $category10) {
            $item2->categories()->attach($category10);
        }
        if ($item4 && $category1) {
            $item4->categories()->attach($category1);
        }
        if ($item4 && $category5) {
            $item4->categories()->attach($category5);
        }
        if ($item5 && $category2) {
            $item5->categories()->attach($category2);
        }
        if ($item6 && $category2) {
            $item6->categories()->attach($category2);
        }
        if ($item7 && $category1) {
            $item7->categories()->attach($category1);
        }
        if ($item7 && $category4) {
            $item7->categories()->attach($category4);
        }
        if ($item8 && $category4) {
            $item8->categories()->attach($category4);
        }
        if ($item9 && $category10) {
            $item9->categories()->attach($category10);
        }
        if ($item10 && $category4) {
            $item10->categories()->attach($category4);
        }
        if ($item10 && $category6) {
            $item10->categories()->attach($category6);
        }

    }
}
