<?php

namespace MLM\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRankChangedEmail extends Mailable implements SettingableMail
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        $setting = $this->getSetting();

        $rank = getAndUpdateUserRank($this->user);
        $setting['body'] = str_replace('{{full_name}}', (is_null($this->user->full_name) || empty($this->user->full_name)) ? 'Unknown' : $this->user->full_name, $setting['body']);
        $setting['body'] = str_replace('{{rank}}', (is_null($rank) || empty($rank)) ? 'Unknown' : $rank->rank_name, $setting['body']);

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html($setting['body']);
    }


    public function getSetting(): array
    {
        return getEmailAndTextSetting('USER_ACCOUNT_HAS_BEEN_DEACTIVATED');
    }
}
