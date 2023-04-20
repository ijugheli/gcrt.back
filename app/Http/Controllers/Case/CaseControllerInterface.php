<?php

namespace App\Http\Controllers\Case;

use Illuminate\Http\Request;

interface CaseControllerInterface
{
    public function index();
    public function show($id);
    public function store(Request $request);
    public function update(Request $request);
    public function destroy($id);
}
