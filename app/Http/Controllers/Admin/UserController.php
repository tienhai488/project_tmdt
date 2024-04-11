<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Gender;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Position\PositionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected PositionRepositoryInterface $positionRepository,
        protected DepartmentRepositoryInterface $departmentRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->userRepository->getDataForDatatable($request->all());
            return UserResource::collection($users);
        }

        return view('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userStatuses = UserStatus::getUserStatuses();
        $positions = $this->positionRepository->all();
        $departments = $this->departmentRepository->all();
        $genders = Gender::getGenders();

        return view(
            'admin.user.create',
            compact(
                'userStatuses',
                'positions',
                'departments',
                'genders',
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->userRepository->create($request->except('_token')) ?
            session()->flash('success', 'Thêm người dùng thành công')
            :
            session()->flash('error', 'Thêm người dùng không thành công');

        return to_route('admin.user.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}