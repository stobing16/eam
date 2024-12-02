<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(){

        $employees = Employee::paginate(10);
        return view('employee', compact('employees'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request){

        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Email' => 'nullable|string|max:255',
        ]);

        $validatedData['RowId'] = Employee::getNextRowId();
        $validatedData['CreatedDate'] = date('Y-m-d H:i:s');

        Employee::create($validatedData);

        return response()->json(['success' => true, 'message' => 'Employee created successfully!']);
    }
}
