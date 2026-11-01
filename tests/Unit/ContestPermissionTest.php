<?php

namespace Tests\Unit;

use App\Entities\Contest;
use App\Entities\Permission;
use App\Services\ContestService;
use Tests\TestCase;

class ContestPermissionTest extends TestCase
{
    public function testContestPermissionHelperUsesContestId()
    {
        $contest = $this->createContest();

        $this->assertSame('contest.'.$contest->id, contest_permission($contest));
        $this->assertSame('contest.'.$contest->id, contest_permission($contest->id));
    }

    public function testGetContestPermissionCreatesLaratrustPermission()
    {
        $contest = $this->createContest();
        $service = app(ContestService::class);

        $permission = $service->getContestPermission($contest);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertSame('contest.'.$contest->id, $permission->name);
        $this->assertDatabaseHas('permissions', [
            'name' => 'contest.'.$contest->id,
        ]);
    }

    public function testGetContestPermissionReturnsExistingRecord()
    {
        $contest = $this->createContest();
        $service = app(ContestService::class);

        $first = $service->getContestPermission($contest);
        $second = $service->getContestPermission($contest);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Permission::query()->where('name', 'contest.'.$contest->id)->count());
    }
}
