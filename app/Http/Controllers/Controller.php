<?php

$request->validate([
    'login_type' => 'required|in:sekolah,verifikator',
    'password'   => 'required',
    'npsn'       => 'required_if:login_type,sekolah',
    'nip'        => 'required_if:login_type,verifikator|digits:18',
]);

if ($request->login_type === 'sekolah') {
    // Auth::guard('sekolah')->attempt(['npsn' => ..., 'password' => ...])
} else {
    // Auth::guard('verifikator')->attempt(['nip' => ..., 'password' => ...])
}
