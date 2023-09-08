<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(Address::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!User::find($request->get('user_id'))) {
                throw new BadRequestException('User does not exist.');
            }

            $address = new Address([
                'cep'          => $request->get('cep'),
                'public_place' => $request->get('public_place'),
                'neighborhood' => $request->get('neighborhood'),
                'locality'     => $request->get('locality'),
                'uf'           => $request->get('uf'),
                'user_id'      => $request->get('user_id'),
            ]);

            $address->save();

            return response($address, Response::HTTP_CREATED);
        } catch (BadRequestException $e) {
            return response(['Error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response(['Error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $address = Address::find($id);

            if (!$address) {
                throw new BadRequestException('Address not found.');
            }

            if (!User::find($address->user_id)) {
                throw new BadRequestException('User does not exist.');
            }

            $address->cep          = $request->get('cep');
            $address->public_place = $request->get('public_place');
            $address->neighborhood = $request->get('neighborhood');
            $address->locality     = $request->get('locality');
            $address->uf           = $request->get('uf');

            $address->save();

            return response($address, Response::HTTP_OK);
        } catch (BadRequestException $e) {
            return response(['Error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response(['Error' => 'Internal server error', 'Log' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!Address::destroy($id)) {
                throw new \Exception('Fail on delete address');
            }

            return response(status: Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response(
                ['Error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
