<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseModule;
use App\Models\CourseModuleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query()
            ->with([
                'category',
                'gradeOptions',
                'moduleGroups.modules',
            ])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(
                    'name',
                    'like',
                    '%' . $request->input('search') . '%'
                )
            )
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get();

        return view(
            'admin.courses.index',
            [
                'courses' => $courses,
                'categories' => CourseCategory::orderBy('sort_order')->get(),
                'search' => $request->input('search', ''),
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'course_category_id' => [
                'required',
                'exists:course_categories,id',
            ],

            'offered' => [
                'nullable',
                'boolean',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],

            'grade_options' => [
                'nullable',
                'array',
            ],

            'grade_options.*' => [
                'nullable',
                'array',
            ],

            'grade_options.*.*' => [
                'integer',
                'in:1,2',
            ],
        ]);

        /*
         * Yeni oluşturulan dersler şu an sistemde
         * tek aşamalı ders olarak oluşturulur.
         *
         * Bakanlık ders kataloğu yönetimi daha sonra
         * ayrı bir yapı üzerinden yapılacaktır.
         */
        $maxSelections = 1;

        $course = Course::create([
            'course_category_id' => $validated['course_category_id'],
            'name' => $validated['name'],
            'max_selections' => $maxSelections,
            'is_modular' => $maxSelections > 1,
            'offered' => $request->boolean('offered'),
            'active' => $request->boolean('active'),
        ]);

        $this->syncGradeOptions(
            $course,
            $request->input('grade_options', [])
        );

        return redirect()
            ->route('admin.courses.index')
            ->with(
                'success',
                "{$course->name} dersi oluşturuldu."
            );
    }

    public function update(
        Request $request,
        Course $course
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'course_category_id' => [
                'required',
                'exists:course_categories,id',
            ],

            'offered' => [
                'nullable',
                'boolean',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],

            'grade_options' => [
                'nullable',
                'array',
            ],

            'grade_options.*' => [
                'nullable',
                'array',
            ],

            'grade_options.*.*' => [
                'integer',
                'in:1,2',
            ],
        ]);

        /*
         * max_selections Bakanlık tarafından belirlenmiş
         * katalog bilgisidir ve okul yönetimi tarafından
         * değiştirilemez.
         *
         * is_modular da bundan türetilir.
         */
        $isModular = (int) $course->max_selections > 1;

        $course->update([
            'course_category_id' => $validated['course_category_id'],
            'name' => $validated['name'],
            'is_modular' => $isModular,
            'offered' => $request->boolean('offered'),
            'active' => $request->boolean('active'),
        ]);

        $this->syncGradeOptions(
            $course,
            $request->input('grade_options', [])
        );

        /*
         * Ders daha önce tek aşamalı iken modüler hale
         * gelmişse ve henüz modül grubu yoksa, sistem
         * otomatik tek bir program/modül grubu oluşturur.
         */
        if ($isModular) {
            $this->ensureDefaultModuleGroup($course);
        }

        return redirect()
            ->route('admin.courses.index')
            ->with(
                'success',
                "{$course->name} dersi güncellendi."
            );
    }

    public function updateModule(
        Request $request,
        Course $course,
        CourseModule $module
    ) {
        abort_unless(
            $module->course_id === $course->id,
            404
        );

        abort_unless(
            $module->course_module_group_id !== null,
            404
        );

        $validated = $request->validate([
            'weekly_hours' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],
        ]);

        $module->update([
            'weekly_hours' =>
                $validated['weekly_hours'] ?? null,
        ]);

        return back()->with(
            'success',
            "{$module->name} saat bilgisi güncellendi."
        );
    }

    /**
     * Sınıf + saat seçeneklerini güvenli şekilde senkronize eder.
     *
     * İşareti kaldırılan mevcut kayıtlar silinmez,
     * active=false yapılır.
     *
     * Yeni seçenekler gerekiyorsa oluşturulur.
     */
    private function syncGradeOptions(
        Course $course,
        array $gradeOptions
    ): void {
        foreach ($gradeOptions as $grade => $hours) {
            $grade = (int) $grade;

            foreach ($hours as $weeklyHours) {
                $weeklyHours = (int) $weeklyHours;

                $option = $course->gradeOptions()
                    ->where('grade', $grade)
                    ->where('weekly_hours', $weeklyHours)
                    ->first();

                if ($option) {
                    $option->update([
                        'active' => true,
                    ]);
                } else {
                    $course->gradeOptions()->create([
                        'grade' => $grade,
                        'weekly_hours' => $weeklyHours,
                        'active' => true,
                    ]);
                }
            }
        }

        $activeKeys = [];

        foreach ($gradeOptions as $grade => $hours) {
            foreach ($hours as $weeklyHours) {
                $activeKeys[] =
                    (int) $grade . ':' .
                    (int) $weeklyHours;
            }
        }

        $course->gradeOptions()
            ->get()
            ->each(function ($option) use ($activeKeys) {
                $key =
                    $option->grade . ':' .
                    $option->weekly_hours;

                if (! in_array($key, $activeKeys, true)) {
                    $option->update([
                        'active' => false,
                    ]);
                }
            });

        /*
         * Dersin sınıf/saat seçenekleri değiştiğinde,
         * bu seçeneklerin mevcut modüller için de
         * kullanılabilir olmasını sağla.
         */
        $this->syncModuleHourOptions($course);
    }

    /**
     * Bir dersin aktif sınıf/saat seçeneklerini,
     * aktif modüllere aktarır.
     */
    private function syncModuleHourOptions(
        Course $course
    ): void {
        if ((int) $course->max_selections <= 1) {
            return;
        }

        $course->loadMissing([
            'gradeOptions',
            'modules' => function ($query) {
                $query
                    ->where('active', true)
                    ->whereHas(
                        'moduleGroup',
                        fn ($groupQuery) =>
                            $groupQuery->where('active', true)
                    );
            },
        ]);

        $activeOptions = $course->gradeOptions
            ->where('active', true);

        foreach ($course->modules as $module) {
            foreach ($activeOptions as $option) {
                $module->hourOptions()->updateOrCreate(
                    [
                        'grade' => $option->grade,
                        'weekly_hours' => $option->weekly_hours,
                    ],
                    [
                        'active' => true,
                    ]
                );
            }

            /*
             * Artık aktif olmayan ders saat seçeneklerini
             * modül tarafında da pasifleştir.
             */
            $activeKeys = $activeOptions
                ->map(
                    fn ($option) =>
                        $option->grade . ':' .
                        $option->weekly_hours
                )
                ->values()
                ->all();

            $module->hourOptions()
                ->get()
                ->each(function ($hourOption) use ($activeKeys) {
                    $key =
                        $hourOption->grade . ':' .
                        $hourOption->weekly_hours;

                    if (! in_array($key, $activeKeys, true)) {
                        $hourOption->update([
                            'active' => false,
                        ]);
                    }
                });
        }
    }

    /**
     * Modüler ders için en az bir program/modül grubu
     * yoksa ders adıyla varsayılan grup oluşturur.
     */
    private function ensureDefaultModuleGroup(
        Course $course
    ): void {
        if ((int) $course->max_selections <= 1) {
            return;
        }

        if ($course->moduleGroups()->exists()) {
            return;
        }

        DB::transaction(function () use ($course) {
            $group = $course->moduleGroups()->create([
                'name' => $course->name,
                'active' => true,
            ]);

            $this->createModulesForGroup(
                $course,
                $group
            );
        });
    }

    /**
     * Bakanlık tarafından belirlenen max_selections
     * kadar modülü otomatik oluşturur.
     */
    private function createModulesForGroup(
        Course $course,
        CourseModuleGroup $group
    ): void {
        $moduleCount = (int) $course->max_selections;

        for ($i = 1; $i <= $moduleCount; $i++) {
            $module = $group->modules()->firstOrCreate(
                [
                    'module_number' => $i,
                ],
                [
                    'course_id' => $course->id,
                    'name' => 'Modül ' . $i,
                    'weekly_hours' => null,
                    'active' => true,
                    'notes' => null,
                ]
            );

            /*
             * Modül oluşturulduğu anda dersin aktif
             * sınıf/saat seçeneklerini aktar.
             */
            $this->syncSingleModuleHourOptions(
                $course,
                $module
            );
        }
    }

    /**
     * Tek bir modülün sınıf/saat seçeneklerini
     * ders kataloğundan üretir.
     */
    private function syncSingleModuleHourOptions(
        Course $course,
        CourseModule $module
    ): void {
        $course->loadMissing('gradeOptions');

        $activeOptions = $course->gradeOptions
            ->where('active', true);

        foreach ($activeOptions as $option) {
            $module->hourOptions()->updateOrCreate(
                [
                    'grade' => $option->grade,
                    'weekly_hours' => $option->weekly_hours,
                ],
                [
                    'active' => true,
                ]
            );
        }
    }

    public function storeModuleGroup(
        Request $request,
        Course $course
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        if ((int) $course->max_selections <= 1) {
            return back()
                ->withErrors([
                    'name' =>
                        'Bu ders tek aşamalı olduğu için modül/program grubu eklenemez.',
                ])
                ->withInput();
        }

        $exists = $course->moduleGroups()
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'name' =>
                        'Bu program/modül grubu zaten tanımlı.',
                ])
                ->withInput();
        }

        return DB::transaction(function () use (
            $course,
            $validated
        ) {
            $group = $course->moduleGroups()->create([
                'name' => $validated['name'],
                'active' => true,
            ]);

            $this->createModulesForGroup(
                $course,
                $group
            );

            return redirect()
                ->back()
                ->with(
                    'success',
                    "{$group->name} programı oluşturuldu. " .
                    "{$course->max_selections} modül otomatik oluşturuldu."
                );
        });
    }

    public function toggleModuleGroup(
        Course $course,
        CourseModuleGroup $group
    ) {
        abort_unless(
            $group->course_id === $course->id,
            404
        );

        $group->update([
            'active' => ! $group->active,
        ]);

        /*
         * Grup pasifleştirilirse modülleri de pasifleştir.
         * Aktifleştirilirse modülleri yeniden aktif et.
         */
        $group->modules()->update([
            'active' => $group->active,
        ]);

        return back()->with(
            'success',
            'Modül/program durumu güncellendi.'
        );
    }
}