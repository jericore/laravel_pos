<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use File;
use Image;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //Query ini masih sama seperti sebelumnya, yang berbeda karena terdapat method with().
      //Fungsi dari method ini adalah untuk memanggil nama dari relasi yang telah didefinisikan. Darimana kata category? Kata ini diambil dari sebuah method yang telah didefinisikan didalam model Product. Lalu kenapa mesti di model Product?
      //Karena dasarnya, kita menggunakan model Product untuk melakukan query ke database, maka secara otomatis hasil dari query tersebut akan menampilkan data category yang terkait.
      $products = Product::with('category')->orderBy('created_at', 'DESC')->paginate(10);
//       echo '<pre>';
// echo json_encode($products);
// echo '</pre>';
//atau memakai ini
// dd($product);
// var_dump($products);die();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      // die('1');
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('products.create', compact('categories'));
    }

    public function test()
    {
        die('1');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //validasi data
        $this->validate($request, [
          'code'            => 'required|string|max:10|unique:products',
          'nama_produk'      => 'required|string|max:100',
          'description'     => 'nullable|string|max:100',
          'stock'           => 'required|integer',
          'price'           => 'required|integer',
          'category_id'     => 'required|exists:categories,id',
          'photo'           => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        //buat ngetes apakah datanya masuk atau tidak
        // $data_vardump = array(
        //   'code'            => $request->code,
        //   'name'            => $request->nama_produk,
        //   'description'     => $request->description,
        //   'stock'           => $request->stock,
        //   'price'           => $request->price,
        //   'category_id'     => $request->category_id,
        //   // 'photo'           => $photo,
    		// );
          // print_r($data_vardump);die('test');

        try{
          //default $photo = null
          $photo = null;
          //jika terdapat file (foto/gambar) yang dikirim
          if ($request->hasFile('photo')) {
            //maka jalankan method saveFile()
            $photo = $this->saveFile($request->nama_produk, $request->file('photo'));
          }

          //Simpan data ke dalam table products
          $product = Product::create([
            'code'            => $request->code,
            'name'            => $request->nama_produk,
            'description'     => $request->description,
            'stock'           => $request->stock,
            'price'           => $request->price,
            'category_id'     => $request->category_id,
            'photo'           => $photo
          ]);


          //jika berhasil direct ke produk.index
          return redirect(route('produk.index'))
          ->with(['success' => '<strong>' .$product->name. '</strong> Ditambahkan']);
        } catch(\Exception $e){
          //jika gagal, kembali ke halaman sebelumnya kemudian tampilkan error
          return redirect()->back()
          ->with(['error' => $e->getMessage()]);
        }
    }

    private function saveFile($name, $photo)
    {
        //set nama file adalah gabungan antara nama produk dan time(). Ekstensi gambar tetap dipertahankan
        $images = str_slug($name) . time() . '.' . $photo->getClientOriginalExtension();
        //set path untuk menyimpan gambar
        $path = public_path('uploads/product');

        //cek jika uploads/product bukan direktori / folder
        if (!File::isDirectory($path)) {
          //maka folder tersebut dibuat
            File::makeDirectory($path, 0777, true, true);
        }
        //simpan gambar yang diuplaod ke folrder uploads/produk
        Image::make($photo)->save($path . '/' . $images);
        //mengembalikan nama file yang ditampung divariable $images
        return $images;
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
          //query select berdasarkan id
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      // die('2');
        //validasi
        $this->validate($request, [
          'code'            => 'required|string|max:10|exists:products,code',
          'nama_produk'     => 'required|string|max:100',
          'description'     => 'nullable|string|max:100',
          'stock'           => 'required|integer',
          'price'           => 'required|integer',
          'category_id'     => 'required|exists:categories,id',
          'photo'           => 'nullable|image|mimes:jpg,png,jpeg'
        ]);
        // die('3');

                // buat ngetes apakah datanya masuk atau tidak
                // $data_vardump = array(
                //
                //   'name'            => $request->nama_produk,
                //   'description'     => $request->description,
                //   'stock'           => $request->stock,
                //   'price'           => $request->price,
                //   'category_id'     => $request->category_id,
                //   // 'photo'           => $photo,
            		// );
                //   print_r($data_vardump);die('test');

        try{
          //query select berdasarkan id
          $product = Product::findOrFail($id);
          $photo = $product->photo;

          //cek jika ada file yang dikirim dari form
          if ($request->hasFile('photo')) {
            //cek, jika photo tidak kosong maka file yang ada di folder uploads/product akan dihapus
            !empty($photo) ? File::delete(public_path('uploads/product/' . $photo)):null;

            //uploading file dengan menggunakan method saveFile() yang telah dibuat sebelumnya
            $photo = $this->saveFile($request->nama_produk,$request->file('photo'));
          }

          //perbaharui data di database
            $product->update([
              'name'            => $request->nama_produk,
              'description'     => $request->description,
              'stock'           => $request->stock,
              'price'           => $request->price,
              'category_id'     => $request->category_id,
              'photo'           => $photo
            ]);

            return redirect(route('produk.index'))
                    -> with(['success' => '<strong>' . $product->name . '</strong> Diperbaharui']);
    } catch(\Exception $e){
      return redirect()->back()
             ->with(['error' => $e->getMessage()]);
    }
  }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //query select berdasarkan id
        $products = Product::findOrFail($id);
        //mengecek, jika field photo tidak null / kosong
        if (!empty($products->photo)) {
            //file akan dihapus dari folder uploads/produk
            File::delete(public_path('uploads/product/' . $products->photo));
        }
        //hapus data dari table
        $products->delete();
        return redirect()->back()->with(['success' => '<strong>' . $products->name . '</strong> Telah Dihapus!']);
          }
}
