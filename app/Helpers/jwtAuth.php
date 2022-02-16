<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\support\Facade\DB;
use App\Models\User;

class JwtAuth{
    protected $key;

    public function __construct(){
        $this->key = 'm1-cl4v3-35-pr!v4d4.';
    }

    public function singup($email, $password, $getToken=null){
        //Verficiar usuario y contraseña
        $user = User::where(
            array(
                'email' => $email,
                'password' => $password
            ))->first();

        if(is_object($user)){
            //Generamos el token
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (24 * 60 * 60)
            );

            //codificar el token
            $jwt = JWT::encode($token, $this->key, 'HS256');
            //decodificar el token
            $decode = JWT::decode($jwt, $this->key, array('HS256'));
            if(is_null($getToken)){
                return array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Login Correcto',
                    'token' => $jwt
                );
            }else{
                return array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Consulta Correcta',
                    'token' => $decode
                );
            }
        }else{
            //error
            return array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Login Incorrecto'
            );
        }
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth =true;
        try{
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        }catch(\UnexpectedValueException $e){
            $auth =false;
        }catch(\DomainException $e){
            $auth =false;
        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth =true;
        }else{
            $auth =false;
        }

        if($getIdentity){
            return $decoded;
        }
        return $auth;       

    }
}