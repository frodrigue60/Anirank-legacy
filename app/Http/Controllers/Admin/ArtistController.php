<?php

namespace App\Http\Controllers\Admin;

use App\Models\Artist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumb = [
            [
                'name' => 'Artists',
                'url' => route('admin.artists.index'),
            ]
        ];

        $query = Artist::query();
        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%")
                ->orWhere('name_jp', 'like', "%{$request->q}%");
        }

        $artists = $query->paginate(15);

        return view('admin.artists.index', compact('artists', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            [
                'name' => 'Artists',
                'url' => route('admin.artists.index'),
            ],
            [
                'name' => 'Create',
                'url' => '',
            ]
        ];
        return view('admin.artists.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->only(['name', 'name_jp']))
                ->withErrors($validator);
        }

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        if ($this->artistExists($name)) {
            return redirect(route('admin.artists.index'))->with('warning', 'Artist ' . $name . ' already exists!');
        }

        $artist = new Artist();
        $artist->name = $name;
        #dd($request->all());
        if ($request->name_jp) {
            $artist->name_jp = trim(preg_replace('/\s+/', ' ', $request->name_jp));
        }

        $artist->slug = $this->generateUniqueSlug($request->name);

        if ($artist->save()) {
            return redirect(route('admin.artists.index'))->with('success', 'Data has been inserted successfully');
        }

        return redirect(route('admin.artists.index'))->with('error', 'Something went wrong');
    }

    function artistExists($name)
    {
        return Artist::where('name', $name)
            ->where('slug', Str::slug($name))
            ->exists();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $breadcrumb = [
            [
                'name' => 'Artists',
                'url' => route('admin.artists.index'),
            ],
            [
                'name' => 'Edit',
                'url' => '',
            ]
        ];
        $artist = Artist::find($id);
        return view('admin.artists.edit', compact('artist', 'breadcrumb'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $artist = Artist::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->only(['name', 'name_jp']))
                ->withErrors($validator);
        }

        $name = trim(preg_replace('/\s+/', ' ', $request->name));
        $artist->name = $name;

        $artist->slug = $this->generateUniqueSlug($request->name);

        if ($request->name_jp) {
            $name_jp = trim(preg_replace('/\s+/', ' ', $request->name_jp));
            $artist->name_jp = $name_jp;
        }

        #dd($artist);

        if ($artist->save()) {
            return redirect(route('admin.artists.index'))->with('success', 'Data has been updated successfully');
        }

        return redirect(route('admin.artists.index'))->with('error', 'Something went wrong');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $artist = Artist::find($id);
        $artist->delete();

        return redirect(route('admin.artists.index'))->with('success', 'Data deleted');
    }

    public function paginate($artists, $perPage = 10, $page = null, $options = [])
    {
        $page = Paginator::resolveCurrentPage();
        $options = ['path' => Paginator::resolveCurrentPath()];
        $items = $artists instanceof Collection ? $artists : Collection::make($artists);
        $artists = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $artists;
    }

    /**
     * Genera un slug único agregando un número incremental si es necesario
     * 
     * @param string $name Nombre original del artista
     * @return string Slug único
     */
    public function generateUniqueSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        // Verificar si el slug ya existe en la base de datos
        while (Artist::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
