<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ilStudyProgrammeCronRiskyToFailTest extends TestCase
{
    /**
     * @var ilPrgUserNotRestartedCronJob|mixed|MockObject
     */
    protected $job;
    /**
     * @var ilStudyProgrammeSettingsDBRepository|mixed|MockObject
     */
    protected $settings_repo;
    /**
     * @var ilStudyProgrammeProgressDBRepository|mixed|MockObject
     */
    protected $progress_repo;
    protected ProgrammeEventsMock $events;

    protected function setUp() : void
    {
        $this->job = $this
            ->getMockBuilder(ilPrgUserRiskyToFailCronJob::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEvents', 'getSettingsRepository', 'getProgressRepository', 'log'])
            ->getMock();

        $this->settings_repo = $this->getMockBuilder(ilStudyProgrammeSettingsDBRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProgrammeIdsWithRiskyToFailSettings'])
            ->getMock();

        $this->progress_repo = $this->getMockBuilder(ilStudyProgrammeProgressDBRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRiskyToFail'])
            ->getMock();

        $this->events = new ProgrammeEventsMock();
    }

    public function testRiskyToFailNoSettings() : void
    {
        $this->settings_repo
            ->expects($this->once())
            ->method('getProgrammeIdsWithRiskyToFailSettings')
            ->willReturn([]);


        $this->job->expects($this->once())
            ->method('getSettingsRepository')
            ->willReturn($this->settings_repo);
      
        $this->job->expects($this->never())
            ->method('getProgressRepository');
      
        $this->job->expects($this->never())
            ->method('getEvents');

        $this->job->run();
    }

    public function testRiskyToFailNoRepos() : void
    {
        $this->settings_repo
            ->expects($this->once())
            ->method('getProgrammeIdsWithRiskyToFailSettings')
            ->willReturn([
                71 => 2, //id 71, 2 days
                72 => 4
            ]);

        $this->progress_repo
            ->expects($this->once())
            ->method('getRiskyToFail')
            ->willReturn([]);

        $this->job->expects($this->once())
            ->method('getSettingsRepository')
            ->willReturn($this->settings_repo);
      
        $this->job->expects($this->once())
            ->method('getProgressRepository')
            ->willReturn($this->progress_repo);
      
        $this->job->expects($this->never())
            ->method('getEvents');
        
        $this->job->run();
    }

    public function testRiskyToFail()
    {
        $this->settings_repo
            ->expects($this->once())
            ->method('getProgrammeIdsWithRiskyToFailSettings')
            ->willReturn([71 => 2]);

        $progress_1 = (new ilStudyProgrammeProgress(1))->withUserId(11)->withNodeId(71);
        $progress_2 = (new ilStudyProgrammeProgress(2))->withUserId(22)->withNodeId(71);
        $progress_3 = (new ilStudyProgrammeProgress(3))->withUserId(33)->withNodeId(71);
  
        $expected_events = [
            ['userRiskyToFail', ["progress_id" => 1, "usr_id" => 11]],
            ['userRiskyToFail', ["progress_id" => 2, "usr_id" => 22]],
            ['userRiskyToFail', ["progress_id" => 3, "usr_id" => 33]]
        ];
  
        $this->progress_repo
            ->expects($this->once())
            ->method('getRiskyToFail')
            ->willReturn([
                $progress_1,
                $progress_2,
                $progress_3
            ]);

        $this->job->expects($this->once())
            ->method('getSettingsRepository')
            ->willReturn($this->settings_repo);
      
        $this->job->expects($this->once())
            ->method('getProgressRepository')
            ->willReturn($this->progress_repo);
      
        $this->job->expects($this->once())
            ->method('getEvents')
            ->willReturn($this->events);

        $this->job->run();
        $this->assertEquals($expected_events, $this->events->raised);
    }
}
