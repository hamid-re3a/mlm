<?php


use Illuminate\Http\Request;
use User\Services\UserService;


/**
 * user_roles
 */
const USER_ROLE_SUPER_ADMIN = 'super-admin';
const USER_ROLE_ADMIN_GATEWAY = 'user-gateway-admin';
const USER_ROLE_ADMIN_KYC = 'kyc-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_ORDER = 'subscriptions-order-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_PACKAGE = 'subscriptions-package-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_PAYMENT = 'subscriptions-payment-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_WALLET = 'subscriptions-wallet-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_GIFTCODE = 'subscriptions-giftcode-admin';
const USER_ROLE_ADMIN_MLM = 'mlm-admin';
const USER_ROLE_CLIENT = 'client';
const USER_ROLE_HELP_DESK = 'help-desk';
const USER_ROLES = [
    USER_ROLE_SUPER_ADMIN,
    USER_ROLE_ADMIN_GATEWAY,
    USER_ROLE_ADMIN_KYC,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_ORDER,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_PACKAGE,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_PAYMENT,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_WALLET,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_GIFTCODE,
    USER_ROLE_ADMIN_MLM,
    USER_ROLE_CLIENT,
    USER_ROLE_HELP_DESK,
];

const SETTINGS = [
    'APP_NAME' => [
        'value' => APP_NAME,
        'description' => 'Website name',
        'category' => 'General',
    ],
    'OTP_CONTAIN_ALPHABET' => [
        'value' => OTP_CONTAIN_ALPHABET,
        'description' => 'Otp can contain alphabet in them',
        'category' => 'General > Otp',
    ],
    'OTP_CONTAIN_ALPHABET_LOWER_CASE' => [
        'value' => OTP_CONTAIN_ALPHABET_LOWER_CASE,
        'description' => 'Otp can contain only lower case alphabet in them',
        'category' => 'General > Otp',
    ],
    'OTP_LENGTH' => [
        'value' => OTP_LENGTH,
        'description' => 'Otp length',
        'category' => 'General > Otp',
    ],
    'USER_CHECK_PASSWORD_HISTORY_FOR_NEW_PASSWORD' => [
        'value' => USER_CHECK_PASSWORD_HISTORY_FOR_NEW_PASSWORD,
        'description' => 'When user wants to reset/change password, should we check history and not allow shim to use previous ones.',
        'category' => 'User > Password',
    ],
    'USER_CHECK_TRANSACTION_PASSWORD_HISTORY_FOR_NEW_PASSWORD' => [
        'value' => USER_CHECK_TRANSACTION_PASSWORD_HISTORY_FOR_NEW_PASSWORD,
        'description' => 'When user wants to change transaction password, should we check history and not allow shim to use previous ones.',
        'category' => 'User > Password',
    ],

    'USER_FORGOT_PASSWORD_OTP_DURATION' => [
        'value' => USER_FORGOT_PASSWORD_OTP_DURATION,
        'description' => '(in seconds) Forget otp is valid for 90 seconds as default.',
        'category' => 'User > Password',
    ],
    'USER_FORGOT_PASSWORD_OTP_TRIES' => [
        'value' => USER_FORGOT_PASSWORD_OTP_TRIES,
        'description' => 'This is used with max user duration to stop user requesting a lot for forgot password otp',
        'category' => 'User > Password',
    ],
    'USER_EMAIL_VERIFICATION_OTP_DURATION' => [
        'value' => USER_EMAIL_VERIFICATION_OTP_DURATION,
        'description' => '(in seconds) Email verification otp is valid for 90 seconds as default.',
        'category' => 'User > Email Verification',
    ],
    'USER_EMAIL_VERIFICATION_OTP_TRIES' => [
        'value' => USER_EMAIL_VERIFICATION_OTP_TRIES,
        'description' => 'This is used with max user duration to stop user requesting a lot for email verification  otp',
        'category' => 'User > Email Verification',
    ],
    'USER_CHANGE_TRANSACTION_OTP_DURATION' => [
        'value' => USER_CHANGE_TRANSACTION_OTP_DURATION,
        'description' => '(in seconds) Transaction password otp is valid for 90 seconds as default.',
        'category' => 'User > Transaction password',
    ],
    'USER_CHANGE_TRANSACTION_OTP_TRIES' => [
        'value' => USER_CHANGE_TRANSACTION_OTP_TRIES,
        'description' => 'This is used with max user duration to stop user requesting a lot for email verification  otp',
        'category' => 'User > Email Verification',
    ],
    'USER_REGISTRATION_PASSWORD_CRITERIA' => [
        'value' => USER_REGISTRATION_PASSWORD_CRITERIA,
        'description' => 'Password pattern for user registration',
        'category' => 'User > Password',
    ],
    'IS_LOGIN_PASSWORD_CHANGE_EMAIL_ENABLE' => [
        'value' => 1,
        'description' => null,
        'category' => 'User > Profile Management'
    ],
    'IS_TRANSACTION_PASSWORD_CHANGE_EMAIL_ENABLE' => [
        'value' => 1,
        'description' => null,
        'category' => 'User > Profile Management'
    ],
    'SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE' => [
        'value' => SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE,
        'description' => null,
        'category' => 'User > Registration'
    ],
    'SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE' => [
        'value' => SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE,
        'description' => null,
        'category' => 'User > Registration'
    ],
    'LOGOUT_CLIENTS_FOR_MAINTENANCE' => [
        'value' => LOGOUT_CLIENTS_FOR_MAINTENANCE,
        'description' => 'Logout all logged-in clients for maintenance mode',
        'category' => 'User > Registration'
    ],
];


//email constants
const EMAIL_CONTENT_SETTINGS = [
    'FORGOT_PASSWORD_OTP_EMAIL' => [
        'is_active' => true,
        'subject' => 'Forgot Password Code',
        'from' => 'it@ridetothefuture.com',
        'from_name' => 'Janex Support Team',
        'body' => <<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <p>We received a request to reset your password. Please use the below code to set up a new password for your account.&nbsp;</p>
                <h2 style="text-align: center;"><span style="background-color: #ffff00;"> {{otp}}</span><span style="background-color: #ffff00;"></span><span style="background-color: #ffff00;"></span></h2>
                <p>This code is valid only for {{otp_expire_duration}}. You will need to request another code once expired</p>
                <p>If you didn't request to reset your password, ignore this email.</p>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,otp,otp_expire_duration',
        'variables_description'=>'full_name user full name, otp otp token',
        'type'=>'email',
    ],
    'TRANSACTION_PASSWORD_OTP'=>[

        'is_active' => true,
        'subject'=>'Transaction Password OTP',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <p>To change your transaction password, please use the below code</p>
                <p></p>
                <h2 style="text-align: center;"><span style="background-color: #ffff00;"> {{otp}}</span></h2>
                <p><span style="background-color: #ffff00;"></span></p>
                <p>This code is valid only for {{otp_expire_duration}}. You will need to request another code once expired</p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,otp,otp_expire_duration',
        'variables_description'=>'full_name user full name, otp otp token',
        'type'=>'email',
    ],
    'VERIFICATION_EMAIL_OTP_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Email Verification Code',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <p>To continue with your email verification, please use the below code</p>
                <p></p>
                <h2 style="text-align: center;"><span style="background-color: #ffff00;"> {{otp}}</span></h2>
                <p><span style="background-color: #ffff00;"></span></p>
                <p>This code is valid only for {{otp_expire_duration}}. You will need to request another code once expired</p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,otp,otp_expire_duration',
        'variables_description'=>'full_name user full name, otp otp token',
        'type'=>'email',
    ],
    'USER_REGISTRATION_WELCOME_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Welcome to Janex',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <div>Hello,&nbsp;{{full_name}}!</div>
                <div>&nbsp;</div>
                <div>We're excited to have you get started. First, you need to confirm your account by using the below code.</div>
                <div>&nbsp;</div>
                <h2 style="text-align: center;"><span style="background-color: #ffff00;">{{otp}}</span></h2>
                <p>This code is valid only for {{otp_expire_duration}}. You will need to request another code once expired.</p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,otp',
        'variables_description'=>'full_name user full name, otp otp token',
        'type'=>'email',
    ],
    'SUSPICIOUS_LOGIN_ATTEMPT_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Suspicious Login Attempt',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>We detected an unusual login attempt.</div>
                <p></p>
                <div><strong>Login Information:</strong><strong></strong></div>
                <div>Country: {{country}}</div>
                <div>City: {{city}}</div>
                <div>IP: {{ip}}</div>
                <div>Platform: {{platform}}</div>
                <div>Browser: {{browser}}</div>
                <p></p>
                <div>If this was you, ignore this email. Otherwise, change your password immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser,status',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'NORMAL_LOGIN_EMAIL'=>[
        'is_active' => true,
        'subject'=>'Someone Logged-In',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>Someone has logged-in to your account.</div>
                <p></p>
                <div><strong>Login Information:</strong><strong></strong></div>
                <div>Country: {{country}}</div>
                <div>City: {{city}}</div>
                <div>IP: {{ip}}</div>
                <div>Platform: {{platform}}</div>
                <div>Browser: {{browser}}</div>
                <p></p>
                <div>If this was you, you can ignore this email. Otherwise you should change your password immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser,status',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'TOO_MANY_LOGIN_ATTEMPTS_TEMPORARY_BLOCK_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Too Many Attempts',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>You have exceeded the limit of login attempts. Your account is temporary blocked and will be unblocked automatically after
                <div>
                <div><span>{{next_try_time}}.</span></div>
                </div>
                </div>
                <p></p>
                <div><strong>Device Information:</strong><strong></strong></div>
                <div>Country: {{country}}</div>
                <div>City: {{city}}</div>
                <div>IP: {{ip}}</div>
                <div>Platform: {{platform}}</div>
                <div>Browser: {{browser}}</div>
                <p></p>
                <div>If this was you, you should wait <span>{{next_try_time}}</span>. Otherwise you should change your password or reach the support team immediately.</div>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser,status,next_try_time,login_attempt_times',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'TOO_MANY_LOGIN_ATTEMPTS_PERMANENT_BLOCK_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Too Many Attempts - Account Blocked',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>Again you have exceeded the limit of login attempts. Your account is temporary blocked.<span></span></div>
                <p></p>
                <div><strong>Device Information:</strong><strong></strong></div>
                <div>Country: {{country}}</div>
                <div>City: {{city}}</div>
                <div>IP: {{ip}}</div>
                <div>Platform: {{platform}}</div>
                <div>Browser: {{browser}}</div>
                <p></p>
                <div>Please reach the support team to unblock your account.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser,status,next_try_time,login_attempt_times',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'PASSWORD_CHANGED_WARNING_EMAIL'=>[
        'is_active' => true,
        'subject'=>'Password Changed Warning',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>This is a confirmation that the password for your account has just been changed.<span></span></div>
                <p></p>
                <div><strong>Device Information:</strong><strong></strong></div>
                <div>Country: {{country}}</div>
                <div>City: {{city}}</div>
                <div>IP: {{ip}}</div>
                <div>Platform: {{platform}}</div>
                <div>Browser: {{browser}}</div>
                <p></p>
                <div>If this was you, you can disregard this email. Otherwise reach the support team immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'EMAIL_VERIFICATION_SUCCESS_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Email Verified Successfully',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>You have successfully verified your email address. Please login to your account, purchase a package and start earning.<span></span></div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser,status',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'USER_ACCOUNT_ACTIVATED_EMAIL'=>[
        'is_active' => true,
        'subject'=>'Account Activated',
        'from'=>'info@site.com',
        'from_name'=>'Ride To Future',
        'body'=>'<p>Hello, {{full_name}}</p><p>Your account is activated by {{actor_full_name}}</p>',
        'variables'=>'full_name,actor_full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'USER_ACCOUNT_DEACTIVATED_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Account Deactivated',
        'from'=>'info@site.com',
        'from_name'=>'Ride To Future',
        'body'=>'<p>Hello, {{full_name}}</p><p>Your account is deactivated by {{actor_full_name}}</p>',
        'variables'=>'full_name,actor_full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'USER_ACCOUNT_ACTIVATED_AUTOMATICALLY_EMAIL'=>[

        'is_active' => true,
        'subject'=>'Account Activated',
        'from'=>'info@site.com',
        'from_name'=>'Ride To Future',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>Your account is activated, Now you can try again to login.<span></span></div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'TRANSACTION_PASSWORD_CHANGED_WARNING_EMAIL'=>[
        'is_active' => true,
        'subject'=>'Transaction Password Changed Successfully',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>This is a confirmation that the transaction password for your account has just been changed.<span></span></div>
                <p></p>
                <div><strong>Device Information:</strong><strong></strong></div>
                <div>Country: {{country}}</div>
                <div>City: {{city}}</div>
                <div>IP: {{ip}}</div>
                <div>Platform: {{platform}}</div>
                <div>Browser: {{browser}}</div>
                <p></p>
                <div>If this was you, you can disregard this email. Otherwise reach the support team immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name,country,city,ip,platform,browser',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'FREEZE_ACCOUNT_EMAIL'=>[
        'is_active' => true,
        'subject'=>'Your account has been frozen',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>This is a confirmation that your account has been frozen.<span></span></div>
                <p></p>
                <div>If this was you, you can disregard this email. Otherwise reach the support team immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'UNFREEZE_ACCOUNT_EMAIL'=>[
        'is_active' => true,
        'subject'=>'Your account has been unfrozen',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>This is a confirmation that your account has been unfrozen .<span></span></div>
                <p></p>
                <div>If this was you, you can disregard this email. Otherwise reach the support team immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'USER_ACCOUNT_HAS_BEEN_DEACTIVATED'=>[
        'is_active' => true,
        'subject'=>'Your account has been deactivated',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>This is a confirmation that your account has been deactivated .<span></span></div>
                <p></p>
                <div>If this was you, you can disregard this email. Otherwise reach the support team immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
    'USER_ACCOUNT_HAS_BEEN_ACTIVATED'=>[
        'is_active' => true,
        'subject'=>'Your account has been activated',
        'from'=>'it@ridetothefuture.com',
        'from_name'=>'Janex Support Team',
        'body'=><<<EOT
                <div>
                <p>Hello {{full_name}},</p>
                <div>This is a confirmation that your account has been activated .<span></span></div>
                <p></p>
                <div>If this was you, you can disregard this email. Otherwise reach the support team immediately.</div>
                <p></p>
                <p>Cheers,</p>
                <p>Janex Support Team</p>
                </div>
            EOT,
        'variables'=>'full_name',
        'variables_description'=>'full_name user full name',
        'type'=>'email',
    ],
];

if (!function_exists('user')) {

    function user(int $id): ?\User\Services\Grpc\User
    {
        $user_db = \User\Models\User::query()->find($id);

        $user = new \User\Services\Grpc\User();
        $user->setId((int)$user_db->id);
        $user->setFirstName($user_db->first_name);
        $user->setLastName($user_db->last_name);
        $user->setUsername($user_db->username);
        $user->setEmail($user_db->email);
        return $user;

    }
}


if (!function_exists('getUserGrpcServerClient')) {
    function getUserGrpcServerClient()
    {
        return new \User\Services\Grpc\UserServiceClient('staging-api-gateway.janex.org:9595', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}
if (!function_exists('updateUserFromGrpcServer')) {

    function updateUserFromGrpcServer($user_id): ?\User\Services\Grpc\User
    {
        if (!is_numeric($user_id))
            return null;

        $id = new \User\Services\Grpc\Id();
        $id->setId((int)$user_id);
        try {
            /** @var $user \User\Services\Grpc\User */
            list($user, $status) = getUserGrpcServerClient()->getUserById($id)->wait();
            if ($status->code == 0) {
                app(UserService::class)->userUpdate($user);
                return $user;
            }
            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
