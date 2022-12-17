<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cgy;
use App\Models\Item;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *回傳該品項的所有資料，以 sort 欄位從小到大排序

     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::orderBy('cgy_id', 'asc')->get();
        return $items;
    }

    /**
     * Store a newly created resource in storage.
     *
     *儲存前端傳入的資料，成功後回傳儲存的內容

     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //只能用all(),不能用get()
        $item = Item::create($request->all());
        return $item;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Item $item)
    {
        return $item;
    }

    // public function show($id)
    // {
    //     // $item = Item::where('id', $id)->first();//等於下面那行
    //     $item = Item::find($id);
    //     return $item;

    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Item $item)
    // {

    //     $item->update($request->all());
    //     return "ok";

    // }

    public function update(Request $request, $id)
    {

        $item = Item::where('id', $id)->update($request->all());
        return "ok";

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function destroy(Item $item)
    // {
    //     $num = $item->delete();
    //     return $num;
    // }

    public function destroy($id)
    {
        $item = Item::where('id', $id)->delete();
        return "ok";
    }

    //查詢所有資料，只取 id , subject 以及 content 這三個欄位
    public function querySelect()
    {
        $items = Item::select(['id', 'title', 'desc'])->get();
        return $items;
    }

    //查詢 enabled_at 於 2022/12/13 00:00:00 之後，enabled 為 true 的資料，按照 created_at 從新到舊排序，回傳第一筆資料的 subject 欄位內容
    public function querySpecific()
    {
        $item = Item::where('enabled_at', '>', Carbon::createFromFormat('Y/m/d m:i:s', '2022/12/13 00:00:00'))->where('enabled', true)->orderBy('created_at', 'desc')->first();
        return $item->title;

    }

    //查詢 enabled_at 於 2022/12/10 00:00:00 之後，enabled 為 true 的資料，按照 created_at 從新到舊排序，回傳第2~4筆資料
    public function queryPagination()
    {
        //$date = Carbon::createFromFormat('Y/m/d m:i:s','2022/12/10 00:00:00');
        //$articles = Article::where('enabled_at','>',$date)->where('enabled',true)->orderBy('created_at','desc')->skip(1)->take(3)->get();
        $items = Item::where('enabled_at', '>', '2022-12-10 00:00:00')->where('enabled', true)->orderBy('created_at', 'desc')->skip(1)->take(3)->get();
        return $items;
    }

    //查詢 enabled_at 介於 2022/12/10 00:00:00 和 2022/12/15 23:59:59 之間，sort 位於 $min 到 $max 之間的資料並回傳
    public function queryRange($min, $max)
    {
        $items = Item::where('enabled_at', '<', '2022/12/30 23:59:59')->where('enabled_at', '>', '2022/12/20 00:00:00')->whereBetween('cgy_id', [$min, $max])->get();
        return $items;
    }

    //根據所傳入的分類id，取出該分類所有 enabled 為 true 的資料，依照 sort 從小到大排序，回傳符合的資料
    public function queryByCgy($cgy_id)
    {
        $items = Item::where('cgy_id', $cgy_id)->where('enabled', true)->orderBy('price', 'desc')->get();
        return $items;
    }

    //試著使用 pluck() 來取得 id 為 key ， subject 為 value 的陣列
    public function queryPluck()
    {
        $data = Item::pluck('title', 'id');
        return $data;
    }

    //計算所有 enabled 為 true 的資料筆數後回傳，利用查詢方法 count()
    public function enabledCount()
    {
        $num = Item::where('enabled', true)->count();
        return $num;
    }

//==================================================================

    //取得指定分類的所有文章
    public function queryCgyRelation(Cgy $cgy)
    {
        // articles複數
        //透過Cgy model的關係函式items()->直接取得所有,沒有要設定,不用加()
        return $cgy->items;
    }

    //取得原分類ID為$old_cgy_id的第一個文章，將之改為新分類ID $new_cgy_id
    public function changeCgy($old_cgy_id, $new_cgy_id)
    {
        //article單數
        $article = Item::where('cgy_id', $old_cgy_id)->first();
        // $article->cgy_id = $new_cgy_id;
        // $article->save();

        $new_cgy = Cgy::find($new_cgy_id);
        //透過Cgy model裡關聯的items()方法,把原本$old_cgy_id的第一個文章存進新的cgy_id
        $new_cgy->items()->save($article);
        return Item::find($article->id);
    }

    //取得指定文章的所屬分類
    public function getItemCgy(Item $item)
    {
        //指定文章的所屬分類->單數
        //Item model裡的關係函式cgy
        return $item->cgy;
        // return "ok";
    }

    //取得原分類 id 為$old_cgy_id的所有文章，將之改為新分類ID $new_cgy_id
    public function changeAllCgy($old_cgy_id, $new_cgy_id)
    {
        $items = Item::where('cgy_id', $old_cgy_id)->get();

        $new_cgy = Cgy::find($new_cgy_id);
        //有很多文章->saveMany
        $new_cgy->items()->saveMany($items);
        return $new_cgy->items;
        // return 'changeAllCgy';
    }

    //取得指定文章的所有標籤，連同該標籤建立的時間(在Article Model裡關聯的tags function用with)
    public function queryTags(Item $item)
    {
        //tags複數
        return $item->tags;
        // return $item;//可以return出來
    }

    //為指定的文章加入 id 為 tag_id 的標籤
    public function addTag(Item $item, $tag_id)
    {
        $tag = Tag::find($tag_id);
        //save()用物件,detach()用主鍵id
        $item->tags()->save($tag);
        return $item->tags;
        // return 'addTag';
    }

    //為指定的文章移除 id 為 tag_id 的標籤
    public function removeTag(Item $item, $tag_id)
    {
        //關係函式tags()還要做處理所以要加()
        //save()用物件,detach()用主鍵id
        $item->tags()->detach($tag_id);
        return $item->tags;
        // return 'removeTag';
    }

    //將指定文章的標籤重新設定為 1 , 3 , 5
    public function syncTag(Item $item)
    {
        //叫出Item model裡的關係函式tags()
        $item->tags()->sync([1, 3, 5]);
        return $item->tags;
        // return 'syncTag';
    }

    //為指定的文章加入 id 為 tag_id 的標籤，並設定標籤顏色
    public function addTagWithColor(Item $item, $tag_id, $color)
    {
        $tag = Tag::find($tag_id);
        $item->tags()->save($tag, ['color' => '#' . $color]);
        return $item->tags;
        // return 'addTagWithColor';
    }

    //取得指定文章的所有標籤，連同該標籤建立的時間以及標籤顏色
    public function queryTagsWithColor(Item $item)
    {
        return $item->tags;
    }

    //取得文章連同其關聯的標籤(在關係函式都寫好with了)
    public function getItemWithTags(Item $item)
    {
        return $item->tags;
    }

}