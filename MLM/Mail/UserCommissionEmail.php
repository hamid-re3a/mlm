<?php

namespace MLM\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use MLM\Models\Commission;
use User\Models\User;

class UserCommissionEmail extends Mailable implements SettingableMail
{
    use Queueable, SerializesModels;

    public $user;
    private $commission;
    private $type;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $commission
     */
    public function __construct(User $user,Commission $commission, $type)
    {
        $this->user = $user;
        $this->commission = $commission;
        $this->type = $type;
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

        $setting['body'] = str_replace('{{full_name}}', (is_null($this->user->full_name) || empty($this->user->full_name)) ? 'Unknown' : $this->user->full_name, $setting['body']);
        $setting['body'] = str_replace('{{commission_type}}', (is_null($this->type) || empty($this->type)) ? 'Unknown' : $this->type, $setting['body']);
        $setting['body'] = str_replace('{{amount}}', (is_null($this->commission->amount) || empty($this->commission->amount)) ? 'Unknown' : $this->commission->amount, $setting['body']);

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html($setting['body']);
    }


    public function getSetting(): array
    {
        return getEmailAndTextSetting('USER_GOT_COMMISSION');
    }
}
