<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Criar validate
            // Criar Handler de erros
            if ($this->emailAlreadyRegistered($request->get('email'))) {
                throw new BadRequestHttpException('E-mail already registered');
            }

            if ($cpf = $this->cpfAlreadyRegistered($request->get('cpf'))) {
                throw new BadRequestHttpException('CPF already registered');
            }


            $user = new User();
            $user->name     = $request->get('name');
            $user->cpf      = $request->get('cpf');
            $user->email    = $request->get('email');
            $user->is_admin = $request->get('is_admin');

            $user->save();
            return response($user, Response::HTTP_CREATED);
        } catch(BadRequestHttpException $e) {
            return response(['Error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response([
                'Error' => 'Internal Server Error.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['Error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Criar validate
            $user = User::find($id);
            if (!$user) {
                return response(['Error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            if ($this->emailAlreadyRegistered($request->get('email'), $user->id)) {
                throw new BadRequestHttpException('E-mail already registered');
            }

            if ($cpf = $this->cpfAlreadyRegistered($request->get('cpf'), $user->id)) {
                    throw new BadRequestHttpException('CPF already registered');
            }

            $user->name     = $request->get('name');
            $user->cpf      = $request->get('cpf');
            $user->email    = $request->get('email');
            $user->is_admin = $request->get('is_admin');

            $user->save();
            return response($user, Response::HTTP_OK);
        } catch(BadRequestHttpException $e) {
            return response([
                'Error' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            return response([
                'Error' => 'Falha ao atualizar o usuário',
                'Log' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!User::destroy($id)) {
                throw new Exception('Fail on delete user');
            }

            return response(status: Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response(
                ['Error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function emailAlreadyRegistered(string $email, string $id = null)
    {
        return $this->abstractVerify('email', $email, $id);
    }

    private function cpfAlreadyRegistered(string $cpf, string $id = null)
    {
        return $this->abstractVerify('cpf', $cpf, $id);
    }

    private function abstractVerify(string $field, string $value, ?string $id) {
        $clousure = [
            [$field, $value]
        ];

        if (!is_null($id)) {
            $clousure[] = ['id', '!=', $id];
        }

        return User::where($clousure)->count() > 0;
    }
}
