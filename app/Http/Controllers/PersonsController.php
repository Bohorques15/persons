<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PersonCreateRequest;
use App\Http\Requests\PersonUpdateRequest;
use DB;

class PersonsController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        try {
            $user = Auth::user();

            $currentPage = $request->input('page');
            $search = $request->input('search');
            $currentPage = $currentPage ? $currentPage : 1;
            $itesmPerPage = 10;

            $persons = Person::orderByRaw("concat(first_name, ' ', last_name)");

            if (isset($request->type_person)) {
                $persons = $persons->where('type_person',$request->type_person);
            }

            if ($search && strlen(trim($search))) {
                $persons = Person::where(function ($q) use ($search) {
                    $q->where(DB::raw("concat(first_name, ' ', last_name)"), 'like', "%$search%");
                });
            }

            $persons = $persons->paginate($itesmPerPage);

            $currentPage = $persons->url($persons->currentPage());

            $lastPage = $persons->url($persons->lastPage());

            $paginate = [
                'next_page'=>$persons->nextPageUrl(),
                'last_page'=>$lastPage,
                'current_page'=>$currentPage,
                'previous_page'=>$persons->previousPageUrl(),
                'page_number'=>$persons->currentPage()
            ];


            return response()->json([
            	'status' => 200,
            	'message' => 'Personas obtenidas con éxito.',
            	'data' => [
            		'persons' => $persons->items(),
            		'paginate' => $paginate
            	]
            ]);
        } catch (Exception $e) {
            return response()->json([
            	'status' => 500,
            	'message' => 'No se ha podido obtener el listado de personas.',
            	'data' => [
            		'error' => $e->getMessage(),
                	'trace' => $e->getTrace()
            	]
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PersonCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $person = new Person();
            $person->first_name = $request->first_name;
            $person->last_name = $request->last_name;
            $person->document = $request->document;
            $person->ima_profile = $request->ima_profile;

            $person->type_person = 1;

            $person->save();

            DB::commit();
            return response()->json([
                'status' => 200,
                'successMessage' => "La persona ha creado exitosamente.",
                'data' => [
                    'person' => $person,
                ]
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => 'Ha ocurrido un error al crear la persona.',
                'data' => [
                    'errors' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        try {
            return response()->json([
                'status' => 200,
                'message' => 'Persona obtenida con éxito.',
                'data' => [
                    'person' => $person
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ha ocurrido un error al obtener la persona.',
                'data' => [
            		'error' => $e->getMessage(),
                	'trace' => $e->getTrace()
                ]
            ],500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonUpdateRequest $request, Person $person)
    {
        try{
            
            DB::beginTransaction();
            $person->first_name = $request->first_name;
            $person->last_name = $request->last_name;
            $person->document = $request->document;
            $person->ima_profile = $request->ima_profile;
            $person->save();
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => "Persona actualizada con éxito",
                'data' => [
                    'person' => $person
                ]
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => 'Ha ocurrido un error al tratar de actualizar la persona',
                'data' => [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
            ], 500);
        }
    }

    public function destroy(Person $person){
        try {
            DB::beginTransaction();
            $person->delete();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => "Persona eliminada con éxito.",
                'data' => []
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => 'Ha ocurrido un error al tratar de eliminar a la persona.',
                'data' => [
            		'error' => $e->getMessage(),
                	'trace' => $e->getTrace()
                ]
            ],500);
        }
    }
}
