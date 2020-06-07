<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        return tap($user)->update($request->only('first_name', 'last_name', 'email'));
    }

    /**
     *
     * Delete the current user's account.
     *
     *  @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = auth('api')->user();

        $user->delete();

        return response()->json([
            'success' => 'Your profile has been deleted.'
        ]);
    }
}
