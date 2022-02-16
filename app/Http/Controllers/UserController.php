<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\JwtAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //registro de usuario
    public function register(Request $request){
        //Recoger datos por post con json
        $json = $request->input('json',null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ADMIN';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if(!is_null($email) && !is_null($name) && !is_null($password)){
            //Crear usuario
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //validar que el usuario no exista
            $isset_user = User::where('email','=',$email)->get();
            if(count($isset_user)==0){
                $user->save();
                $response = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario creado correctamente'
                );
            }else{
                $response = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El usuario ya existe'
                );
            }

        }else{
            $response = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no ha sido creado'
            );
        }
        return response()->json($response,200);
    }

    //login de usuario

    public function login(Request $request){
        //Instanciar la Clase de JWT
        $jwtAuth = new JwtAuth();
        //Recibir los datos por Post
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if(!is_null($email) && !is_null($password)){
            $pwd = hash('sha256', $password);
            $response = $jwtAuth->singup($email,$pwd);
        }else{
            $response = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Error al intentar logear'
            );
        }
        return response()->json($response, 200); 
    }
}
