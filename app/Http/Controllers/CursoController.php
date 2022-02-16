<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Models\Curso;
use App\Models\User;

class CursoController extends Controller
{

    public function index(Request $request)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if ($checkToken) {
            echo "Index Autenticado";
            die();
        } else {
            echo "No Autenticado";
            die();
        }
    }

    public function store(Request $request)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if ($checkToken) {
            //Recibimos datos por Post
            $json = $request->input('json', null);
            $params = json_decode($json); //a Objeto
            $params_array = json_decode($json, true); // a Arreglo

            $validate = \Validator::make($params_array, [
                'nombre' => 'required',
                'horas' => 'required | integer',
                'status' => 'required'
            ]);

            if ($validate->fails()) {
                $response = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => $validate->errors()
                );
            } else {
                //obtengo token decodificado
                $user = $jwtAuth->checkToken($hash, true);

                // verificamos que el curso no exista
                $valCurso = Curso::where('nombre', $params->nombre)->first();
                if (!isset($valCurso)) {

                    // si no existe, crea el curso

                    $curso = new Curso();
                    $curso->user_id = $user->sub;
                    $curso->nombre = $params->nombre;
                    $curso->horas = $params->horas;
                    $curso->status = $params->status;
                    $curso->save();
                    $response = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Curso guardado',
                        'data' => $curso
                    );
                } else {
                    //si existe, arroja error
                    $response = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El curso ya existe'
                    );
                }
            }
        } else {
            $response = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Autenticación fallida'
            );
        }
        return response()->json($response, 200);
    }

    public function show($uid)
    {
        $usuario = User::where('id', $uid)->get();
        if (count($usuario) > 0) {
            $curso = Curso::where('user_id', $uid)->get();
            if (count($curso) > 0) {
                $response = array(
                    'status' => 'correcto',
                    'code' => 200,
                    'message' => 'Los cursos encontrados son:',
                    'data' => $curso
                );
            } else {
                $response = array(
                    'status' => 'incorrecto',
                    'code' => 400,
                    'message' => 'Este usuario no posee cursos'
                );
            }
        } else {
            $response = array(
                'status' => 'incorrecto',
                'code' => 400,
                'message' => 'No se encuentra el usuario en la base de datos'
            );
        }

        return response()->json($response, 200);
    }

    public function update($idCurso, Request $request)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if ($checkToken) {
            //Recibimos datos por Post
            $json = $request->input('json', null);
            $params = json_decode($json); //a Objeto
            $params_array = json_decode($json, true); // a Arreglo

            $validate = \Validator::make($params_array, [
                'nombre' => 'required',
                'horas' => 'required | integer',
                'status' => 'required'
            ]);

            if ($validate->fails()) {
                $response = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => $validate->errors()
                );
            } else {
                //obtenemos el curso a editar
                $curso = Curso::where('id', $idCurso)->first();
                // verificamos que el curso exista
                if (isset($curso)) {
                    //si existe, edita curso
                    $curso->nombre = $params->nombre;
                    $curso->horas = $params->horas;
                    $curso->status = $params->status;
                    $curso->save();
                    $response = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Curso editado correctamente',
                        'data' => $curso
                    );
                }else{
                    //si no existe, arroja error
                    $response = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El curso no existe'
                    );
                }
            }
        } else {
            $response = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Autenticación fallida'
            );
        }
        return response()->json($response, 200);
    }

    public function destroy($idCurso, Request $request)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if ($checkToken) {
            //obtenemos el curso a eliminar
            $curso = Curso::where('id', $idCurso)->first();
            //Si encuentra curso, elimina
            if (isset($curso)) {
                $curso->delete();
                $response = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Curso eliminado correctamente'
                );
            } else {
                //si no existe, arroja error
                $response = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Curso no encontrado'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Autenticación fallida'
            );
        }
        return response()->json($response, 200);
    }
}
