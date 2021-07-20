<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Review;

class ReviewController extends Controller
{
	/**
	 * レビュー 一覧
	 */
    public function index()
    {
    	// レビュー 一覧を取得、ページネーション:１ページ
    	$reviews = Review::where('status', 1)
    		->orderBy('created_at', 'DESC')
    		->paginate(9);
    	
    	// dd($reviews);//デバッグ関数(dump die)
    	
        return view('index', compact('reviews'));
    }
    
    /**
     * レビュー詳細
     */
    public function show($id)
    {
    	$review = Review::where('id', $id)->where('status', 1)->first();
    	return view('show', compact('review'));
    }
    
    /**
	 * レビュー 作成
	 */
    public function create()
    {
    	return view('review');
    }

    /**
	 * レビュー 登録
	 */
    public function store(Request $request)
    {
    	$post = $request->all();
    	
    	// バリデーション
    	$validateData = $request->validate([
			'title' => 'required | max:255',
			'body'  => 'required',
			'image' => 'mimes:jpeg,png,jpg,gif,svg | max:2048',
    	]);
    	
    	
    	
    	// 画像ファイルがある場合
    	if ($request->hasFile('image')) {
    		
	    	$request->file('image')->store('/public/images');
	    	$data = [
	    		'user_id' => \Auth::id(),
	    		'title'   => $post['title'],
	    		'body'    => $post['body'],
	    		'image'   => $request->file('image')->hashName(),
	    	];
	    // 画像ファイルがない場合
    	} else {
    		$data = [
	    		'user_id' => \Auth::id(),
	    		'title'   => $post['title'],
	    		'body'    => $post['body'],
	    	];
    	}
    	
    	// レビューを保存
    	Review::insert($data);
    	// トップページへ遷移し、フラッシュメッセージを表示
    	return redirect('/')->with('flash_message', '投稿が完了しました');
    }
}
