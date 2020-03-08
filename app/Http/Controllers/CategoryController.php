<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('created_at','DESC')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'name'          => 'required|string|max:50',
          'description'   => 'nullable|string'
        ]);

        try {
          $categories = Category::FirstOrCreate([
            'name'        => $request->name  //-> ini untuk validasi kalo misal namanya udah ada berarti cuman nge select doang kalo ga ada dia langsung nge crea
          ], [
            'description' => $request->description
          ]);
          var_dump($categories);die();
          return redirect()->back()->with(['success' => 'Kategori: ' .$categories->name. ' Ditambahkan']);
        } catch (\Exception $e) {
          return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categoriess   = Category::findOrFail($id);
        return view('categories.edit', compact('categoriess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        //validasi form
        $this->validate($request,[
          'name'        => 'required|string|max:50',
          'description' => 'nullable|string'
        ]);

        try {
          //select data berdasarkan id
          $categories = Category::findOrFail($id);
          // var_dump($id);die();
          //update data
          $categories->update([
            'name'          => $request->name,
            'description '  => $request->description
          ]);
          //redirect ke route kategori.index
              return redirect(route('kategori.index'))->with(['success' => 'Kategori: ' . $categories->name . ' Ditambahkan']);
         } catch (\Exception $e) {
             //jika gagal, redirect ke form yang sama lalu membuat flash message error
              return redirect()->back()->with(['error' => $e->getMessage()]);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categories = Category::findOrFail($id);
        // var_dump($categories);die();
        $categories -> delete();
        return redirect()->back()->with(['success' => 'Kategori: '.$categories->name .' Telah Dihapus']);
    }
}
