<?php

namespace App\Http\Controllers\Doctor;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportStudentsRequest;
use App\Imports\StudentsImport;
use App\Models\Level;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $service) {}


    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $filter = $request->query('filter');
        
        $query = Student::where('level_id', $level->id)
                        ->where('academic_year_id', $activeYear->id);

        match($filter) {
            'activated'     => $query->where('is_active', true),
            'not_activated' => $query->where('is_active', false),
            'trashed'       => $query->onlyTrashed(),
            default         => null
        };

        $students = $query->paginate(25);

        return view('doctor.students.index', compact('level', 'activeYear', 'students', 'filter'));
    }

    // ── Show Import Form ───────────────────────────────────────────────────────

    public function showImport(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        return view('doctor.students.import', compact('level', 'activeYear'));
    }

    // ── Process Import ─────────────────────────────────────────────────────────

    public function import(ImportStudentsRequest $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        try {
            DB::transaction(function () use ($request, $level, $activeYear) {
                // Load the uploaded file using PhpSpreadsheet directly
                $file        = $request->file('file');
                $spreadsheet = IOFactory::load($file->getPathname());
                $sheet       = $spreadsheet->getActiveSheet();
                $rows        = $sheet->toArray(null, true, true, false);

                if (empty($rows)) {
                    throw new \Exception('The uploaded file is empty.');
                }

                // First row is the heading row — extract and normalize keys
                $headers = array_map(
                    fn($h) => strtolower(trim(str_replace(' ', '_', (string) $h))),
                    array_shift($rows)
                );

                $importer = new StudentsImport($level->id, $activeYear->id);

                // Convert to named-key collection matching what StudentsImport expects
                $collection = collect($rows)->map(function ($row) use ($headers) {
                    return collect(array_combine($headers, array_pad($row, count($headers), null)));
                });

                $importer->collection($collection);
            });

            return redirect()
                ->route('doctor.students.index', $level->id)
                ->with('success', __('cms.student.import_success'));

        } catch (\Exception $e) {
            return redirect()
                ->route('doctor.students.import', $level->id)
                ->with('error', $e->getMessage());
        }
    }

    // ── Export ─────────────────────────────────────────────────────────────────

    public function export(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $filename = 'students_level_' . $level->id . '_year_' . $activeYear->id . '.xlsx';
        $export   = new StudentsExport($level->id, $activeYear->id);

        return $export->download($filename);
    }

    // ── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(Request $request, Level $level, Student $student)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        // Verify the student belongs to this level + year
        if ($student->level_id !== $level->id || $student->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $this->service->deleteStudent($student); // Soft delete

        return redirect()
            ->route('doctor.students.index', $level->id)
            ->with('success', __('cms.student.delete_success'));
    }

    // ── Restore ────────────────────────────────────────────────────────────────

    public function restore(Request $request, Level $level, int $studentId)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $student = Student::onlyTrashed()
                          ->where('level_id', $level->id)
                          ->where('academic_year_id', $activeYear->id)
                          ->findOrFail($studentId);

        $student->restore();

        return back()->with('success', __('cms.student.restore_success'));
    }
}
