<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MigraineDiaryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return view('migrainediary::index');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		return view('migrainediary::create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): RedirectResponse
	{
		return redirect()->route('welcome')->with('success', 'we1e');
	}

	/**
	 * Show the specified resource.
	 */
	public function show($id)
	{
		return view('migrainediary::show');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id)
	{
		return view('migrainediary::edit');
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, $id): RedirectResponse
	{
		return redirect()->route('welcome')->with('success', 'we2e');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy($id)
	{
		//
	}
}
