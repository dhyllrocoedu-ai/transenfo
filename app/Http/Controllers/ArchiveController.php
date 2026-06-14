<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin,administrator');
    }

    public function index(Request $request): View
    {
        $query = Archive::with('archivedBy')->latest('archived_at');

        if ($request->filled('type')) {
            $query->where('archivable_type', 'App\\Models\\' . $request->type);
        }

        $archives = $query->paginate(20);

        return view('archives.index', compact('archives'));
    }
}
