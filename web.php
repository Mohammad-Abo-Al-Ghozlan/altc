Route::get('/online', function () {
    return view('online.online', ['title' => 'Online Work']);
});
Route::post('/form/submit', [OnlineController::class, 'submitForm'])->name('form.submit');
Route::post('/working-hours/submit', [OnlineController::class, 'submitWorkingHours'])
    ->name('working.hours.submit');
Route::post('/leave/submit', [OnlineController::class, 'submitLeave'])
    ->name('leave.submit');
    Route::post('/overtime/submit', [OnlineController::class, 'submitOvertime'])
    ->name('overtime.submit');