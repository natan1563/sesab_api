<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDataRequest;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(User::with('addresses')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserDataRequest $request)
    {
        try {
            $request->validated();

            if ($this->emailAlreadyRegistered($request->get('email'))) {
                throw new BadRequestHttpException('E-mail already registered');
            }

            $cpf = $this->removeExtraCharacters($request->get('cpf'));
            if ($this->cpfAlreadyRegistered($cpf)) {
                throw new BadRequestHttpException('CPF already registered');
            }

            $user = new User();
            $user->name     = $request->get('name');
            $user->cpf      = $cpf;
            $user->email    = $request->get('email');
            $user->is_admin = $request->get('is_admin');

            $user->save();
            return response($user, Response::HTTP_CREATED);
        } catch(BadRequestHttpException $e) {
            return response(['Error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch(ValidationException $e) {
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
        $user = User::with('addresses')->find($id);

        if (!$user) {
            return response(['Error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserDataRequest $request, string $id)
    {
        try {
            $request->validated();

            $user = User::find($id);
            if (!$user) {
                return response(['Error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            if ($this->emailAlreadyRegistered($request->get('email'), $user->id)) {
                throw new BadRequestHttpException('E-mail already registered');
            }

            $cpf = $this->removeExtraCharacters($request->get('cpf'));
            if ($this->cpfAlreadyRegistered($cpf, $user->id)) {
                    throw new BadRequestHttpException('CPF already registered');
            }

            $user->name     = $request->get('name');
            $user->cpf      = $cpf;
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

    public function searchUsers(Request $request)
    {
        $request->validate([
            'name'           => 'required|max:255',
            'cpf'            => 'required|max:14|min:11',
            'initial_date'   => 'required',
            'finished_date'  => 'required',
        ]);

        $cpf = $this->removeExtraCharacters($request->get('cpf'));

        $redisKey = strtolower(
            "filtered-user:{$cpf}:{$request->get('name')}:{$request->get('initial_date')}:{$request->get('finished_date')}"
        );

        if ($request->header('Cache-Control') === 'no-cache') {
            Redis::del($redisKey);
        }

        if ($cachedUsers = Redis::get($redisKey)) {
            return json_decode($cachedUsers);
        }

        $timezone = new DateTimeZone('America/Sao_Paulo');

        $usersOnRange = DB::table('users')
                                        ->where('cpf', $cpf, boolean: 'or')
                                        ->orWhere('name', 'LIKE', "%{$request->get('name')}%")
                                        ->whereBetween(
                                            'created_at',
                                            [
                                                new DateTime($request->get('initial_date'), $timezone),
                                                new DateTime($request->get('finished_date'), $timezone)
                                            ]
                                        )
                                        ->get('id');

        foreach ($usersOnRange as $key => $user) {
           $usersOnRange[$key] = User::with('addresses')->find($user->id);
        }

        Redis::set($redisKey, $usersOnRange, 'EX', 60 * 5);

        return $usersOnRange;
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

    private function removeExtraCharacters($cpf) {
        return preg_replace( '/[^0-9]/is', '', $cpf);
    }
}
