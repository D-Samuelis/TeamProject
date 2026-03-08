<?php

// Docasne riesenie  -len test napojenia na backend
// potom odstranit

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
{
    $validated = $request->validate([
        'title_prefix' => ['nullable', 'string', 'max:50'],
        'name' => ['required', 'string', 'max:255'],
        'title_suffix' => ['nullable', 'string', 'max:50'],
        'email' => ['required', 'email', 'max:255'],
        'phone_number' => ['nullable', 'string', 'max:50'],
        'city' => ['nullable', 'string', 'max:255'],
        'country' => ['nullable', 'string', 'max:255'],
    ]);

    $user = $request->user();

    $user->title_prefix = $validated['title_prefix'] ?? null;
    $user->name = $validated['name'];
    $user->title_suffix = $validated['title_suffix'] ?? null;
    $user->email = $validated['email'];
    $user->phone_number = $validated['phone_number'] ?? null;
    $user->city = $validated['city'] ?? null;
    $user->country = $validated['country'] ?? null;

    $user->save();

    return redirect()->route('profile')->with('success', 'Profile updated.');
}



public function updatePassword(Request $request)
{
    $validated = $request->validate([
        'current_password' => ['required'],
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            /*'regex:/[A-Z]/',
            'regex:/[a-z]/',
            'regex:/[0-9]/', */
        ],
    ], [
        'current_password.required' => 'Current password is required.',
        'password.required' => 'New password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Passwords do not match.',
        /*'password.regex' => 'Password must contain uppercase, lowercase and numeric characters.', */
    ]);

    $user = $request->user();

    if (!Hash::check($validated['current_password'], $user->password)) {
        return redirect()
            ->back()
            ->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])
            ->withInput()
            ->withFragment('security');
    }

    $user->password = Hash::make($validated['password']);
    $user->save();

    return redirect()
        ->back()
        ->with('success', 'Password updated successfully.')
        ->withFragment('security');
}





/*nice to have*/


        public function index()
    {
        $ratings = $this->getRatingsData();
        $summary = $this->getRatingsSummary($ratings);

        return view('pages.profile', [
            'ratings' => $ratings,
            'average' => $summary['average'],
            'count' => $summary['count'],
        ]);
    }

    private function getRatingsData(): array
    {
        return [
            [
                'name' => 'Jozko',
                'rating' => 4,
                'text' => 'blabla',
                'date' => '12.02.2026',
            ],
            [
                'name' => 'Peto',
                'rating' => 5,
                'text' => 'super',
                'date' => '29.01.2026',
            ],
            [
                'name' => 'Jana',
                'rating' => 2,
                'text' => 'zle',
                'date' => '03.01.2026',
            ],
            [
                'name' => 'Peto',
                'rating' => 1,
                'text' => 'zle',
                'date' => '03.01.2026',
            ],
        ];
    }

    private function getRatingsSummary(array $ratings): array
    {
        $count = count($ratings);
        $sum = array_sum(array_column($ratings, 'rating'));
        $average = $count > 0 ? round($sum / $count, 1) : 0;

        return [
            'average' => $average,
            'count' => $count,
        ];
    }



}