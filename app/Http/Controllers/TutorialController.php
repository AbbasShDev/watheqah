<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Tutorial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Mews\Purifier\Facades\Purifier;

class TutorialController extends Controller {

    public function index(): View
    {

        $tutorials = Tutorial::where('user_id', auth()->id())->withCount('steps')->get();

        return view('tutorial.index', compact('tutorials'));
    }


    public function create(Request $request): View
    {
        $attributes = $request->validate([
            'title' => ['required']
        ]);

        return view('tutorial.create', ['title' => $attributes['title']]);
    }


    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'title'                => ['required'],
            'main_image'           => ['required'],
            'description'          => ['required'],
            'difficulty'           => ['required'],
            'duration'             => ['required'],
            'duration_measurement' => ['required'],
            'tags'                 => ['array'],
            'tags.*'               => ['required', 'string'],
            'area'                 => ['required'],
            'introduction'         => ['nullable'],
            'introduction_video'   => ['nullable'],
            'steps'                => ['array'],
            'steps.*.step_images'  => ['nullable'],
            'steps.*.step_order'   => ['required', 'numeric'],
            'steps.*.step_title'   => ['required', 'string'],
            'steps.*.step_content' => ['nullable', 'string'],
            'tutorial_status'      => ['required'],
        ]);

        $Tutorial = Tutorial::create([
            'user_id'              => auth()->id(),
            'title'                => $request->title,
            'main_image'           => $request->main_image,
            'description'          => $request->description,
            'difficulty'           => $request->difficulty,
            'duration'             => $request->duration,
            'duration_measurement' => $request->duration_measurement,
            'area'                 => $request->area,
            'introduction'         => Purifier::clean($request->introduction),
            'introduction_video'   => $request->introduction_video ? getYoutubeId($request->introduction_video) : "",
            'tutorial_status'      => $request->tutorial_status,
        ]);


        foreach ($request->tags as $tag) {
            $Tutorial->tags()->attach(Tag::firstOrCreate(['name' => $tag]));
        }

        foreach ($request->steps as $step) {
            $Tutorial->steps()->create([
                'order'       => $step['step_order'],
                'title'       => $step['step_title'],
                'content'     => Purifier::clean($step['step_content']),
                'images'      => $step['step_images'] ?? null,
                'tutorial_id' => $Tutorial->id
            ]);
        }

        return redirect()->route('home')->with('success', 'تم انشاء الإرشادات بتجاح.');
    }


    public function show(Tutorial $tutorial): View
    {
        $tutorial->load('tags', 'steps', 'user');

        return view('tutorial.show', compact('tutorial'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Tutorial $tutorial): RedirectResponse
    {
        $tutorial->tags()->detach();
        $tutorial->delete();

        return redirect()->back()->with('success', 'تم حذف الإرشادات بتجاح.');
    }
}
