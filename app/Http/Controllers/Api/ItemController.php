<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cgy;
use App\Models\Item;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class ItemController extends Controller
{

    //用於生成 JSON 字串
    //這個方法不會讓外部路由呼叫所以用private
    //$status->請求api的結果是成功還失敗
    //$data->要傳回前端的資料
    //$msg->如果失敗要給什麼訊息
    private function makeJson($status, $data, $msg)
    {
        //用return response()->json([])回傳json格式的陣列
        //轉 JSON 時確保中文不會變成 Unicode
        return response()->json(['status' => $status, 'data' => $data, 'message' => $msg])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
    /**
     * Display a listing of the resource.
     *
     *回傳該品項的所有資料，以 sort 欄位從小到大排序

     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $items = Item::orderBy('cgy_id', 'asc')->get();
        // return $items;

        //RESTFUL API
        $items = Item::get(); //取得Item table的所有資料
        //先判斷是否為空值,再來算數量(如果沒有先判斷的話,是空的就會爆)
        if (isset($items) && count($items) > 0) {
            $data = ['items' => $items];
            return $this->makeJson(1, $data, null);
        } else {
            return $this->makeJson(0, null, '找不到任何商品');
        }

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
        // $item = Item::create($request->all());
        // return $item;

        //RESTFUL API
        //從前端得到資料
        // $input = ['title' => $request->title, 'desc' => $request->desc];
        // $input = $request->all();//不好的作法,即使前端回傳了我們不需要的資料還是得吃
        $input = $request->only(['title', 'pic', 'price', 'enabled', 'desc', 'cgy_id']);
        //用Item model的create()方法來建立一筆資料,把儲存的資料用$item變數丟回來
        $item = Item::create($input);
        //檢查$item這個實例是否存在,成功的話回傳id,失敗的話回傳訊息
        if (isset($item)) {
            $data = ['item_id' => $item->id];
            return $this->makeJson(1, $data, '新增商品成功');
        } else {
            return $this->makeJson(0, null, '新增商品失敗');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        // dd($item);
        // return $item; //public function show(Item $item)

        //RESTFUL API
        //用主鍵找到資料
        $item = Item::find($id);

        if (isset($item)) {
            $data = ['item' => $item];
            return $this->makeJson(1, $data, null);
        } else {
            return $this->makeJson(0, null, '找不到該商品');
        }

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

        // $item = Item::where('id', $id)->update($request->all());
        // return "ok";

        //RESTFUL API
        //把錯誤攔截下來:在try區域裡任何一行出現錯誤的話,會自動跳到catch出現錯誤訊息
        try {
            $item = Item::findOrFail($id);
            $input = $request->only(['title', 'pic', 'price', 'enabled', 'desc', 'cgy_id']);
            $item->update($input);
            $item->save();
        } catch (Throwable $e) {
            //更新失敗
            return $this->makeJson(0, null, '更新商品失敗');
        }

        $data = ['item' => $item];
        return $this->makeJson(1, $data, '更新商品成功');

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
        // $item = Item::where('id', $id)->delete();
        // return "ok";

        //RESTFUL API
        try {
            $item = Item::findOrFail($id);
            $item->delete();
        } catch (Throwable $e) {
            //刪除失敗
            return $this->makeJson(0, null, '刪除文章失敗');
        }
        return $this->makeJson(1, null, '刪除文章成功');

    }

    //連商品分類一起抓
    public function getwithCgy(Item $item)
    {

        $item = Item::with('cgy')->get();

        if (isset($item)) {
            $data = ['item' => $item];
            return $this->makeJson(1, $data, null);
        } else {
            return $this->makeJson(0, null, '找不到該商品');
        }

    }

//==================================================

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
        // return Item::with('tags')->find($item->id)
        return $item->tags;
    }

}