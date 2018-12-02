<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Articles extends Controller
{
   	public function articles(Request $response)
   	{
   		try {
   			$sort = $response->Input('sort')?:'created_at';
	   		$order = $response->Input('order')?:'desc';
	   		$limit = $response->Input('limit')?:10;
	   		$paginate = $response->Input('paginate')?:null;
	   		$page = $response->Input('page')?:1;
	   		$offset = 0;
	   		if ($paginate!==NULL) {
	   			$offset = ($page-1)*$limit;
	   		}
	   		$articles = \DB::table('articles')
				->join('article_comment', 'articles.id', '=', 'article_comment.article_id')
				->select('articles.id','articles.title','articles.created_at',\DB::raw("count(article_comment.article_id) as comment_count"))
				->groupBy('articles.id')->orderBy($sort,$order)->offset($offset)->limit($limit)
				->get();
				foreach ($articles as $key => $article) {
					$article->tags = $this->getTags($article->id);
				}
	   		return response($articles, 200)
	                  ->header('Content-Type', 'application/json');
   		} catch (Exception $e) {
   			return response('', 400)
	                  ->header('Content-Type', 'application/json');
   		}
   		
   	}

   	public function comments($id, Request $response)
   	{
   		if (!is_numeric($id)) {
   			return response('',404)
	                  ->header('Content-Type', 'application/json');
   		}
   		try {
   		$sort = $response->Input('sort')?:'created_at';
   		$order = $response->Input('order')?:'desc';
   		$comments = \DB::table('comments')
   					->join('article_comment','article_comment.comment_id','=','comments.id')
   					->where('article_comment.article_id',$id)->select('comments.id','comments.content','comments.created_at')->orderBy($sort,$order)->get();
   		return response($comments, 200)
	                  ->header('Content-Type', 'application/json');		
   		} catch (Exception $e) {
   			return response('', 400)
	                  ->header('Content-Type', 'application/json');	
   		}
   	}

   	public function tags(Request $response)
   	{
   		try {
   			$sort = $response->Input('sort')?:'article_count';
	   		$order = $response->Input('order')?:'desc';
	   		$tags = \DB::table('tags')
	   					->join('article_tag','article_tag.tag_id','=','tags.id','left')->select('tags.id','tags.title',\DB::raw("count(article_tag.article_id) as article_count"))
	   					->groupBy('tags.id')
	   					->orderBy($sort,$order)->get();
			return response($tags, 200)
                  ->header('Content-Type', 'application/json');	
   		} catch (Exception $e) {
   			return response('', 400)
	                  ->header('Content-Type', 'application/json');	
   		}
   		
   		
   	}

   	public function articlesByTag($id)
   	{
   		if (!is_numeric($id)) {
   			return response('',404)
	                  ->header('Content-Type', 'application/json');
   		}
   		try {
   			$articles = \DB::table('article_tag')
   						->join('articles', 'articles.id','=','article_tag.article_id')
   						->join('article_comment', 'articles.id', '=', 'article_comment.article_id')
   						->select('articles.id', 'articles.title','articles.created_at',\DB::raw("count(article_comment.article_id) as comment_count"))->where('article_tag.tag_id',$id)->groupBy('articles.id')->get();
   			foreach ($articles as $key => $article) {
   				$article->tags = $this->getTags($article->id);
   			}
   			return response($articles, 200)
                  ->header('Content-Type', 'application/json');	
   		} catch (Exception $e) {
   			return response('', 400)
	                  ->header('Content-Type', 'application/json');
   		}
   	}

   	private function getTags($id)
   	{
   		$itm = \DB::table('article_tag')->where('article_id',$id)->get();
   		$arr = [];
   		foreach ($itm as $key => $v) {
   			array_push($arr,\DB::table('tags')->where('id',$v->tag_id)->first());
   		}
   		return $arr;
   	}
}
