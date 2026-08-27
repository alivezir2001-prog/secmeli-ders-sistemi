<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $academicYears =
            AcademicYear::orderByDesc('name')
            ->get();

        $academicYear =
            AcademicYear::where(
                'id',
                $request->input(
                    'academic_year_id',
                    AcademicYear::where(
                        'active',
                        true
                    )->value('id')
                )
            )
            ->firstOrFail();

        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $status =
            $request->input(
                'status',
                'active'
            );

        $students =
            Student::query()
            ->with([
                'studentYears' => function ($query) use ($academicYear) {
                    $query->where(
                        'academic_year_id',
                        $academicYear->id
                    );
                },
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($studentQuery) use ($search) {
                            $studentQuery
                                ->where(
                                    'first_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'student_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $status === 'active',
                fn($query) =>
                $query->where(
                    'active',
                    true
                )
            )
            ->when(
                $status === 'passive',
                fn($query) =>
                $query->where(
                    'active',
                    false
                )
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'admin.students.index',
            compact(
                'students',
                'academicYears',
                'academicYear',
                'search',
                'status'
            )
        );
    }

    public function store(Request $request)
    {
        $validated =
            $request->validate([
                'student_number' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'first_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'last_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'national_id' => [
                    'nullable',
                    'string',
                    'size:11',
                ],

                'academic_year_id' => [
                    'required',
                    'exists:academic_years,id',
                ],

                'grade' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:12',
                ],

                'section' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]);

        DB::transaction(function () use ($validated) {
            $student =
                Student::create([
                    'student_number' =>
                    $validated['student_number']
                        ?? null,

                    'first_name' =>
                    $validated['first_name'],

                    'last_name' =>
                    $validated['last_name'],

                    'national_id' =>
                    $validated['national_id']
                        ?? null,

                    'active' =>
                    true,
                ]);

            $student->studentYears()->create([
                'academic_year_id' =>
                $validated['academic_year_id'],

                'grade' =>
                $validated['grade'],

                'section' =>
                $validated['section']
                    ?? null,

                'active' =>
                true,
            ]);
        });

        return redirect()
            ->route(
                'admin.students.index',
                [
                    'academic_year_id' =>
                    $validated['academic_year_id'],
                ]
            )
            ->with(
                'success',
                'Öğrenci başarıyla oluşturuldu.'
            );
    }

    public function update(
        Request $request,
        Student $student
    ) {
        $validated =
            $request->validate([
                'student_number' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'first_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'last_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'national_id' => [
                    'nullable',
                    'string',
                    'size:11',
                ],

                'academic_year_id' => [
                    'required',
                    'exists:academic_years,id',
                ],

                'grade' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:12',
                ],

                'section' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]);

        DB::transaction(function () use (
            $student,
            $validated
        ) {
            $student->update([
                'student_number' =>
                $validated['student_number']
                    ?? null,

                'first_name' =>
                $validated['first_name'],

                'last_name' =>
                $validated['last_name'],

                'national_id' =>
                $validated['national_id']
                    ?? null,
            ]);

            $student->studentYears()->updateOrCreate(
                [
                    'academic_year_id' =>
                    $validated['academic_year_id'],
                ],
                [
                    'grade' =>
                    $validated['grade'],

                    'section' =>
                    $validated['section']
                        ?? null,

                    'active' =>
                    true,
                ]
            );
        });

        return redirect()
            ->route(
                'admin.students.index',
                [
                    'academic_year_id' =>
                    $validated['academic_year_id'],
                ]
            )
            ->with(
                'success',
                'Öğrenci bilgileri güncellendi.'
            );
    }

    public function updateStatus(
        Request $request,
        Student $student
    ) {
        $student->update([
            'active' =>
            ! $student->active,
        ]);

        return back()->with(
            'success',
            $student->active
                ? 'Öğrenci aktif hale getirildi.'
                : 'Öğrenci pasif hale getirildi.'
        );
    }

    public function importForm(Request $request)
    {
        $academicYears =
            AcademicYear::orderByDesc('name')
            ->get();

        $academicYear =
            AcademicYear::where(
                'id',
                $request->input(
                    'academic_year_id',
                    AcademicYear::where(
                        'active',
                        true
                    )->value('id')
                )
            )
            ->firstOrFail();

        return view(
            'admin.students.import',
            compact(
                'academicYears',
                'academicYear'
            )
        );
    }

    public function importPreview(Request $request)
    {
        $validated =
            $request->validate([
                'academic_year_id' => [
                    'required',
                    'exists:academic_years,id',
                ],

                'pdf' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:20480',
                ],
            ]);

        $file =
            $request->file('pdf');

        $absolutePath =
            $file->getRealPath();

        if (
            ! $absolutePath
            ||
            ! is_file($absolutePath)
        ) {
            return back()->withErrors([
                'Yüklenen PDF dosyasına erişilemedi.',
            ]);
        }

        $escapedPath =
            escapeshellarg($absolutePath);

        $command =
            '/usr/bin/pdftotext -layout ' .
            $escapedPath .
            ' - 2>&1';

        $outputLines = [];
        $exitCode = 0;

        exec(
            $command,
            $outputLines,
            $exitCode
        );

        if ($exitCode !== 0) {
            return back()->withErrors([
                'PDF okunamadı. PDF dosyasının e-Okul sınıf listesi formatında olduğundan emin olun.',
            ]);
        }

        $output =
            implode(
                "\n",
                $outputLines
            );

        if (
            trim($output) === ''
        ) {
            return back()->withErrors([
                'PDF içeriği okunamadı.',
            ]);
        }

        $lines =
            preg_split(
                '/\R/u',
                $output
            );

        $currentGrade = null;
        $currentSection = null;

        $students = [];

        $ignoredCount = 0;
        $ignoreCurrentPage = false;

        foreach ($lines as $line) {
            $line =
                trim($line);

            if ($line === '') {
                continue;
            }

            /*
             * 5–8. sınıf başlıkları.
             */
            if (
                preg_match(
                    '/^([1-8])\.\s*Sınıf\s*\/\s*(.+?)\s+Şube(?:si)?\s+Sınıf\s+Listesi$/u',
                    $line,
                    $matches
                )
            ) {
                $ignoreCurrentPage = false;

                $currentGrade =
                    (int) $matches[1];

                $sectionText =
                    trim($matches[2]);

                $currentSection =
                    strcasecmp(
                        $sectionText,
                        'Geçici'
                    ) === 0
                    ? 'Geçici'
                    : $sectionText;

                continue;
            }

            /*
             * Ana sınıfı sayfaları içeri alınmaz.
             */
            if (
                str_contains(
                    $line,
                    'Ana Sınıfı'
                )
            ) {
                $currentGrade = null;
                $currentSection = null;
                $ignoreCurrentPage = true;

                continue;
            }

            /*
             * Ana sınıfı öğrenci satırı.
             */
            if (
                $ignoreCurrentPage
                &&
                preg_match(
                    '/^\s*(\d+)\s+([0-9]+)\s+(.+?)\s{2,}(.+?)\s{2,}(Kız|Erkek)\s*$/u',
                    $line,
                    $matches
                )
            ) {
                $ignoredCount++;

                continue;
            }

            /*
             * 5–8. sınıf öğrenci satırı.
             */
            if (
                $currentGrade !== null
                &&
                preg_match(
                    '/^\s*(\d+)\s+([0-9]+)\s+(.+?)\s{2,}(.+?)\s{2,}(Kız|Erkek)\s*$/u',
                    $line,
                    $matches
                )
            ) {
                $students[] = [
                    'grade' =>
                    $currentGrade,

                    'section' =>
                    $currentSection,

                    'student_number' =>
                    trim($matches[2]),

                    'first_name' =>
                    trim($matches[3]),

                    'last_name' =>
                    trim($matches[4]),

                    'gender' =>
                    trim($matches[5]),
                ];
            }
        }

        $studentCollection =
            collect($students);

        /*
         * PDF içinde aynı öğrenci numarası
         * birden fazla kez bulunmuş mu?
         */
        $duplicates =
            $studentCollection
            ->groupBy('student_number')
            ->filter(
                fn($items) =>
                $items->count() > 1
            )
            ->keys()
            ->values();

        /*
         * Veritabanındaki mevcut öğrencileri bul.
         *
         * Student.student_number unique olmadığı için
         * bir numaranın birden fazla öğrenciye ait
         * olması da ayrıca kontrol edilir.
         */
        $studentNumbers =
            $studentCollection
            ->pluck('student_number')
            ->filter()
            ->unique()
            ->values();

        $existingStudents =
            Student::query()
            ->whereIn(
                'student_number',
                $studentNumbers
            )
            ->with([
                'studentYears' => function ($query) use ($validated) {
                    $query->where(
                        'academic_year_id',
                        $validated['academic_year_id']
                    );
                },
            ])
            ->get()
            ->groupBy('student_number');

        $newCount = 0;
        $existingCount = 0;
        $changeCount = 0;
        $unchangedCount = 0;
        $ambiguousCount = 0;

        foreach ($studentCollection as $student) {
            $number =
                $student['student_number'];

            $matches =
                $existingStudents->get(
                    $number,
                    collect()
                );

            if ($matches->count() === 0) {
                $newCount++;

                continue;
            }

            if ($matches->count() > 1) {
                $ambiguousCount++;

                continue;
            }

            $existingCount++;

            $existingStudent =
                $matches->first();

            $existingYear =
                $existingStudent
                ->studentYears
                ->first();

            $changed =
                ! $existingYear
                ||
                (int) $existingYear->grade
                !== (int) $student['grade']
                ||
                (string) (
                    $existingYear->section
                    ?? ''
                )
                !== (string) (
                    $student['section']
                    ?? ''
                )
                ||
                ! $existingStudent->active;

            if ($changed) {
                $changeCount++;
            } else {
                $unchangedCount++;
            }
        }

        /*
         * Sınıf / şube dağılımı.
         */
        $gradeSectionCounts =
            $studentCollection
            ->groupBy(
                fn($student) =>
                $student['grade']
                    . '|'
                    . $student['section']
            )
            ->map(
                fn($items) =>
                $items->count()
            );

        /*
         * Önizleme verisini geçici JSON dosyasına yaz.
         *
         * 580 öğrenciyi session/cookie içine koymak yerine
         * sunucuda geçici dosya kullanıyoruz.
         */
        $token =
            Str::random(48);

        $directory =
            storage_path(
                'app/student-imports'
            );

        if (! File::exists($directory)) {
            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        $previewPath =
            $directory
            . '/'
            . $token
            . '.json';

        File::put(
            $previewPath,
            json_encode(
                [
                    'academic_year_id' =>
                    (int) $validated['academic_year_id'],

                    'created_at' =>
                    now()->toIso8601String(),

                    'students' =>
                    $studentCollection
                        ->values()
                        ->all(),
                ],
                JSON_UNESCAPED_UNICODE |
                    JSON_PRETTY_PRINT |
                    JSON_THROW_ON_ERROR
            )
        );

        return view(
            'admin.students.import-preview',
            [
                'academicYear' =>
                AcademicYear::findOrFail(
                    $validated['academic_year_id']
                ),

                'students' =>
                $studentCollection,

                'duplicates' =>
                $duplicates,

                'ignoredCount' =>
                $ignoredCount,

                'totalCount' =>
                $studentCollection->count(),

                'gradeSectionCounts' =>
                $gradeSectionCounts,

                'newCount' =>
                $newCount,

                'existingCount' =>
                $existingCount,

                'changeCount' =>
                $changeCount,

                'unchangedCount' =>
                $unchangedCount,

                'ambiguousCount' =>
                $ambiguousCount,

                'importToken' =>
                $token,
            ]
        );
    }

    public function importExecute(Request $request)
    {
        $validated =
            $request->validate([
                'academic_year_id' => [
                    'required',
                    'exists:academic_years,id',
                ],

                'token' => [
                    'required',
                    'string',
                    'size:48',
                ],
            ]);

        $path =
            storage_path(
                'app/student-imports/'
                    . $validated['token']
                    . '.json'
            );

        if (
            ! File::exists($path)
        ) {
            return redirect()
                ->route(
                    'admin.students.import',
                    [
                        'academic_year_id' =>
                        $validated['academic_year_id'],
                    ]
                )
                ->withErrors([
                    'İçe aktarma önizlemesi bulunamadı veya süresi doldu. PDF dosyasını yeniden analiz edin.',
                ]);
        }

        try {
            $payload =
                json_decode(
                    File::get($path),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
        } catch (\Throwable) {
            return redirect()
                ->route(
                    'admin.students.import',
                    [
                        'academic_year_id' =>
                        $validated['academic_year_id'],
                    ]
                )
                ->withErrors([
                    'İçe aktarma önizleme dosyası okunamadı.',
                ]);
        }

        if (
            (int) (
                $payload['academic_year_id']
                ?? 0
            )
            !==
            (int) $validated['academic_year_id']
        ) {
            return redirect()
                ->route(
                    'admin.students.import',
                    [
                        'academic_year_id' =>
                        $validated['academic_year_id'],
                    ]
                )
                ->withErrors([
                    'İçe aktarma eğitim yılı uyuşmuyor.',
                ]);
        }

        $students =
            collect(
                $payload['students']
                    ?? []
            );

        if ($students->isEmpty()) {
            File::delete($path);

            return redirect()
                ->route(
                    'admin.students.import',
                    [
                        'academic_year_id' =>
                        $validated['academic_year_id'],
                    ]
                )
                ->withErrors([
                    'İçe aktarılacak öğrenci bulunamadı.',
                ]);
        }

        $duplicateNumbers =
            $students
            ->groupBy('student_number')
            ->filter(
                fn($items) =>
                $items->count() > 1
            )
            ->keys();

        if (
            $duplicateNumbers->isNotEmpty()
        ) {
            return back()->withErrors([
                'PDF içinde tekrar eden öğrenci numaraları var. İçe aktarma yapılmadı.',
            ]);
        }

        $newCount = 0;
        $changedCount = 0;
        $unchangedCount = 0;

        DB::transaction(function () use (
            $students,
            $validated,
            &$newCount,
            &$changedCount,
            &$unchangedCount
        ) {
            foreach ($students as $row) {
                $studentNumber =
                    trim(
                        (string) (
                            $row['student_number']
                            ?? ''
                        )
                    );

                if (
                    $studentNumber === ''
                ) {
                    continue;
                }

                $matches =
                    Student::query()
                    ->where(
                        'student_number',
                        $studentNumber
                    )
                    ->with([
                        'studentYears' => function ($query) use (
                            $validated
                        ) {
                            $query->where(
                                'academic_year_id',
                                $validated['academic_year_id']
                            );
                        },
                    ])
                    ->get();

                if (
                    $matches->count() > 1
                ) {
                    throw new \RuntimeException(
                        'Veritabanında öğrenci no '
                            . $studentNumber
                            . ' birden fazla öğrencide kayıtlı.'
                    );
                }

                $student =
                    $matches->first();

                if (! $student) {
                    $student =
                        Student::create([
                            'student_number' =>
                            $studentNumber,

                            'first_name' =>
                            $row['first_name'],

                            'last_name' =>
                            $row['last_name'],

                            'national_id' =>
                            null,

                            'active' =>
                            true,
                        ]);

                    $student->studentYears()->create([
                        'academic_year_id' =>
                        $validated['academic_year_id'],

                        'grade' =>
                        (int) $row['grade'],

                        'section' =>
                        $row['section']
                            ?? null,

                        'active' =>
                        true,
                    ]);

                    $newCount++;

                    continue;
                }

                $studentYear =
                    $student->studentYears->first();

                $changed =
                    (string) $student->first_name
                    !== (string) $row['first_name']
                    ||
                    (string) $student->last_name
                    !== (string) $row['last_name']
                    ||
                    ! $student->active
                    ||
                    ! $studentYear
                    ||
                    (int) $studentYear->grade
                    !== (int) $row['grade']
                    ||
                    (string) (
                        $studentYear->section
                        ?? ''
                    )
                    !== (string) (
                        $row['section']
                        ?? ''
                    )
                    ||
                    ! $studentYear->active;

                if ($changed) {
                    $student->update([
                        'first_name' =>
                        $row['first_name'],

                        'last_name' =>
                        $row['last_name'],

                        'active' =>
                        true,
                    ]);

                    $student->studentYears()->updateOrCreate(
                        [
                            'academic_year_id' =>
                            $validated['academic_year_id'],
                        ],
                        [
                            'grade' =>
                            (int) $row['grade'],

                            'section' =>
                            $row['section']
                                ?? null,

                            'active' =>
                            true,
                        ]
                    );

                    $changedCount++;
                } else {
                    $unchangedCount++;
                }
            }
        });

        File::delete($path);

        return redirect()
            ->route(
                'admin.students.index',
                [
                    'academic_year_id' =>
                    $validated['academic_year_id'],
                ]
            )
            ->with(
                'success',
                $newCount
                    . ' yeni öğrenci oluşturuldu. '
                    . $changedCount
                    . ' mevcut öğrencide değişiklik yapıldı. '
                    . $unchangedCount
                    . ' öğrencide değişiklik yoktu.'
            );
    }
}
