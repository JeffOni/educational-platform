<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseDelegation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseDelegationController extends Controller
{
    /**
     * Mostrar delegaciones de un curso
     */
    public function index(Course $course)
    {
        // Verificar que el usuario puede gestionar el curso
        if (!$course->userCanEdit(auth()->user())) {
            abort(403, 'No tienes permiso para gestionar este curso');
        }

        $delegations = $course->delegations()
            ->with(['delegatedBy', 'delegatedTo'])
            ->latest()
            ->get();

        return response()->json([
            'delegations' => $delegations,
        ]);
    }

    /**
     * Crear una nueva delegación
     */
    public function store(Request $request, Course $course)
    {
        // Solo el profesor titular o admin pueden delegar
        if (!auth()->user()->hasRole('admin') && $course->user_id !== auth()->id()) {
            abort(403, 'Solo el profesor titular puede delegar permisos');
        }

        $request->validate([
            'delegated_to' => 'required|exists:users,id',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'in:view_course,grade_assignments,answer_questions,edit_content,view_analytics',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'reason' => 'nullable|string|max:255',
        ]);

        // Verificar que el usuario a delegar sea profesor
        $teacherToDelegate = User::findOrFail($request->delegated_to);
        if (!$teacherToDelegate->hasRole('teacher')) {
            return response()->json([
                'message' => 'Solo puedes delegar permisos a profesores'
            ], 422);
        }

        // Verificar que no se esté delegando a sí mismo
        if ($request->delegated_to == auth()->id()) {
            return response()->json([
                'message' => 'No puedes delegarte permisos a ti mismo'
            ], 422);
        }

        // Crear la delegación
        $delegation = $course->delegations()->create([
            'delegated_by' => auth()->id(),
            'delegated_to' => $request->delegated_to,
            'permissions' => $request->permissions,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'reason' => $request->reason,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Delegación creada correctamente',
            'delegation' => $delegation->load(['delegatedBy', 'delegatedTo']),
        ]);
    }

    /**
     * Actualizar una delegación existente
     */
    public function update(Request $request, Course $course, CourseDelegation $delegation)
    {
        // Solo el profesor titular o admin pueden actualizar
        if (!auth()->user()->hasRole('admin') && $course->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'permissions' => 'sometimes|array|min:1',
            'permissions.*' => 'in:view_course,grade_assignments,answer_questions,edit_content,view_analytics',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'reason' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $delegation->update($request->only([
            'permissions',
            'starts_at',
            'expires_at',
            'reason',
            'is_active',
        ]));

        return response()->json([
            'message' => 'Delegación actualizada correctamente',
            'delegation' => $delegation->load(['delegatedBy', 'delegatedTo']),
        ]);
    }

    /**
     * Revocar una delegación (desactivarla)
     */
    public function revoke(Course $course, CourseDelegation $delegation)
    {
        // Solo el profesor titular o admin pueden revocar
        if (!auth()->user()->hasRole('admin') && $course->user_id !== auth()->id()) {
            abort(403);
        }

        $delegation->update([
            'is_active' => false,
            'expires_at' => now(),
        ]);

        return response()->json([
            'message' => 'Delegación revocada correctamente',
        ]);
    }

    /**
     * Eliminar una delegación permanentemente
     */
    public function destroy(Course $course, CourseDelegation $delegation)
    {
        // Solo admin puede eliminar permanentemente
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $delegation->delete();

        return response()->json([
            'message' => 'Delegación eliminada correctamente',
        ]);
    }

    /**
     * Listar profesores disponibles para delegar
     */
    public function availableTeachers(Course $course)
    {
        // Obtener todos los profesores excepto el titular
        $teachers = User::role('teacher')
            ->where('id', '!=', $course->user_id)
            ->select('id', 'name', 'email')
            ->get();

        return response()->json([
            'teachers' => $teachers,
        ]);
    }
}
