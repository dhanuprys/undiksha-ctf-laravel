export type Team = {
    id: number;
    name: string;
    join_code?: string;
    event?: Event;
    users?: { id: number; name: string; email: string }[];
    submissions?: Submission[];
    total_score?: number;
};

export type Event = {
    id: number;
    name: string;
    year: number;
    is_active: boolean;
    start_time: string | null;
    end_time: string | null;
};

export type Category = {
    id: number;
    name: string;
    description: string | null;
    challenges?: Challenge[];
};

export type Challenge = {
    id: number;
    title: string;
    description: string;
    base_score: number;
    difficulty: 'easy' | 'medium' | 'hard' | 'very_hard';
    category_id: number;
    category?: Category;
    solved_by_team?: boolean;
    solve_count?: number;
    attachments?: Attachment[];
};

export type Attachment = {
    id: number;
    file_name: string;
    download_url: string;
    download_count: number;
};

export type Submission = {
    id: number;
    challenge?: Pick<Challenge, 'id' | 'title'>;
    is_correct: boolean;
    points_awarded: number;
    created_at: string;
};

export type LeaderboardEntry = {
    rank: number | string;
    team: Pick<Team, 'id' | 'name'>;
    total_score: number;
    solved_count: number;
    last_solve_time: string | null;
    is_current_team: boolean;
};

export type LeaderboardGraphPoint = {
    x: string | number;
    y: number;
};

export type LeaderboardGraphData = {
    team_id: number;
    team_name: string;
    color: string;
    data: LeaderboardGraphPoint[];
};
