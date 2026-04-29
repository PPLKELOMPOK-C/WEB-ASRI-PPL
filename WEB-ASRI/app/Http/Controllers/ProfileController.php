<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Validasi & Fill data name/email
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 2. LOGIKA HAPUS FOTO
        if ($request->has('delete_photo')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
                $user->foto_profil = null;
            }
        }

        // 3. LOGIKA SAVE FOTO DARI CROPPER (BASE64)
        // Kita cek input hidden 'foto_profil_cropped' yang dikirim JS
        if ($request->filled('foto_profil_cropped')) {
            $base64Image = $request->foto_profil_cropped;

            // Proses memisahkan metadata base64 dari data aslinya
            // Format: data:image/jpeg;base64,/9j/4AAQSkZ...
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    return back()->with('error', 'Format gambar tidak valid.');
                }

                $imageData = base64_decode($base64Image);

                if ($imageData === false) {
                    return back()->with('error', 'Gagal memproses gambar.');
                }
            } else {
                return back()->with('error', 'Data gambar tidak valid.');
            }

            // Hapus foto lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Simpan file baru
            $fileName = 'profile-photos/' . $user->id . '_' . time() . '.' . $type;
            Storage::disk('public')->put($fileName, $imageData);
            
            // Simpan path ke database
            $user->foto_profil = $fileName;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}