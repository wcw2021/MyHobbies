<?php

namespace App\Http\Controllers;

use App\Hobby;
use App\Tag;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;

class HobbyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function test($name, $age)
    {
        dd("$name is $age years old.");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $hobbies = Hobby::all();
        // $hobbies = Hobby::paginate(10);
        $hobbies = Hobby::orderBy('created_at', 'desc')->paginate(10);
        // dd($hobbies);
        return view('hobby.index')->with(['hobbies' => $hobbies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hobby.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate the form data
        $request->validate([
            'name' => 'required|min:3',
            'description' => 'required|min:5',
            'image' => 'mimes:jpeg,jpg,bmp,png,gif'
        ]);

        // process the data and submit it
        $hobby = new Hobby([
            'name' => $request['name'],
            'description' => $request['description'],
            'user_id' => auth()->id()
        ]);

        $hobby->save();

        if($request->image){
            $this->saveImages($request->image, $hobby->id);
        }
        
        // return $this->index()->with(['message_success' => "The hobby <b>" .$hobby->name. "</b> was created."]);
        return redirect('/hobby/' . $hobby->id)->with(['message_warning' => "Please assign some tags now."]);

        // // if successful we want to redirect
        // if ($hobby->save()) {
        //     return redirect()->route('hobby.show', $hobby->id);
        // } else {
        //     return redirect()->route('hobby.create');
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function show(Hobby $hobby)
    {
        $allTags = Tag::all();
        $usedTags = $hobby->tags;
        $availableTags = $allTags->diff($usedTags);

        return view('hobby.show')->with([
            'hobby' => $hobby,
            'availableTags' => $availableTags, 
            'message_success' => Session::get('message_success'),
            'message_warning' => Session::get('message_warning')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function edit(Hobby $hobby)
    {
        abort_unless(Gate::allows('update', $hobby), 403);

        return view('hobby.edit')->with([
            'hobby' => $hobby,
            'message_success' => Session::get('message_success'),
            'message_warning' => Session::get('message_warning')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hobby $hobby)
    {
        abort_unless(Gate::allows('update', $hobby), 403);

        // validate the form data
        $request->validate([
            'name' => 'required|min:3',
            'description' => 'required|min:5',
            'image' => 'mimes:jpeg,jpg,bmp,png,gif'
        ]);

        if($request->image){
            $this->saveImages($request->image, $hobby->id);
        }

        // update data
        $hobby->update([
            'name' => $request['name'],
            'description' => $request['description']
        ]);

        return $this->index()->with(['message_success' => "The hobby <b>" .$hobby->name. "</b> was updated."]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hobby $hobby)
    {
        abort_unless(Gate::allows('delete', $hobby), 403);

        $oldName = $hobby->name;
        $hobby->delete();
        return $this->index()->with(['message_success' => "The hobby <b>" .$oldName. "</b> was deleted."]);

    }

    public function saveImages($imageInput, $hobby_id)
    {
        $image = Image::make($imageInput);
        if($image->width() > $image->height()){ // landscape
            $image->widen(1200)->save(public_path()."/img/hobbies/".$hobby_id."_large.jpg")
                    ->widen(400)->pixelate(12)->save(public_path()."/img/hobbies/".$hobby_id."_pixelated.jpg");

            $image = Image::make($imageInput);  
            $image->widen(60)->save(public_path()."/img/hobbies/".$hobby_id."_thumb.jpg");
        }else{ //portrait
            $image->heighten(900)->save(public_path()."/img/hobbies/".$hobby_id."_large.jpg")
                    ->heighten(400)->pixelate(12)->save(public_path()."/img/hobbies/".$hobby_id."_pixelated.jpg");

            $image = Image::make($imageInput);  
            $image->heighten(60)->save(public_path()."/img/hobbies/".$hobby_id."_thumb.jpg");
        }

    }

    public function deleteImages($hobby_id)
    {
        if(file_exists('img/hobbies/'.$hobby_id.'_large.jpg')){
            unlink(public_path()."/img/hobbies/".$hobby_id."_large.jpg");
        }

        if(file_exists('img/hobbies/'.$hobby_id.'_thumb.jpg')){
            unlink(public_path()."/img/hobbies/".$hobby_id."_thumb.jpg");
        }

        if(file_exists('img/hobbies/'.$hobby_id.'_pixelated.jpg')){
            unlink(public_path()."/img/hobbies/".$hobby_id."_pixelated.jpg");
        }

        return back()->with(['message_success' => "The Image was deleted."]);

    }

}



