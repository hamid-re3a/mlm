<?php
const BF_TO_BB_RATIO = 5;

const TRADING_PROFIT_COMMISSION = 'trading-profit-commission';
const DIRECT_SELL_COMMISSION = 'direct-sell-commission';
const BINARY_COMMISSION = 'binary-commission';
const TRAINER_BONUS_COMMISSION = 'trainer-bonus-commission';
const INDIRECT_SELL_COMMISSION = 'indirect-sell-commission';
const RESIDUAL_BONUS_COMMISSION = 'residual-bonus-commission';

const COMMISSIONS = [
    TRADING_PROFIT_COMMISSION,
    DIRECT_SELL_COMMISSION,
    TRAINER_BONUS_COMMISSION,
    INDIRECT_SELL_COMMISSION,
    RESIDUAL_BONUS_COMMISSION,
];

const RANK_1_BINARY_ACTIVE = [
    'rank_name' => 'binary_active',
    'rank' => 1,
    'condition_converted_in_bp' => 0, 'condition_sub_rank' => 0, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => null, 'prize_alternative' => null,
    'cap' => 500,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 500
];
const RANK_2_BRONZE = [
    'rank_name' => 'bronze',
    'rank' => 2,
    'condition_converted_in_bp' => 1000, 'condition_sub_rank' => 1, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => null, 'prize_alternative' => 'PIN',
    'cap' => 1000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 1000
];
const RANK_3_SILVER = [
    'rank_name' => 'silver',
    'rank' => 3,
    'condition_converted_in_bp' => 2500, 'condition_sub_rank' => 1, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => 150, 'prize_alternative' => 'Watch',
    'cap' => 1500,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 1500
];
const RANK_4_GOLD = [
    'rank_name' => 'gold',
    'rank' => 4,
    'condition_converted_in_bp' => 7000, 'condition_sub_rank' => 1, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => 400, 'prize_alternative' => 'iPad',
    'cap' => 2000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 2000
];
const RANK_5_PLATINUM = [
    'rank_name' => 'platinum',
    'rank' => 5,
    'condition_converted_in_bp' => 10000, 'condition_sub_rank' => 1, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => 800, 'prize_alternative' => 'iPhone',
    'cap' => 3000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 3000
];
const RANK_6_EXECUTIVE = [
    'rank_name' => 'executive',
    'rank' => 6,
    'condition_converted_in_bp' => 20000, 'condition_sub_rank' => 1, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => 1500, 'prize_alternative' => 'Macbook',
    'cap' => 4000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 4000,
    'residual_bonus_settings' => [
        [
            'level' => 1,
            'percentage' => 3
        ]
    ]
];
const RANK_7_SENIOR_EXECUTIVE = [
    'rank_name' => 'senior_executive',
    'rank' => 7,
    'condition_converted_in_bp' => 70000, 'condition_sub_rank' => 1, 'condition_direct_or_indirect' => false,
    'prize_in_pf' => 7500, 'prize_alternative' => 'Rolex',
    'cap' => 5000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 5000,
    'residual_bonus_settings' => [
        [
            'level' => 1,
            'percentage' => 3
        ]
    ]
];
const RANK_8_EMERALD = [
    'rank_name' => 'emerald',
    'rank' => 8,
    'condition_converted_in_bp' => 200000, 'condition_sub_rank' => 4, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 40000, 'prize_alternative' => 'c/3/A4',
    'cap' => 6000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 6000,
    'residual_bonus_settings' => [
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
    'rank_name' => 'ruby',
    'rank' => 9,
    'condition_converted_in_bp' => 600000, 'condition_sub_rank' => 7, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 50000, 'prize_alternative' => 'e/5/A6',
    'cap' => 10000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 10000,
    'residual_bonus_settings' => [
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
    'rank_name' => 'diamond',
    'rank' => 10,
    'condition_converted_in_bp' => 2000000, 'condition_sub_rank' => 8, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 100000, 'prize_alternative' => 'Range Rover Sport',
    'cap' => 15000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 15000,
    'residual_bonus_settings' => [
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
    'rank_name' => 'double_diamond',
    'rank' => 11,
    'condition_converted_in_bp' => 4800000, 'condition_sub_rank' => 9, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 200000, 'prize_alternative' => 'Ferrari',
    'cap' => 20000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 20000,
    'residual_bonus_settings' => [
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
    'rank_name' => 'vice_president',
    'rank' => 12,
    'condition_converted_in_bp' => 9600000, 'condition_sub_rank' => 10, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 1000000, 'prize_alternative' => 'Villa',
    'cap' => 25000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 25000,
    'residual_bonus_settings' => [
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
    'rank_name' => 'president',
    'rank' => 13,
    'condition_converted_in_bp' => 13400000, 'condition_sub_rank' => 11, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 5000000, 'prize_alternative' => 'Villa',
    'cap' => 30000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 30000,
    'residual_bonus_settings' => [
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
    'rank_name' => 'royal_president',
    'rank' => 14,
    'condition_converted_in_bp' => 17200000, 'condition_sub_rank' => 12, 'condition_direct_or_indirect' => true,
    'prize_in_pf' => 10000000, 'prize_alternative' => 'Private Jet',
    'cap' => 35000,
    'condition_number_of_right_children' => 1,
    'condition_number_of_left_children' => 1,
    'withdrawal_limit' => 35000,
    'residual_bonus_settings' => [
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

CONST MLM_SETTINGS = [
    'IS_UNDER_MAINTENANCE' => [
        'value' => false,
        'title' => 'MLM is Under construction',
        'description' => 'If this setting is true, Then MLM is not working.'
    ],
];

const APP_NAME = 'Ride To Future';
const OTP_LENGTH = 6;
const OTP_CONTAIN_ALPHABET = false;
const OTP_CONTAIN_ALPHABET_LOWER_CASE = true;
const SETTINGS = [
    'APP_NAME' => [
        'value' => APP_NAME,
        'description' => 'Website name',
        'category' => 'General',
    ],

];
const LOGIN_ATTEMPT_SETTINGS = [
    [
        'priority' => 0,
        'times' => 3,
        'duration' => 90,
        'blocking_duration' => 5 * 60,
    ], [
        'priority' => 0,
        'times' => 2,
        'duration' => 90,
        'blocking_duration' => 10 * 60,
    ], [
        'priority' => 0,
        'times' => 6,
        'duration' => 90,
        'blocking_duration' => 20 * 60,
    ],
];
const EMAIL_CONTENT_SETTINGS = [
    'USER_RANK_HAS_BEEN_CHANGED'=>[
        'is_active' => true,
        'subject'=>'Your rank has been changed',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>Your rank has been changed to {{rank}} .<span></span></div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,rank',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'USER_GOT_COMMISSION'=>[
        'is_active' => true,
        'subject'=>'You have received commissions',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>You have received {{amount}}PF for {{commission_type}} .<span></span></div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,amount,commission_type',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
];

