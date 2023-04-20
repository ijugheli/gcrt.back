<?php
namespace App\Http\Services\Case;


interface BaseCaseInterface
{
    public function index($caseID =null);
    public function show($id);
    public function store($data, $caseID = null);
    public function update($data);
    public function destroy($id);
}
