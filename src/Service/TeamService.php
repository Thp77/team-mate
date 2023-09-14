<?php


namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;

class TeamService {

    public function __construct(private TeamRepository $teamRepository)
    {
        
    }

    public function get_team(int $id) : Team {
        $team = $this->teamRepository->find($id);
        return $team;
    }
}

