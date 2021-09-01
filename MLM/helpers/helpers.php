<?php

const TRADING_PROFIT_COMMISSION = 'trading-profit-commission';
const DIRECT_SELL_COMMISSION = 'direct-sell-commission';
const BINARY_COMMISSION = 'binary-commission';
const QUICK_START_BONUS_COMMISSION = 'quick-start-bonus-commission';
const TRAINER_BONUS_COMMISSION = 'trainer-bonus-commission';
const INDIRECT_SELL_COMMISSION = 'indirect-sell-commission';
const RESIDUAL_BONUS_COMMISSION = 'residual-bonus-commission';

const COMMISSIONS = [
    TRADING_PROFIT_COMMISSION,
    DIRECT_SELL_COMMISSION,
    QUICK_START_BONUS_COMMISSION,
    TRAINER_BONUS_COMMISSION,
    INDIRECT_SELL_COMMISSION,
    RESIDUAL_BONUS_COMMISSION,
];

const RANK_1_BINARY_ACTIVE = [
    'rank' => 1,
    'condition_converted_in_bp' => 0, 'condition_sub_rank' => 0,
    'prize_in_pf' => null, 'prize_alternative' => null,
    'cap' => 500
];
const RANK_2_BRONZE = [
    'rank' => 2,
    'condition_converted_in_bp' => 1000, 'condition_sub_rank' => 1,
    'prize_in_pf' => null, 'prize_alternative' => 'PIN',
    'cap' => 1000
];
const RANK_3_SILVER = [
    'rank' => 3,
    'condition_converted_in_bp' => 2500, 'condition_sub_rank' => 1,
    'prize_in_pf' => 150, 'prize_alternative' => 'Watch',
    'cap' => 1500
];
const RANK_4_GOLD = [
    'rank' => 4,
    'condition_converted_in_bp' => 7000, 'condition_sub_rank' => 1,
    'prize_in_pf' => 400, 'prize_alternative' => 'iPad',
    'cap' => 2000
];
const RANK_5_PLATINUM = [
    'rank' => 5,
    'condition_converted_in_bp' => 10000, 'condition_sub_rank' => 1,
    'prize_in_pf' => 800, 'prize_alternative' => 'iPhone',
    'cap' => 3000
];
const RANK_6_EXECUTIVE = [
    'rank' => 6,
    'condition_converted_in_bp' => 20000, 'condition_sub_rank' => 1,
    'prize_in_pf' => 1500, 'prize_alternative' => 'Macbook',
    'cap' => 4000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ]
    ]
];
const RANK_7_SENIOR_EXECUTIVE = [
    'rank' => 7,
    'condition_converted_in_bp' => 70000, 'condition_sub_rank' => 1,
    'prize_in_pf' => 7500, 'prize_alternative' => 'Rolex',
    'cap' => 5000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ]
    ]
];
const RANK_8_EMERALD = [
    'rank' => 8,
    'condition_converted_in_bp' => 200000, 'condition_sub_rank' => 4,
    'prize_in_pf' => 40000, 'prize_alternative' => 'c/3/A4',
    'cap' => 6000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ]
    ]
];
const RANK_9_RUBY = [
    'rank' => 9,
    'condition_converted_in_bp' => 600000, 'condition_sub_rank' => 7,
    'prize_in_pf' => 50000, 'prize_alternative' => 'e/5/A6',
    'cap' => 10000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ]
    ]
];
const RANK_10_DIAMOND = [
    'rank' => 10,
    'condition_converted_in_bp' => 2000000, 'condition_sub_rank' => 8,
    'prize_in_pf' => 100000, 'prize_alternative' => 'Range Rover Sport',
    'cap' => 15000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ],
        [
            'level' => 4,
            'percentage' => 1
        ]
    ]
];
const RANK_11_DOUBLE_DIAMOND = [
    'rank' => 11,
    'condition_converted_in_bp' => 4800000, 'condition_sub_rank' => 9,
    'prize_in_pf' => 200000, 'prize_alternative' => 'Ferrari',
    'cap' => 20000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ],
        [
            'level' => 4,
            'percentage' => 1
        ],
        [
            'level' => 5,
            'percentage' => 1
        ]
    ]
];
const RANK_12_VICE_PRESIDENT = [
    'rank' => 12,
    'condition_converted_in_bp' => 9600000, 'condition_sub_rank' => 10,
    'prize_in_pf' => 1000000, 'prize_alternative' => 'Villa',
    'cap' => 25000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ],
        [
            'level' => 4,
            'percentage' => 1
        ],
        [
            'level' => 5,
            'percentage' => 1
        ],
        [
            'level' => 6,
            'percentage' => 1
        ]
    ]
];
const RANK_13_PRESIDENT = [
    'rank' => 13,
    'condition_converted_in_bp' => 13400000, 'condition_sub_rank' => 11,
    'prize_in_pf' => 5000000, 'prize_alternative' => 'Villa',
    'cap' => 30000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ],
        [
            'level' => 4,
            'percentage' => 1
        ],
        [
            'level' => 5,
            'percentage' => 1
        ],
        [
            'level' => 6,
            'percentage' => 1
        ],
        [
            'level' => 7,
            'percentage' => 1
        ]
    ]
];
const RANK_14_ROYAL_PRESIDENT = [
    'rank' => 14,
    'condition_converted_in_bp' => 17200000, 'condition_sub_rank' => 12,
    'prize_in_pf' => 10000000, 'prize_alternative' => 'Private Jet',
    'cap' => 35000,
    'roi_commissions' => [
        [
            'level' => 1,
            'percentage' => 3
        ],
        [
            'level' => 2,
            'percentage' => 2
        ],
        [
            'level' => 3,
            'percentage' => 1
        ],
        [
            'level' => 4,
            'percentage' => 1
        ],
        [
            'level' => 5,
            'percentage' => 1
        ],
        [
            'level' => 6,
            'percentage' => 1
        ],
        [
            'level' => 7,
            'percentage' => 1
        ],
        [
            'level' => 8,
            'percentage' => 1
        ]
    ]
];

const RANKS = [
    1 => RANK_1_BINARY_ACTIVE,
    2 => RANK_2_BRONZE,
    3 => RANK_3_SILVER,
    4 => RANK_4_GOLD,
    5 => RANK_5_PLATINUM,
    6 => RANK_6_EXECUTIVE,
    7 => RANK_7_SENIOR_EXECUTIVE,
    8 => RANK_8_EMERALD,
    9 => RANK_9_RUBY,
    10 => RANK_10_DIAMOND,
    11 => RANK_11_DOUBLE_DIAMOND,
    12 => RANK_12_VICE_PRESIDENT,
    13 => RANK_13_PRESIDENT,
    14 => RANK_14_ROYAL_PRESIDENT,
];

if (!function_exists('subset')) {


    function subset(Illuminate\Support\Collection $collection, $set_number = 1, $chunk_size = 3, $from_node = 0, $sortBy = 'id')
    {
        return $collection->sortBy($sortBy)
            ->values()->filter(function ($item, $index) use ($set_number, $chunk_size, $from_node) {
                $start_index = $index - $from_node;
                return floor($start_index / $chunk_size) + 1 == $set_number;
            })->values()->toArray();
    }
}

if (!function_exists('getRank')) {

    function getRank($rank)
    {
        if (Illuminate\Support\Facades\DB::table('ranks')->exists()) {
            $key_db = \MLM\Models\Rank::query()->where('rank', $rank)->first();
            if ($key_db && !empty($key_db->value))
                return $key_db->toArray();
        }

        if (isset(RANKS[$rank]))
            return RANKS[$rank];

        throw new Exception(trans('responses.main-key-settings-is-missing'));
    }
}
