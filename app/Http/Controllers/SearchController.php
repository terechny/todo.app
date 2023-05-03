<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;

class SearchController extends Controller
{

    private SearchService $service;

    public function __construct(SearchService $searchService) {
         
        $this->service = $searchService;
    }

    public function search(Request $request){

        return $this->service->search($request);
    }

    public function searchTag(Request $request){

        return $this->service->searchTag($request);
    }

}
