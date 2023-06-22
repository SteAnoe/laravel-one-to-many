<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();
        return view('admin.project.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('admin.project.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|unique:projects',
                'description' => 'required',
                'img' => 'nullable|image'
            ],
            [
                'name.required' => 'Il campo name deve essere compilato',
                'name.unique' => 'Esiste già un project con quel nome',
                'description.required' => 'Il campo Description deve essere compilato',
            ]
        );

        $form_data = $request->all();

        if($request->hasFile('img')){
            $path = Storage::disk('public')->put('project_images', $request->img);
            $form_data['img'] = $path;
        }


        $slug = Project::generateSlug($request->name);

        $form_data['slug'] = $slug;

        

        $new_project = new Project();

        $new_project->fill($form_data);
        
        $new_project->save();

        return redirect()->route('admin.project.index')->with('success', "Project $new_project->name creato");;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('admin.project.show', compact( 'project' ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        return view('admin.project.edit', compact( 'project' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $request->validate(
            [
                'name' => 'required|unique:projects,name,' . $project->id,
                'description' => 'required',
                'img' => 'nullable|image'
            ],
            [
                'name.required' => 'Il campo name deve essere compilato',
                'name.unique' => 'Esiste già un project con quel nome',
                'description.required' => 'Il campo Description deve essere compilato',
            ]
        );
        $form_data = $request->all();

        $slug = Project::generateSlug($request->name);

        $form_data['slug'] = $slug;

        $project->update($form_data);

        return redirect()->route('admin.project.index')->with('success', "Project $project->name modificato");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.project.index');
    }
}
