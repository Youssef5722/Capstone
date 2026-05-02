<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * PREP-4: Pull the middleware-resolved Level and AcademicYear
     * objects from the request attributes set by EnsureDoctorLevelAccess.
     *
     * Centralised here so StudentController and ProjectIdeaController
     * no longer need to duplicate a private resolved() helper.
     *
     * @return array{0: \App\Models\Level, 1: \App\Models\AcademicYear}
     */
    protected function resolveMiddlewareContext(Request $request): array
    {
        return [
            $request->attributes->get('resolvedLevel'),
            $request->attributes->get('activeYear'),
        ];
    }
}
