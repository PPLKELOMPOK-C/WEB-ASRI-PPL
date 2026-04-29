<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $unitTersedia   = Unit::where('status', 'tersedia')->take(6)->get();
        $beritaTerbaru  = News::published()->orderBy('published_at', 'desc')->take(3)->get();

        $stats = [
            'total_unit'    => Unit::count(),
            'tersedia'      => Unit::where('status', 'tersedia')->count(),
            'total_penghuni'=> \App\Models\User::where('role', 'penghuni')->count(),
            'wilayah'       => Unit::distinct('wilayah')->count(),
        ];

        return view('public.home', compact('unitTersedia', 'beritaTerbaru', 'stats'));
    }

    public function units(Request $request)
    {
        $query = Unit::query();

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->wilayah);
        }
        if ($request->has('tersedia')) {
            $query->where('status', 'tersedia');
        }
        if ($request->filled('harga_min')) {
            $query->where('harga_sewa', '>=', $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('harga_sewa', '<=', $request->harga_max);
        }
        if ($request->filled('luas_min')) {
            $query->where('luas_m2', '>=', $request->luas_min);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('gedung', 'like', "%$search%")
                  ->orWhere('blok', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%");
            });
        }

        $units     = $query->paginate(12)->appends($request->query());
        $wilayahs  = Unit::distinct()->pluck('wilayah')->sort();
        $totalHasil = $query->count();

        return view('public.units', compact('units', 'wilayahs', 'totalHasil'));
    }

    public function unitDetail(Unit $unit)
    {
        // Unit serupa di wilayah yang sama
        $unitSerupa = Unit::where('wilayah', $unit->wilayah)
            ->where('id', '!=', $unit->id)
            ->where('status', 'tersedia')
            ->take(3)
            ->get();

        return view('public.unit_detail', compact('unit', 'unitSerupa'));
    }

    public function news(Request $request)
    {
        $query = News::published();

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $news = $query->orderBy('published_at', 'desc')->paginate(9)->withQueryString();

        return view('public.news', compact('news'));
    }

    public function newsDetail($slug)
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();
        $related = News::published()
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('public.news_detail', compact('article', 'related'));
    }
}
